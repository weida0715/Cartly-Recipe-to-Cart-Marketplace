<?php
declare(strict_types=1);
namespace App\Controllers\Admin;

use App\Helpers\Controller;
use App\Helpers\AuthHelper;
use App\Helpers\Flash;
use App\Models\Report;
use App\Models\Product;
use App\Models\Recipe;
use App\Models\Review;

class AdminReportController extends Controller
{
    public function index(): void
    {
        AuthHelper::requireRole('admin');
        $report = new Report();
        $this->view('admin/reports', [
            'title' => 'Reports',
            'reports' => $report->moderationList(),
            'counts' => $report->statusCounts(),
            'loadD3' => true,
        ], 'layout/admin-layout');
    }

    public function resolve(string $id): void
    {
        AuthHelper::requireRole('admin');
        $this->requireCsrf();
        $action = (string) $this->input('action', 'reviewed');
        $report = (new Report())->find((int) $id);
        if (!$report) {
            Flash::set('error', 'Report not found.');
            $this->redirect('/admin/reports');
        }

        if ($action === 'hide') {
            $this->hideTarget($report);
        }
        $status = $action === 'resolve' || $action === 'hide' ? 'resolved' : 'reviewed';
        (new Report())->update((int) $id, ['status' => $status, 'resolved_at' => date('Y-m-d H:i:s')]);
        Flash::set('success', 'Report marked as ' . $status . '.');
        $this->redirect('/admin/reports');
    }

    private function hideTarget(array $report): void
    {
        $targetId = (int) $report['target_id'];
        switch ($report['target_type']) {
            case 'product':
                (new Product())->update($targetId, ['status' => 'inactive']);
                break;
            case 'recipe':
                (new Recipe())->update($targetId, ['status' => 'hidden']);
                break;
            case 'review':
                (new Review())->update($targetId, ['status' => 'hidden']);
                break;
        }
    }
}

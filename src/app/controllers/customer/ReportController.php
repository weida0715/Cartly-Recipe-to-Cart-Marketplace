<?php
declare(strict_types=1);
namespace App\Controllers\Customer;

use App\Helpers\Controller;
use App\Helpers\AuthHelper;
use App\Helpers\Flash;
use App\Models\Report;

class ReportController extends Controller
{
    public function store(): void
    {
        AuthHelper::requireLogin();
        $this->requireCsrf();

        $targetType = (string) $this->input('target_type', '');
        $targetId = (int) $this->input('target_id', 0);
        $reason = trim((string) $this->input('reason', ''));
        if (!in_array($targetType, ['product', 'recipe', 'review'], true) || $targetId < 1 || $reason === '') {
            Flash::set('error', 'Valid report target and reason are required.');
            $this->redirect('/');
        }

        (new Report())->createForUser((int) AuthHelper::id(), $targetType, $targetId, $reason);
        Flash::set('success', 'Report submitted for admin review.');
        $this->redirect($targetType === 'recipe' ? '/recipes/' . $targetId : '/products/' . $targetId);
    }
}
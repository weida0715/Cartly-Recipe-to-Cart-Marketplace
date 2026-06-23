<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\AuthHelper;
use App\Helpers\Controller;
use App\Helpers\Flash;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index(): void
    {
        AuthHelper::requireLogin();
        $this->view('notification/index', [
            'title' => 'Notifications',
            'notifications' => (new Notification())->allForUser((int) AuthHelper::id()),
        ]);
    }

    public function show(string $id): void
    {
        AuthHelper::requireLogin();
        $model = new Notification();
        $notification = $model->findForUser((int) $id, (int) AuthHelper::id());
        if (!$notification) {
            http_response_code(404);
            echo 'Notification not found';
            return;
        }
        $model->markRead((int) $id, (int) AuthHelper::id());
        $notification['is_read'] = 1;
        $this->view('notification/show', [
            'title' => 'Notification details',
            'notification' => $notification,
        ]);
    }

    public function markAllRead(): void
    {
        AuthHelper::requireLogin();
        $this->requireCsrf();
        (new Notification())->markAllRead((int) AuthHelper::id());
        Flash::set('success', 'Notifications marked as read.', false);
        $this->redirect('/notifications');
    }
}

<?php
// BLL — Notification Service
require_once __DIR__ . '/../dal/MiscDAL.php';

class NotificationService {
    private NotificationDAL $notifDAL;

    public function __construct() { $this->notifDAL = new NotificationDAL(); }

    public function send(int $userId, string $title, string $body, string $type = 'info'): void {
        try {
            $this->notifDAL->insert([
                'user_id' => $userId,
                'title'   => $title,
                'body'    => $body,
                'type'    => $type,
            ]);
        } catch (Throwable $e) { /* swallow */ }
    }

    public function listForUser(int $userId): array {
        $list = $this->notifDAL->findByUser($userId);
        $this->notifDAL->markAllRead($userId);
        return $list;
    }
}

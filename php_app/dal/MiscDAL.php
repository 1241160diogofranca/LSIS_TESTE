<?php
require_once __DIR__ . '/BaseDAL.php';

class NotificationDAL extends BaseDAL {
    protected string $table = 'notifications';

    public function findByUser(int $userId): array {
        $st = $this->db->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY id DESC");
        $st->execute([$userId]);
        return $st->fetchAll();
    }

    public function markAllRead(int $userId): void {
        $st = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
        $st->execute([$userId]);
    }
}

class LogDAL extends BaseDAL {
    protected string $table = 'logs';
}

class StoreDAL extends BaseDAL {
    protected string $table = 'stores';
}

class CatDAL extends BaseDAL {
    protected string $table = 'cats';
}

class SettingDAL extends BaseDAL {
    protected string $table = 'settings';
    protected string $pk = 'k';

    public function all(): array {
        return $this->db->query("SELECT * FROM settings")->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    public function set(string $key, string $value): void {
        $st = $this->db->prepare("INSERT INTO settings (k,v) VALUES (?,?) ON DUPLICATE KEY UPDATE v=VALUES(v)");
        $st->execute([$key, $value]);
    }
}

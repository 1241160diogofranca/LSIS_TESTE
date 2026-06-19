<?php
require_once __DIR__ . '/BaseDAL.php';

class OrderDAL extends BaseDAL {
    protected string $table = 'orders';

    public function findByUser(int $userId, ?int $limit = null): array {
        $sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC";
        if ($limit) $sql .= " LIMIT " . (int)$limit;
        $st = $this->db->prepare($sql);
        $st->execute([$userId]);
        return $st->fetchAll();
    }

    public function findByIdAndUser(int $id, int $userId): ?array {
        $st = $this->db->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
        $st->execute([$id, $userId]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public function allWithClient(): array {
        return $this->db->query("
            SELECT o.*, u.name AS client, u.email
            FROM orders o JOIN users u ON u.id = o.user_id
            ORDER BY o.id DESC")->fetchAll();
    }

    public function recentWithClient(int $limit = 8): array {
        $st = $this->db->prepare("
            SELECT o.*, u.name AS client
            FROM orders o JOIN users u ON u.id = o.user_id
            ORDER BY o.id DESC LIMIT ?");
        $st->bindValue(1, $limit, PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll();
    }

    public function updateStatus(int $id, string $status): void {
        $st = $this->db->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $st->execute([$status, $id]);
    }

    public function updatePayment(int $id, string $payStatus, string $orderStatus): void {
        $st = $this->db->prepare("UPDATE orders SET payment_status = ?, status = ? WHERE id = ?");
        $st->execute([$payStatus, $orderStatus, $id]);
    }

    public function totalRevenuePaid(): float {
        return (float)$this->db->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE payment_status='paid'")->fetchColumn();
    }

    public function revenueByMonth(int $months = 6): array {
        $st = $this->db->prepare("
            SELECT DATE_FORMAT(created_at,'%Y-%m') AS ym, COUNT(*) AS n, COALESCE(SUM(total),0) AS tot
            FROM orders WHERE payment_status='paid'
            GROUP BY ym ORDER BY ym DESC LIMIT ?");
        $st->bindValue(1, $months, PDO::PARAM_INT);
        $st->execute();
        return array_reverse($st->fetchAll());
    }
}

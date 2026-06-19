<?php
require_once __DIR__ . '/BaseDAL.php';

class OrderItemDAL extends BaseDAL {
    protected string $table = 'order_items';

    public function findByOrder(int $orderId): array {
        $st = $this->db->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $st->execute([$orderId]);
        return $st->fetchAll();
    }

    public function countByOrder(int $orderId): int {
        $st = $this->db->prepare("SELECT COUNT(*) FROM order_items WHERE order_id = ?");
        $st->execute([$orderId]);
        return (int)$st->fetchColumn();
    }

    public function revenueByCategory(): array {
        return $this->db->query("
            SELECT c.name, COALESCE(SUM(oi.line_total),0) AS tot
            FROM categories c
            LEFT JOIN products p   ON p.category_id = c.id
            LEFT JOIN order_items oi ON oi.product_id = p.id
            LEFT JOIN orders o     ON o.id = oi.order_id AND o.payment_status='paid'
            GROUP BY c.id ORDER BY tot DESC")->fetchAll();
    }
}

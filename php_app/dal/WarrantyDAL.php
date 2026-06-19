<?php
require_once __DIR__ . '/BaseDAL.php';

class WarrantyDAL extends BaseDAL {
    protected string $table = 'warranties';

    public function findByUserWithProduct(int $userId): array {
        $st = $this->db->prepare("
            SELECT w.*, p.name AS product_name, p.image_url, p.warranty_months
            FROM warranties w JOIN products p ON p.id = w.product_id
            WHERE w.user_id = ? ORDER BY w.id DESC");
        $st->execute([$userId]);
        return $st->fetchAll();
    }

    public function findRecentByUser(int $userId, int $limit = 5): array {
        $st = $this->db->prepare("
            SELECT w.*, p.name AS product_name
            FROM warranties w JOIN products p ON p.id = w.product_id
            WHERE w.user_id = ? ORDER BY w.id DESC LIMIT ?");
        $st->bindValue(1, $userId, PDO::PARAM_INT);
        $st->bindValue(2, $limit, PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll();
    }
}

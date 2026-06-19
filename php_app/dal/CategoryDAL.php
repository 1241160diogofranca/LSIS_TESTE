<?php
require_once __DIR__ . '/BaseDAL.php';

class CategoryDAL extends BaseDAL {
    protected string $table = 'categories';

    public function allWithProductCount(): array {
        return $this->db->query("
            SELECT c.*, (SELECT COUNT(*) FROM products WHERE category_id = c.id) AS n
            FROM categories c")->fetchAll();
    }
}

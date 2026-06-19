<?php
require_once __DIR__ . '/BaseDAL.php';

class ProductDAL extends BaseDAL {
    protected string $table = 'products';

    public function findByIdWithCategory(int $id): ?array {
        $st = $this->db->prepare("
            SELECT p.*, c.slug AS cat_slug, c.name AS cat_name
            FROM products p JOIN categories c ON c.id = p.category_id
            WHERE p.id = ?");
        $st->execute([$id]);
        $row = $st->fetch();
        return $row ?: null;
    }

    /** Pesquisa com filtros opcionais. */
    public function search(?string $cat_slug = null, ?string $q = null, ?string $brand = null, ?float $maxPrice = null): array {
        $sql = "SELECT p.*, c.slug AS cat_slug, c.name AS cat_name
                FROM products p JOIN categories c ON c.id = p.category_id
                WHERE 1=1";
        $args = [];
        if ($cat_slug) { $sql .= " AND c.slug = ?"; $args[] = $cat_slug; }
        if ($q)        { $sql .= " AND (p.name LIKE ? OR p.model LIKE ? OR p.sku LIKE ?)"; $args[] = "%$q%"; $args[] = "%$q%"; $args[] = "%$q%"; }
        if ($brand)    { $sql .= " AND p.brand = ?"; $args[] = $brand; }
        if ($maxPrice) { $sql .= " AND p.price <= ?"; $args[] = $maxPrice; }
        $sql .= " ORDER BY p.name";
        $st = $this->db->prepare($sql);
        $st->execute($args);
        return $st->fetchAll();
    }

    public function featured(int $limit = 4): array {
        return $this->db->query("
            SELECT p.*, c.slug AS cat_slug, c.name AS cat_name
            FROM products p JOIN categories c ON c.id = p.category_id
            ORDER BY p.id LIMIT {$limit}")->fetchAll();
    }

    public function distinctBrands(): array {
        return $this->db->query("SELECT DISTINCT brand FROM products")->fetchAll(PDO::FETCH_COLUMN);
    }

    public function allWithCategoryName(): array {
        return $this->db->query("
            SELECT p.*, c.name AS cat_name
            FROM products p JOIN categories c ON c.id = p.category_id
            ORDER BY p.id DESC")->fetchAll();
    }
}

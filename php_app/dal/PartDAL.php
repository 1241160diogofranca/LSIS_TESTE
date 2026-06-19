<?php
require_once __DIR__ . '/BaseDAL.php';

class PartDAL extends BaseDAL {
    protected string $table = 'parts';

    public function search(?string $q = null): array {
        $sql = "SELECT * FROM parts";
        $args = [];
        if ($q) {
            $sql .= " WHERE name LIKE ? OR sku LIKE ? OR compatible_models LIKE ?";
            $args = ["%$q%","%$q%","%$q%"];
        }
        $sql .= " ORDER BY name";
        $st = $this->db->prepare($sql);
        $st->execute($args);
        return $st->fetchAll();
    }

    public function findCompatibleWith(string $model, int $limit = 6): array {
        $st = $this->db->prepare("SELECT * FROM parts WHERE compatible_models LIKE ? LIMIT {$limit}");
        $st->execute(["%{$model}%"]);
        return $st->fetchAll();
    }
}

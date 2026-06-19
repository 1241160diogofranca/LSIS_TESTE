<?php
require_once __DIR__ . '/BaseDAL.php';

class UserDAL extends BaseDAL {
    protected string $table = 'users';

    public function findByEmail(string $email): ?array {
        $st = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $st->execute([$email]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public function allWithStoreCat(): array {
        $sql = "SELECT u.*, s.name AS store_name, c.name AS cat_name
                FROM users u
                LEFT JOIN stores s ON s.id = u.store_id
                LEFT JOIN cats   c ON c.id = u.cat_id
                ORDER BY u.id DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function findByRoleAndCat(string $role, int $cat_id): array {
        $st = $this->db->prepare("SELECT id FROM users WHERE role = ? AND cat_id = ?");
        $st->execute([$role, $cat_id]);
        return $st->fetchAll();
    }

    public function findByRole(string $role): array {
        $st = $this->db->prepare("SELECT id FROM users WHERE role = ?");
        $st->execute([$role]);
        return $st->fetchAll();
    }
}

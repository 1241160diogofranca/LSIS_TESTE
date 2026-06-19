<?php
require_once __DIR__ . '/BaseDAL.php';

class ServiceTicketDAL extends BaseDAL {
    protected string $table = 'service_tickets';

    public function findByUserWithProduct(int $userId): array {
        $st = $this->db->prepare("
            SELECT t.*, p.name AS product_name, c.name AS cat_name
            FROM service_tickets t
            LEFT JOIN products p ON p.id = t.product_id
            LEFT JOIN cats c     ON c.id = t.cat_id
            WHERE t.user_id = ? ORDER BY t.id DESC");
        $st->execute([$userId]);
        return $st->fetchAll();
    }

    public function findRecentByUser(int $userId, int $limit = 5): array {
        $st = $this->db->prepare("
            SELECT * FROM service_tickets WHERE user_id = ? ORDER BY id DESC LIMIT ?");
        $st->bindValue(1, $userId, PDO::PARAM_INT);
        $st->bindValue(2, $limit, PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll();
    }

    public function findByCat(int $catId, ?int $limit = null): array {
        $sql = "SELECT t.*, u.name AS client, p.name AS product_name
                FROM service_tickets t
                JOIN users u ON u.id = t.user_id
                LEFT JOIN products p ON p.id = t.product_id
                WHERE t.cat_id = ? ORDER BY t.id DESC";
        if ($limit) $sql .= " LIMIT " . (int)$limit;
        $st = $this->db->prepare($sql);
        $st->execute([$catId]);
        return $st->fetchAll();
    }

    public function findByCatAndId(int $id, int $catId): ?array {
        $st = $this->db->prepare("
            SELECT t.*, u.name AS client, u.email AS client_email, u.phone AS client_phone,
                   p.name AS product_name, p.model
            FROM service_tickets t
            JOIN users u ON u.id = t.user_id
            LEFT JOIN products p ON p.id = t.product_id
            WHERE t.id = ? AND t.cat_id = ?");
        $st->execute([$id, $catId]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public function findByIdAndUser(int $id, int $userId): ?array {
        $st = $this->db->prepare("
            SELECT t.*, p.name AS product_name, c.name AS cat_name
            FROM service_tickets t
            LEFT JOIN products p ON p.id = t.product_id
            LEFT JOIN cats c     ON c.id = t.cat_id
            WHERE t.id = ? AND t.user_id = ?");
        $st->execute([$id, $userId]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public function allWithClient(): array {
        return $this->db->query("
            SELECT t.*, u.name AS client, c.name AS cat_name
            FROM service_tickets t
            JOIN users u ON u.id = t.user_id
            LEFT JOIN cats c ON c.id = t.cat_id
            ORDER BY t.id DESC")->fetchAll();
    }

    public function recentWithClient(int $limit = 6): array {
        $st = $this->db->prepare("
            SELECT t.*, u.name AS client
            FROM service_tickets t JOIN users u ON u.id = t.user_id
            ORDER BY t.id DESC LIMIT ?");
        $st->bindValue(1, $limit, PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll();
    }

    public function countByCatAndStatus(int $catId, array $statuses): int {
        $in = implode(',', array_fill(0, count($statuses), '?'));
        $st = $this->db->prepare("SELECT COUNT(*) FROM service_tickets WHERE cat_id = ? AND status IN ({$in})");
        $st->execute(array_merge([$catId], $statuses));
        return (int)$st->fetchColumn();
    }

    public function averageResolutionHoursByCat(int $catId): ?float {
        $st = $this->db->prepare("
            SELECT AVG(TIMESTAMPDIFF(HOUR, opened_at, closed_at))
            FROM service_tickets WHERE cat_id = ? AND closed_at IS NOT NULL");
        $st->execute([$catId]);
        $v = $st->fetchColumn();
        return $v === false || $v === null ? null : (float)$v;
    }

    public function countByStatusGroup(): array {
        return $this->db->query("SELECT status, COUNT(*) AS n FROM service_tickets GROUP BY status")->fetchAll();
    }

    public function catPerformance(): array {
        return $this->db->query("
            SELECT c.name, COUNT(t.id) AS n,
                   AVG(TIMESTAMPDIFF(HOUR, t.opened_at, t.closed_at)) AS avg_hours
            FROM cats c
            LEFT JOIN service_tickets t ON t.cat_id = c.id AND t.closed_at IS NOT NULL
            GROUP BY c.id")->fetchAll();
    }

    public function updateIntervention(int $id, array $data): void {
        $sql = "UPDATE service_tickets SET diagnosis=?, intervention=?, parts_used=?, status=?, closed_at=? WHERE id=?";
        $st = $this->db->prepare($sql);
        $st->execute([$data['diagnosis'], $data['intervention'], $data['parts_used'], $data['status'], $data['closed_at'], $id]);
    }

    public function assignToCat(int $id, ?int $catId): void {
        $st = $this->db->prepare("UPDATE service_tickets SET cat_id=?, status='assigned' WHERE id=?");
        $st->execute([$catId, $id]);
    }
}

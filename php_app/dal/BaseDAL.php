<?php
// BaseDAL — classe base para todas as Data Access Layer classes.
// Encapsula o acesso PDO, mapeia tabela <-> objecto e fornece CRUD genérico.
require_once __DIR__ . '/../config/db.php';

abstract class BaseDAL {
    protected PDO $db;
    protected string $table;
    protected string $pk = 'id';

    public function __construct() {
        $this->db = get_db();
    }

    /** Encontra um registo por chave primária. */
    public function findById(int $id): ?array {
        $st = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->pk} = ?");
        $st->execute([$id]);
        $row = $st->fetch();
        return $row ?: null;
    }

    /** Devolve todos os registos (com ORDER BY opcional). */
    public function findAll(string $orderBy = 'id DESC'): array {
        return $this->db->query("SELECT * FROM {$this->table} ORDER BY {$orderBy}")->fetchAll();
    }

    /** Cria registo a partir de array associativo coluna=>valor; devolve novo id. */
    public function insert(array $data): int {
        $cols = array_keys($data);
        $params = array_values($data);
        $placeholders = implode(',', array_fill(0, count($cols), '?'));
        $colList = implode(',', $cols);
        $sql = "INSERT INTO {$this->table} ({$colList}) VALUES ({$placeholders})";
        $st = $this->db->prepare($sql);
        $st->execute($params);
        return (int)$this->db->lastInsertId();
    }

    /** Actualiza registo por PK. Devolve nº linhas afectadas. */
    public function update(int $id, array $data): int {
        $sets = [];
        $params = [];
        foreach ($data as $col => $val) {
            $sets[] = "{$col} = ?";
            $params[] = $val;
        }
        $params[] = $id;
        $sql = "UPDATE {$this->table} SET " . implode(', ', $sets) . " WHERE {$this->pk} = ?";
        $st = $this->db->prepare($sql);
        $st->execute($params);
        return $st->rowCount();
    }

    /** Apaga registo por PK. */
    public function delete(int $id): int {
        $st = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->pk} = ?");
        $st->execute([$id]);
        return $st->rowCount();
    }

    /** Conta total de registos opcionalmente com WHERE. */
    public function count(string $where = '', array $params = []): int {
        $sql = "SELECT COUNT(*) FROM {$this->table}" . ($where ? " WHERE {$where}" : "");
        $st = $this->db->prepare($sql);
        $st->execute($params);
        return (int)$st->fetchColumn();
    }
}

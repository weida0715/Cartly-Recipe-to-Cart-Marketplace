<?php
declare(strict_types=1);

namespace App\Helpers;

use PDO;

abstract class Model
{
    protected string $table = '';
    protected string $primaryKey = 'id';

    protected function db(): PDO
    {
        return db();
    }

    public function all(string $order = ''): array
    {
        $sql = "SELECT * FROM {$this->table}" . ($order ? " ORDER BY {$order}" : '');
        return $this->db()->query($sql)->fetchAll();
    }

    public function find($id): ?array
    {
        $stmt = $this->db()->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function where(string $column, $value, string $order = ''): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = :v" . ($order ? " ORDER BY {$order}" : '');
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([':v' => $value]);
        return $stmt->fetchAll();
    }

    public function insert(array $data): int
    {
        $cols = array_keys($data);
        $place = array_map(fn($c) => ':' . $c, $cols);
        $sql = "INSERT INTO {$this->table} (" . implode(',', $cols) . ") VALUES (" . implode(',', $place) . ")";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute($data);
        return (int) $this->db()->lastInsertId();
    }

    public function update($id, array $data): bool
    {
        $set = implode(',', array_map(fn($c) => "{$c}=:{$c}", array_keys($data)));
        $sql = "UPDATE {$this->table} SET {$set} WHERE {$this->primaryKey} = :_id";
        $stmt = $this->db()->prepare($sql);
        $data['_id'] = $id;
        return $stmt->execute($data);
    }

    public function delete($id): bool
    {
        $stmt = $this->db()->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id");
        return $stmt->execute([':id' => $id]);
    }

    /** Run an arbitrary SELECT with bound params. */
    public function query(string $sql, array $params = []): array
    {
        $stmt = $this->db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}

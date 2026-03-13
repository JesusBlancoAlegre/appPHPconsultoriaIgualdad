<?php
/**
 * Clase base para todos los modelos
 * Maneja operaciones CRUD comunes
 */

class Model {
    protected $pdo;
    protected $table;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Obtener todos los registros
     */
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll();
    }

    /**
     * Obtener por ID
     */
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Insertar registro
     */
    public function insert($data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute(array_values($data));
    }

    /**
     * Actualizar registro
     */
    public function update($id, $data) {
        $set = implode(', ', array_map(fn($k) => "$k = ?", array_keys($data)));
        $sql = "UPDATE {$this->table} SET {$set} WHERE id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        $values = array_merge(array_values($data), [$id]);
        
        return $stmt->execute($values);
    }

    /**
     * Eliminar registro
     */
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>
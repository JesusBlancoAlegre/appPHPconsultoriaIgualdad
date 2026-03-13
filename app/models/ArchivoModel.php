<?php
require_once __DIR__ . '/Model.php';

class ArchivoModel extends Model {
    protected $table = 'archivos';

    /**
     * Guardar un archivo
     */
    public function guardar($data) {
        $sql = "INSERT INTO {$this->table} 
                (tipo, nombre_original, nombre_guardado, ruta_relativa, tamano_bytes, mime, sha256, id_cliente_medida) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['tipo'] ?? 'REGISTRO_RETRIBUTIVO',
            $data['nombre_original'],
            $data['nombre_guardado'],
            $data['ruta_relativa'],
            $data['tamano_bytes'],
            $data['mime'] ?? null,
            $data['sha256'] ?? null,
            $data['id_cliente_medida']
        ]);

        return $this->pdo->lastInsertId();
    }

    /**
     * Obtener archivos por cliente_medida
     */
    public function getByClienteMedida($id_cliente_medida) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM {$this->table} 
            WHERE id_cliente_medida = ? 
            ORDER BY subido_en DESC
        ");
        $stmt->execute([$id_cliente_medida]);
        return $stmt->fetchAll();
    }

    /**
     * Obtener por tipo
     */
    public function getByTipo($tipo) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE tipo = ? ORDER BY subido_en DESC");
        $stmt->execute([$tipo]);
        return $stmt->fetchAll();
    }
}
?>
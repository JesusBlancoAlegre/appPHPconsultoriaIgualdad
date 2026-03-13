<?php
require_once __DIR__ . '/Model.php';

class UsuarioModel extends Model {
    protected $table = 'usuario';

    /**
     * Obtener usuario por nombre
     */
    public function getByNombre($nombre_usuario) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE nombre_usuario = ?");
        $stmt->execute([$nombre_usuario]);
        return $stmt->fetch();
    }

    /**
     * Obtener usuario por email
     */
    public function getByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    /**
     * Validar login
     */
    public function validarLogin($usuario, $password) {
        $user = $this->getByNombre($usuario);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return null;
    }
}
?>
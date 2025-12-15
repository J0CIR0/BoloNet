<?php
require_once __DIR__ . '/../config/conexion.php';

class RolPermiso {
    private $db;
    
    public function __construct() {
        require_once __DIR__ . '/Database.php';
        $this->db = Database::getConnection();
    }
    
    public function getPermisosByRol($rol_id) {
        $sql = "SELECT p.* FROM permiso p 
                JOIN rol_permiso_detalle rpd ON p.id = rpd.permiso_id 
                WHERE rpd.rol_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $rol_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getPermisoIdsByRol($rol_id) {
        $sql = "SELECT permiso_id FROM rol_permiso_detalle WHERE rol_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $rol_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $ids = [];
        while ($row = $result->fetch_assoc()) {
            $ids[] = $row['permiso_id'];
        }
        return $ids;
    }
    
    public function updatePermisos($rol_id, $permiso_ids) {
        $this->db->begin_transaction();
        
        try {
            $sql_delete = "DELETE FROM rol_permiso_detalle WHERE rol_id = ?";
            $stmt = $this->db->prepare($sql_delete);
            $stmt->bind_param("i", $rol_id);
            $stmt->execute();
            
            if (!empty($permiso_ids)) {
                $sql_insert = "INSERT INTO rol_permiso_detalle (rol_id, permiso_id) VALUES (?, ?)";
                $stmt = $this->db->prepare($sql_insert);
                
                foreach ($permiso_ids as $permiso_id) {
                    $stmt->bind_param("ii", $rol_id, $permiso_id);
                    $stmt->execute();
                }
            }
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }
}
?>
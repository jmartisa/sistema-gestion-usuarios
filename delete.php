<?php
// 1. Iniciar sesión y proteger la ruta
session_start();
if (!isset($_SESSION['idusuario'])) {
    header("Location: login.php");
    exit();
}

include_once 'conexion.php';

// 2. Verificar si viene el ID por la URL (Método GET)
if (isset($_GET['idusuario'])) {
    
    // ¡Excelente práctica! Forzamos a que sea un número entero
    $idusuario = (int) $_GET['idusuario'];

    try {
        // 3. CORRECCIÓN: La tabla es 'usuario', no 'tblrol'
        // (Si en tu base de datos real tiene otro nombre, cámbialo aquí)
        $delete = $con->prepare('DELETE FROM usuario WHERE idusuario = :idusuario');
        $delete->bindParam(':idusuario', $idusuario, PDO::PARAM_INT);
        
        if ($delete->execute()) {
            // Guardamos un mensaje de éxito en la sesión para mostrarlo en el index
            $_SESSION['mensaje'] = "Usuario eliminado correctamente.";
            $_SESSION['tipo_mensaje'] = "success";
        } else {
            $_SESSION['mensaje'] = "No se pudo eliminar el usuario.";
            $_SESSION['tipo_mensaje'] = "danger";
        }
    } catch (PDOException $e) {
        // 4. Manejo de errores: Si hay una restricción de clave foránea, lo capturamos
        error_log("Error al eliminar usuario ID $idusuario: " . $e->getMessage());
        
        $_SESSION['mensaje'] = "Error: No se puede eliminar este usuario porque tiene registros asociados en el sistema.";
        $_SESSION['tipo_mensaje'] = "warning";
    }
} else {
    $_SESSION['mensaje'] = "Solicitud no válida: Falta el ID del usuario.";
    $_SESSION['tipo_mensaje'] = "danger";
}

// 5. Redirigir siempre de vuelta al listado
header('Location: index.php');
exit();
?>
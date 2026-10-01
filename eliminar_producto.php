<?php
session_start();
if (!isset($_SESSION['idusuario'])) {
    header("Location: login.php");
    exit();
}

include_once 'conexion.php';

if (isset($_GET['id'])) {
    $idproducto = (int) $_GET['id'];

    try {
        $delete = $con->prepare('DELETE FROM producto WHERE Idproducto = :idproducto');
        $delete->bindParam(':idproducto', $idproducto, PDO::PARAM_INT);
        
        if ($delete->execute()) {
            $_SESSION['mensaje'] = "Producto eliminado correctamente.";
            $_SESSION['tipo_mensaje'] = "success";
        } else {
            $_SESSION['mensaje'] = "No se pudo eliminar el producto.";
            $_SESSION['tipo_mensaje'] = "danger";
        }
    } catch (PDOException $e) {
        error_log("Error al eliminar producto ID $idproducto: " . $e->getMessage());
        $_SESSION['mensaje'] = "Error al eliminar el producto. Verifica que no esté asociado a otros registros.";
        $_SESSION['tipo_mensaje'] = "warning";
    }
} else {
    $_SESSION['mensaje'] = "Solicitud no válida: Falta el ID del producto.";
    $_SESSION['tipo_mensaje'] = "danger";
}

header('Location: productos.php');
exit();
?>
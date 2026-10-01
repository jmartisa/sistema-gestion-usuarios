<?php 
$titulo_pagina = "Nuevo Producto - Sistema GD";
include_once 'includes/header.php'; 
include_once 'conexion.php';

$mensaje = '';
$tipo_mensaje = '';

// Obtener las categorías para el select
$categorias = $con->query("SELECT idcate_prod, categoria FROM categorizacion_productos WHERE estado = 'activo' ORDER BY categoria ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar'])) {
    
    $nombre = trim($_POST['nombre']);
    $valor = trim($_POST['valor']);
    $idcategoria = trim($_POST['idcategoria']);

    if (!empty($nombre) && !empty($valor) && !empty($idcategoria)) {
        try {
            $sql = "INSERT INTO producto (NombePro, Valor, idcategoria) 
                    VALUES (:nombre, :valor, :idcategoria)";
            
            $stmt = $con->prepare($sql);
            $stmt->execute([
                ':nombre' => $nombre,
                ':valor' => $valor,
                ':idcategoria' => $idcategoria
            ]);

            $_SESSION['mensaje'] = "Producto creado exitosamente.";
            $_SESSION['tipo_mensaje'] = "success";
            header("Location: productos.php");
            exit();

        } catch (PDOException $e) {
            $mensaje = "Error al crear el producto: " . $e->getMessage();
            $tipo_mensaje = "danger";
        }
    } else {
        $mensaje = "Por favor, completa todos los campos.";
        $tipo_mensaje = "warning";
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0"><i class="bi bi-box"></i> Registrar Nuevo Producto</h4>
            </div>
            <div class="card-body">
                
                <?php if ($mensaje): ?>
                    <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
                        <?php echo $mensaje; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Nombre del Producto</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Valor (Precio)</label>
                        <input type="number" name="valor" class="form-control" min="0" step="100" required>
                        <div class="form-text">Ingresa el valor en pesos, sin puntos ni comas.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Categoría</label>
                        <select name="idcategoria" class="form-select" required>
                            <option value="" disabled selected>Seleccione una categoría...</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?php echo $cat['idcate_prod']; ?>">
                                    <?php echo htmlspecialchars($cat['categoria']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="productos.php" class="btn btn-secondary me-md-2">Cancelar</a>
                        <button type="submit" name="guardar" class="btn btn-success">
                            <i class="bi bi-save-fill"></i> Guardar Producto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
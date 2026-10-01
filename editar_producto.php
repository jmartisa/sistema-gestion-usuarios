<?php 
$titulo_pagina = "Editar Producto - Sistema GD";
include_once 'includes/header.php'; 
include_once 'conexion.php';

$mensaje = '';
$tipo_mensaje = '';

// 1. Obtener el ID del producto a editar
$idproducto = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($idproducto <= 0) {
    header("Location: productos.php");
    exit();
}

// 2. Buscar el producto actual
$stmt = $con->prepare("SELECT * FROM producto WHERE Idproducto = :id LIMIT 1");
$stmt->execute([':id' => $idproducto]);
$producto = $stmt->fetch();

// Si no existe, redirigir
if (!$producto) {
    $_SESSION['mensaje'] = "El producto no existe.";
    $_SESSION['tipo_mensaje'] = "warning";
    header("Location: productos.php");
    exit();
}

// 3. Obtener las categorías para el select
$categorias = $con->query("SELECT idcate_prod, categoria FROM categorizacion_productos WHERE estado = 'activo' ORDER BY categoria ASC")->fetchAll();

// 4. Procesar la actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar'])) {
    
    $nombre = trim($_POST['nombre']);
    $valor = trim($_POST['valor']);
    $idcategoria = trim($_POST['idcategoria']);

    if (!empty($nombre) && !empty($valor) && !empty($idcategoria)) {
        try {
            $sql = "UPDATE producto SET 
                    NombePro = :nombre, 
                    Valor = :valor, 
                    idcategoria = :idcategoria 
                    WHERE Idproducto = :idproducto";
            
            $stmt = $con->prepare($sql);
            $stmt->execute([
                ':nombre' => $nombre,
                ':valor' => $valor,
                ':idcategoria' => $idcategoria,
                ':idproducto' => $idproducto
            ]);

            $_SESSION['mensaje'] = "Producto actualizado correctamente.";
            $_SESSION['tipo_mensaje'] = "success";
            header("Location: productos.php");
            exit();

        } catch (PDOException $e) {
            $mensaje = "Error al actualizar: " . $e->getMessage();
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
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0"><i class="bi bi-pencil-square"></i> Editar Producto</h4>
            </div>
            <div class="card-body">
                
                <?php if ($mensaje): ?>
                    <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
                        <?php echo $mensaje; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <!-- ID (solo lectura, no se puede cambiar) -->
                    <div class="mb-3">
                        <label class="form-label">ID del Producto</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($producto['Idproducto']); ?>" disabled>
                    </div>

                    <!-- Nombre -->
                    <div class="mb-3">
                        <label class="form-label">Nombre del Producto</label>
                        <input type="text" name="nombre" class="form-control" 
                               value="<?php echo htmlspecialchars($producto['NombePro']); ?>" required>
                    </div>

                    <!-- Valor -->
                    <div class="mb-3">
                        <label class="form-label">Valor (Precio)</label>
                        <input type="number" name="valor" class="form-control" min="0" step="100" 
                               value="<?php echo htmlspecialchars($producto['Valor']); ?>" required>
                        <div class="form-text">Ingresa el valor en pesos, sin puntos ni comas.</div>
                    </div>

                    <!-- Categoría -->
                    <div class="mb-3">
                        <label class="form-label">Categoría</label>
                        <select name="idcategoria" class="form-select" required>
                            <option value="" disabled>Seleccione una categoría...</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?php echo $cat['idcate_prod']; ?>" 
                                    <?php echo ($producto['idcategoria'] == $cat['idcate_prod']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['categoria']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="productos.php" class="btn btn-secondary me-md-2">Cancelar</a>
                        <button type="submit" name="guardar" class="btn btn-warning text-dark">
                            <i class="bi bi-save-fill"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
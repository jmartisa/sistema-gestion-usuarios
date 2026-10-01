<?php 
$titulo_pagina = "Listado de Productos - Sistema SGD";
include_once 'includes/header.php'; 
include_once 'conexion.php';

$mensaje = '';
$tipo_mensaje = '';
$resultado = [];
$buscar_prod = '';

// Procesar búsqueda
// Procesar búsqueda
if (isset($_POST['btn_buscar']) && !empty(trim($_POST['buscar']))) {
    $buscar_prod = trim($_POST['buscar']);
    $sql = "SELECT p.Idproducto, p.NombePro, p.Valor, c.categoria 
            FROM producto p 
            LEFT JOIN categorizacion_productos c ON p.idcategoria = c.idcate_prod 
            WHERE p.Idproducto LIKE :campo OR p.NombePro LIKE :campo OR c.categoria LIKE :campo 
            ORDER BY p.Idproducto ASC";
    $sent_select = $con->prepare($sql);
    $sent_select->execute([':campo' => "%" . $buscar_prod . "%"]);
    $resultado = $sent_select->fetchAll();
} else {
    $sql = "SELECT p.Idproducto, p.NombePro, p.Valor, c.categoria 
            FROM producto p 
            LEFT JOIN categorizacion_productos c ON p.idcategoria = c.idcate_prod 
            ORDER BY p.Idproducto ASC";
    $sent_select = $con->prepare($sql);
    $sent_select->execute();
    $resultado = $sent_select->fetchAll();
}
?>

<!-- Mostrar mensajes de sesión -->
<?php if (isset($_SESSION['mensaje'])): ?>
    <div class="alert alert-<?php echo $_SESSION['tipo_mensaje']; ?> alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['mensaje']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php 
    unset($_SESSION['mensaje']); 
    unset($_SESSION['tipo_mensaje']); 
    ?>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="bi bi-box"></i> Gestión de Productos</h2>
    <a href="insertar_producto.php" class="btn btn-success">
        <i class="bi bi-plus-circle"></i> Nuevo Producto
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <!-- Buscador -->
        <div class="row mb-3">
            <div class="col-md-6">
                <form method="POST" action="" class="d-flex gap-2">
                    <input type="text" name="buscar" class="form-control" 
                           placeholder="Buscar por ID, Nombre o Categoría..." 
                           value="<?php echo htmlspecialchars($buscar_prod); ?>">
                    <button type="submit" name="btn_buscar" class="btn btn-outline-primary">
                        <i class="bi bi-search"></i> Buscar
                    </button>
                    <a href="productos.php" class="btn btn-secondary">Limpiar</a>
                </form>
            </div>
        </div>

        <!-- Tabla de productos -->
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre del Producto</th>
                        <th>Valor</th>
                        <th>Categoría</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($resultado) > 0): ?>
                   <?php foreach ($resultado as $fila): ?>
    <tr>
        <td><?php echo htmlspecialchars($fila['Idproducto']); ?></td>
        
        <!-- CAMBIO: Usamos NombePro (tal cual está en tu BD) -->
        <td><?php echo htmlspecialchars($fila['NombePro']); ?></td>
        
        <td>
            <span class="badge bg-success">
                <!-- CAMBIO: Usamos Valor con V mayúscula -->
                $<?php echo number_format($fila['Valor'], 0, ',', '.'); ?>
            </span>
        </td>
        <td>
            <span class="badge bg-info text-dark">
                <?php echo htmlspecialchars($fila['categoria'] ?? 'Sin categoría'); ?>
            </span>
        </td>
        <td class="text-center">
            <a href="editar_producto.php?id=<?php echo $fila['Idproducto']; ?>" 
               class="btn btn-sm btn-warning text-white" title="Editar">
                <i class="bi bi-pencil-square"></i>
            </a>
            <a href="eliminar_producto.php?id=<?php echo $fila['Idproducto']; ?>" 
               class="btn btn-sm btn-danger" 
               title="Eliminar"
               onclick="return confirm('¿Estás seguro de eliminar este producto?');">
                <i class="bi bi-trash-fill"></i>
            </a>
        </td>
    </tr>
<?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                No hay productos registrados. 
                                <?php echo !empty($buscar_prod) ? 'Intenta con otro término de búsqueda.' : '¡Crea el primero!'; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
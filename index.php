<?php 
$titulo_pagina = "Listado de Usuarios - Sistema GD";
include_once 'includes/header.php'; 
include_once 'conexion.php';

// Variables por defecto
$resultado = [];
$buscar_user = '';

// Lógica de búsqueda
// Variables por defecto
$resultado = [];
$buscar_user = '';

// Lógica de búsqueda
if (isset($_POST['btn_buscar'])) {
    $buscar_user = trim($_POST['buscar'] ?? '');
    
    if (!empty($buscar_user)) {
        // Búsqueda con filtros
        $sql = "SELECT * FROM usuario WHERE idusuario LIKE :campo1 OR nombre LIKE :campo2 OR apellidos LIKE :campo3 ORDER BY idusuario ASC";
        $sent_select = $con->prepare($sql);
        $sent_select->execute([
            ':campo1' => "%" . $buscar_user . "%",
            ':campo2' => "%" . $buscar_user . "%",
            ':campo3' => "%" . $buscar_user . "%"
        ]);
        $resultado = $sent_select->fetchAll();
    } else {
        // Si el campo está vacío, mostrar todos
        $sql = "SELECT * FROM usuario ORDER BY idusuario ASC";
        $sent_select = $con->prepare($sql);
        $sent_select->execute();
        $resultado = $sent_select->fetchAll();
    }
} else {
    // Si no se envió el formulario, mostrar todos los usuarios
    $sql = "SELECT * FROM usuario ORDER BY idusuario ASC";
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
    <h2 class="mb-0"><i class="bi bi-people-fill"></i> Gestión de Usuarios</h2>
    <a href="insert.php" class="btn btn-success">
        <i class="bi bi-person-plus-fill"></i> Nuevo Usuario
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <!-- Buscador -->
        <div class="row mb-3">
            <div class="col-md-6">
                <form method="POST" action="" class="d-flex gap-2">
                    <input type="text" name="buscar" class="form-control" 
                           placeholder="Buscar por Cédula, Nombre o Apellido..." 
                           value="<?php echo htmlspecialchars($buscar_user); ?>">
                    <button type="submit" name="btn_buscar" class="btn btn-outline-primary">
                        <i class="bi bi-search"></i> Buscar
                    </button>
                    <a href="index.php" class="btn btn-secondary">Limpiar</a>
                </form>
            </div>
        </div>

        <!-- Tabla de usuarios -->
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Cédula</th>
                        <th>Nombre Completo</th>
                        <th>Correo</th>
                        <th>Celular</th>
                        <th>Ciudad</th>
                        <th>Perfil</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($resultado) > 0): ?>
                        <?php foreach ($resultado as $fila): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($fila['idusuario']); ?></td>
                                <td><?php echo htmlspecialchars($fila['nombre'] . ' ' . $fila['apellidos']); ?></td>
                                <td><?php echo htmlspecialchars($fila['correo']); ?></td>
                                <td><?php echo htmlspecialchars($fila['cel']); ?></td>
                                <td><?php echo htmlspecialchars($fila['ciudad']); ?></td>
                                <td>
                                    <span class="badge bg-info text-dark">
                                        <?php echo htmlspecialchars($fila['perfil_usuario']); ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="update.php?idusuario=<?php echo $fila['idusuario']; ?>" 
                                       class="btn btn-sm btn-warning text-white" title="Editar">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="delete.php?idusuario=<?php echo $fila['idusuario']; ?>" 
                                       class="btn btn-sm btn-danger" 
                                       title="Eliminar"
                                       onclick="return confirm('¿Estás seguro de eliminar a este usuario?');">
                                        <i class="bi bi-trash-fill"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                No se encontraron usuarios. 
                                <?php echo !empty($buscar_user) ? 'Intenta con otro término de búsqueda.' : ''; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
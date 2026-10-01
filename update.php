<?php 
$titulo_pagina = "Editar Usuario - Sistema GD";
include_once 'includes/header.php'; 
include_once 'conexion.php';

$mensaje = '';
$tipo_mensaje = '';

// 1. Obtener el ID del usuario a editar
$idusuario = isset($_GET['idusuario']) ? (int)$_GET['idusuario'] : 0;

if ($idusuario <= 0) {
    header("Location: index.php");
    exit();
}

// 2. Buscar los datos actuales del usuario
$buscar_id = $con->prepare('SELECT * FROM usuario WHERE idusuario = :idusuario LIMIT 1');
$buscar_id->execute([':idusuario' => $idusuario]);
$usuario = $buscar_id->fetch();

// Si no existe, redirigir
if (!$usuario) {
    $_SESSION['mensaje'] = "El usuario no existe.";
    $_SESSION['tipo_mensaje'] = "warning";
    header("Location: index.php");
    exit();
}

// 3. Procesar la actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar'])) {
    
    $nombre = trim($_POST['nombre']);
    $apellidos = trim($_POST['apellidos']);
    $correo = trim($_POST['correo']);
    $cel = trim($_POST['cel']);
    $telefono = trim($_POST['telefono']);
    $perfil_usuario = trim($_POST['perfil_usuario']);
    $ciudad = trim($_POST['ciudad']);
    $direccion = trim($_POST['direccion']);
    $departamento = trim($_POST['departamento']);

    if (!empty($nombre) && !empty($correo)) {
        try {
            $sql = "UPDATE usuario SET 
                    nombre = :nombre, 
                    apellidos = :apellidos, 
                    correo = :correo, 
                    cel = :cel, 
                    telefono = :telefono, 
                    perfil_usuario = :perfil_usuario, 
                    ciudad = :ciudad, 
                    direccion = :direccion, 
                    departamento = :departamento 
                    WHERE idusuario = :idusuario";
            
            $consulta_update = $con->prepare($sql);
            $consulta_update->execute([
                ':nombre' => $nombre,
                ':apellidos' => $apellidos,
                ':correo' => $correo,
                ':cel' => $cel,
                ':telefono' => $telefono,
                ':perfil_usuario' => $perfil_usuario,
                ':ciudad' => $ciudad,
                ':direccion' => $direccion,
                ':departamento' => $departamento,
                ':idusuario' => $idusuario
            ]);

            $_SESSION['mensaje'] = "Usuario actualizado correctamente.";
            $_SESSION['tipo_mensaje'] = "success";
            header("Location: index.php");
            exit();

        } catch (PDOException $e) {
            $mensaje = "Error al actualizar: " . $e->getMessage();
            $tipo_mensaje = "danger";
        }
    } else {
        $mensaje = "Por favor, completa al menos el Nombre y el Correo.";
        $tipo_mensaje = "warning";
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0"><i class="bi bi-pencil-square"></i> Editar Usuario: <?php echo htmlspecialchars($usuario['nombre']); ?></h4>
            </div>
            <div class="card-body">
                
                <?php if ($mensaje): ?>
                    <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
                        <?php echo $mensaje; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="row g-3">
                        <!-- Cédula (solo lectura, no se puede cambiar) -->
                        <div class="col-md-4">
                            <label class="form-label">Cédula (ID)</label>
                            <input type="text" class="form-control" 
                                   value="<?php echo htmlspecialchars($usuario['idusuario']); ?>" disabled>
                        </div>
                        
                        <!-- Nombre -->
                        <div class="col-md-4">
                            <label class="form-label">Nombre *</label>
                            <input type="text" name="nombre" class="form-control" 
                                   value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                        </div>
                        
                        <!-- Apellidos -->
                        <div class="col-md-4">
                            <label class="form-label">Apellidos *</label>
                            <input type="text" name="apellidos" class="form-control" 
                                   value="<?php echo htmlspecialchars($usuario['apellidos']); ?>" required>
                        </div>

                        <!-- Correo -->
                        <div class="col-md-6">
                            <label class="form-label">Correo Electrónico *</label>
                            <input type="email" name="correo" class="form-control" 
                                   value="<?php echo htmlspecialchars($usuario['correo']); ?>" required>
                        </div>

                        <!-- Celular -->
                        <div class="col-md-6">
                            <label class="form-label">Celular *</label>
                            <input type="text" name="cel" class="form-control" 
                                   value="<?php echo htmlspecialchars($usuario['cel']); ?>" 
                                   pattern="[0-9]{7,15}" title="Solo números" required>
                        </div>

                        <!-- Teléfono Fijo -->
                        <div class="col-md-6">
                            <label class="form-label">Teléfono Fijo</label>
                            <input type="text" name="telefono" class="form-control" 
                                   value="<?php echo htmlspecialchars($usuario['telefono']); ?>" 
                                   pattern="[0-9]{7,15}" title="Solo números">
                        </div>

                        <!-- Perfil -->
                        <div class="col-md-6">
                            <label class="form-label">Perfil de Usuario *</label>
                            <select name="perfil_usuario" class="form-select" required>
                                <option value="admin" <?php echo ($usuario['perfil_usuario'] == 'admin') ? 'selected' : ''; ?>>Administrador</option>
                                <option value="aux" <?php echo ($usuario['perfil_usuario'] == 'aux') ? 'selected' : ''; ?>>Auxiliar</option>
                                <option value="compras" <?php echo ($usuario['perfil_usuario'] == 'compras') ? 'selected' : ''; ?>>Aux Compras</option>
                                <option value="ventas" <?php echo ($usuario['perfil_usuario'] == 'ventas') ? 'selected' : ''; ?>>Ejecutivo Comercial</option>
                            </select>
                        </div>

                        <!-- Ciudad -->
                        <div class="col-md-6">
                            <label class="form-label">Ciudad *</label>
                            <input type="text" name="ciudad" class="form-control" 
                                   value="<?php echo htmlspecialchars($usuario['ciudad']); ?>" required>
                        </div>

                        <!-- Departamento -->
                        <div class="col-md-6">
                            <label class="form-label">Departamento</label>
                            <input type="text" name="departamento" class="form-control" 
                                   value="<?php echo htmlspecialchars($usuario['departamento']); ?>">
                        </div>

                        <!-- Dirección -->
                        <div class="col-md-12">
                            <label class="form-label">Dirección</label>
                            <input type="text" name="direccion" class="form-control" 
                                   value="<?php echo htmlspecialchars($usuario['direccion']); ?>">
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <a href="index.php" class="btn btn-secondary me-md-2">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
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
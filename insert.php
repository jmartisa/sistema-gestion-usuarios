<?php
session_start();
if (!isset($_SESSION['idusuario'])) {
    header("Location: login.php");
    exit();
}

include_once 'conexion.php';

$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar'])) {
    
    $idusuario = trim($_POST['idusuario']);
    $nombre = trim($_POST['nombre']);
    $apellidos = trim($_POST['apellidos']);
    $password = $_POST['contraseña']; // Del formulario
    $correo = trim($_POST['correo']);
    $direccion = trim($_POST['direccion']);
    $cel = trim($_POST['cel']);
    $telefono = trim($_POST['telefono']);
    $perfil_usuario = trim($_POST['perfil_usuario']);
    $departamento = trim($_POST['departamento']);
    $ciudad = trim($_POST['ciudad']);

    if (!empty($idusuario) && !empty($nombre) && !empty($password)) {
        
        if (strlen($password) > 8) {
            $mensaje = "La contraseña no puede tener más de 8 caracteres.";
            $tipo_mensaje = "warning";
        } else {
            try {
                // Crear el rol primero
                $sql_rol = "INSERT IGNORE INTO roles (idpermiso, permiso) VALUES (:id, 'Usuario')";
                $con->prepare($sql_rol)->execute([':id' => $idusuario]);

                // Insertar usuario con 'password' en lugar de 'contraseña'
                $sql = "INSERT INTO usuario (idusuario, nombre, apellidos, password, correo, direccion, cel, telefono, perfil_usuario, departamento, ciudad) 
                        VALUES (:idusuario, :nombre, :apellidos, :password, :correo, :direccion, :cel, :telefono, :perfil_usuario, :departamento, :ciudad)";
                
                $consulta_insert = $con->prepare($sql);
                $consulta_insert->execute([
                    ':idusuario' => $idusuario,
                    ':nombre' => $nombre,
                    ':apellidos' => $apellidos,
                    ':password' => $password,
                    ':correo' => $correo,
                    ':direccion' => $direccion,
                    ':cel' => $cel,
                    ':telefono' => $telefono,
                    ':perfil_usuario' => $perfil_usuario,
                    ':departamento' => $departamento,
                    ':ciudad' => $ciudad
                ]);

                $_SESSION['mensaje'] = "Usuario creado exitosamente.";
                $_SESSION['tipo_mensaje'] = "success";
                header("Location: index.php");
                exit();

            } catch (PDOException $e) {
                $mensaje = "Error: " . $e->getMessage();
                $tipo_mensaje = "danger";
            }
        }
    } else {
        $mensaje = "Por favor, completa al menos la Cédula, el Nombre y la Contraseña.";
        $tipo_mensaje = "warning";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Usuario - Sistema GD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="bi bi-person-plus-fill"></i> Registrar Nuevo Usuario</h4>
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
                            <div class="col-md-4">
                                <label class="form-label">Cédula (ID)</label>
                                <input type="text" name="idusuario" class="form-control" pattern="[0-9]+" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Nombre</label>
                                <input type="text" name="nombre" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Apellidos</label>
                                <input type="text" name="apellidos" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contraseña</label>
                                <input type="password" name="contraseña" class="form-control" maxlength="8" minlength="4" required>
                                <div class="form-text">Máximo 8 caracteres.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Correo Electrónico</label>
                                <input type="email" name="correo" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Celular</label>
                                <input type="text" name="cel" class="form-control" pattern="[0-9]{7,15}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Teléfono Fijo</label>
                                <input type="text" name="telefono" class="form-control" pattern="[0-9]{7,15}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Perfil de Usuario</label>
                                <select name="perfil_usuario" class="form-select" required>
                                    <option value="" disabled selected>Seleccione un perfil...</option>
                                    <option value="admin">Administrador</option>
                                    <option value="aux">Auxiliar</option>
                                    <option value="compras">Aux Compras</option>
                                    <option value="ventas">Ejecutivo Comercial</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Ciudad</label>
                                <input type="text" name="ciudad" class="form-control" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Dirección</label>
                                <input type="text" name="direccion" class="form-control">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Departamento</label>
                                <input type="text" name="departamento" class="form-control">
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="index.php" class="btn btn-secondary me-md-2">Cancelar</a>
                            <button type="submit" name="guardar" class="btn btn-success">
                                <i class="bi bi-save-fill"></i> Guardar Usuario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
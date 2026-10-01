<?php
require_once 'conexion.php';
session_start();

if (isset($_SESSION['idusuario'])) {
    header("Location: dashboard.php");
    exit();
}

$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['username']) || empty($_POST['password'])) {
        $mensaje = 'Por favor, completa todos los campos.';
        $tipo_mensaje = 'error';
    } else {
        $username = trim($_POST['username']);
        $password = $_POST['password'];

        try {
            $query = $con->prepare("SELECT * FROM usuario WHERE idusuario = :nombre LIMIT 1");
            $query->bindParam(':nombre', $username, PDO::PARAM_STR);
            $query->execute();
            $resultado = $query->fetch();

            // Comparación en texto plano (temporal)
            if ($resultado && $password === $resultado['contraseña']) {
                $_SESSION['idusuario'] = $resultado['idusuario'];
                $_SESSION['nombre'] = $resultado['nombre'];
                $_SESSION['apellidos'] = $resultado['apellidos'];
                $_SESSION['perfil_usuario'] = $resultado['perfil_usuario'];
                
                header("Location: dashboard.php");
                exit();
            } else {
                $mensaje = 'Usuario o contraseña incorrectos.';
                $tipo_mensaje = 'error';
            }
        } catch (PDOException $e) {
            error_log("Error en login: " . $e->getMessage());
            $mensaje = 'Error en el sistema. Intenta más tarde.';
            $tipo_mensaje = 'error';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión - Sistemas SGD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/login.css">
</head>
<body class="bg-light">

<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0">Iniciar Sesión</h4>
                </div>
                <div class="card-body">
                    
                    <?php if ($mensaje): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($mensaje); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="" autocomplete="off">
                        <div class="mb-3">
                            <label for="username" class="form-label">Usuario (Cédula)</label>
                            <input type="text" class="form-control" id="username" name="username" required autofocus>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">Ingresar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
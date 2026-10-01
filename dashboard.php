<?php 
// Definimos el título antes de llamar al header
$titulo_pagina = "Panel de Control - Sistema GD";
include_once 'includes/header.php'; 
include_once 'conexion.php';

// Consultas del Dashboard
$total_usuarios = $con->query("SELECT COUNT(*) as total FROM usuario")->fetch()['total'];
$perfiles = $con->query("SELECT perfil_usuario, COUNT(*) as cantidad FROM usuario GROUP BY perfil_usuario")->fetchAll();
$recientes = $con->query("SELECT idusuario, nombre, apellidos, perfil_usuario FROM usuario ORDER BY idusuario DESC LIMIT 5")->fetchAll();
?>

<h2 class="mb-4">Panel de Control</h2>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card text-white bg-primary h-100 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <i class="bi bi-people-fill display-4 me-3"></i>
                <div>
                    <h5 class="card-title mb-0">Total Usuarios</h5>
                    <h2 class="display-6 fw-bold"><?php echo $total_usuarios; ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card h-100 shadow-sm">
            <div class="card-header bg-white fw-bold">
                <i class="bi bi-pie-chart-fill text-primary"></i> Usuarios por Perfil
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($perfiles as $perfil): ?>
                        <div class="col-6 col-md-3 text-center mb-3">
                            <div class="p-3 bg-light rounded border">
                                <h4 class="text-primary mb-1"><?php echo $perfil['cantidad']; ?></h4>
                                <small class="text-muted text-uppercase fw-bold"><?php echo htmlspecialchars($perfil['perfil_usuario']); ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center">
        <span><i class="bi bi-clock-history text-warning"></i> Últimos Usuarios Registrados</span>
        <a href="index.php" class="btn btn-sm btn-outline-primary">Ver todos</a>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Nombre Completo</th>
                    <th>Perfil</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recientes as $rec): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($rec['idusuario']); ?></td>
                        <td><?php echo htmlspecialchars($rec['nombre'] . ' ' . $rec['apellidos']); ?></td>
                        <td><span class="badge bg-secondary"><?php echo htmlspecialchars($rec['perfil_usuario']); ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
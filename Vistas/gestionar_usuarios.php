<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

require_once '../Clases/Conexion.php';
require_once '../Clases/Usuario.php';

$usuarioObj = new Usuario();
$usuarios = $usuarioObj->listarTodos(); // Debes tener este método en tu clase Usuario

$mensaje = isset($_SESSION['mensaje']) ? $_SESSION['mensaje'] : null;
$error = isset($_SESSION['error']) ? $_SESSION['error'] : null;
unset($_SESSION['mensaje'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="../css/index.css">
    <style>
        .tabla-usuarios { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .tabla-usuarios th, .tabla-usuarios td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        .tabla-usuarios th { background: #f5f5f5; }
        .btn-desactivar { background: #e74c3c; color: #fff; border: none; padding: 5px 12px; border-radius: 3px; cursor: pointer; }
        .btn-desactivar[disabled] { background: #aaa; cursor: not-allowed; }
    </style>
</head>
<body>
    <header class="header-container">
        <a href="../index.php" class="boton">Volver</a>
        <h2>Gestión de Usuarios</h2>
    </header>
    <main class="contenido">
        <?php if ($mensaje): ?>
            <div class="alert alert-exito"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <table class="tabla-usuarios">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?= htmlspecialchars($usuario['id']) ?></td>
                        <td><?= htmlspecialchars($usuario['usuario']) ?></td>
                        <td><?= htmlspecialchars($usuario['rol']) ?></td>
                        <td><?= $usuario['activo'] ? 'Activo' : 'Inactivo' ?></td>
                        <td>
                            <?php if ($usuario['activo']): ?>
                                <form method="post" action="../Procesos/usuario_crud.php" style="display:inline;">
                                    <input type="hidden" name="accion" value="desactivar">
                                    <input type="hidden" name="id_usuario" value="<?= $usuario['id'] ?>">
                                    <button type="submit" class="btn-desactivar" onclick="return confirm('¿Seguro que deseas desactivar este usuario?');">Desactivar</button>
                                </form>
                            <?php else: ?>
                                <form method="post" action="../Procesos/usuario_crud.php" style="display:inline;">
                                    <input type="hidden" name="accion" value="activar">
                                    <input type="hidden" name="id_usuario" value="<?= $usuario['id'] ?>">
                                    <button type="submit" class="btn-desactivar" style="background:#27ae60;" onclick="return confirm('¿Seguro que deseas activar este usuario?');">Activar</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>
</html>

<?php
session_start();
include("../inc/conexion.php");

if (!isset($_SESSION['admin'])) {
    header("Location: login_admin.php");
    exit;
}

// Procesar activación / desactivación
if (isset($_GET['accion'], $_GET['id_cliente'])) {
    $id_cliente = (int) $_GET['id_cliente'];
    if ($_GET['accion'] === 'activar') {
        $stmt = $conn->prepare("UPDATE clientes SET activo = TRUE WHERE id_cliente = ?");
        $stmt->execute([$id_cliente]);
    } elseif ($_GET['accion'] === 'desactivar') {
        $stmt = $conn->prepare("UPDATE clientes SET activo = FALSE WHERE id_cliente = ?");
        $stmt->execute([$id_cliente]);
    }
    header("Location: admin_clientes.php");
    exit;
}

// Obtener lista de clientes
$sql = "SELECT id_cliente, nombre, apellido, telefono, email, fecha_registro, activo FROM clientes ORDER BY fecha_registro DESC";
$clientes = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Admin | Clientes</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen">

<!-- Header -->
<header class="bg-pink-600 text-white p-4 shadow-md">
  <div class="container mx-auto flex justify-between items-center">
    <h1 class="text-xl font-bold"><i class="fas fa-users mr-2"></i>Clientes - Admin</h1>
    <a href="admin_panel.php" class="hover:underline text-sm text-white">
      <i class="fas fa-arrow-left"></i> Volver al panel
    </a>
  </div>
</header>

<main class="max-w-6xl mx-auto py-8 px-4">
  <h2 class="text-2xl font-bold text-pink-700 mb-6">Clientes Registrados</h2>

  <?php if (count($clientes) > 0): ?>
  <div class="overflow-x-auto">
    <table class="w-full table-auto text-sm bg-white rounded shadow-md border">
      <thead class="bg-pink-100 text-pink-800">
        <tr>
          <th class="px-4 py-2">Nombre</th>
          <th>Teléfono</th>
          <th>Email</th>
          <th>Fecha de Registro</th>
          <th>Estado</th>
          <th>Acción</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($clientes as $cliente): ?>
        <tr class="border-b hover:bg-pink-50 transition">
          <td class="px-4 py-2 font-medium"><?= htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido']) ?></td>
          <td><?= htmlspecialchars($cliente['telefono']) ?></td>
          <td title="<?= htmlspecialchars($cliente['email']) ?>">
            <?= $cliente['email'] ? (strlen($cliente['email']) > 30 
              ? substr(htmlspecialchars($cliente['email']), 0, 30) . '...' 
              : htmlspecialchars($cliente['email'])) : '-' ?>
          </td>
          <td><?= date("d/m/Y", strtotime($cliente['fecha_registro'])) ?></td>
          <td>
            <span class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-full 
              <?= $cliente['activo'] 
                ? 'bg-green-100 text-green-700' 
                : 'bg-red-100 text-red-700' ?>">
              <i class="fas fa-circle"></i> <?= $cliente['activo'] ? 'Activo' : 'Inactivo' ?>
            </span>
          </td>
          <td>
            <?php if ($cliente['activo']): ?>
              <a href="?accion=desactivar&id_cliente=<?= $cliente['id_cliente'] ?>" 
                 class="text-red-600 hover:underline text-sm font-semibold"
                 onclick="return confirm('¿Seguro que quieres desactivar este cliente?');">
                <i class="fas fa-user-slash"></i> Desactivar
              </a>
            <?php else: ?>
              <a href="?accion=activar&id_cliente=<?= $cliente['id_cliente'] ?>" 
                 class="text-green-600 hover:underline text-sm font-semibold"
                 onclick="return confirm('¿Quieres activar este cliente?');">
                <i class="fas fa-user-check"></i> Activar
              </a>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
    <p class="text-gray-500 text-center mt-4">No hay clientes registrados aún.</p>
  <?php endif; ?>
</main>

<footer class="text-center text-gray-500 text-sm mt-12 pb-6">
  &copy; <?= date("Y") ?> Morelia Véliz Beauty Salon - Admin
</footer>

</body>
</html>
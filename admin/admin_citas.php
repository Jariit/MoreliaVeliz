<?php
session_start();
include("../inc/conexion.php");

if (!isset($_SESSION['admin'])) {
    header("Location: login_admin.php");
    exit;
}

// Cambio de estado si se envía por GET
if (isset($_GET['cambiar_estado'], $_GET['id'], $_GET['nuevo'])) {
    $id = $_GET['id'];
    $nuevo = $_GET['nuevo'];
    $stmt = $conn->prepare("UPDATE citas SET estado = ? WHERE id_cita = ?");
    $stmt->execute([$nuevo, $id]);
    header("Location: admin_citas.php");
    exit;
}

$sql = "SELECT 
            c.id_cita,
            cl.nombre || ' ' || cl.apellido AS cliente,
            cl.telefono,
            s.nombre_servicio,
            c.fecha_hora,
            c.estado,
            c.color_esmaltado,
            c.notas_especiales
        FROM citas c
        JOIN clientes cl ON c.id_cliente = cl.id_cliente
        JOIN servicios s ON c.id_servicio = s.id_servicio
        ORDER BY c.fecha_hora DESC";
$citas = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Admin | Citas</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen">

<header class="bg-pink-600 text-white p-4 shadow">
  <div class="container mx-auto flex justify-between items-center">
    <h1 class="text-xl font-bold"><i class="fas fa-calendar-alt mr-2"></i>Gestión de Citas</h1>
    <a href="admin_panel.php" class="hover:underline text-sm"><i class="fas fa-arrow-left"></i> Volver</a>
  </div>
</header>

<main class="max-w-7xl mx-auto py-8 px-4">
  <h2 class="text-2xl font-bold text-pink-700 mb-6">Historial de Citas</h2>

  <?php if (count($citas) > 0): ?>
  <div class="overflow-x-auto">
    <table class="w-full text-sm bg-white rounded shadow border table-auto">
      <thead class="bg-pink-100 text-pink-800 text-left">
        <tr>
          <th class="px-4 py-2">Cliente</th>
          <th>Teléfono</th>
          <th>Servicio</th>
          <th>Fecha y Hora</th>
          <th>Estado</th>
          <th>Esmaltado</th>
          <th>Notas</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($citas as $c): ?>
        <tr class="border-b hover:bg-pink-50 transition">
          <td class="px-4 py-2 font-medium"><?= htmlspecialchars($c['cliente']) ?></td>
          <td><?= htmlspecialchars($c['telefono']) ?></td>
          <td><?= htmlspecialchars($c['nombre_servicio']) ?></td>
          <td><?= date("d/m/Y H:i", strtotime($c['fecha_hora'])) ?></td>
          <td>
            <?php
            $estado = $c['estado'];
            $color_estado = match($estado) {
              'reservada' => 'bg-yellow-100 text-yellow-700',
              'confirmada' => 'bg-blue-100 text-blue-700',
              'completada' => 'bg-green-100 text-green-700',
              'cancelada', 'no_show' => 'bg-red-100 text-red-700',
              default => 'bg-gray-100 text-gray-700'
            };
            ?>
            <span class="px-2 py-1 text-xs rounded-full <?= $color_estado ?>">
              <?= ucfirst($estado) ?>
            </span>
          </td>
          <td><?= $c['color_esmaltado'] ? htmlspecialchars($c['color_esmaltado']) : '-' ?></td>
          <td>
            <?php if ($c['notas_especiales']): ?>
              <span title="<?= htmlspecialchars($c['notas_especiales']) ?>">
                <?= substr(htmlspecialchars($c['notas_especiales']), 0, 30) ?><?= strlen($c['notas_especiales']) > 30 ? '...' : '' ?>
              </span>
            <?php else: ?>
              -
            <?php endif; ?>
          </td>
          <td>
            <div class="flex flex-wrap gap-1">
              <a href="?cambiar_estado=1&id=<?= $c['id_cita'] ?>&nuevo=confirmada"
                 class="bg-blue-100 text-blue-700 px-2 py-1 text-xs rounded hover:bg-blue-200"
                 title="Confirmar"><i class="fas fa-check"></i></a>
              <a href="?cambiar_estado=1&id=<?= $c['id_cita'] ?>&nuevo=completada"
                 class="bg-green-100 text-green-700 px-2 py-1 text-xs rounded hover:bg-green-200"
                 title="Completar"><i class="fas fa-check-double"></i></a>
              <a href="?cambiar_estado=1&id=<?= $c['id_cita'] ?>&nuevo=cancelada"
                 class="bg-red-100 text-red-700 px-2 py-1 text-xs rounded hover:bg-red-200"
                 title="Cancelar"><i class="fas fa-ban"></i></a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
    <p class="text-gray-500 text-center mt-4">No hay citas registradas aún.</p>
  <?php endif; ?>
</main>

<footer class="text-center text-gray-500 text-sm mt-12 pb-6">
  &copy; <?= date("Y") ?> Morelia Véliz Beauty Salon - Admin
</footer>

</body>
</html>
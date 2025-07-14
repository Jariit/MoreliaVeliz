<?php
session_start();
include("../inc/conexion.php");
include("funciones/promociones_funciones.php");

if (!isset($_SESSION['admin'])) {
    header("Location: login_admin.php");
    exit;
}

$servicios = $conn->query("SELECT id_servicio, nombre_servicio FROM servicios WHERE activo = TRUE ORDER BY nombre_servicio")->fetchAll(PDO::FETCH_ASSOC);

$modo_edicion = false;
$promo_actual = null;

if (isset($_GET['eliminar'])) {
    eliminarPromocion($conn, (int)$_GET['eliminar']);
    header("Location: admin_promociones.php");
    exit;
}

if (isset($_GET['editar'])) {
    $promo_actual = obtenerPromocionPorID($conn, (int)$_GET['editar']);
    if ($promo_actual) {
        $modo_edicion = true;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre_promocion'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $tipo = $_POST['tipo_promocion'] ?? '';
    $descuento = $_POST['descuento'] ?? null;
    $precio = $_POST['precio_promocional'] ?? null;
    $servicios_incluidos = $_POST['servicios_incluidos'] ?? [];
    $desde = $_POST['valido_desde'] ?? '';
    $hasta = $_POST['valido_hasta'] ?? '';
    $imagen_url = null;

    if (!empty($_FILES['imagen']['tmp_name'])) {
        $nombre_img = basename($_FILES['imagen']['name']);
        $destino = "uploads/" . uniqid() . "_" . $nombre_img;
        move_uploaded_file($_FILES['imagen']['tmp_name'], $destino);
        $imagen_url = $destino;
    } else {
        $imagen_url = $_POST['imagen_promocion_url'] ?? '';
    }

    if ($modo_edicion) {
        $datos = [
            'id_promocion' => $_POST['id_promocion'],
            'nombre_promocion' => $nombre,
            'descripcion' => $descripcion,
            'tipo_promocion' => $tipo,
            'descuento' => $descuento,
            'precio_promocional' => $precio,
            'servicios_incluidos' => $servicios_incluidos,
            'valido_desde' => $desde,
            'valido_hasta' => $hasta,
            'imagen_promocion' => $imagen_url
        ];
        actualizarPromocion($conn, $datos);
    } else {
        registrarPromocion($conn, [
            'nombre_promocion' => $nombre,
            'descripcion' => $descripcion,
            'tipo_promocion' => $tipo,
            'descuento' => $descuento,
            'precio_promocional' => $precio,
            'servicios_incluidos' => $servicios_incluidos,
            'valido_desde' => $desde,
            'valido_hasta' => $hasta,
            'imagen_promocion' => $imagen_url
        ]);
    }
    header("Location: admin_promociones.php");
    exit;
}

$promos = $conn->query("SELECT * FROM promociones ORDER BY valido_desde DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Admin | Promociones</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen">

<header class="bg-pink-600 text-white p-4 shadow-md">
  <div class="container mx-auto flex justify-between items-center">
    <h1 class="text-xl font-bold"><i class="fas fa-gift mr-2"></i>Promociones - Admin</h1>
    <a href="admin_panel.php" class="hover:underline text-white">
      <i class="fas fa-arrow-left"></i> Volver al panel
    </a>
  </div>
</header>

<main class="max-w-6xl mx-auto py-8 px-4">
  <h2 class="text-2xl font-bold text-pink-700 mb-6">Promociones Registradas</h2>

  <div class="overflow-x-auto mb-10">
    <table class="w-full table-auto text-sm bg-white rounded shadow-md">
      <thead class="bg-pink-100 text-pink-800">
        <tr>
          <th class="px-4 py-2">Nombre</th>
          <th>Tipo</th>
          <th>Desde</th>
          <th>Hasta</th>
          <th>Activa</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($promos as $p): ?>
        <tr class="border-b">
          <td class="px-4 py-2"><?= htmlspecialchars($p['nombre_promocion']) ?></td>
          <td><?= ucfirst($p['tipo_promocion']) ?></td>
          <td><?= $p['valido_desde'] ?></td>
          <td><?= $p['valido_hasta'] ?></td>
          <td><?= $p['activa'] ? '✅' : '❌' ?></td>
          <td>
            <a href="?editar=<?= $p['id_promocion'] ?>" class="text-blue-600 hover:underline"><i class="fas fa-edit"></i></a>
            <a href="?eliminar=<?= $p['id_promocion'] ?>" onclick="return confirm('¿Eliminar esta promoción?')" class="text-red-600 hover:underline ml-3"><i class="fas fa-trash"></i></a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <h2 class="text-xl font-bold text-pink-700 mb-4">
    <?= $modo_edicion ? 'Editar promoción' : 'Crear nueva promoción' ?>
  </h2>
  <form method="POST" enctype="multipart/form-data" class="grid gap-4 bg-white p-6 rounded shadow-md">
    <?php if ($modo_edicion): ?>
      <input type="hidden" name="modo" value="editar">
      <input type="hidden" name="id_promocion" value="<?= $promo_actual['id_promocion'] ?>">
    <?php endif; ?>

    <input type="text" name="nombre_promocion" value="<?= $promo_actual['nombre_promocion'] ?? '' ?>" required placeholder="Nombre de la promoción" class="border p-2 rounded">
    <textarea name="descripcion" placeholder="Descripción" class="border p-2 rounded"><?= $promo_actual['descripcion'] ?? '' ?></textarea>

    <select name="tipo_promocion" required class="border p-2 rounded">
      <option value="">-- Tipo de promoción --</option>
      <option value="combo" <?= (isset($promo_actual) && $promo_actual['tipo_promocion'] == 'combo') ? 'selected' : '' ?>>Combo</option>
      <option value="descuento" <?= (isset($promo_actual) && $promo_actual['tipo_promocion'] == 'descuento') ? 'selected' : '' ?>>Descuento</option>
      <option value="fidelidad" <?= (isset($promo_actual) && $promo_actual['tipo_promocion'] == 'fidelidad') ? 'selected' : '' ?>>Fidelidad</option>
    </select>

    <div class="grid grid-cols-2 gap-4">
      <input type="number" step="0.01" name="descuento" value="<?= $promo_actual['descuento'] ?? '' ?>" placeholder="Descuento %" class="border p-2 rounded">
      <input type="number" step="0.01" name="precio_promocional" value="<?= $promo_actual['precio_promocional'] ?? '' ?>" placeholder="Precio promocional" class="border p-2 rounded">
    </div>

    <label class="block font-medium text-sm text-gray-700">Servicios incluidos</label>
    <select name="servicios_incluidos[]" multiple required class="border p-2 rounded h-32">
      <?php foreach ($servicios as $s): ?>
        <option value="<?= $s['id_servicio'] ?>" <?= (isset($promo_actual) && strpos($promo_actual['servicios_incluidos'], (string)$s['id_servicio']) !== false) ? 'selected' : '' ?>><?= htmlspecialchars($s['nombre_servicio']) ?></option>
      <?php endforeach; ?>
    </select>

    <div class="grid grid-cols-2 gap-4">
      <input type="date" name="valido_desde" value="<?= $promo_actual['valido_desde'] ?? '' ?>" required class="border p-2 rounded">
      <input type="date" name="valido_hasta" value="<?= $promo_actual['valido_hasta'] ?? '' ?>" required class="border p-2 rounded">
    </div>

    <label class="block text-sm">Imagen de promoción:</label>
    <input type="file" name="imagen" class="border p-2 rounded">
    <input type="hidden" name="imagen_promocion_url" value="<?= $promo_actual['imagen_promocion'] ?? '' ?>">

    <button type="submit" class="bg-pink-600 text-white py-2 rounded hover:bg-pink-700">
      <i class="fas fa-save"></i> <?= $modo_edicion ? 'Guardar Cambios' : 'Registrar Promoción' ?>
    </button>
  </form>
</main>

<footer class="text-center text-gray-500 text-sm mt-12 pb-6">
  &copy; <?= date("Y") ?> Morelia Véliz Beauty Salon - Admin
</footer>

</body>
</html>

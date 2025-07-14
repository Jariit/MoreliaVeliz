<?php
session_start();
include 'inc/conexion.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

try {
    $stmt = $conn->query("SELECT * FROM vista_promociones_activas");
    $promociones = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Error en la consulta: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Promociones - Morelia Véliz</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body class="bg-pink-50 text-gray-800 min-h-screen flex flex-col">

  <!-- Header -->
  <header class="bg-pink-600 text-white p-4 shadow-md">
    <div class="container mx-auto flex justify-between items-center">
      <div class="flex items-center gap-3">
        <img src="inc/assets/logo.jpg" alt="Logo" class="w-10 h-10 rounded-full bg-white" />
        <span class="text-xl font-bold">Morelia Véliz Salon</span>
      </div>
      <nav>
        <ul class="flex gap-4 items-center text-sm">
          <li><a href="usuario_inicio.php" class="hover:underline">Inicio</a></li>
          <li><a href="servicios.php" class="hover:underline">Servicios</a></li>
          <li><a href="promociones.php" class="font-bold underline">Promociones</a></li>
          <li><a href="reservar.php" class="hover:underline">Reservar</a></li>
          <li class="bg-white text-pink-600 px-3 py-1 rounded shadow">
            <i class="fas fa-user"></i> <?= htmlspecialchars($_SESSION['usuario']) ?>
          </li>
          <li><a href="inc/logout.php" class="hover:underline text-white"><i class="fas fa-sign-out-alt"></i></a></li>
        </ul>
      </nav>
    </div>
  </header>

  <!-- Contenido -->
  <main class="p-6 max-w-6xl mx-auto flex-grow">
    <h2 class="text-3xl text-pink-700 font-bold mb-8 text-center">Promociones Activas</h2>

    <?php if (!$promociones): ?>
      <p class="text-center text-gray-600">No hay promociones activas en este momento.</p>
    <?php else: ?>
      <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <?php foreach ($promociones as $promo): 
          $nombre = htmlspecialchars($promo['nombre_promocion']);
          $descripcion = htmlspecialchars($promo['descripcion']);
          $tipo = ucfirst(htmlspecialchars($promo['tipo_promocion']));
          $desde = date('d M Y', strtotime($promo['valido_desde']));
          $hasta = date('d M Y', strtotime($promo['valido_hasta']));
          $img = $promo['imagen_promocion'] ?: 'https://picsum.photos/400/300?random=1';
          $servicios = json_decode($promo['detalles_servicios'], true);
        ?>
          <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition overflow-hidden">
            <img src="<?= $img ?>" alt="Promo <?= $nombre ?>" class="w-full h-48 object-cover" loading="lazy" />
            <div class="p-5 flex flex-col h-full">
              <h3 class="text-xl font-bold text-pink-700 mb-2"><i class="fas fa-gift text-yellow-400 mr-2"></i><?= $nombre ?></h3>
              <p class="text-gray-700 text-sm mb-3 flex-grow"><?= $descripcion ?></p>
              <p class="text-sm text-pink-600"><strong>Tipo:</strong> <?= $tipo ?></p>
              <p class="text-sm text-gray-500 mb-3"><strong>Válido:</strong> <?= $desde ?> - <?= $hasta ?></p>

              <?php if ($servicios && is_array($servicios) && count($servicios) > 0): ?>
                <div class="text-sm text-gray-800 mb-2">
                  <strong>Incluye:</strong>
                  <ul class="list-disc list-inside">
                    <?php foreach ($servicios as $s): ?>
                      <li><?= htmlspecialchars($s['nombre_servicio'] ?? 'Servicio desconocido') ?></li>
                    <?php endforeach; ?>
                  </ul>
                </div>
              <?php endif; ?>

              <a href="reservar.php" class="mt-3 inline-block bg-pink-600 text-white px-4 py-2 text-sm rounded hover:bg-pink-700 transition">
                <i class="fas fa-calendar-check mr-1"></i> Reservar ahora
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </main>

  <!-- Footer -->
  <footer class="bg-pink-600 text-white text-center p-6 mt-10 text-sm">
    <p>&copy; <?= date('Y') ?> Morelia Véliz Beauty Salon </p>
    <p class="mt-1"><i class="fas fa-map-marker-alt"></i> Av. Universitaria y Calle Belleza, Portoviejo, Ecuador</p>
    <div class="mt-2">
      <a href="#" class="mx-2"><i class="fab fa-facebook-f"></i></a>
      <a href="#" class="mx-2"><i class="fab fa-instagram"></i></a>
      <a href="#" class="mx-2"><i class="fab fa-whatsapp"></i></a>
    </div>
  </footer>

</body>
</html>
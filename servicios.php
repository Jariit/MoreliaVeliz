<?php
session_start();
include("inc/conexion.php");

$stmt = $conn->prepare("SELECT * FROM servicios WHERE activo = true ORDER BY categoria, nombre_servicio");
$stmt->execute();
$servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

$usuario = $_SESSION['usuario'] ?? null; // Aquí guardas el usuario logueado, o null si no hay sesión
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Servicios | Morelia Véliz</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-pink-50 text-gray-700 min-h-screen flex flex-col">

  <!-- Header -->
  <header class="bg-pink-600 text-white p-4 shadow-md">
    <div class="container mx-auto flex justify-between items-center">
      <div class="flex items-center gap-3">
        <img src="inc/assets/logo.jpg" alt="Logo" class="w-10 h-10 rounded-full bg-white" />
        <span class="text-xl font-bold">Morelia Véliz Salon</span>
      </div>

      <nav>
        <ul class="flex gap-4 items-center text-sm">

          <!-- Siempre visibles -->
          <li><a href="index.php" class="hover:underline">Inicio</a></li>
          <li><a href="servicios.php" class="underline font-bold">Servicios</a></li>

          <!-- Solo para usuarios logueados -->
          <?php if ($usuario): ?>
            <li><a href="promociones.php" class="hover:underline">Promociones</a></li>
            <li><a href="reservar.php" class="hover:underline">Reservar</a></li>
            <li class="bg-white text-pink-600 px-3 py-1 rounded shadow flex items-center gap-2">
              <i class="fas fa-user"></i>
              <?= htmlspecialchars($usuario) ?>
            </li>
            <li>
              <a href="inc/logout.php" class="hover:underline text-white" title="Cerrar sesión">
                <i class="fas fa-sign-out-alt"></i>
              </a>
            </li>
          <?php else: ?>
            <!-- Solo para usuarios NO logueados -->
            <li><a href="login.php" class="hover:underline">Iniciar sesión</a></li>
          <?php endif; ?>

        </ul>
      </nav>
    </div>
  </header>

  <!-- Contenido -->
  <main class="max-w-6xl mx-auto px-4 py-10 flex-grow">
    <h1 class="text-3xl font-bold text-pink-700 mb-8 text-center">Nuestros Servicios</h1>

    <!-- Si no hay usuario logueado, muestra un call to action para iniciar sesión -->
    <?php if (!$usuario): ?>
      <div class="text-center mb-8">
        <p class="text-gray-600 text-sm mb-2">¿Quieres agendar una cita? <br> Inicia sesión o regístrate para acceder a promociones y reservar.</p>
        <a href="login.php" class="bg-pink-600 text-white px-4 py-2 rounded hover:bg-pink-700 transition">
          Iniciar sesión / Registrarse
        </a>
      </div>
    <?php endif; ?>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php if ($servicios): ?>
        <?php foreach ($servicios as $row): ?>
          <div class="bg-white rounded-2xl shadow hover:shadow-xl transition p-4 flex flex-col">
            <img
              src="<?= htmlspecialchars($row['imagen_url'] ?: 'https://via.placeholder.com/300x200?text=Sin+Imagen') ?>"
              alt="<?= htmlspecialchars($row['nombre_servicio']) ?>"
              class="w-full h-48 object-cover rounded-lg mb-4"
            />
            <h2 class="text-xl font-bold text-pink-600"><?= htmlspecialchars($row['nombre_servicio']) ?></h2>
            <p class="text-sm text-gray-600 mb-2"><?= htmlspecialchars($row['descripcion']) ?></p>
            <p class="text-sm"><strong>Categoría:</strong> <?= htmlspecialchars($row['categoria']) ?></p>
            <p class="text-sm"><strong>Esmaltado:</strong> <?= htmlspecialchars($row['tipo_esmaltado']) ?></p>
            <p class="text-sm"><strong>Precio:</strong> $<?= number_format($row['precio'], 2) ?></p>
            <p class="text-sm"><strong>Duración:</strong> <?= htmlspecialchars($row['duracion_minutos']) ?> min</p>
            <?php if ($row['incluye_extras']): ?>
              <p class="text-xs text-gray-500 mt-2 italic"><?= htmlspecialchars($row['incluye_extras']) ?></p>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="text-center col-span-3 text-gray-500">No hay servicios registrados aún.</p>
      <?php endif; ?>
    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-pink-600 text-white text-center p-6 mt-12 text-sm">
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
<?php
session_start();
include("inc/conexion.php");

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$usuario_nombre = $_SESSION['usuario'] ?? '';

// Obtener servicios activos
try {
    $stmt = $conn->prepare("SELECT id_servicio, nombre_servicio, precio, duracion_minutos FROM servicios WHERE activo = TRUE ORDER BY nombre_servicio");
    $stmt->execute();
    $servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener servicios: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Reservar cita | Morelia Véliz</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body class="bg-pink-50 text-gray-700 min-h-screen flex flex-col">

  <!-- Encabezado -->
  <header class="bg-pink-600 text-white p-4 shadow-md">
    <div class="container mx-auto flex justify-between items-center">
      <h1 class="text-xl font-bold flex items-center">
        <i class="fas fa-calendar-alt mr-2"></i>Reservar Cita
      </h1>
      <nav class="flex gap-4 text-sm">
        <a href="index.php" class="hover:underline">Inicio</a>
        <a href="servicios.php" class="hover:underline">Servicios</a>
        <a href="promociones.php" class="hover:underline">Promociones</a>
        <a href="reservar.php" class="underline font-bold">Reservar</a>
        <span class="bg-white text-pink-600 px-3 py-1 rounded shadow"><?= $usuario_nombre ?></span>
        <a href="inc/logout.php" class="hover:underline text-white"><i class="fas fa-sign-out-alt"></i></a>
      </nav>
    </div>
  </header>

  <!-- Contenido -->
  <main class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow-lg mt-10 flex-grow">
    <h2 class="text-2xl font-bold text-pink-600 text-center mb-6">Completa tu reserva</h2>

    <form action="procesar_reserva.php" method="POST" class="space-y-6">

      <!-- Mostrar información del usuario -->
      <div class="bg-pink-100 p-4 rounded text-sm text-pink-900">
        <p><strong>Reservando como:</strong> <?= htmlspecialchars($usuario_nombre) ?></p>
      </div>

      <!-- Selección de servicio -->
      <div>
        <label class="block font-medium mb-1">Servicio</label>
        <select name="id_servicio" required class="w-full border rounded p-2">
          <option value="">Seleccione...</option>
          <?php foreach ($servicios as $row): ?>
            <option value="<?= $row['id_servicio'] ?>">
              <?= htmlspecialchars($row['nombre_servicio']) ?> - $<?= number_format($row['precio'], 2) ?> (<?= $row['duracion_minutos'] ?> min)
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Fecha y hora -->
      <div>
        <label class="block font-medium mb-1">Fecha y hora</label>
        <input type="datetime-local" name="fecha_hora" required class="w-full border rounded p-2">
      </div>

      <!-- Opcionales -->
      <div>
        <label class="block font-medium mb-1">Color de esmaltado (opcional)</label>
        <input type="text" name="color_esmaltado" placeholder="Ej: Rojo pasión" class="w-full border rounded p-2">
      </div>

      <div>
        <label class="block font-medium mb-1">Notas especiales (opcional)</label>
        <textarea name="notas_especiales" rows="3" placeholder="Ej: Alergia al acetona." class="w-full border rounded p-2"></textarea>
      </div>

      <!-- Botón -->
      <div class="text-center pt-4">
        <button type="submit" class="bg-pink-600 text-white px-6 py-2 rounded hover:bg-pink-700 transition shadow-lg">
          <i class="fas fa-calendar-check mr-1"></i> Confirmar Reserva
        </button>
      </div>
    </form>
  </main>

  <!-- Footer -->
  <footer class="bg-pink-600 text-white text-center p-6 mt-12 text-sm">
    &copy; <?= date('Y') ?> Morelia Véliz Beauty Salon 
    <p class="mt-1"><i class="fas fa-map-marker-alt"></i> Av. Universitaria y Calle Belleza, Portoviejo, Ecuador</p>
    <div class="mt-2">
      <a href="#" class="mx-2"><i class="fab fa-facebook-f"></i></a>
      <a href="#" class="mx-2"><i class="fab fa-instagram"></i></a>
      <a href="#" class="mx-2"><i class="fab fa-whatsapp"></i></a>
    </div>
  </footer>

</body>
</html>
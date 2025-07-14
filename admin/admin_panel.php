<?php
session_start();

// Verifica si el admin está logueado
if (!isset($_SESSION['admin'])) {
    header("Location: ../login_admin.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Panel de Administración</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    />
    <style>
      /* Sutil animación de íconos al hacer hover */
      a div i {
        transition: transform 0.3s ease;
      }
      a:hover div i {
        transform: scale(1.15);
      }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col">

    <!-- Header -->
    <header class="bg-pink-600 text-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold flex items-center gap-2">
                <i class="fas fa-tools"></i> Panel de Administración
            </h1>
            <a href="../inc/logout.php" class="hover:underline text-white flex items-center gap-2">
                <i class="fas fa-sign-out-alt"></i> Cerrar sesión
            </a>
        </div>
    </header>

    <!-- Contenido -->
    <main class="flex-grow max-w-4xl mx-auto py-12 px-4">
        <h2 class="text-3xl font-extrabold text-pink-700 mb-12 text-center">
          ¡Hola, <span class="capitalize"><?= htmlspecialchars($_SESSION['admin']) ?></span>!
        </h2>

        <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-4">
            <a href="admin_servicios.php" class="bg-white shadow-md rounded-lg p-6 hover:shadow-xl transition text-center cursor-pointer">
                <div>
                  <i class="fas fa-hand-sparkles fa-3x text-pink-500 mb-4"></i>
                </div>
                <h3 class="font-bold text-lg">Gestionar Servicios</h3>
            </a>

            <a href="admin_promociones.php" class="bg-white shadow-md rounded-lg p-6 hover:shadow-xl transition text-center cursor-pointer">
                <div>
                  <i class="fas fa-gift fa-3x text-pink-500 mb-4"></i>
                </div>
                <h3 class="font-bold text-lg">Gestionar Promociones</h3>
            </a>

            <a href="admin_citas.php" class="bg-white shadow-md rounded-lg p-6 hover:shadow-xl transition text-center cursor-pointer">
                <div>
                  <i class="fas fa-calendar-check fa-3x text-pink-500 mb-4"></i>
                </div>
                <h3 class="font-bold text-lg">Citas Agendadas</h3>
            </a>

            <a href="admin_clientes.php" class="bg-white shadow-md rounded-lg p-6 hover:shadow-xl transition text-center cursor-pointer">
                <div>
                  <i class="fas fa-users fa-3x text-pink-500 mb-4"></i>
                </div>
                <h3 class="font-bold text-lg">Historial de Clientes</h3>
            </a>
        </div>
    </main>

    <!-- Footer -->
    <footer class="text-center text-gray-500 text-sm mt-12 py-6">
        &copy; <?= date("Y") ?> Morelia Véliz Beauty Salon - Admin
    </footer>

</body>
</html>
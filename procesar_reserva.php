<?php
session_start();
include("inc/conexion.php");

// Verificar usuario logueado
if (!isset($_SESSION['usuario_id'])) {
    echo "<script>alert('Debes iniciar sesión para reservar'); window.location.href='login.php';</script>";
    exit;
}

$id_cliente = $_SESSION['usuario_id']; // ID del usuario en sesión

// Recibir datos del formulario
$id_servicio = $_POST['id_servicio'] ?? '';
$fecha_hora = $_POST['fecha_hora'] ?? '';
$color = $_POST['color_esmaltado'] ?? null;
$notas = $_POST['notas_especiales'] ?? null;

// Validación básica
if (empty($id_servicio) || empty($fecha_hora)) {
    echo "<script>alert('Faltan datos requeridos.'); window.history.back();</script>";
    exit;
}

try {
    // Insertar cita
    $stmt = $conn->prepare("INSERT INTO citas (id_cliente, id_servicio, fecha_hora, color_esmaltado, notas_especiales) VALUES (?, ?, ?, ?, ?)");
    $insert_cita = $stmt->execute([$id_cliente, $id_servicio, $fecha_hora, $color, $notas]);

    if ($insert_cita) {
        echo "<script>alert('✅ Cita registrada con éxito'); window.location.href='usuario_inicio.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error al registrar la cita.'); window.history.back();</script>";
        exit;
    }

} catch (PDOException $e) {
    $mensajeError = addslashes($e->getMessage());
    echo "<script>alert('Error en la base de datos: $mensajeError'); window.history.back();</script>";
    exit;
}
?>
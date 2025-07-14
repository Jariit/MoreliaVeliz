<?php
// admin/servicios_funciones.php

function obtenerServicios(PDO $conn) {
    $stmt = $conn->query("SELECT * FROM servicios ORDER BY categoria, nombre_servicio");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function agregarServicio(PDO $conn, $datos, $imagenPath = null) {
    $sql = "INSERT INTO servicios 
        (nombre_servicio, descripcion, categoria, tipo_esmaltado, precio, duracion_minutos, incluye_extras, imagen_url, activo) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, true)";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([
        $datos['nombre'], 
        $datos['descripcion'], 
        $datos['categoria'], 
        $datos['esmaltado'],
        $datos['precio'], 
        $datos['duracion'], 
        $datos['extras'], 
        $imagenPath
    ]);
}

function obtenerServicioPorId(PDO $conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM servicios WHERE id_servicio = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function actualizarServicio(PDO $conn, $id, $datos, $imagenPath = null) {
    $sql = "UPDATE servicios SET
        nombre_servicio = ?, 
        descripcion = ?, 
        categoria = ?, 
        tipo_esmaltado = ?, 
        precio = ?, 
        duracion_minutos = ?, 
        incluye_extras = ?";
    $params = [
        $datos['nombre'], 
        $datos['descripcion'], 
        $datos['categoria'], 
        $datos['esmaltado'], 
        $datos['precio'], 
        $datos['duracion'], 
        $datos['extras']
    ];
    if ($imagenPath !== null) {
        $sql .= ", imagen_url = ?";
        $params[] = $imagenPath;
    }
    $sql .= " WHERE id_servicio = ?";
    $params[] = $id;

    $stmt = $conn->prepare($sql);
    return $stmt->execute($params);
}

function eliminarServicio(PDO $conn, $id) {
    $stmt = $conn->prepare("DELETE FROM servicios WHERE id_servicio = ?");
    return $stmt->execute([$id]);
}
<?php

$conexion = new mysqli(
    "localhost",
    "dev_user",
    "User*2026",
    "Tony_5"
);

if ($conexion->connect_error) {
    error_log("Error de conexión: " . $conexion->connect_error);
    die("No se pudo conectar a la base de datos. Intenta más tarde.");
}

$conexion->set_charset("utf8mb4");
?>

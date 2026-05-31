<?php
session_start();

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login.php");
    exit;
}

include("conexion.php");

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id) {
    $stmt = $conexion->prepare("DELETE FROM articulos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: admin.php");
exit;
?>

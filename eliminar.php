<?php
include("conexion.php");

$id = $_GET['id'];

$conexion->query("DELETE FROM articulos WHERE id=$id");

header("Location: admin.php");
?>

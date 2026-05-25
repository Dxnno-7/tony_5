<?php
session_start();

if ($_POST) {
    $user = $_POST['usuario'];
    $pass = $_POST['password'];

    if ($user == "24160817@itoaxaca.edu.mx" && $pass == "24160817TSO") {
        $_SESSION['login'] = true;
        header("Location: admin.php");
    } else { 
        echo "Datos incorrectos"; 
    }
}
?>

<form method="POST">
    Usuario: <input type="text" name="usuario"><br>
    Password: <input type="password" name="password"><br>
    <button type="submit">Ingresar</button>
</form>

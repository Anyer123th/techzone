<?php
session_start();
include "conexion.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = trim($_POST["usuario"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($usuario === "" || $password === "") {
        header("Location: login.php?error=1");
        exit;
    }

    $stmt = $conn->prepare("SELECT id, usuario, password, rol FROM usuarios WHERE usuario = ?");
    if ($stmt) {
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res && $res->num_rows === 1) {
            $user = $res->fetch_assoc();
            if (password_verify($password, $user["password"])) {
                $_SESSION["id"] = (int)$user["id"];
                $_SESSION["usuario"] = $user["usuario"];
                $_SESSION["rol"] = $user["rol"];
                header("Location: dashboard.php");
                exit;
            }
        }
    }
    header("Location: login.php?error=1");
    exit;
} else {
    header("Location: login.php");
    exit;
}
?>

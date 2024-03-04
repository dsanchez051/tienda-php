<?php 

require "database.php";

session_start();

// Se redirige al usuario a la página de inicio si no ha iniciado sesión como cliente
if (!isset($_SESSION["customer"])) {
  header("Location: index.php");
}

$error = null;

// Procesamiento del formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $type = $_POST["type"];

  if (empty($type)) {
    $error = "Please fill all the fields.";
  } else {
    $conn
      ->prepare("INSERT INTO categories (type) VALUES (:type)")
      ->execute([":type" => $type]);
  }
}


?>
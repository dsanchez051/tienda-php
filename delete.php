
<?php

require "database.php";

session_start();

// Se redirige al usuario a la página de inicio si no ha iniciado sesión como cliente
if (!isset($_SESSION["customer"])) {
  header("Location: index.php");
  return;
}

$error = null;

// Procesamiento del formulario
if ($_SERVER["REQUEST_METHOD"] === "GET") {
  $id = $_GET["id"];
  $type = $_GET["type"];

  if (empty($id) || empty($type)) {
    $error = "Please fill all the fields.";
  } else {

    if ($type == "category") {

      $statement = $conn->prepare("SELECT * FROM categories WHERE id = :id LIMIT 1");
      $statement->execute([":id" => $id]);

      if ($statement->rowCount() == 0) {
        http_response_code(404);
        echo ("HTTP 404 NOT FOUND");
        return;
      }

      $conn->prepare("DELETE FROM categories WHERE id = :id")->execute([":id" => $id]);

      $_SESSION["flash"] = ["message" => "Category {$type} deleted.", "type" => "danger"];

      header("Location: add_category.php");
      return;

    } else if ($type == "product") {

      $conn->prepare("DELETE FROM products WHERE id = :id")->execute([":id" => $id]);

      header("Location: add_product.php");
      return;

    } else if ($type == "order") {

      $conn->prepare("DELETE FROM orders WHERE id = :id")->execute([":id" => $id]);

      header("Location: add_order.php");
      return;

    } else if ($type == "customer") {

      if ($id != $_SESSION["customer"]["id"]){
        http_response_code(403);
        echo("HTTP 403 UNAUTHORIZED");
        return;
      }

      $conn->prepare("DELETE FROM customers WHERE id = :id")->execute([":id" => $id]);

      header("Location: logout.php");
      return;

    } else{
      http_response_code(404);
      echo("HTTP 404 NOT FOUND");
      return;
    }
  }

  // Si no se proporcionaron los parámetros necesarios, redirecciona a la página principal
  header("Location: index.php");
  return;
}

?>

<?php

require "database.php";

session_start();

function isAdmin()
{
    return $_SESSION["customer"]["email"] == "admin@admin.com";
}

// Se redirige al usuario a la página de inicio si no ha iniciado sesión como cliente
if (!isset($_SESSION["customer"])) {
    header("Location: index.php");
    return;
}

$error = null;

// Procesamiento del formulario
if ($_SERVER["REQUEST_METHOD"] === "GET") {

    $id = $_GET["id"]; // id del producto, categoría, pedido o cliente
    $type = $_GET["type"]; // product, category, order, customer

    switch ($type) {
        case "product":

            // Solo puede realizar esta acción el administrador
            if ($_SESSION["customer"]["email"] != "admin@admin.com") {
                http_response_code(403);
                echo ("HTTP 403 UNAUTHORIZED");
                return;
            }

            $statement = $conn->prepare("SELECT * FROM products WHERE id = :id LIMIT 1");
            $statement->execute([":id" => $id]);
            $product = $statement->fetch(PDO::FETCH_ASSOC);


            if ($statement->rowCount() == 0) {
                http_response_code(404);
                echo ("HTTP 404 NOT FOUND");
                return;
            }

            $conn->prepare("DELETE FROM products WHERE id = :id")->execute([":id" => $id]);

            // $_SESSION["flash"] = ["message" => "Product '{$product["name"]}' deleted.", "type" => "danger"];

            header("Location: products.php");
            return;
            // break;

        case "category":

            // Solo puede realizar esta acción el administrador
            if ($_SESSION["customer"]["email"] != "admin@admin.com") {
                http_response_code(403);
                echo ("HTTP 403 UNAUTHORIZED");
                return;
            }

            $statement = $conn->prepare("SELECT * FROM categories WHERE id = :id LIMIT 1");
            $statement->execute([":id" => $id]);
            $category = $statement->fetch(PDO::FETCH_ASSOC);

            if ($statement->rowCount() == 0) {
                http_response_code(404);
                echo ("HTTP 404 NOT FOUND");
                return;
            }

            $conn->prepare("DELETE FROM categories WHERE id = :id")->execute([":id" => $id]);

            // $_SESSION["flash"] = ["message" => "Category '{$category["type"]}' deleted.", "type" => "danger"];

            header("Location: categories.php");
            return;
            // break;

        case "order":

            // Solo puede realizar esta acción el administrador
            if ($_SESSION["customer"]["email"] != "admin@admin.com") {
                http_response_code(403);
                echo ("HTTP 403 UNAUTHORIZED");
                return;
            }

            // comprueba antes de eliminar si existe el pedido
            $statement = $conn->prepare("SELECT * FROM orders WHERE id = :id LIMIT 1");
            $statement->execute([":id" => $id]);
            $order = $statement->fetch(PDO::FETCH_ASSOC);

            if ($statement->rowCount() == 0) {
                http_response_code(404);
                echo ("HTTP 404 NOT FOUND");
                return;
            }

            $conn->prepare("DELETE FROM orders WHERE id = :id")->execute([":id" => $id]);

            header("Location: orders.php");
            return;
            // break;

        case "customer":

            // Solo se puede eliminar uno a sí mismo pero el administrador puede eliminar a cualquiera
            if ($id != $_SESSION["customer"]["id"] && !isAdmin()) {
                http_response_code(403);
                echo ("HTTP 403 UNAUTHORIZED");
                return;
            }

            // El admin no puede eliminarse a sí mismo
            if ($id == $_SESSION["customer"]["id"] && $_SESSION["customer"]["email"] == "admin@admin.com") {
                $_SESSION["flash"] = ["message" => "You can't delete yourself.", "type" => "danger"];
                header("Location: customers.php");
                return;
            }

            // Mostrar mensaje de confirmación
            if ($_SESSION["customer"]["email"] == "admin@admin.com") {
                $confirmMessage = "¿Estás seguro de que quieres eliminar a este cliente?";
            } else {
                $confirmMessage = "¿Estás seguro de que quieres eliminar tu cuenta?";
            }

            // Mostrar mensaje de confirmación
            echo "<script>
                if (confirm('$confirmMessage')) {
                    window.location.href = 'delete.php?id=$id&type=customer';
                } else {
                    window.location.href = 'customers.php';
                }
                </script>";
            return;


            // Eliminar cliente
            $conn->prepare("DELETE FROM customers WHERE id = :id")->execute([":id" => $id]);

            // Redirigir después de la eliminación
            if ($_SESSION["customer"]["email"] == "admin@admin.com") {
                header("Location: customers.php");
            } else {
                header("Location: logout.php");
            }

            return;
            // break;

        default:
            http_response_code(404);
            echo ("HTTP 404 NOT FOUND");
            break;
    }
}


?>
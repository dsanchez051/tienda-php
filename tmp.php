
<?php

require "database.php";

session_start();

// Se redirige al usuario a la página de inicio si no ha iniciado sesión como cliente
if (!isset($_SESSION["customer"])) {
    header("Location: index.php");
    return;
}

$error = null;

function isAdmin()
{
    return $_SESSION["customer"]["email"] == "admin@admin.com";
}

function checkAdmin()
{
    if (!isAdmin()) {
        http_response_code(403);
        echo ("HTTP 403 UNAUTHORIZED");
        return;
    }
}

// Procesamiento del formulario
if ($_SERVER["REQUEST_METHOD"] === "GET") {

    $id = $_GET["id"]; // id del producto, categoría, pedido o cliente
    $type = $_GET["type"]; // product, category, order, customer

    switch ($type) {
        case "product":

            // Solo puede realizar esta acción el administrador
            checkAdmin();

            // Comprueba si existe el producto antes de eliminarlo
            $statement = $conn->prepare("SELECT * FROM products WHERE id = :id LIMIT 1");
            $statement->execute([":id" => $id]);
            $product = $statement->fetch(PDO::FETCH_ASSOC);

            if ($statement->rowCount() == 0) {
                http_response_code(404);
                echo ("HTTP 404 NOT FOUND");
                return;
            }

            // Verificar si hay pedidos asociados a este producto
            $statement = $conn->prepare("SELECT COUNT(*) AS count FROM orders WHERE product_id = :id");
            $statement->execute([":id" => $id]);
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            $orderCount = $result['count'];

            if ($orderCount > 0) {
                // Si hay pedidos asociados, redirigir con un mensaje de error
                $_SESSION["flash"] = ["message" => "Cannot delete product because there are orders associated with it.", "type" => "danger"];
                header("Location: products.php");
                return;
            }

            // Eliminar producto
            $conn->prepare("DELETE FROM products WHERE id = :id")->execute([":id" => $id]);

            header("Location: products.php");
            return;
            // break;

        case "category":

            // Solo puede realizar esta acción el administrador
            checkAdmin();

            // Compueba si existe la categoría antes de eliminarla
            $statement = $conn->prepare("SELECT * FROM categories WHERE id = :id LIMIT 1");
            $statement->execute([":id" => $id]);
            $category = $statement->fetch(PDO::FETCH_ASSOC);

            if ($statement->rowCount() == 0) {
                http_response_code(404);
                echo ("HTTP 404 NOT FOUND");
                return;
            }

            // Verificar si hay productos asociados a esta categoría
            $statement = $conn->prepare("SELECT COUNT(*) AS count FROM products WHERE category_id = :id");
            $statement->execute([":id" => $id]);
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            $productCount = $result['count'];

            if ($productCount > 0) {
                // Si hay productos asociados, redirigir con un mensaje de error
                $_SESSION["flash"] = ["message" => "Cannot delete category because there are products associated with it.", "type" => "danger"];
                header("Location: categories.php");
                return;
            }

            // No hay productos asociados --> eliminar la categoría
            $conn->prepare("DELETE FROM categories WHERE id = :id")->execute([":id" => $id]);

            header("Location: categories.php");
            return;
            // break;

        case "order":

            // Solo puede realizar esta acción el administrador
            checkAdmin();

            // Comprueba si existe el pedido antes de eliminarlo 
            $statement = $conn->prepare("SELECT * FROM orders WHERE id = :id LIMIT 1");
            $statement->execute([":id" => $id]);
            $order = $statement->fetch(PDO::FETCH_ASSOC);

            if ($statement->rowCount() == 0) {
                http_response_code(404);
                echo ("HTTP 404 NOT FOUND");
                return;
            }

            // Eliminar pedido
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
            if ($id == $_SESSION["customer"]["id"] && isAdmin()) {
                $_SESSION["flash"] = ["message" => "You can't delete yourself.", "type" => "danger"];
                header("Location: customers.php");
                return;
            }

            // Mostrar mensaje de confirmación
            $confirmMessage = isAdmin() ?
                "¿Estás seguro de que quieres eliminar a este cliente? Se eliminarán todos sus pedidos." :
                "¿Estás seguro de que quieres eliminar tu cuenta? Se eliminarán todos tus pedidos.";


            // Mostrar mensaje de confirmación
            // echo "<script>
            //     if (confirm('$confirmMessage')) {
            //         window.location.href = 'delete.php?id=$id&type=customer';
            //     } else {
            //         window.location.href = 'customers.php';
            //     }
            //     </script>";
            // return;

            // Eliminar todos los pedidos asociados a este cliente
            $conn->prepare("DELETE FROM orders WHERE customer_id = :id")->execute([":id" => $id]);

            // Eliminar cliente
            $conn->prepare("DELETE FROM customers WHERE id = :id")->execute([":id" => $id]);

            // Redirigir después de la eliminación
            isAdmin() ? header("Location: customers.php") : header("Location: logout.php");

            return;
            // break;

        default:
            http_response_code(404);
            echo ("HTTP 404 NOT FOUND");
            break;
    }
}


?>
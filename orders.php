<?php

require "database.php";

session_start();

function isAdmin() {
    return $_SESSION["customer"]["email"] == "admin@admin.com";
}

// Se redirige al usuario a la página de inicio si no ha iniciado sesión como cliente
if (!isset($_SESSION["customer"])) {
    header("Location: index.php");
    return;
}

// Procesa el formulario de enviar el pedido
if ($_SERVER["REQUEST_METHOD"] === "POST" && isAdmin()) {
    $order_id = $_POST["order_id"];

    $conn
        ->prepare("UPDATE orders SET shipped = 1 WHERE id = :order_id")
        ->execute([
            ":order_id" => $order_id
        ]);

    $_SESSION["flash"] = ["message" => "Order {$order_id} shipped."];

    header("Location: orders.php");
    return;
}


// Si soy el admin, obtener todos los pedidos de todos los clientes. Si soy un cliente, obtener todos mis pedidos.
if (isAdmin()) {
    $orders = $conn
        ->query("SELECT orders.id, products.name as product, orders.date_time as date, orders.shipped as shipped, customers.email as email FROM orders 
            JOIN products ON orders.product_id = products.id 
            JOIN categories ON products.category_id = categories.id
            JOIN customers ON orders.customer_id = customers.id")
        ->fetchAll(PDO::FETCH_ASSOC);
} else {
    $orders = $conn
        ->query("SELECT products.name as product, orders.date_time as date, categories.type as category, orders.shipped as shipped FROM orders 
            JOIN products ON orders.product_id = products.id 
            JOIN categories ON products.category_id = categories.id 
            WHERE orders.customer_id = {$_SESSION["customer"]["id"]}")
        ->fetchAll(PDO::FETCH_ASSOC);
}

?>

<?php require "partials/header.php" ?>

<!-- Mensaje de bienvenida -->
<div class="container pt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <p class="display-6">
                        Welcome, <?= $_SESSION["customer"]["name"] ?>!
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Listado de pedidos -->
<?php if (isAdmin()) : ?>
    <div class="container pt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header display-6">Orders</div>
                    <div class="card-body text-center">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Product</th>
                                    <th>Date</th>
                                    <th>Shipped</th>
                                    <th>Customer</th>
                                    <th>Send</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order) : ?>
                                    <tr>
                                        <td><?= $order["id"] ?></td>
                                        <td><?= $order["product"] ?></td>
                                        <td><?= $order["date"] ?></td>
                                        <td><?= $order["shipped"] ? "Yes" : "No" ?></td>
                                        <td><?= $order["email"] ?></td>
                                        <td>
                                            <?php if (!$order["shipped"]) : ?>
                                                <form action="orders.php" method="POST">
                                                    <input type="hidden" name="order_id" value="<?= $order["id"] ?>">
                                                    <button class="btn btn-primary">Ship</button>
                                                </form>
                                            <?php endif ?>
                                        </td>
                                        <td>
                                            <a href="delete.php?id=<?= $order["id"] ?>&type=order" class="btn btn-danger">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php else : ?>
    <!-- Listado de pedidos para el cliente -->
    <div class="container pt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header display-6">Orders</div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Date</th>
                                    <th>Shipped</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order) : ?>
                                    <tr>
                                        <td><?= $order["product"] ?></td>
                                        <td><?= $order["category"] ?></td>
                                        <td><?= $order["date"] ?></td>
                                        <td><?= $order["shipped"] ? "Yes" : "No" ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif ?>

<?php require "partials/footer.php" ?>
<?php

require "database.php";

session_start();

// Se redirige al usuario a la página de inicio si no ha iniciado sesión como cliente
if (!isset($_SESSION["customer"])) {
    header("Location: index.php");
    return;
}

// Obtener todos productos actualizados con el nombre de su respectiva categoría
$products = $conn
    ->query("SELECT products.id, products.name, products.price, categories.type as category
            FROM products 
            JOIN categories ON products.category_id = categories.id")
    ->fetchAll(PDO::FETCH_ASSOC);

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

<!-- Listado de productos -->
<div class="container pt-5">
    <div class="row justify-content-center">

        <div class="col-md-6 text-center">
            <div class="card">
                <div class="card-header display-6">Products</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Category</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product) : ?>
                                    <tr>
                                        <td><?= $product["name"] ?></td>
                                        <td><?= $product["price"] ?></td>
                                        <td><?= $product["category"] ?></td>
                                        <td>
                                            <a href="delete.php?id=<?= $product["id"] ?>&type=product" class="btn btn-danger">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 text-center">
            <div class="card">
                <div class="card-header display-6">My Orders</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order) : ?>
                                    <tr>
                                        <td><?= $order["id"] ?></td>
                                        <td><?= $order["product"] ?></td>
                                        <td><?= $order["quantity"] ?></td>
                                        <td><?= $order["total"] ?></td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


        </div>
    </div>

    <?php require "partials/footer.php" ?>
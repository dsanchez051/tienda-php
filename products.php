<?php

require "database.php";

session_start();

function isAdmin(){
    return $_SESSION["customer"]["email"] == "admin@admin.com";
}

// Se redirige al admin a la página de inicio si no ha iniciado sesión
if (!isset($_SESSION["customer"])) {
    header("Location: index.php");
    return;
}

$error = null;

// Formulario para el admin
if ($_SERVER["REQUEST_METHOD"] === "POST" && isAdmin()) {
    $name = $_POST["name"];
    $price = $_POST["price"];
    $category_id = $_POST["category_id"];

    if (empty($name) || empty($price) || empty($category_id)) {
        $error = "Please fill all the fields.";
    } else if (!is_numeric($price)) {
        $error = "Price must be a number";
    } else {
        $conn
            ->prepare("INSERT INTO products (name, price, category_id) VALUES (:name, :price, :category_id)")
            ->execute([
                ":name" => $name,
                ":price" => $price,
                ":category_id" => $category_id
            ]);

        // $_SESSION["flash"] = ["message" => "Product '{$name}' added."];

        header("Location: products.php");
        return;
    }
}


// Formulario para el cliente
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["product_id"])) {
    $customer_id = $_SESSION["customer"]["id"];
    $product_id = $_POST["product_id"];
    $date = date('Y-m-d H:i:s');

    // Insertar el pedido en la base de datos
    $conn
        ->prepare("INSERT INTO orders (customer_id, product_id, date_time) VALUES (:customer_id, :product_id, :date)")
        ->execute([
            ":customer_id" => $customer_id,
            ":product_id" => $product_id,
            ":date" => $date
        ]);


    $_SESSION["flash"] = ["message" => "Product added to cart."];

    header("Location: products.php");
    return;
}


// Obtener todas las categorías
$categories = $conn->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);

// Obtener todos productos actualizados con el nombre de su respectiva categoría
$products = $conn
    ->query("SELECT products.id, products.name, products.price, categories.type as category FROM products 
            JOIN categories ON products.category_id = categories.id")
    ->fetchAll(PDO::FETCH_ASSOC);

?>

<?php require "partials/header.php" ?>


<!-- Formulario para añadir una nuevo producto -->
<div class="container pt-5">
    <div class="row justify-content-center">

        <!-- Solo el admin puede añadir productos -->
        <?php if (isAdmin()) : ?>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Add New Product</div>
                    <div class="card-body">

                        <?php if ($error) : ?>
                            <p class="text-danger">
                                <?= $error ?>
                            </p>
                        <?php endif ?>

                        <form method="POST" action="products.php">

                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name">
                            </div>

                            <div class="mb-3">
                                <label for="price" class="form-label">Price</label>
                                <input type="number" step="0.01" class="form-control" id="price" name="price">
                            </div>

                            <div class="mb-3">
                                <label for="category_id" class="form-label">Category</label>
                                <select class="form-select" id="category_id" name="category_id">
                                    <option value=""> - </option>
                                    <?php foreach ($categories as $category) : ?>
                                        <option value="<?= $category["id"] ?>">
                                            <?= $category["type"] ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary">Add Product</button>

                        </form>
                    </div>
                </div>
            </div>
        <?php endif ?>

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
                                            <?php if (isAdmin()) : ?>
                                                <a href="delete.php?id=<?= $product["id"] ?>&type=product" class="btn btn-danger">Delete</a>
                                            <?php else : ?>
                                                <form method="POST" action="products.php">
                                                    <input type="hidden" name="product_id" value="<?= $product["id"] ?>">
                                                    <button type="submit" class="btn btn-primary">Buy</button>
                                                </form>
                                            <?php endif ?>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require "partials/footer.php" ?>
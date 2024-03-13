<?php

require "database.php";

session_start();

// Se redirige al usuario a la página de inicio si no ha iniciado sesión como cliente
if (!isset($_SESSION["customer"])) {
    header("Location: index.php");
    return;
}

// Solo puede acceder a esta página el administrador
if ($_SESSION["customer"]["email"] != "admin@admin.com") {
    http_response_code(403);
    echo ("HTTP 403 UNAUTHORIZED");
    return;
}

$error = null;

// Procesamiento del formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $type = $_POST["type"];

    if (empty($type)) {
        $error = "Please fill all the fields.";
    } else {

        // Insertar nueva categoría en la base de datos comprobando si ya existe
        $statement = $conn->prepare("SELECT * FROM categories WHERE type = :type");
        $statement->bindValue(":type", $type);
        $statement->execute();

        if ($statement->rowCount() > 0) {
            $error = "Category already exists";
        } else {
            $conn
                ->prepare("INSERT INTO categories (type) VALUES (:type)")
                ->execute([
                    ":type" => $type
                ]);

            $_SESSION["flash"] = ["message" => "Category '{$type}' added."];
        }

        header("Location: categories.php");
        return;
    }
}

// Obtener todas las categorías actualizadas
$categories = $conn->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);

?>

<?php require "partials/header.php" ?>

<!-- Formulario para añadir una nueva categoría -->
<div class="container pt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Add New Category</div>
                <div class="card-body">

                    <?php if ($error) : ?>
                        <p class="text-danger">
                            <?= $error ?>
                        </p>
                    <?php endif ?>

                    <form method="POST" action="categories.php">

                        <div class="mb-3">
                            <label for="type" class="form-label">Category name</label>
                            <input type="text" class="form-control" id="type" name="type">
                        </div>

                        <button type="submit" class="btn btn-primary">Add Category</button>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Listado de las categorías -->
<div class="container pt-4 p-3">
    <div class="row">
        <?php foreach ($categories as $category) : ?>
            <div class="col-md-4 mb-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="card-title text-capitalize"><?= $category["type"] ?></h3>
                        <a href="delete.php?id=<?= $category["id"] ?>&type=category" class="btn btn-danger">Delete</a>
                    </div>
                </div>
            </div>
        <?php endforeach ?>
    </div>
</div>

<?php require "partials/footer.php" ?>
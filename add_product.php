<?php

require "database.php";

session_start();

if (!isset($_SESSION["customer"])) {
  header("Location: index.php");
}

$error = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
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
  }
}

$products = $conn->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);

$categories = ["Electronics", "Clothes", "Books", "Home"];


?>

<?php require "partials/header.php" ?>

<div class="container pt-4">
  <div class="row">
    <div class="col-md-6">
      <h2>Add Product</h2>

      <?php if ($error) : ?>
        <p class="text-danger"><?= $error ?></p>
      <?php endif ?>

      <form method="POST">

        <div class="mb-3">
          <label for="name" class="form-label">Name</label>
          <input type="text" class="form-control" id="name" name="name">
        </div>

        <div class="mb-3">
          <label for="description" class="form-label">Description</label>
          <textarea class="form-control" id="description" name="description"></textarea>
        </div>

        <div class="mb-3">
          <label for="price" class="form-label">Price</label>
          <input type="number" class="form-control" id="price" name="price">
        </div>


        <div class="mb-3">
          <label for="category" class="form-label">Category</label>
          <select class="form-select" id="category" name="category_id">
            <?php foreach ($categories as $category) : ?>
              <option value="<?= $category ?>"><?= $category ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <button type="submit" class="btn btn-primary">Add Product</button>

      </form>
    </div>
  </div>

  <div class="row mt-4">
    <div class="col-md-12">
      <h2>Products</h2>
      <table class="table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Description</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($products as $product) : ?>
            <tr>
              <td><?= $product["name"] ?></td>
              <td><?= $product["description"] ?></td>
              <td><?= $product["price"] ?></td>
              <td><?= $product["stock"] ?></td>
              <td>
                <form method="POST" action="delete_product.php">
                  <input type="hidden" name="id" value="<?= $product["id"] ?>">
                  <button type="submit" class="btn btn-danger">Delete</button>
                </form>
              </td>
            </tr>
          <?php endforeach ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require "partials/footer.php" ?>
<?php

require "database.php";

session_start();

// Se redirige al usuario a la página de inicio si no ha iniciado sesión como cliente
if (!isset($_SESSION["customer"])) {
  header("Location: index.php");
}

$customers = $conn->query("SELECT * FROM customers")->fetchAll(PDO::FETCH_ASSOC);

?>

<?php require "partials/header.php" ?>

<div class="container pt-4 p-3">
  <div class="row">

    <div class="col-md-12">
      <h1>Customers</h1>
      <table class="table">
        <thead>
          <tr>
            <th>DNI</th>
            <th>Name</th>
            <th>Address</th>
            <th>Email</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($customers as $customer) : ?>
            <tr>
              <td><?= $customer["dni"] ?></td>
              <td><?= $customer["name"] ?></td>
              <td><?= $customer["address"] ?></td>
              <td><?= $customer["email"] ?></td>
            </tr>
          <?php endforeach ?>
        </tbody>
      </table>
    </div>

    <?php foreach ($customers as $customer) : ?>
      <div class="col-md-4 mb-3">
        <div class="card text-center">
          <div class="card-body">
            <h3 class="card-title text-capitalize"><?= $customer["name"] ?></h3>
            <p class="m-2">DNI: <?= $customer["dni"] ?></p>
            <p class="m-2">Address: <?= $customer["address"] ?></p>
            <p class="m-2">Email: <?= $customer["email"] ?></p>
          </div>
        </div>
      </div>
    <?php endforeach ?>

  </div>
</div>


<?php require "partials/footer.php" ?>
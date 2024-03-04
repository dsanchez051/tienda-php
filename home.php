<?php

require "database.php";

session_start();

// Se redirige al usuario a la página de inicio si no ha iniciado sesión como cliente
if (!isset($_SESSION["customer"])) {
  header("Location: index.php");
  return;
}

$customers = $conn->query("SELECT * FROM customers")->fetchAll(PDO::FETCH_ASSOC);

?>

<?php require "partials/header.php" ?>

<!-- Listado de los clientes -->
<div class="container pt-4 p-3">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header display-6">Customers</div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered table-striped">
              <thead class="table-dark">
                <tr>
                  <th>DNI</th>
                  <th>Name</th>
                  <th>Address</th>
                  <th>Email</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($customers as $customer) : ?>
                  <tr>
                    <td><?= $customer["dni"] ?></td>
                    <td><?= $customer["name"] ?></td>
                    <td><?= $customer["address"] ?></td>
                    <td><?= $customer["email"] ?></td>
                    <td>
                      <?php if ($customer["id"] === $_SESSION["customer"]["id"]) : ?>
                        <a href="edit_customer.php?id=<?= $customer["id"] ?>" class="btn btn-primary">Edit</a>
                        <a href="delete.php?id=<?= $customer["id"] ?>&type=customer" class="btn btn-danger">Delete</a>
                      <?php endif; ?>
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
<?php

require "database.php";

$error = null;

// Procesamiento del formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $dni = $_POST["dni"];
  $name = $_POST["name"];
  $address = $_POST["address"];
  $email = $_POST["email"];
  $password = $_POST["password"];

  if (empty($dni) || empty($name) || empty($address) || empty($email) || empty($password)) {
    $error = "Please fill all the fields.";
  } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = "Invalid email format";
  } else if (!preg_match('/^\d{8}[A-Za-z]$/', $dni)) {
    $error = "Invalid DNI format. It should be 8 numbers followed by a letter.";
  } else {

    // Verificar si el correo electrónico ya existe en la base de datos
    $statement = $conn->prepare("SELECT * FROM customers WHERE email = :email");
    $statement->bindValue(":email", $email);
    $statement->execute();

    if ($statement->rowCount() > 0) {
      $error = "Email already exists";
    } else {

      // Verificar si el correo electrónico o el DNI ya existen en la base de datos
      $statement = $conn->prepare("SELECT * FROM customers WHERE email = :email OR dni = :dni");
      $statement->execute([
        ":email" => $email,
        ":dni" => $dni
      ]);

      $result = $statement->fetchAll();

      if (count($result) > 0) {
        foreach ($result as $row) {
          if ($row['email'] == $email) {
            $error = "Email already exists";
          } else if ($row['dni'] == $dni) {
            $error = "DNI already exists";
          }
        }
      } else {

        // Insertar nuevo cliente en la base de datos
        $conn
          ->prepare("INSERT INTO customers (dni, name, address, email, password) VALUES (:dni, :name, :address, :email, :password)")
          ->execute([
            ":dni" => $dni,
            ":name" => $name,
            ":address" => $address,
            ":email" => $email,
            ":password" => password_hash($password, PASSWORD_DEFAULT)
          ]);

        // Obtener el cliente recién insertado
        $statement = $conn->prepare("SELECT * FROM customers WHERE email = :email LIMIT 1");
        $statement->bindValue(":email", $email);
        $statement->execute();
        $customers = $statement->fetch(PDO::FETCH_ASSOC);

        // Iniciar sesión y guardar el cliente (excepto su contraseña) en la sesión	
        session_start();

        $_SESSION["customer"] = $customer;

        // Redirigir a la página de home después de la inserción
        header("Location: home.php");
      }
    }
  }
}

?>

<?php require "partials/header.php" ?>

<div class="container pt-5">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">Register</div>
        <div class="card-body">

          <?php if ($error) : ?>
            <p class="text-danger">
              <?= $error ?>
            </p>
          <?php endif ?>

          <form method="POST" action="register.php">

            <div class="mb-3 row">
              <label for="name" class="col-md-4 col-form-label text-md-end">Name</label>

              <div class="col-md-6">
                <input id="name" type="text" class="form-control" name="name" autocomplete="name" autofocus>
              </div>
            </div>

            <div class="mb-3 row">
              <label for="dni" class="col-md-4 col-form-label text-md-end">DNI</label>

              <div class="col-md-6">
                <input id="dni" type="text" class="form-control" name="dni" autocomplete="dni" autofocus>
              </div>
            </div>

            <div class="mb-3 row">
              <label for="email" class="col-md-4 col-form-label text-md-end">Email</label>

              <div class="col-md-6">
                <input id="email" type="email" class="form-control" name="email" autocomplete="email" autofocus>
              </div>
            </div>

            <div class="mb-3 row">
              <label for="password" class="col-md-4 col-form-label text-md-end">Password</label>

              <div class="col-md-6">
                <input id="password" type="password" class="form-control" name="password" autocomplete="password" autofocus>
              </div>
            </div>

            <div class="mb-3 row">
              <label for="address" class="col-md-4 col-form-label text-md-end">Address</label>

              <div class="col-md-6">
                <input id="address" type="text" class="form-control" name="address" autocomplete="address">
              </div>
            </div>

            <div class="mb-3 row">
              <div class="col-md-6 offset-md-4">
                <button type="submit" class="btn btn-primary">Submit</button>
              </div>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
</div>


<?php require "partials/footer.php" ?>
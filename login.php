<?php

require "database.php";

$error = null;

// Procesamiento del formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {
        $error = "Please fill all the fields.";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } else {

        // Verificar si el correo electrónico ya existe en la base de datos
        $statement = $conn->prepare("SELECT * FROM customers WHERE email = :email");
        $statement->bindValue(":email", $email);
        $statement->execute();

        if ($statement->rowCount() === 0) {
            $error = "Invalid credentials.";
        } else {
            $customer = $statement->fetch(PDO::FETCH_ASSOC);

            if (!password_verify($password, $customer["password"])) {
                $error = "Invalid credentials.";
            } else {
                // Iniciar sesión y guardar el cliente (excepto su contraseña) en la sesión	
                session_start();

                unset($customer["password"]);

                $_SESSION["customer"] = $customer;

                // Redirigir a la página de home
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
                <div class="card-header">Log in</div>
                <div class="card-body">

                    <?php if ($error) : ?>
                        <p class="text-danger">
                            <?= $error ?>
                        </p>
                    <?php endif ?>

                    <form method="POST" action="login.php">

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
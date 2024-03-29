<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">

        <a class="navbar-brand font-weight-bold" href="orders.php">
            <img class="mr-2" src="./static/img/logo.png" />
            Tienda
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="d-flex justify-content-between w-100">
                <ul class="navbar-nav">

                    <?php if (isset($_SESSION["customer"]) && $_SESSION["customer"]["email"] != "admin@admin.com") : ?>
                        <li class="nav-item card border-secondary">
                            <a class="nav-link" href="products.php">Buy</a>
                        </li>
                        <li class="nav-item card border-secondary">
                            <a class="nav-link" href="orders.php">My Orders</a>
                        </li>
                        <li class="nav-item card border-secondary">
                            <a class="nav-link" href="customers.php">My Profile</a>
                        </li>
                        <li class="nav-item card border-secondary">
                            <a class="nav-link" href="logout.php">Logout</a>
                        </li>
                    <?php elseif (isset($_SESSION["customer"]) && $_SESSION["customer"]["email"] == "admin@admin.com") : ?>
                        <li class="nav-item card border-secondary">
                            <a class="nav-link" href="orders.php">Orders</a>
                        </li>
                        <li class="nav-item card border-secondary">
                            <a class="nav-link" href="customers.php">Customers</a>
                        </li>
                        <li class="nav-item card border-secondary">
                            <a class="nav-link" href="products.php">Manage Products</a>
                        </li>
                        <li class="nav-item card border-secondary">
                            <a class="nav-link" href="categories.php">Manage Categories</a>
                        </li>
                        <li class="nav-item card border-secondary">
                            <a class="nav-link" href="logout.php">Logout</a>
                        </li>
                    <?php else : ?>
                        <li class="nav-item card border-secondary">
                            <a class="nav-link" href="register.php">Register</a>
                        </li>
                        <li class="nav-item card border-secondary">
                            <a class="nav-link" href="login.php">Login</a>
                        </li>
                    <?php endif ?>

                </ul>

                <?php if (isset($_SESSION["customer"])) : ?>
                    <div class="p-2">
                        <?= $_SESSION["customer"]["email"] ?>
                    </div>
                <?php endif ?>

            </div>
        </div>

    </div>
</nav>
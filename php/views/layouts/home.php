<?php
use app\core\Application;

$rootPath = Application::$ROOT_DIR;
?>

<html>
    <head>
        <meta charset="UTF-8">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

        <title><?php echo $this->title ?></title>
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid ">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                  <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    
                        <?php if (Application::isGuest()): ?>
                        <ul class="navbar-nav me-auto">
                            <a class="navbar-brand mb-0 h3 text-dark" href="<?= $rootPath ?>">PICK-A-BOOK</a>
                        </ul>
                        <?php else: ?>
                        <ul class="navbar-nav me-auto">
                            <?php if (Application::$app->user->uporabnik_tip == "stranka"): ?>
                                <li class="nav-item">
                                  <a class="nav-link <?php echo $this->activeHome ?>" aria-current="page" href="<?= $rootPath ?>store?page=1">Home</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo $this->activeCart ?>" href="<?= $rootPath ?>store/cart?page=1">Cart</a>
                                </li>
                            <?php elseif (Application::$app->user->uporabnik_tip == "prodajalec"): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo $this->activeHome ?>" aria-current="page" href="<?= $rootPath ?>store?page=1">Home</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo $this->activeBook ?>" href="<?= $rootPath ?>store/addBook">Add Book</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo $this->activeCustomers ?>" href="<?= $rootPath ?>store/allCustomers">All Customers</a>
                                </li>
                            <?php elseif (Application::$app->user->uporabnik_tip == "administrator"): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo $this->activeSellers ?>" href="<?= $rootPath ?>store/allSellers">All Sellers</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo $this->activeCustomers ?>" href="<?= $rootPath ?>store/allCustomers">All Customers</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                        <?php endif; ?>
                    
                    
                        <?php if (Application::isGuest()): ?>
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item">
                              <a class=" btn btn-dark" href="<?= $rootPath ?>register">Register</a>
                            </li>
                        </ul>
                        <?php else: ?>
                        <ul class="navbar-nav mx-auto d-none d-sm-none d-md-none d-lg-block">
                            <span class="navbar-text mb-0 h3 text-dark">Welcome, <?php echo Application::$app->user->getDisplayName() ?>!</span>
                        </ul>
                        <ul class="navbar-nav ms-auto ">
                            <li class="dropdown me-2">
                                <a class="btn btn-dark dropdown-toggle " href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                                  My Profile
                                </a>

                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                    <?php if (Application::$app->user->uporabnik_tip == "stranka"): ?>
                                        <li><a class="dropdown-item" href="<?= $rootPath ?>profile/myOrders">My orders</a></li>
                                    <?php elseif (Application::$app->user->uporabnik_tip == "prodajalec"): ?>
                                        <li><a class="dropdown-item" href="<?= $rootPath ?>profile/pendingOrders">Pending orders</a></li>
                                        <li><a class="dropdown-item" href="<?= $rootPath ?>profile/approvedOrders">Approved orders</a></li>
                                        <li><a class="dropdown-item" href="<?= $rootPath ?>profile/allOrders">All orders</a></li>
                                    <?php elseif (Application::$app->user->uporabnik_tip == "administrator"): ?>
                                        
                                    <?php endif; ?>
                                    <li><a class="dropdown-item" href="<?= $rootPath ?>profile/editProfile">Edit profile</a></li>
                                </ul>
                            </li>
                            <li class="nav-item">
                              <a class="btn btn-outline-dark " href="<?= $rootPath ?>logout">Log out</a>
                            </li>
                        </ul>
                        <?php endif; ?>
                </div>
            </div>
        </nav>
        <?php if(Application::$app->session->getFlash('success')): ?>
            <div class="alert alert-success">
                <?php echo Application::$app->session->getFlash('success')?>
            </div>
        <?php endif; ?>
        {{content}}
        
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    </body>
</html>
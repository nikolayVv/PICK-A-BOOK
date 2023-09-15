<?php
use app\core\Application;

if (Application::$app->user && (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off")) {
    $url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('Location: ' . $url);
}
$this->title = 'Pick-A-Book: Users';
$this->activeHome = '';
$this->activeCart = '';
$this->activeBook = '';
if ($active == "customers") {
    $this->activeCustomers = 'active';
    $this->activeSellers = '';
} else {
    $this->activeCustomers = '';
    $this->activeSellers = 'active';
}
$rootPath= Application::$ROOT_DIR;
?>

<div class="container-fluid">
    <div class="d-flex justify-content-center row">
        <?php if (empty($users)): ?>
            <div class="card h-100 align-items-center mb-3">
                <div class="card-body p-4 align-items-center row justify-content-center align-self-center">
                    <div class="text-center">
                        <h1 class="text-secondary">There aren't any <?= $active ?> yet!</h1>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <table class="table table-hover">
                <thead>
                  <tr>
                    <th> ID user </th>
                    <th> Name </th>
                    <th> Account </th> 
                    <th> User details </th> 
                  </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= $user["uporabnik_id"] ?></td>
                                <td><?= $user["uporabnik_ime"] ?> <?= $user["uporabnik_priimek"] ?></td>
                                <td>
                                    <?php if (intval($user["uporabnik_aktiviran"], 10) == 1): ?>
                                        Active
                                    <?php else: ?>
                                        Not active
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?= $rootPath ?>store/editUser?id=<?= $user["uporabnik_id"] ?>&type=<?= $type ?>"> <button> Edit user </button></a>
                                </td>
                                <td>
                                    <?php 
                                    $id = $user['uporabnik_id'];
                                    $form = \app\core\form\Form::begin("${rootPath}store/editUser?id=$id&status=delete&prev=$type", "post") ?>
                                        <button type="submit"> Delete user </button>
                                    <?php \app\core\form\Form::end() ?> 
                                </td>
                            </tr>  
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif;
            if ($active == "customers"): ?>
                <a href="<?= $rootPath ?>store/addUser?prev=Customers&role=Customer"><button> Add new Customer </button></a>
            <?php else: ?>
                <a href="<?= $rootPath ?>store/addUser?prev=Sellers&role=Seller"><button> Add new Seller </button></a>
            <?php endif; ?>
    </div>
</div>

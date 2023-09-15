<?php
use app\core\Application;

if (Application::$app->user && (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off")) {
    $url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('Location: ' . $url);
}
$this->title = 'Pick-A-Book: Orders';
$this->activeHome = '';
$this->activeCart = '';
$this->activeBook = '';
$this->activeCustomers = '';
$this->activeSellers = '';
$rootPath = Application::$ROOT_DIR;
?>

<div class="container-fluid">
    <div class="d-flex justify-content-center row">
        <?php if (empty($orders)): ?>
            <div class="card h-100 align-items-center mb-3">
                <div class="card-body p-4 align-items-center row justify-content-center align-self-center">
                    <div class="text-center">
                        <h1 class="text-secondary">There aren't any <?= $statusLabel ?> orders yet!</h1>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <table class="table table-hover">
                <thead>
                  <tr>
                    <th> ID order </th>
                    <th> ID user </th>
                    <th> Order status </th>
                    <th> Order price </th> 
                    <th> Order details </th> 
                  </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?= $order["narocilo_id"] ?></td>
                                <td><?= $order["uporabnik_id"] ?> </td>
                                <td><?= $order["narocilo_status"] ?></td>
                                <td><?= number_format($order["narocilo_postavka"], 2) ?> EUR</td>
                                <td>
                                    <?php if ($statusLabel == "pending"): ?>
                                        <a href="<?= $rootPath ?>profile/editOrder?id=<?= $order["narocilo_id"] ?>&prev=<?= $statusLabel ?>&type=<?= $type ?>"> <button> Edit order </button></a>
                                    <?php else: ?>
                                        <a href="<?= $rootPath ?>profile/viewOrder?id=<?= $order["narocilo_id"] ?>&prev=<?= $statusLabel ?>&type=<?= $type ?>"> <button> View order </button></a>
                                    <?php endif; ?>
                                </td>
                                <?php if ($statusLabel == "approved"): ?>
                                    <td>
                                        <?php $form = \app\core\form\Form::begin("${rootPath}profile/editOrder?id=${order['narocilo_id']}&prev=$statusLabel&status=delete&type=$type", "post") ?>
                                            <button type="submit"> Delete order </button>
                                        <?php \app\core\form\Form::end() ?> 
                                    </td>
                                <?php endif; ?>
                            </tr>  
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

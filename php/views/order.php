<?php
use app\core\Application;

if (Application::$app->user && (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off")) {
    $url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('Location: ' . $url);
}
$this->title = 'Pick-A-Book: Confirm Order';
$this->activeHome = '';
$this->activeCart = '';
$this->activeBook = '';
$this->activeCustomers = '';
$this->activeSellers = '';
$rootPath = Application::$ROOT_DIR;
?>

<div class="container-fluid ">
    <?php if ($status == "edit"): 
        if ($prev == ''): ?>
            <a class="link-dark" href="<?= $rootPath ?>profile/<?= $type?>Orders">Back</a>
        <?php else: ?>
            <a class="link-dark" href="<?= $rootPath ?>profile/<?= $prev ?>Orders">Back</a>
        <?php endif;
    endif; ?>
    <div class="d-flex justify-content-center row">
        <div class="p-3 bg-white rounded">
            <div class="row">
                <div class="col col-8 col-sm-8 col-md-8 col-lg-9 col-xl-9 col-xxl-10">
                    <h1 class="text-uppercase">Invoice</h1>
                    <div class="billed"><span class="font-weight-bold text-uppercase">Billed: </span><span class="ml-1"><?php echo Application::$app->user->getDisplayName() ?></span></div>
                    <div class="billed"><span class="font-weight-bold text-uppercase">Order ID: </span><span class="ml-1">#<?= $index ?></span></div>
                </div>
                <div class="col col-4 col-sm-4 col-md-4 col-lg-3 col-xl-3 col-xxl-2 text-right mt-3 ms-auto">
                    <h4 class="text-danger mb-0">Pick-A-Book</h4>
                </div>
            </div>
            <div class="mt-3">
                <div class="table-responsive">
                    <table class="table">


                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Unit</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($books as $book): ?>
                                <tr>
                                    <td><?= $book['data']->knjiga_naslov ?> (by <?= $book['data']->knjiga_avtor ?>)</td>
                                    <td><?= $book['quantity']?></td>
                                    <td><?= number_format($book['data']->knjiga_cena, 2) ?> EUR</td>
                                    <td><?= number_format($book['data']->knjiga_cena * $book['quantity'], 2) ?> EUR</td>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td></td>
                                <td></td>
                                <td>Total:</td>
                                <th><?= number_format($total, 2) ?> EUR</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mb-3 row">
                <div class="col col-6 col-sm-8 col-md-9 col-lg-9 col-xl-9 col-xxl-10"></div>
                <div class="row col col-6 col-sm-4 col-md-3 col-lg-3 col-xl-3 col-xxl-2 mt-5">
                    <?php if ($status == "confirm"): ?>
                        <div class="col col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6">
                            <?php $form = \app\core\form\Form::begin("{$rootPath}store/makeOrder", "post") ?>
                                <button type="submit" class="btn btn-danger btn-md " type="button">Confirm</button>
                            <?php \app\core\form\Form::end() ?>
                        </div>
                        <div class="col col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6">
                            <?php $form = \app\core\form\Form::begin("${rootPath}?>store/cart?page=1", "get") ?>
                                <button type="submit" class="btn btn-outline-danger btn-md" type="button">Cancel</button>
                            <?php \app\core\form\Form::end() ?>
                        </div>
                    <?php elseif ($status == "edit" && $prev == "pending"): ?>
                        <div class="col col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6">
                            <?php $form = \app\core\form\Form::begin("${rootPath}profile/editOrder?id=$index&prev=$prev&status=accept", "post") ?>
                                <button type="submit" class="btn btn-danger btn-md " type="button">Approve</button>
                            <?php \app\core\form\Form::end() ?>
                        </div>
                        <div class="col col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6">
                            <?php $form = \app\core\form\Form::begin("${rootPath}profile/editOrder?id=$index&prev=$prev&status=reject", "post") ?>
                                <button type="submit" class="btn btn-outline-danger btn-md" type="button">Reject</button>
                            <?php \app\core\form\Form::end() ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

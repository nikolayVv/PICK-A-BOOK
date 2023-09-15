<?php
use app\core\Application;

if (Application::$app->user && (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off")) {
    $url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('Location: ' . $url);
}
$this->title = 'Pick-A-Book: Add Book';
$this->activeBook = 'active';
$this->activeCart = '';
$this->activeHome = '';
$this->activeCustomers = '';
$this->activeSellers = '';
$rootPath = Application::$ROOT_DIR;
?>

<div class="container-fluid pt-3">
    <div class="d-flex justify-content-center pt-5 pb-5">
        <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5 col-xxl-5 shadow-lg pt-5 p-3 bg-light">
            <div class="text-center pb-4">
                <h3>Add new book</h3>
            </div>
            <?php $form = \app\core\form\Form::begin("${rootPath}store/addBook", "post") ?>
                <?php echo $form->field($model, 'knjiga_avtor') ?>
                <?php echo $form->field($model, 'knjiga_naslov') ?>
                <div class="row">
                    <div class="col">
                         <?php echo $form->field($model, 'knjiga_cena')->numberField() ?>
                    </div>
                    <div class="col">
                        <?php echo $form->field($model, 'knjiga_leto') ?>
                    </div>
                </div>
                <?php echo $form->textarea($model, 'knjiga_opis')->editable() ?>
                <?php echo $form->field($model, 'knjiga_slika')->fileField() ?>
                <div class="d-flex justify-content-center pb-4 pt-5">
                    <button
                        class="btn btn-outline-dark text-center"
                        type="submit"
                    >Add Book</button>
                </div>
            <?php \app\core\form\Form::end() ?>
        </div>
    </div>
</div>
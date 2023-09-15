<?php
use app\core\Application;

if (Application::$app->user && (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off")) {
    $url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('Location: ' . $url);
}
$this->title = 'Pick-A-Book: Edit Profile';
$this->activeBook = '';
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
                <h3>Edit Profile</h3>
            </div>
            <?php $form = \app\core\form\Form::begin('', "post") ?>
                <?php if (Application::$app->user->uporabnik_tip != 'stranka') { ?>
                    <div class="row">
                        <div class="col">
                            <?php echo $form->field($model, 'uporabnik_ime') ?>
                        </div>
                        <div class="col">
                            <?php echo $form->field($model, 'uporabnik_priimek') ?>
                        </div>
                    </div>
                    <?php 
                    echo $form->field($model, 'uporabnik_email')->notEditable();
                } else { ?>
                    <div class="row">
                        <div class="col">
                            <?php echo $form->field($model, 'uporabnik_ime')->notEditable(); ?>
                        </div>
                        <div class="col">
                            <?php echo $form->field($model, 'uporabnik_priimek')->notEditable(); ?>
                        </div>
                    </div>
                    <?php
                    echo $form->field($model, 'uporabnik_email');
                } 
                echo $form->field($model, 'uporabnik_geslo')->passwordField();?>
                <div class="row">
                    <div class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-7">
                        <?php echo $form->field($model, 'uporabnik_naslov') ?>
                    </div>
                    <div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3 col-xxl-3">
                        <?php echo $form->field($model, 'uporabnik_mesto') ?>
                    </div>
                    <div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3 col-xxl-2">
                        <?php echo $form->field($model, 'posta_stevilka') ?>
                    </div>
                </div>
                <?php echo $form->field($model, 'uporabnik_telefon') ?>
                <div class="d-flex justify-content-center pb-4 pt-5">
                    <button
                        class="btn btn-outline-dark text-center"
                        type="submit"
                    >Edit</button>
                </div>
            <?php \app\core\form\Form::end() ?>
        </div>
    </div>
</div>
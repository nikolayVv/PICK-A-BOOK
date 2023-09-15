<?php
use app\core\Application;

$this->title = 'Login';
$rootPath= Application::$ROOT_DIR;
if (Application::$app->user && (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off")) {
    $url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('Location: ' . $url);
}
?>

<div class="container-fluid pt-5">
    <h1 class="text-center">WELCOME TO PICK-A-BOOK</h1>
    <h2 class="text-center fst-italic pb-5">Buy books fast and easy</h2>
    <div class="d-flex justify-content-center mt-5">
        <div class="col-12 col-sm-10 col-md-7 col-lg-5 col-xl-4 col-xxl-4 shadow-lg pt-5 p-3 bg-light">
            <div class="text-center pb-4">
                <h3>Sign In</h3>
            </div>
            <?php $form = \app\core\form\Form::begin('', "post") ?>
                <?php echo $form->field($model, 'uporabnik_email')->notEditable(); ?>
                <?php echo $form->field($model, 'uporabnik_geslo')->passwordField(); ?>
                <div class="d-flex justify-content-center">
                    <button
                        class="btn btn-outline-dark text-center mt-2"
                        type="submit"
                    >
                        Login
                    </button>
                </div>
                <div class="d-flex justify-content-center mt-3">
                    <a href="<?= $rootPath ?>" class="link-dark">Login without certificate</a>
                </div>
                <p class="text-center mt-4">Don't have an account?
                    <a href="<?= $rootPath ?>register" class="link-secondary">Sign up</a>
                </p>
                <div class="d-flex justify-content-center">
                    <a href="<?= $rootPath ?>store?page=1" class="btn btn-outline-dark text-center mt-2">Just checking...</a>
                </div>
            <?php \app\core\form\Form::end() ?>
        </div>
    </div>
</div>
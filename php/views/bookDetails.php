<?php
use app\core\Application;

if (Application::$app->user && (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off")) {
    $url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('Location: ' . $url);
}
$this->title = 'Pick-A-Book: Book Details';
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
                <h3>Book details:</h3>
            </div>
            <?php 
                if (Application::isGuest()): 
                    $form = \app\core\form\Form::begin("${rootPath}", "get");
                else: 
                    $idBook = $_GET['id'];
                    $form = \app\core\form\Form::begin("${rootPath}store/addInCart?id=$idBook", "post"); 
                endif; ?>
                
                <div class="row">
                    <div class=" mt-5 col col-7 col-sm-8 col-md-8 col-lg-8 col-xl-8 col-xxl-9">
                        <?php echo $form->field($book, 'knjiga_avtor')->notEditable()?>
                        <?php echo $form->field($book, 'knjiga_naslov')->notEditable() ?>
                    </div>
                    <div class="col col-5 col-sm-4 col-md-4 col-lg-4 col-xl-4 col-xxl-3">
                        <img src="../../<?= $book->knjiga_slika ?>" alt="book photo" width="150" height="200">
                    </div>
                </div>
                <div class="row">
                    <div class="row">
                        <?php echo $form->field($book, 'knjiga_cena')->notEditable() ?>
                    </div>
                    <div class="row">
                        <?php echo $form->field($book, 'knjiga_leto')->notEditable() ?>
                    </div>
                </div>
                <?php echo $form->textarea($book, 'knjiga_opis')->notEditable() ?>
                <div class="d-flex justify-content-center pb-4 pt-5">
                    <button
                        class="btn btn-outline-dark text-center"
                        type="submit"
                    >Add in cart</button>
                </div>
            <?php \app\core\form\Form::end() ?>
        </div>
    </div>
</div>
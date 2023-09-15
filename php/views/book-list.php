<?php
use app\core\Application;

if (Application::$app->user && (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off")) {
    $url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('Location: ' . $url);
}
$this->title = 'Pick-A-Book';
$this->activeHome = 'active';
$this->activeCart = '';
$this->activeBook = '';
$this->activeCustomers = '';
$this->activeSellers = '';
$rootPath = Application::$ROOT_DIR;
?>

<div class="container-fluid h-100">
        <?php
            $currPage = 0;
            if ($_GET['page'] <= 1) {
                $currPage = 1;
            } else if ($_GET['page'] >= ceil(count($books)/8)) {
                $currPage = ceil(count($books)/8);
            } else {
                $currPage = $_GET['page'];
            }
            $pageSet = $currPage;
        ?>
        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12 mt-3">
        <?php if (empty($books)): ?>
            <div class="card h-100 align-items-center mb-3">
                <div class="card-body p-4 align-items-center row justify-content-center align-self-center">
                    <div class="text-center">
                        <h1 class="text-secondary">There aren't any books in the database yet!</h1>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div id="bookList" class="row row-cols-1 row-cols-sm-2 row-cols-md-2 row-cols-lg-3 row-cols-xl-3 row-cols-xxl-4 justify-content-left">
                <?php 
                    $i = 0;
                    while ($currPage > 1) {
                        $i += 8;
                        $currPage--;
                    }
                    $meja=$i+8;
                    for ($j = $i; $j < $meja; $j++): 
                        if ($j < count($books)): ?>
                                <div class="col mb-5 book">
                                    <div class="card h-100">
                                        <img class="card-img-top" src="../<?= $books[$j]["knjiga_slika"] ?>" alt="book photo" height="250"/>
                                        <div class="card-body p-4">
                                            <div class="text-center">
                                                <h6 class="fw-bolder"><?= $books[$j]["knjiga_naslov"] ?></h6>
                                                        € <?= number_format($books[$j]["knjiga_cena"], 2) ?><br />
                                            </div>
                                        </div>
                                        <?php if (Application::$app->user && Application::$app->user->uporabnik_tip == 'prodajalec'): ?>
                                            <div class="card-footer p-4 pt-0 border-top-0 bg-transparent row">
                                                <div class="text-center col">
                                                    <a class="btn btn-light btn-outline-dark " href="<?= $rootPath ?>store/editBook?id=<?= $books[$j]["knjiga_id"] ?>">Edit book</a>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="card-footer p-4 pt-0 border-top-0 bg-transparent row">
                                                <div class="text-center col">
                                                    <a class="btn btn-light btn-outline-dark mt-auto" href="<?= $rootPath ?>store/bookDetails?id=<?= $books[$j]["knjiga_id"] ?>">Show more...</a>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php
                        else: break;
                        endif;
                    endfor; ?>  
            </div> 
        <?php endif; ?>
        </div>   
    <?php if (!empty($books)): ?>
        <nav aria-label="pagination">
            <ul class="pagination text-secondary justify-content-center">
                <p>Page <span id="currentPage"><strong><?= $pageSet ?></strong></span> of <span id="allPages"><strong><?= ceil(count($books)/8) ?></strong></span></p>
            </ul>
            <ul class="pagination text-secondary justify-content-center">
                <?php if($_GET['page'] <= 1 || ceil(count($books)/8) == 1): ?>
                    <li id="prev" class="page-item disabled"><a class="page-link" href="<?= $rootPath ?>store?page=1">&laquo; Previous</a></li>
                    <?php if($_GET['page'] >= ceil(count($books)/8)): ?>
                        <li id="next" class="page-item disabled"><a class="page-link" href="<?= $rootPath ?>store?page=<?= ceil(count($books)/8) ?>">Next &raquo;</a></li>
                    <?php else: ?>
                        <li id="next" class="page-item"><a class="page-link" href="<?= $rootPath ?>store?page=<?= $pageSet + 1 ?>">Next &raquo;</a></li>
                    <?php endif; ?>
                <?php else : ?>
                    <li id="prev" class="page-item"><a class="page-link" href="<?= $rootPath ?>store?page=<?= $pageSet - 1 ?>">&laquo; Previous</a></li>
                    <?php if($_GET['page'] >= ceil(count($books)/8)): ?>
                        <li id="next" class="page-item disabled"><a class="page-link" href="<?= $rootPath ?>store?page=<?= ceil(count($books)/8) ?>">Next &raquo;</a></li>
                    <?php else: ?>
                        <li id="next" class="page-item"><a class="page-link" href="<?= $rootPath ?>store?page=<?= $pageSet + 1 ?>">Next &raquo;</a></li>
                    <?php endif; ?>
                <?php endif;?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

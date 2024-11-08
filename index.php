<?php
require 'functions.php';

if (!isset($_SESSION['email'])) {
    redirect('page_login.php');
}
$error = getFlashMessage('error');
$success = getFlashMessage();
$users = getAllUsers();
?>
<?php require 'header.php'; ?>
<body class="mod-bg-1 mod-nav-link">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary bg-primary-gradient">
    <a class="navbar-brand d-flex align-items-center fw-500" href="index.php"><img alt="logo"
                                                                                    class="d-inline-block align-top mr-2"
                                                                                    src="img/logo.png"> Учебный
        проект</a>
    <button aria-controls="navbarColor02" aria-expanded="false" aria-label="Toggle navigation" class="navbar-toggler"
            data-target="#navbarColor02" data-toggle="collapse" type="button"><span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarColor02">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <a class="nav-link" href="page_profile.php">Профиль <span class="sr-only">(current)</span></a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="exit.php">Выйти</a>
            </li>
        </ul>
    </div>
</nav>

<main id="js-page-content" role="main" class="page-content mt-3">
    <?php if(isset($error)) :?>
        <div class="alert alert-danger">
            <?php echo $error; ?>
        </div>
    <?php endif;?>
    <?php if ($success): ?>
        <div class="alert alert-success">
            <?php echo $success ?>
        </div>
    <?php endif; ?>
    <div class="subheader">
        <h1 class="subheader-title">
            <i class='subheader-icon fal fa-users'></i> Список пользователей
        </h1>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <?php if ($_SESSION['is_admin']) : ?>
                <a class="btn btn-success" href="create_user.php">Добавить</a>
            <?php endif; ?>
            <div class="border-faded bg-faded p-3 mb-g d-flex mt-3">
                <input type="text" id="js-filter-contacts" name="filter-contacts"
                       class="form-control shadow-inset-2 form-control-lg" placeholder="Найти пользователя">
                <div class="btn-group btn-group-lg btn-group-toggle hidden-lg-down ml-3" data-toggle="buttons">
                    <label class="btn btn-default active">
                        <input type="radio" name="contactview" id="grid" checked="" value="grid"><i
                                class="fas fa-table"></i>
                    </label>
                    <label class="btn btn-default">
                        <input type="radio" name="contactview" id="table" value="table"><i class="fas fa-th-list"></i>
                    </label>
                </div>
            </div>
        </div>
    </div>
    <div class="row" id="js-contacts">
        <?php foreach ($users as $user): ?>
            <div class="col-xl-4">
                <div id="c_1" class="card border shadow-0 mb-g shadow-sm-hover"
                     data-filter-tags="<?php echo !empty($user['name']) ? strtolower($user['name']) : strtolower($user['email']) ?>">
                    <div class="card-body border-faded border-top-0 border-left-0 border-right-0 rounded-top">
                        <div class="d-flex flex-row align-items-center">
                                <span class="status status-<?php echo $user['online_status'] ?> mr-3">
                                    <span class="rounded-circle profile-image d-block "
                                          style="background-image:url('<?php echo !empty($user['avatar'])? $user['avatar'] : 'img/demo/avatars/avatar-m.png'  ?>'); background-size: cover;"></span>
                                </span>
                            <div class="info-card-text flex-1">
                                <a href="javascript:void(0);" class="fs-xl text-truncate text-truncate-lg text-info"
                                   data-toggle="dropdown" aria-expanded="false">
                                    <?php echo !empty($user['name']) ? $user['name'] : $user['email'] ?>
                                    <?php if ($user['id'] === $_SESSION['id'] || $_SESSION['is_admin']) : ?>
                                        <i class="fal fas fa-cog fa-fw d-inline-block ml-1 fs-md"></i>
                                        <i class="fal fa-angle-down d-inline-block ml-1 fs-md "></i>
                                    <?php endif; ?>
                                </a>
                                <?php if ($user['id'] === $_SESSION['id'] || $_SESSION['is_admin']) : ?>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="edit.php?id=<?php echo $user['id']?>">
                                            <i class="fa fa-edit"></i>
                                            Редактировать</a>
                                        <a class="dropdown-item" href="security.php?id=<?php echo $user['id']?>">
                                            <i class="fa fa-lock"></i>
                                            Безопасность</a>
                                        <a class="dropdown-item" href="status.php?id=<?php echo $user['id']?>">
                                            <i class="fa fa-sun"></i>
                                            Установить статус</a>
                                        <a class="dropdown-item" href="media.php?id=<?php echo $user['id']?>">
                                            <i class="fa fa-camera"></i>
                                            Загрузить аватар
                                        </a>
                                        <a href="deleteUser.php?id=<?php echo $user['id']?>" class="dropdown-item" onclick="return confirm('are you sure?');">
                                            <i class="fa fa-window-close"></i>
                                            Удалить
                                        </a>
                                    </div>
                                <?php endif; ?>
                                <span class="text-truncate text-truncate-xl"><?php echo !empty($user['workplace']) ? $user['workplace'] : '' ?></span>
                            </div>
                            <button class="js-expand-btn btn btn-sm btn-default d-none" data-toggle="collapse"
                                    data-target="#c_1 > .card-body + .card-body" aria-expanded="false">
                                <span class="collapsed-hidden">+</span>
                                <span class="collapsed-reveal">-</span>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0 collapse show">
                        <div class="p-3">
                            <a href="tel:+13174562564" class="mt-1 d-block fs-sm fw-400 text-dark">
                                <i class="fas fa-mobile-alt text-muted mr-2"></i><?php echo !empty($user['phone']) ? $user['phone'] : '' ?>
                            </a>
                            <a href="mailto:oliver.kopyov@smartadminwebapp.com"
                               class="mt-1 d-block fs-sm fw-400 text-dark">
                                <i class="fas fa-mouse-pointer text-muted mr-2"></i> <?php echo $user['email'] ?></a>
                            <address class="fs-sm fw-400 mt-4 text-muted">
                                <i class="fas fa-map-pin mr-2"></i> <?php echo !empty($user['address']) ? $user['address'] : '' ?>
                            </address>
                            <div class="d-flex flex-row">
                                <a href="<?php echo !empty($user['vk_link']) ? $user['vk_link'] : '$javascript:void(0);' ?>"
                                   class="mr-2 fs-xxl" style="color:#4680C2">
                                    <?php echo !empty($user['vk_link']) ? '<i class="fab fa-vk"></i>' : '' ?>
                                </a>
                                <a href="<?php echo !empty($user['telegram_link']) ? $user['telegram_link'] : '$javascript:void(0);' ?>"
                                   class="mr-2 fs-xxl" style="color:#38A1F3">
                                    <?php echo !empty($user['telegram_link']) ? '<i class="fab fa-telegram"></i>' : '' ?>
                                </a>
                                <a href="<?php echo !empty($user['instagram_link']) ? $user['instagram_link'] : '$javascript:void(0);' ?>"
                                   class="mr-2 fs-xxl" style="color:#E1306C">
                                    <?php echo !empty($user['instagram_link']) ? '<i class="fab fa-instagram"></i>' : '' ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>
<?php require 'footer.php'; ?>
</body>

<script src="js/vendors.bundle.js"></script>
<script src="js/app.bundle.js"></script>
<script>

    $(document).ready(function () {

        $('input[type=radio][name=contactview]').change(function () {
            if (this.value == 'grid') {
                $('#js-contacts .card').removeClassPrefix('mb-').addClass('mb-g');
                $('#js-contacts .col-xl-12').removeClassPrefix('col-xl-').addClass('col-xl-4');
                $('#js-contacts .js-expand-btn').addClass('d-none');
                $('#js-contacts .card-body + .card-body').addClass('show');

            } else if (this.value == 'table') {
                $('#js-contacts .card').removeClassPrefix('mb-').addClass('mb-1');
                $('#js-contacts .col-xl-4').removeClassPrefix('col-xl-').addClass('col-xl-12');
                $('#js-contacts .js-expand-btn').removeClass('d-none');
                $('#js-contacts .card-body + .card-body').removeClass('show');
            }

        });

        //initialize filter
        initApp.listFilter($('#js-contacts'), $('#js-filter-contacts'));
    });

</script>
</html>
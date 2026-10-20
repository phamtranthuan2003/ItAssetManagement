<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Quản lý thiết bị kho') ?> - <?= APP_NAME ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Custom Style -->
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>

<!-- SIDEBAR -->
<nav id="sidebar">
    <a href="<?= url('dashboard') ?>" class="brand">
        <i class="bi bi-cpu-fill text-primary fs-4"></i>
        <span>Kho Thiết Bị</span>
    </a>

    <div class="nav-header">Tổng quan</div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link <?= (str_contains($_SERVER['REQUEST_URI'], '/dashboard') || $_SERVER['REQUEST_URI'] === url('')) ? 'active' : '' ?>" href="<?= url('dashboard') ?>">
                <i class="bi bi-speedometer2"></i> Bảng điều khiển
            </a>
        </li>
    </ul>

    <div class="nav-header">Quản lý kho</div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/devices') ? 'active' : '' ?>" href="<?= url('devices') ?>">
                <i class="bi bi-laptop"></i> Thiết bị (PC/Laptop)
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/tickets') ? 'active' : '' ?>" href="<?= url('tickets') ?>">
                <i class="bi bi-arrow-left-right"></i> Cấp phát & Thu hồi
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/maintenance') ? 'active' : '' ?>" href="<?= url('maintenance') ?>">
                <i class="bi bi-tools"></i> Bảo trì & Nâng cấp
            </a>
        </li>
    </ul>

    <div class="nav-header">Tổ chức & Nhân sự</div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/departments') ? 'active' : '' ?>" href="<?= url('departments') ?>">
                <i class="bi bi-buildings"></i> Phòng ban
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/employees') ? 'active' : '' ?>" href="<?= url('employees') ?>">
                <i class="bi bi-people"></i> Nhân viên
            </a>
        </li>
    </ul>

    <div class="nav-header">Báo cáo & Hệ thống</div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/reports') ? 'active' : '' ?>" href="<?= url('reports') ?>">
                <i class="bi bi-bar-chart-line"></i> Báo cáo thống kê
            </a>
        </li>
        <?php if (Auth::isAdmin()): ?>
        <li class="nav-item">
            <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/users') ? 'active' : '' ?>" href="<?= url('users') ?>">
                <i class="bi bi-shield-lock"></i> Quản lý tài khoản
            </a>
        </li>
        <?php endif; ?>
    </ul>
</nav>

<!-- MAIN WRAPPER -->
<div id="main-wrapper">
    <!-- TOPBAR -->
    <header id="topbar">
        <div class="d-flex align-items-center">
            <h5 class="mb-0 text-dark fw-bold"><?= e($pageTitle ?? 'Hệ Thống') ?></h5>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle fs-5 text-primary"></i>
                    <span><strong><?= e(Auth::user()['fullname'] ?? 'User') ?></strong></span>
                    <span class="badge <?= Auth::isAdmin() ? 'bg-danger' : 'bg-primary' ?>"><?= Auth::isAdmin() ? 'Admin' : 'Thủ kho' ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li><a class="dropdown-item" href="<?= url('profile') ?>"><i class="bi bi-person me-2"></i> Hồ sơ & Đổi mật khẩu</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="<?= url('logout') ?>"><i class="bi bi-box-arrow-right me-2"></i> Đăng xuất</a></li>
                </ul>
            </div>
        </div>
    </header>

    <!-- CONTENT BODY -->
    <main class="content-body">
        <?php require __DIR__ . '/alert.php'; ?>
        <?= $content ?>
    </main>

    <!-- FOOTER -->
    <footer class="bg-white border-top py-3 text-center text-muted small mt-auto">
        &copy; <?= date('Y') ?> <?= APP_NAME ?> - Phiên bản <?= APP_VERSION ?> | PHP Pure MVC + MySQL
    </footer>
</div>

<!-- Bootstrap 5 Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Main Custom JS -->
<script src="<?= asset('js/main.js') ?>"></script>
</body>
</html>

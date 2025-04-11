<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>HUMG Dormitory</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/style.css">
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand" href="<?php echo SITE_URL; ?>">HUMG</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>"><i class="fas fa-home"></i> Trang chủ</a>
                        </li>
                        
                        <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-list"></i> Biểu mẫu
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>dorm-register.php">Đăng ký nội trú KTX</a></li>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>payment-room.php">Thanh toán tiền phòng</a></li>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>payment-utilities.php">Thanh toán tiền điện</a></li>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>payment-utilities.php">Thanh toán tiền nước</a></li>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>payment-others.php">Thanh toán chi phí khác</a></li>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>laundry-register.php">Đăng ký Giặt Sấy</a></li>
                            </ul>
                        </li>
                        <?php endif; ?>
                    </ul>
                    
                    <ul class="navbar-nav ms-auto">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <?php if ($_SESSION['is_admin'] == 1): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo SITE_URL; ?>admin/"><i class="fas fa-cogs"></i> Quản trị</a>
                            </li>
                            <?php endif; ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['fullname']); ?>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                    <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>profile.php">Thông tin cá nhân</a></li>
                                    <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>change-password.php">Đổi mật khẩu</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>logout.php">Đăng xuất</a></li>
                                </ul>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo SITE_URL; ?>login.php"><i class="fas fa-sign-in-alt"></i> Đăng nhập</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo SITE_URL; ?>register.php"><i class="fas fa-user-plus"></i> Đăng ký</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    
    <main class="py-4">
        <div class="container">
            <?php displayNotification(); ?>
        </div>
    </main>
</body>
</html> 
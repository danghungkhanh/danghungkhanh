<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
session_start();

// Kiểm tra xem người dùng đã đăng nhập
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;

$pageTitle = "Trang chủ";
include 'includes/header.php';
?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="jumbotron text-center">
                <h2>KÝ TÚC XÁ DORMITORY</h2>
                <h3>HUMG UNIVERSITY</h3>
            </div>
        </div>
    </div>

    <?php if (!$isLoggedIn): ?>
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4>Đăng nhập vào hệ thống</h4>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <p>Vui lòng đăng nhập để sử dụng các dịch vụ</p>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="login.php" class="btn btn-primary btn-lg">Đăng nhập</a>
                        <a href="register.php" class="btn btn-secondary btn-lg">Đăng ký</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-info">
                <h4>Xin chào, <?php echo htmlspecialchars($_SESSION['fullname']); ?></h4>
                <p>Mã số sinh viên: <?php echo htmlspecialchars($_SESSION['student_id']); ?></p>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5>Đăng ký nội trú</h5>
                </div>
                <div class="card-body">
                    <p>Đơn đăng ký nội trú dành cho sinh viên</p>
                    <a href="dorm-register.php" class="btn btn-primary">Đăng ký ngay</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5>Thanh toán tiền phòng</h5>
                </div>
                <div class="card-body">
                    <p>Thanh toán theo từng sinh viên nội trú</p>
                    <a href="payment-room.php" class="btn btn-success">Thanh toán</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5>Thanh toán tiền điện</h5>
                </div>
                <div class="card-body">
                    <p>Thanh toán theo từng phòng</p>
                    <a href="payment-utilities.php" class="btn btn-info">Thanh toán</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5>Thanh toán tiền nước</h5>
                </div>
                <div class="card-body">
                    <p>Thanh toán theo từng phòng</p>
                    <a href="payment-utilities.php" class="btn btn-warning">Thanh toán</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5>Thanh toán chi phí khác</h5>
                </div>
                <div class="card-body">
                    <p>Các chi phí phát sinh khác</p>
                    <a href="payment-others.php" class="btn btn-danger">Thanh toán</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5>Đăng ký Giặt Sấy</h5>
                </div>
                <div class="card-body">
                    <p>Dịch vụ giặt sấy trong ký túc xá</p>
                    <a href="laundry-register.php" class="btn btn-secondary">Đăng ký</a>
                </div>
            </div>
        </div>
    </div>
    
    <?php if ($isAdmin): ?>
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5>Quản lý hệ thống</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <a href="admin/users.php" class="btn btn-outline-primary btn-lg btn-block">Quản lý người dùng</a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="admin/rooms.php" class="btn btn-outline-success btn-lg btn-block">Quản lý phòng</a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="admin/payments.php" class="btn btn-outline-info btn-lg btn-block">Quản lý thanh toán</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?> 
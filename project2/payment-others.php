<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
session_start();

// Kiểm tra người dùng đã đăng nhập
if (!isset($_SESSION['user_id'])) {
    setNotification('warning', 'Vui lòng đăng nhập để tiếp tục!');
    redirect(SITE_URL . 'login.php');
}

$pageTitle = "Thanh toán các mặt hàng khác";
$errors = [];
$success = false;

// Kiểm tra nếu form được gửi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_type = sanitize($_POST['item_type']);
    $amount = sanitize($_POST['amount']);
    $payment_method = sanitize($_POST['payment_method']);
    
    // Kiểm tra dữ liệu
    if (empty($item_type) || empty($amount) || empty($payment_method)) {
        $errors[] = "Vui lòng điền đầy đủ thông tin.";
    }

    // Nếu không có lỗi, thực hiện thanh toán
    if (empty($errors)) {
        $query = "INSERT INTO payments (user_id, student_id, payment_type, amount, payment_date, payment_method, status, created_at) 
                  VALUES (?, ?, 'other', ?, NOW(), ?, 'completed', NOW())";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "isi", $_SESSION['user_id'], $_SESSION['student_id'], $amount, $payment_method);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = true;
            setNotification('success', 'Thanh toán cho mặt hàng khác thành công!');
        } else {
            $errors[] = "Đã xảy ra lỗi khi thanh toán. Vui lòng thử lại!";
        }
    }
}

include 'includes/header.php';
?>

<div class="container">
    <h2><?php echo $pageTitle; ?></h2>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success">Thanh toán thành công!</div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label for="item_type" class="form-label">Loại mặt hàng</label>
            <select class="form-select" id="item_type" name="item_type" required>
                <option value="bàn chải">Bàn chải</option>
                <option value="sách">Sách</option>
                <option value="vở">Vở</option>
                <option value="quần áo">Quần áo</option>
                <option value="khác">Khác</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="amount" class="form-label">Số tiền</label>
            <input type="number" class="form-control" id="amount" name="amount" required>
        </div>
        <div class="mb-3">
            <label for="payment_method" class="form-label">Phương thức thanh toán</label>
            <select class="form-select" id="payment_method" name="payment_method" required>
                <option value="cash">Tiền mặt</option>
                <option value="bank_transfer">Chuyển khoản ngân hàng</option>
                <option value="credit_card">Thẻ tín dụng</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Thanh toán</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
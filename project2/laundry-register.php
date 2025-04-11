<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
session_start();

// Kiểm tra người dùng đã đăng nhập
if (!isset($_SESSION['user_id'])) {
    setNotification('warning', 'Vui lòng đăng nhập để tiếp tục!');
    redirect(SITE_URL . 'login.php');
}

$pageTitle = "Đăng ký giặt sấy";
$errors = [];
$success = false;

// Kiểm tra nếu form được gửi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $weight = sanitize($_POST['weight']);
    $pickup_date = sanitize($_POST['pickup_date']);
    $completion_date = sanitize($_POST['completion_date']);
    $service_type = sanitize($_POST['service_type']);
    $note = sanitize($_POST['note']);
    $payment_method = sanitize($_POST['payment_method']);
    $location = sanitize($_POST['location']);

    // Kiểm tra dữ liệu
    if (empty($weight) || empty($pickup_date) || empty($completion_date) || empty($service_type) || empty($payment_method) || empty($location)) {
        $errors[] = "Vui lòng điền đầy đủ thông tin.";
    }

    // Nếu không có lỗi, thực hiện đăng ký
    if (empty($errors)) {
        $query = "INSERT INTO laundry_services (user_id, student_id, service_type, weight, schedule_date, completion_date, note, payment_method, location, status, created_at) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ississsss", $_SESSION['user_id'], $_SESSION['student_id'], $service_type, $weight, $pickup_date, $completion_date, $note, $payment_method, $location);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = true;
            setNotification('success', 'Đăng ký giặt sấy thành công!');
        } else {
            $errors[] = "Đã xảy ra lỗi khi đăng ký. Vui lòng thử lại!";
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
        <div class="alert alert-success">Đăng ký thành công!</div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label for="service_type" class="form-label">Loại dịch vụ</label>
            <select class="form-control" id="service_type" name="service_type" required>
                <option value="Giặt thường">Giặt thường</option>
                <option value="Giặt hấp">Giặt hấp</option>
                <option value="Giặt khô">Giặt khô</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="weight" class="form-label">Số lượng (kg)</label>
            <input type="number" class="form-control" id="weight" name="weight" required>
        </div>

        <div class="mb-3">
            <label for="pickup_date" class="form-label">Ngày mang đi</label>
            <input type="date" class="form-control" id="pickup_date" name="pickup_date" required>
        </div>

        <div class="mb-3">
            <label for="completion_date" class="form-label">Ngày giặt xong</label>
            <input type="date" class="form-control" id="completion_date" name="completion_date" required>
        </div>

        <div class="mb-3">
            <label for="note" class="form-label">Ghi chú đặc biệt</label>
            <textarea class="form-control" id="note" name="note" rows="3"></textarea>
        </div>

        <div class="mb-3">
            <label for="payment_method" class="form-label">Phương thức thanh toán</label>
            <select class="form-control" id="payment_method" name="payment_method" required>
                <option value="Tiền mặt">Tiền mặt</option>
                <option value="Chuyển khoản">Chuyển khoản</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="location" class="form-label">Địa điểm nhận/trả đồ</label>
            <input type="text" class="form-control" id="location" name="location" required>
        </div>

        <button type="submit" class="btn btn-primary">Đăng ký</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
session_start();

// Kiểm tra người dùng đã đăng nhập
if (!isset($_SESSION['user_id'])) {
    setNotification('warning', 'Vui lòng đăng nhập để tiếp tục!');
    redirect(SITE_URL . 'login.php');
}

$pageTitle = "Thanh toán tiền phòng";
$errors = [];
$success = false;

// Lấy thông tin phòng của sinh viên
$query = "SELECT dr.room_id, rt.price FROM dorm_registrations dr
          JOIN room_types rt ON dr.room_type_id = rt.id
          WHERE dr.student_id = ? AND dr.status = 'approved'";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $_SESSION['student_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$room_info = mysqli_fetch_assoc($result);

if (!$room_info) {
    setNotification('danger', 'Bạn chưa có phòng đã được phê duyệt!');
    redirect(SITE_URL . 'dorm-register.php');
}

$room_id = $room_info['room_id'];
$amount = $room_info['price'];

// Kiểm tra nếu form được gửi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = sanitize($_POST['payment_method']);
    
    // Kiểm tra dữ liệu
    if (empty($payment_method)) {
        $errors[] = "Vui lòng chọn phương thức thanh toán.";
    }

    // Nếu không có lỗi, thực hiện thanh toán
    if (empty($errors)) {
        $query = "INSERT INTO payments (user_id, student_id, room_id, payment_type, amount, payment_date, payment_method, status, created_at) 
                  VALUES (?, ?, ?, 'room', ?, NOW(), ?, 'completed', NOW())";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "iisds", $_SESSION['user_id'], $_SESSION['student_id'], $room_id, $amount, $payment_method);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = true;
            setNotification('success', 'Thanh toán tiền phòng thành công!');
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
            <label for="student_id" class="form-label">Mã số sinh viên</label>
            <input type="text" class="form-control" id="student_id" name="student_id" value="<?php echo $_SESSION['student_id']; ?>" readonly>
        </div>
        <div class="mb-3">
            <label for="amount" class="form-label">Số tiền</label>
            <input type="number" class="form-control" id="amount" name="amount" value="<?php echo $amount; ?>" readonly>
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
<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
session_start();

// Kiểm tra người dùng đã đăng nhập và là admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    setNotification('warning', 'Bạn không có quyền truy cập trang này!');
    redirect(SITE_URL);
}

$pageTitle = "Sửa người dùng";
$errors = [];
$success = false;

// Lấy thông tin người dùng
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$query = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    setNotification('danger', 'Người dùng không tồn tại!');
    redirect(SITE_URL . 'admin/users.php');
}

// Kiểm tra nếu form được gửi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = sanitize($_POST['student_id']);
    $fullname = sanitize($_POST['fullname']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $gender = sanitize($_POST['gender']);
    $birthday = sanitize($_POST['birthday']);

    // Kiểm tra dữ liệu
    if (empty($student_id) || empty($fullname) || empty($email) || empty($phone) || empty($gender) || empty($birthday)) {
        $errors[] = "Vui lòng điền đầy đủ thông tin.";
    }

    // Nếu không có lỗi, cập nhật người dùng
    if (empty($errors)) {
        $query = "UPDATE users SET student_id = ?, fullname = ?, email = ?, phone = ?, gender = ?, birthday = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssssssi", $student_id, $fullname, $email, $phone, $gender, $birthday, $user_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = true;
            setNotification('success', 'Cập nhật người dùng thành công!');
        } else {
            $errors[] = "Đã xảy ra lỗi khi cập nhật người dùng. Vui lòng thử lại!";
        }
    }
}

include '../includes/header.php';
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
        <div class="alert alert-success">Cập nhật người dùng thành công!</div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label for="student_id" class="form-label">Mã số sinh viên</label>
            <input type="text" class="form-control" id="student_id" name="student_id" value="<?php echo htmlspecialchars($user['student_id']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="fullname" class="form-label">Họ tên</label>
            <input type="text" class="form-control" id="fullname" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Số điện thoại</label>
            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="gender" class="form-label">Giới tính</label>
            <select class="form-select" id="gender" name="gender" required>
                <option value="Nam" <?php echo $user['gender'] == 'Nam' ? 'selected' : ''; ?>>Nam</option>
                <option value="Nữ" <?php echo $user['gender'] == 'Nữ' ? 'selected' : ''; ?>>Nữ</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="birthday" class="form-label">Ngày sinh</label>
            <input type="date" class="form-control" id="birthday" name="birthday" value="<?php echo htmlspecialchars($user['birthday']); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Cập nhật người dùng</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
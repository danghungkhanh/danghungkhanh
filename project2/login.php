<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
session_start();

// Kiểm tra nếu đã đăng nhập
if (isset($_SESSION['user_id'])) {
    redirect(SITE_URL);
}

$pageTitle = "Đăng nhập";
$errors = [];
$student_id = '';

// Kiểm tra nếu form đã được gửi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy và làm sạch dữ liệu từ form
    $student_id = sanitize($_POST['student_id']);
    $password = $_POST['password'];
    
    // Xác thực dữ liệu từ form
    if (empty($student_id)) {
        $errors[] = "Vui lòng nhập mã số sinh viên";
    }
    
    if (empty($password)) {
        $errors[] = "Vui lòng nhập mật khẩu";
    }
    
    // Nếu không có lỗi, cố gắng đăng nhập
    if (empty($errors)) {
        // Kiểm tra xem người dùng có tồn tại trong cơ sở dữ liệu không
        $query = "SELECT * FROM users WHERE student_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $student_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            
            // Xác thực mật khẩu
            if (password_verify($password, $user['password'])) {
                // Thiết lập biến session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['student_id'] = $user['student_id'];
                $_SESSION['fullname'] = $user['fullname'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['is_admin'] = $user['is_admin']; // Đảm bảo rằng biến này được thiết lập

                // Cập nhật thời gian đăng nhập cuối cùng
                $update_query = "UPDATE users SET last_login = NOW() WHERE id = ?";
                $update_stmt = mysqli_prepare($conn, $update_query);
                mysqli_stmt_bind_param($update_stmt, "i", $user['id']);
                mysqli_stmt_execute($update_stmt);
                
                // Thiết lập thông báo và chuyển hướng
                setNotification('success', 'Đăng nhập thành công!');
                redirect(SITE_URL);
            } else {
                $errors[] = "Mật khẩu không chính xác";
            }
        } else {
            $errors[] = "Mã số sinh viên không tồn tại trong hệ thống";
        }
    }
}

include 'includes/header.php';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4><i class="fas fa-sign-in-alt"></i> Đăng nhập</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="student_id" class="form-label">Mã số sinh viên</label>
                            <input type="text" class="form-control" id="student_id" name="student_id" value="<?php echo htmlspecialchars($student_id); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Đăng nhập</button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-3">
                        <p>Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a></p>
                        <p><a href="forgot-password.php">Quên mật khẩu?</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 
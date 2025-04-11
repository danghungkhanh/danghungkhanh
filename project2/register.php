<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
session_start();

// Check if already logged in
if (isset($_SESSION['user_id'])) {
    redirect(SITE_URL);
}

$pageTitle = "Đăng ký";
$errors = [];
$success = false;

// Initialize form fields
$student_id = '';
$fullname = '';
$email = '';
$phone = '';
$gender = '';
$birthday = '';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize form data
    $student_id = sanitize($_POST['student_id']);
    $fullname = sanitize($_POST['fullname']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $gender = isset($_POST['gender']) ? sanitize($_POST['gender']) : '';
    $birthday = sanitize($_POST['birthday']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate form data
    if (empty($student_id)) {
        $errors[] = "Vui lòng nhập mã số sinh viên";
    } elseif (!validateStudentID($student_id)) {
        $errors[] = "Mã số sinh viên không hợp lệ (phải gồm 8 chữ số)";
    }
    
    if (empty($fullname)) {
        $errors[] = "Vui lòng nhập họ tên";
    }
    
    if (empty($email)) {
        $errors[] = "Vui lòng nhập email";
    } elseif (!validateEmail($email)) {
        $errors[] = "Email không hợp lệ";
    }
    
    if (empty($phone)) {
        $errors[] = "Vui lòng nhập số điện thoại";
    } elseif (!validatePhone($phone)) {
        $errors[] = "Số điện thoại không hợp lệ (phải gồm 10 chữ số)";
    }
    
    if (empty($gender)) {
        $errors[] = "Vui lòng chọn giới tính";
    }
    
    if (empty($birthday)) {
        $errors[] = "Vui lòng nhập ngày sinh";
    }
    
    if (empty($password)) {
        $errors[] = "Vui lòng nhập mật khẩu";
    } elseif (strlen($password) < 6) {
        $errors[] = "Mật khẩu phải có ít nhất 6 ký tự";
    }
    
    if ($password !== $confirm_password) {
        $errors[] = "Xác nhận mật khẩu không khớp";
    }
    
    // Check if student ID already exists
    if (empty($errors)) {
        $query = "SELECT * FROM users WHERE student_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $student_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            $errors[] = "Mã số sinh viên đã tồn tại trong hệ thống";
        }
    }
    
    // Check if email already exists
    if (empty($errors)) {
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            $errors[] = "Email đã tồn tại trong hệ thống";
        }
    }
    
    // If no errors, register user
    if (empty($errors)) {
        // Hash password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert user into database
        $query = "INSERT INTO users (student_id, fullname, email, phone, gender, birthday, password, is_admin, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, 0, NOW())";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sssssss", $student_id, $fullname, $email, $phone, $gender, $birthday, $password_hash);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = true;
            
            // Clear form fields
            $student_id = '';
            $fullname = '';
            $email = '';
            $phone = '';
            $gender = '';
            $birthday = '';
        } else {
            $errors[] = "Đã xảy ra lỗi khi đăng ký. Vui lòng thử lại!";
        }
    }
}

include 'includes/header.php';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4><i class="fas fa-user-plus"></i> Đăng ký tài khoản</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                    <div class="alert alert-success">
                        <h5>Đăng ký thành công!</h5>
                        <p>Bạn đã đăng ký tài khoản thành công. Vui lòng <a href="login.php">đăng nhập</a> để tiếp tục.</p>
                    </div>
                    <?php else: ?>
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="student_id" class="form-label">Mã số sinh viên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="student_id" name="student_id" value="<?php echo htmlspecialchars($student_id); ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="fullname" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="fullname" name="fullname" value="<?php echo htmlspecialchars($fullname); ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Giới tính <span class="text-danger">*</span></label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="gender" id="male" value="Nam" <?php echo $gender === 'Nam' ? 'checked' : ''; ?> required>
                                            <label class="form-check-label" for="male">Nam</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="gender" id="female" value="Nữ" <?php echo $gender === 'Nữ' ? 'checked' : ''; ?> required>
                                            <label class="form-check-label" for="female">Nữ</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="birthday" class="form-label">Ngày sinh <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="birthday" name="birthday" value="<?php echo htmlspecialchars($birthday); ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="password" class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="agreement" name="agreement" required>
                            <label class="form-check-label" for="agreement">Tôi đồng ý với <a href="#">điều khoản dịch vụ</a> và <a href="#">chính sách bảo mật</a> của HUMG Dormitory</label>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Đăng ký</button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-3">
                        <p>Đã có tài khoản? <a href="login.php">Đăng nhập ngay</a></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 
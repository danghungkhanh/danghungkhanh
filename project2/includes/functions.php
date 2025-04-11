<?php
// Các hàm bảo mật và kiểm tra dữ liệu đầu vào

/**
 * Làm sạch dữ liệu nhập vào của người dùng
 * @param string $data
 * @return string
 */
function sanitize($data) {
    $data = trim($data); // Loại bỏ khoảng trắng đầu và cuối
    $data = stripslashes($data); // Xóa ký tự gạch chéo ngược
    $data = htmlspecialchars($data); // Chuyển ký tự đặc biệt thành dạng HTML
    return $data;
}

/**
 * Kiểm tra tính hợp lệ của mã số sinh viên
 * @param string $student_id
 * @return bool
 */
function validateStudentID($student_id) {
    return preg_match('/^[0-9]{10}$/', $student_id); // Mã số sinh viên phải có 10 chữ số
}

/**
 * Kiểm tra tính hợp lệ của số điện thoại
 * @param string $phone
 * @return bool
 */
function validatePhone($phone) {
    return preg_match('/^[0-9]{10}$/', $phone); // Số điện thoại phải có 10 chữ số
}

/**
 * Kiểm tra tính hợp lệ của email
 * @param string $email
 * @return bool
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL); // Sử dụng bộ lọc PHP để kiểm tra email hợp lệ
}

/**
 * Tạo chuỗi token ngẫu nhiên
 * @param int $length Độ dài token
 * @return string
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length)); // Tạo token ngẫu nhiên có độ dài nhất định
}

/**
 * Kiểm tra xem người dùng đã đăng nhập chưa
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']); // Kiểm tra nếu biến session `user_id` tồn tại
}

/**
 * Kiểm tra xem người dùng có phải admin không
 * @return bool
 */
function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1; // Kiểm tra biến session `is_admin`
}

/**
 * Chuyển hướng người dùng đến một trang khác
 * @param string $url
 */
function redirect($url) {
    header("Location: {$url}"); // Gửi tiêu đề HTTP để chuyển hướng
    exit;
}

/**
 * Lấy trạng thái của phòng
 * @param int $status
 * @return string
 */
function getRoomStatus($status) {
    switch ($status) {
        case 0:
            return 'Trống'; // Phòng trống
        case 1:
            return 'Đang ở'; // Có người đang ở
        case 2:
            return 'Đầy'; // Phòng đã đầy
        case 3:
            return 'Bảo trì'; // Đang bảo trì
        default:
            return 'Không xác định'; // Trạng thái không xác định
    }
}

/**
 * Định dạng số tiền
 * @param float $amount
 * @return string
 */
function formatCurrency($amount) {
    return number_format($amount, 0, ',', '.') . ' VNĐ'; // Định dạng tiền tệ Việt Nam (dấu chấm phân tách hàng nghìn)
}

/**
 * Định dạng ngày tháng
 * @param string $date
 * @return string
 */
function formatDate($date) {
    return date('d/m/Y', strtotime($date)); // Chuyển ngày thành định dạng dd/mm/yyyy
}

/**
 * Tạo một thông báo
 * @param string $type success, info, warning, danger (loại thông báo)
 * @param string $message Nội dung thông báo
 */
function setNotification($type, $message) {
    $_SESSION['notification'] = [
        'type' => $type, // Loại thông báo (thành công, cảnh báo, lỗi...)
        'message' => $message // Nội dung thông báo
    ];
}

/**
 * Hiển thị thông báo
 */
function displayNotification() {
    if (isset($_SESSION['notification'])) {
        $type = $_SESSION['notification']['type']; // Lấy loại thông báo
        $message = $_SESSION['notification']['message']; // Lấy nội dung thông báo
        echo "<div class='alert alert-{$type} alert-dismissible fade show' role='alert'>
                {$message}
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
              </div>";
        unset($_SESSION['notification']); // Xóa thông báo sau khi hiển thị
    }
}
?>

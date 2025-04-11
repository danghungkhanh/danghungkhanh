<?php
require '../includes/config.php'; // Đường dẫn đến file config
require '../includes/functions.php'; // Đường dẫn đến file functions

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php'); // Đường dẫn đến trang đăng nhập
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $registration_id = $_POST['registration_id'];

    // Cập nhật trạng thái thanh toán
    $query = "UPDATE dorm_registrations SET paid = 1 WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $registration_id);
    
    if (mysqli_stmt_execute($stmt)) {
        setNotification('success', 'Thanh toán đã được chấp thuận!');
    } else {
        setNotification('danger', 'Đã xảy ra lỗi khi chấp thuận thanh toán: ' . mysqli_error($conn));
    }
    header('Location: manage-rooms.php'); // Quay lại trang quản lý phòng
    exit();
}
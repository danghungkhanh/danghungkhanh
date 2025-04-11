<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
session_start();

// Kiểm tra người dùng đã đăng nhập và là admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    setNotification('warning', 'Bạn không có quyền truy cập trang này!');
    redirect(SITE_URL);
}

$pageTitle = "Quản lý phòng";
$errors = [];

// Xử lý chấp thuận đăng ký phòng
if (isset($_GET['action']) && $_GET['action'] === 'approve' && isset($_GET['id'])) {
    $registration_id = (int)$_GET['id'];

    // Cập nhật trạng thái thanh toán
    $query = "UPDATE dorm_registrations SET status = 'approved' WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $registration_id);
    
    if (mysqli_stmt_execute($stmt)) {
        setNotification('success', 'Đăng ký phòng đã được chấp thuận!');
    } else {
        setNotification('danger', 'Đã xảy ra lỗi khi chấp thuận đăng ký phòng!');
    }
    redirect(SITE_URL . 'admin/rooms.php');
}

// Lấy danh sách đăng ký phòng
$query = "SELECT dr.id AS registration_id, dr.student_id, u.fullname, u.email, rt.name AS room_type, dr.status
          FROM dorm_registrations dr
          JOIN users u ON dr.student_id = u.student_id
          JOIN room_types rt ON dr.room_type_id = rt.id
          WHERE dr.status = 'pending'
          ORDER BY dr.check_in_date ASC";
$result = mysqli_query($conn, $query);
$registrations = mysqli_fetch_all($result, MYSQLI_ASSOC);

include '../includes/header.php';
?>

<div class="container">
    <h2><?php echo $pageTitle; ?></h2>
    
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4>Danh sách đăng ký phòng</h4>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID Đăng ký</th>
                        <th>Tên sinh viên</th>
                        <th>Email</th>
                        <th>Loại phòng</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($registrations as $registration): ?>
                    <tr>
                        <td><?php echo $registration['registration_id']; ?></td>
                        <td><?php echo htmlspecialchars($registration['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($registration['email']); ?></td>
                        <td><?php echo htmlspecialchars($registration['room_type']); ?></td>
                        <td><?php echo htmlspecialchars($registration['status']); ?></td>
                        <td>
                            <?php if ($registration['status'] === 'pending'): ?>
                                <a href="rooms.php?action=approve&id=<?php echo $registration['registration_id']; ?>" class="btn btn-sm btn-success" onclick="return confirm('Bạn có chắc chắn muốn chấp thuận đăng ký phòng này?');">Chấp thuận</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="text-end mt-4">
        <a href="view-room.php" class="btn btn-primary">Xem phòng </a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
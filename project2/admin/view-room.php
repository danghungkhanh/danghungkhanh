<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
session_start();

// Kiểm tra người dùng đã đăng nhập
if (!isset($_SESSION['user_id'])) {
    setNotification('warning', 'Bạn cần đăng nhập để xem thông tin!');
    redirect(SITE_URL);
}

$pageTitle = "Thông tin phòng đã đặt";
$student_id = $_SESSION['student_id']; // Lấy student_id từ session
$registrations = [];

// Lấy danh sách đăng ký phòng của sinh viên
$query = "SELECT dr.*, r.room_number, rt.name AS room_type
          FROM dorm_registrations dr
          JOIN rooms r ON dr.room_id = r.id
          JOIN room_types rt ON dr.room_type_id = rt.id
          WHERE dr.student_id = ? AND dr.status = 'approved'"; // Chỉ lấy các đăng ký đã được phê duyệt
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $student_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $registrations[] = $row;
    }
}

include '../includes/header.php';
?>

<div class="container">
    <h2><?php echo $pageTitle; ?></h2>
    
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4>Danh sách phòng đã đặt</h4>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID Đăng ký</th>
                        <th>Số phòng</th>
                        <th>Loại phòng</th>
                        <th>Ngày nhận phòng</th>
                        <th>Ngày trả phòng</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($registrations)): ?>
                        <tr>
                            <td colspan="6" class="text-center">Bạn chưa đặt phòng nào.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($registrations as $registration): ?>
                        <tr>
                            <td><?php echo $registration['id']; ?></td>
                            <td><?php echo htmlspecialchars($registration['room_number']); ?></td>
                            <td><?php echo htmlspecialchars($registration['room_type']); ?></td>
                            <td><?php echo htmlspecialchars($registration['check_in_date']); ?></td>
                            <td><?php echo htmlspecialchars($registration['check_out_date']); ?></td>
                            <td><?php echo htmlspecialchars($registration['status']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
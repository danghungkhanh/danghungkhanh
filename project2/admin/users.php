<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
session_start();

// Kiểm tra người dùng đã đăng nhập và là admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    setNotification('warning', 'Bạn không có quyền truy cập trang này!');
    redirect(SITE_URL);
}

$pageTitle = "Quản lý người dùng";
$errors = [];

// Xử lý các hành động sửa, xóa người dùng
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    // Xóa người dùng
    if ($action === 'delete' && $user_id > 0) {
        $query = "DELETE FROM users WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        
        if (mysqli_stmt_execute($stmt)) {
            setNotification('success', 'Xóa người dùng thành công!');
        } else {
            setNotification('danger', 'Đã xảy ra lỗi khi xóa người dùng!');
        }
        redirect(SITE_URL . 'admin/users.php');
    }
}

// Lấy danh sách người dùng
$query = "SELECT * FROM users ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);

include '../includes/header.php';
?>

<div class="container">
    <h2><?php echo $pageTitle; ?></h2>
    
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4>Danh sách người dùng</h4>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Mã số sinh viên</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Ngày sinh</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['student_id']); ?></td>
                        <td><?php echo htmlspecialchars($user['fullname']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($user['birthday'])); ?></td>
                        <td>
                            <a href="edit-user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-warning">Sửa</a>
                            <a href="users.php?action=delete&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa người dùng này?');">Xóa</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    setNotification('warning', 'Vui lòng đăng nhập để tiếp tục!');
    redirect(SITE_URL . 'login.php');
}

$pageTitle = "Đăng ký nội trú";
$errors = [];
$success = false;

// Get user information
$user_id = $_SESSION['user_id'];
$student_id = $_SESSION['student_id'];
$fullname = $_SESSION['fullname'];

// Check if user already has an active registration
$query = "SELECT * FROM dorm_registrations WHERE student_id = ? AND status IN ('pending', 'approved')";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $student_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$has_active_registration = mysqli_num_rows($result) > 0;
$registration = $has_active_registration ? mysqli_fetch_assoc($result) : null;

// Get available dorms
$query = "SELECT * FROM dorms WHERE status = 'active'";
$dorms_result = mysqli_query($conn, $query);
$dorms = mysqli_fetch_all($dorms_result, MYSQLI_ASSOC);

// Get available room types
$query = "SELECT * FROM room_types WHERE status = 'active'";
$room_types_result = mysqli_query($conn, $query);
$room_types = mysqli_fetch_all($room_types_result, MYSQLI_ASSOC);

// Initialize form fields
$dorm_id = '';
$room_type_id = '';
$preferred_roommates = '';
$check_in_date = '';
$duration = '';
$note = '';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$has_active_registration) {
    // Get and sanitize form data
    $dorm_id = sanitize($_POST['dorm_id']);
    $room_type_id = sanitize($_POST['room_type_id']);
    $preferred_roommates = sanitize($_POST['preferred_roommates']);
    $check_in_date = sanitize($_POST['check_in_date']);
    $duration = sanitize($_POST['duration']);
    $note = sanitize($_POST['note']);
    
    // Validate form data
    if (empty($dorm_id)) {
        $errors[] = "Vui lòng chọn ký túc xá";
    }
    
    if (empty($room_type_id)) {
        $errors[] = "Vui lòng chọn loại phòng";
    }
    
    if (empty($check_in_date)) {
        $errors[] = "Vui lòng chọn ngày nhận phòng";
    } elseif (strtotime($check_in_date) < strtotime(date('Y-m-d'))) {
        $errors[] = "Ngày nhận phòng không được nhỏ hơn ngày hiện tại";
    }
    
    if (empty($duration)) {
        $errors[] = "Vui lòng chọn thời gian ở";
    }
    
    // If no errors, submit registration
    if (empty($errors)) {
        // Calculate check out date
        $check_out_date = date('Y-m-d', strtotime($check_in_date . " + $duration months"));
        
        // Insert registration into database
        $query = "INSERT INTO dorm_registrations (user_id, student_id, fullname, dorm_id, room_type_id, preferred_roommates, check_in_date, check_out_date, duration, note, status, created_at) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "issiisssis", $user_id, $student_id, $fullname, $dorm_id, $room_type_id, $preferred_roommates, $check_in_date, $check_out_date, $duration, $note);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = true;
            
            // Set notification and redirect
            setNotification('success', 'Đăng ký nội trú thành công! Vui lòng đợi quản trị viên phê duyệt.');
            redirect(SITE_URL . 'dorm-register.php');
        } else {
            $errors[] = "Đã xảy ra lỗi khi đăng ký. Vui lòng thử lại!";
        }
    }
}

include 'includes/header.php';
?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-home"></i> Đăng ký nội trú</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Trang chủ</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Đăng ký nội trú</li>
                    </ol>
                </nav>
            </div>
            
            <div class="alert alert-info">
                <p><strong>Chú ý:</strong> Đơn đăng ký nội trú dành cho sinh viên. Vui lòng điền đầy đủ thông tin để được xếp phòng phù hợp.</p>
            </div>
            
            <?php if ($has_active_registration): ?>
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4>Thông tin đăng ký hiện tại</h4>
                </div>
                <div class="card-body">
                    <?php if ($registration['status'] === 'pending'): ?>
                    <div class="alert alert-warning">
                        <h5><i class="fas fa-clock"></i> Đơn đăng ký của bạn đang chờ phê duyệt</h5>
                        <p>Vui lòng đợi quản trị viên phê duyệt đơn đăng ký của bạn. Bạn sẽ nhận được thông báo khi đơn được xử lý.</p>
                    </div>
                    <?php elseif ($registration['status'] === 'approved'): ?>
                    <div class="alert alert-success">
                        <h5><i class="fas fa-check-circle"></i> Đơn đăng ký của bạn đã được phê duyệt</h5>
                        <p>Vui lòng đến văn phòng ký túc xá để hoàn tất thủ tục nhận phòng.</p>
                    </div>
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Thông tin sinh viên</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Mã số sinh viên</th>
                                    <td><?php echo htmlspecialchars($registration['student_id']); ?></td>
                                </tr>
                                <tr>
                                    <th>Họ và tên</th>
                                    <td><?php echo htmlspecialchars($registration['fullname']); ?></td>
                                </tr>
                                <tr>
                                    <th>Ngày đăng ký</th>
                                    <td><?php echo date('d/m/Y', strtotime($registration['created_at'])); ?></td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>Thông tin đăng ký</h5>
                            <table class="table table-bordered">
                                <?php
                                // Get dorm name
                                $dorm_query = "SELECT * FROM dorms WHERE id = ?";
                                $dorm_stmt = mysqli_prepare($conn, $dorm_query);
                                mysqli_stmt_bind_param($dorm_stmt, "i", $registration['dorm_id']);
                                mysqli_stmt_execute($dorm_stmt);
                                $dorm_result = mysqli_stmt_get_result($dorm_stmt);
                                $dorm = mysqli_fetch_assoc($dorm_result);
                                
                                // Get room type name
                                $room_type_query = "SELECT * FROM room_types WHERE id = ?";
                                $room_type_stmt = mysqli_prepare($conn, $room_type_query);
                                mysqli_stmt_bind_param($room_type_stmt, "i", $registration['room_type_id']);
                                mysqli_stmt_execute($room_type_stmt);
                                $room_type_result = mysqli_stmt_get_result($room_type_stmt);
                                $room_type = mysqli_fetch_assoc($room_type_result);
                                ?>
                                <tr>
                                    <th>Ký túc xá</th>
                                    <td><?php echo htmlspecialchars($dorm['name']); ?></td>
                                </tr>
                                <tr>
                                    <th>Loại phòng</th>
                                    <td><?php echo htmlspecialchars($room_type['name']); ?></td>
                                </tr>
                                <tr>
                                    <th>Ngày nhận phòng</th>
                                    <td><?php echo date('d/m/Y', strtotime($registration['check_in_date'])); ?></td>
                                </tr>
                                <tr>
                                    <th>Ngày trả phòng</th>
                                    <td><?php echo date('d/m/Y', strtotime($registration['check_out_date'])); ?></td>
                                </tr>
                                <tr>
                                    <th>Thời gian ở</th>
                                    <td><?php echo htmlspecialchars($registration['duration']); ?> tháng</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <?php if ($registration['status'] === 'pending'): ?>
                    <div class="text-center mt-3">
                        <a href="cancel-registration.php?id=<?php echo $registration['id']; ?>" class="btn btn-danger" onclick="return confirm('Bạn có chắc chắn muốn hủy đơn đăng ký này?');">Hủy đơn đăng ký</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php else: ?>
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4>Đăng ký nội trú mới</h4>
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
                    
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="student_id" class="form-label">Mã số sinh viên</label>
                                    <input type="text" class="form-control" id="student_id" value="<?php echo htmlspecialchars($student_id); ?>" disabled>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="fullname" class="form-label">Họ và tên</label>
                                    <input type="text" class="form-control" id="fullname" value="<?php echo htmlspecialchars($fullname); ?>" disabled>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="dorm_id" class="form-label">Ký túc xá <span class="text-danger">*</span></label>
                                    <select class="form-select" id="dorm_id" name="dorm_id" required>
                                        <option value="">-- Chọn ký túc xá --</option>
                                        <?php foreach ($dorms as $dorm): ?>
                                        <option value="<?php echo $dorm['id']; ?>" <?php echo $dorm_id == $dorm['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($dorm['name']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="room_type_id" class="form-label">Loại phòng <span class="text-danger">*</span></label>
                                    <select class="form-select" id="room_type_id" name="room_type_id" required>
                                        <option value="">-- Chọn loại phòng --</option>
                                        <?php foreach ($room_types as $room_type): ?>
                                        <option value="<?php echo $room_type['id']; ?>" <?php echo $room_type_id == $room_type['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($room_type['name'] . ' - ' . $room_type['capacity'] . ' người - ' . number_format($room_type['price'], 0, ',', '.') . ' VNĐ/tháng'); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="preferred_roommates" class="form-label">Người ở cùng (nếu có)</label>
                                    <textarea class="form-control" id="preferred_roommates" name="preferred_roommates" rows="2" placeholder="Nhập mã số sinh viên của người bạn muốn ở cùng, mỗi người một dòng"><?php echo htmlspecialchars($preferred_roommates); ?></textarea>
                                    <div class="form-text">Nếu bạn muốn ở cùng với bạn bè, hãy nhập mã số sinh viên của họ ở đây.</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="check_in_date" class="form-label">Ngày nhận phòng <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="check_in_date" name="check_in_date" value="<?php echo htmlspecialchars($check_in_date); ?>" min="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="duration" class="form-label">Thời gian ở <span class="text-danger">*</span></label>
                                    <select class="form-select" id="duration" name="duration" required>
                                        <option value="">-- Chọn thời gian --</option>
                                        <option value="1" <?php echo $duration == '1' ? 'selected' : ''; ?>>1 tháng</option>
                                        <option value="3" <?php echo $duration == '3' ? 'selected' : ''; ?>>3 tháng</option>
                                        <option value="6" <?php echo $duration == '6' ? 'selected' : ''; ?>>6 tháng</option>
                                        <option value="12" <?php echo $duration == '12' ? 'selected' : ''; ?>>12 tháng</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="note" class="form-label">Ghi chú</label>
                                    <textarea class="form-control" id="note" name="note" rows="3" placeholder="Nhập các yêu cầu đặc biệt (nếu có)"><?php echo htmlspecialchars($note); ?></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="agreement" name="agreement" required>
                            <label class="form-check-label" for="agreement">Tôi đã đọc và đồng ý với <a href="#">nội quy ký túc xá</a></label>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Đăng ký</button>
                        </div>
                    </form>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="card mt-4">
                <div class="card-header bg-info text-white">
                    <h4>Thông tin các loại phòng</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Loại phòng</th>
                                    <th>Sức chứa</th>
                                    <th>Diện tích</th>
                                    <th>Giá/tháng</th>
                                    <th>Mô tả</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($room_types as $room_type): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($room_type['name']); ?></td>
                                    <td><?php echo htmlspecialchars($room_type['capacity']); ?> người</td>
                                    <td><?php echo htmlspecialchars($room_type['area']); ?> m²</td>
                                    <td><?php echo number_format($room_type['price'], 0, ',', '.'); ?> VNĐ</td>
                                    <td><?php echo htmlspecialchars($room_type['description']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 
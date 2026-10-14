<?php
// appointments.php
require_once 'db.php';
checkAuth();

// Delete request handler
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM appointments WHERE appointment_id = ?");
    $stmt->execute([$delete_id]);
    header("Location: appointments.php?msg=deleted");
    exit();
}

// Pagination configuration
$limit = 15;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$total_records = $pdo->query("SELECT COUNT(*) FROM appointments")->fetchColumn();
$total_pages = ceil($total_records / $limit);

// Fetch paginated appointments
$stmt = $pdo->prepare("SELECT * FROM appointments ORDER BY appointment_id DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$appointments = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Appointments - Hospital System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">Hospital DB System</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link text-white me-3" href="appointments.php">Appointments</a>
            <a class="btn btn-outline-light btn-sm align-self-center" href="logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container mb-5">
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
        <div class="alert alert-success alert-dismissible fade show">Record deleted successfully.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'created'): ?>
        <div class="alert alert-success alert-dismissible fade show">New appointment registered successfully.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Appointments List</h3>
        <a href="insert.php" class="btn btn-success">+ New Appointment</a>
    </div>

    <div class="table-responsive bg-white shadow-sm rounded">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Patient ID</th>
                    <th>Doctor ID</th>
                    <th>Date & Time</th>
                    <th>Reason</th>
                    <th>Room</th>
                    <th>Status</th>
                    <th>Emergency</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appointments as $row): ?>
                <tr>
                    <td><?= $row['appointment_id'] ?></td>
                    <td><?= $row['patient_id'] ?></td>
                    <td><?= $row['doctor_id'] ?></td>
                    <td><?= date('Y-m-d H:i', strtotime($row['appointment_date'])) ?></td>
                    <td><?= htmlspecialchars($row['reason_for_visit']) ?></td>
                    <td><?= htmlspecialchars($row['room_number']) ?></td>
                    <td>
                        <?php 
                        $badgeClass = match($row['status']) {
                            'Scheduled' => 'bg-primary',
                            'Completed' => 'bg-success',
                            'Cancelled' => 'bg-danger',
                            'In-Progress' => 'bg-warning text-dark',
                            default => 'bg-secondary'
                        };
                        ?>
                        <span class="badge <?= $badgeClass ?>"><?= $row['status'] ?></span>
                    </td>
                    <td>
                        <?= $row['is_emergency'] ? '<span class="badge bg-danger">YES</span>' : '<span class="badge bg-secondary">NO</span>' ?>
                    </td>
                    <td>
                        <a href="insert.php?edit=<?= $row['appointment_id'] ?>" class="btn btn-sm btn-outline-warning">Edit</a>
                        <a href="appointments.php?delete=<?= $row['appointment_id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete appointment #<?= $row['appointment_id'] ?>?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <nav class="mt-4">
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($i === $page) ? 'active' : '' ?>">
                    <a class="page-link" href="appointments.php?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
    <?php endif; ?>
</div>
</body>
</html>
<?php
// insert.php
require_once 'db.php';
checkAuth();

$is_edit = false;
$id = $_GET['edit'] ?? null;
$patient_id = '';
$doctor_id = '';
$appointment_date = '';
$reason_for_visit = '';
$status = 'Scheduled';
$room_number = '';
$notes = '';
$is_emergency = 0;
$error = '';

// Fetch patients and doctors for dropdown lists
$patients = $pdo->query("SELECT patient_id, first_name, last_name FROM patients ORDER BY last_name ASC")->fetchAll();
$doctors = $pdo->query("SELECT doctor_id, first_name, last_name FROM doctors ORDER BY last_name ASC")->fetchAll();

// Pre-fill form if editing
if ($id) {
    $is_edit = true;
    $id = (int)$id;
    $stmt = $pdo->prepare("SELECT * FROM appointments WHERE appointment_id = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch();

    if ($data) {
        $patient_id = $data['patient_id'];
        $doctor_id = $data['doctor_id'];
        $appointment_date = date('Y-m-d\TH:i', strtotime($data['appointment_date']));
        $reason_for_visit = $data['reason_for_visit'];
        $status = $data['status'];
        $room_number = $data['room_number'];
        $notes = $data['notes'];
        $is_emergency = $data['is_emergency'];
    } else {
        header("Location: appointments.php");
        exit();
    }
}

// Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $is_edit = !empty($id);

    $patient_id = (int)$_POST['patient_id'];
    $doctor_id = (int)$_POST['doctor_id'];
    $appointment_date = $_POST['appointment_date'];
    $reason_for_visit = trim($_POST['reason_for_visit']);
    $status = $_POST['status'];
    $room_number = trim($_POST['room_number']);
    $notes = trim($_POST['notes']);
    $is_emergency = isset($_POST['is_emergency']) ? 1 : 0;

    try {
        if ($is_edit) {
            $sql = "UPDATE appointments SET patient_id = ?, doctor_id = ?, appointment_date = ?, reason_for_visit = ?, status = ?, room_number = ?, notes = ?, is_emergency = ? WHERE appointment_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$patient_id, $doctor_id, $appointment_date, $reason_for_visit, $status, $room_number, $notes, $is_emergency, $id]);
            header("Location: appointments.php?msg=updated");
            exit();
        } else {
            $sql = "INSERT INTO appointments (patient_id, doctor_id, appointment_date, reason_for_visit, status, room_number, notes, is_emergency) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$patient_id, $doctor_id, $appointment_date, $reason_for_visit, $status, $room_number, $notes, $is_emergency]);
            header("Location: appointments.php?msg=created");
            exit();
        }
    } catch (PDOException $e) {
        $error = "Database Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $is_edit ? 'Edit Appointment' : 'New Appointment' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
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
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h4 class="card-title mb-0"><?= $is_edit ? 'Edit Appointment #' . htmlspecialchars($id) : 'Book New Appointment' ?></h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <?php if ($is_edit): ?>
                            <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
                        <?php endif; ?>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Patient</label>
                                <select name="patient_id" class="form-select" required>
                                    <option value="">-- Select Patient --</option>
                                    <?php foreach ($patients as $p): ?>
                                        <option value="<?= $p['patient_id'] ?>" <?= $patient_id == $p['patient_id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($p['first_name'] . ' ' . $p['last_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Doctor</label>
                                <select name="doctor_id" class="form-select" required>
                                    <option value="">-- Select Doctor --</option>
                                    <?php foreach ($doctors as $d): ?>
                                        <option value="<?= $d['doctor_id'] ?>" <?= $doctor_id == $d['doctor_id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($d['first_name'] . ' ' . $d['last_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Appointment Date & Time</label>
                                <input type="datetime-local" name="appointment_date" class="form-control" value="<?= htmlspecialchars($appointment_date) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Room Number</label>
                                <input type="text" name="room_number" class="form-control" value="<?= htmlspecialchars($room_number) ?>" required placeholder="e.g. R-101">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Reason for Visit</label>
                                <input type="text" name="reason_for_visit" class="form-control" value="<?= htmlspecialchars($reason_for_visit) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="Scheduled" <?= $status === 'Scheduled' ? 'selected' : '' ?>>Scheduled</option>
                                    <option value="In-Progress" <?= $status === 'In-Progress' ? 'selected' : '' ?>>In-Progress</option>
                                    <option value="Completed" <?= $status === 'Completed' ? 'selected' : '' ?>>Completed</option>
                                    <option value="Cancelled" <?= $status === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="3"><?= htmlspecialchars($notes) ?></textarea>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="is_emergency" id="is_emergency" value="1" <?= $is_emergency ? 'checked' : '' ?>>
                            <label class="form-check-label text-danger fw-bold" for="is_emergency">
                                Mark as Emergency
                            </label>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="appointments.php" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary"><?= $is_edit ? 'Update Appointment' : 'Save Appointment' ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
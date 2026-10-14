<?php
// index.php
require_once 'db.php';
checkAuth();

// Fetch summary metrics
$total_appointments = $pdo->query("SELECT COUNT(*) FROM appointments")->fetchColumn();
$total_scheduled = $pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'Scheduled'")->fetchColumn();
$total_completed = $pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'Completed'")->fetchColumn();
$total_emergencies = $pdo->query("SELECT COUNT(*) FROM appointments WHERE is_emergency = 1")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Hospital System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">Hospital DB System</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link text-white me-3" href="appointments.php">Manage Appointments</a>
            <a class="btn btn-outline-light btn-sm align-self-center" href="logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Dashboard Overview</h2>
        <a href="insert.php" class="btn btn-success">+ Book New Appointment</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card text-bg-primary text-center py-3">
                <div class="card-body">
                    <h3><?= $total_appointments ?></h3>
                    <p class="mb-0">Total Appointments</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-warning text-center py-3">
                <div class="card-body">
                    <h3><?= $total_scheduled ?></h3>
                    <p class="mb-0">Scheduled</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-success text-center py-3">
                <div class="card-body">
                    <h3><?= $total_completed ?></h3>
                    <p class="mb-0">Completed</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-danger text-center py-3">
                <div class="card-body">
                    <h3><?= $total_emergencies ?></h3>
                    <p class="mb-0">Emergency Cases</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header fw-bold">Quick Actions</div>
        <div class="card-body">
            <a href="appointments.php" class="btn btn-outline-primary me-2">View All Appointments Table</a>
            <a href="insert.php" class="btn btn-outline-success">Add New Record</a>
        </div>
    </div>
</div>
</body>
</html>
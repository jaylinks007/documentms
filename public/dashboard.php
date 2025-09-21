<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Document Management System</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mt-5 mb-3">
            <h1 class="mb-0">Dashboard</h1>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>

        <?php
        if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1):
            require_once __DIR__ . '/../src/admin_stats.php';
        ?>
        <div class="admin-section mt-4 p-3 bg-light rounded">
            <h3>Admin Overview</h3>
            <div class="row text-center">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Total Users</h5>
                            <p class="card-text display-4"><?php echo htmlspecialchars($admin_stats['total_users']); ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Total Documents</h5>
                            <p class="card-text display-4"><?php echo htmlspecialchars($admin_stats['total_documents']); ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Pending Approvals</h5>
                            <p class="card-text display-4"><?php echo htmlspecialchars($admin_stats['pending_approvals']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="user-section mt-4">
            <h3>Your Dashboard</h3>
            <p>This is your dashboard. More features will be added soon.</p>
        </div>
    </div>
</body>
</html>

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
            <div class="upload-form p-4 mb-4 border rounded">
                <h4>Upload New Document</h4>
                <form action="../src/upload_handler.php" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="title">Document Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description (Optional)</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="documentFile">Select Document</label>
                        <input type="file" class="form-control-file" id="documentFile" name="documentFile" required>
                    </div>
                    <button type="submit" name="upload" class="btn btn-primary">Upload Document</button>
                </form>
            </div>

            <div class="document-list mt-4">
                <h4>Your Documents</h4>
                <?php
                require_once __DIR__ . '/../src/user_documents.php';

                if (count($user_documents) > 0):
                ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Uploaded On</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($user_documents as $doc): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($doc['title']); ?></td>
                                <td><?php echo htmlspecialchars($doc['description']); ?></td>
                                <td><?php echo date('M d, Y H:i', strtotime($doc['upload_date'])); ?></td>
                                <td>
                                    <a href="../<?php echo htmlspecialchars($doc['file_path']); ?>" class="btn btn-sm btn-success" target="_blank" rel="noopener noreferrer">View</a>
                                    <!-- E-sign and Approval buttons will go here -->
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="alert alert-info">You have not uploaded any documents yet.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Document Management System</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mt-5 mb-3">
            <h1 class="mb-0">Dashboard</h1>
            <form action="/logout" method="POST">
                <button type="submit" class="btn btn-danger">Logout</button>
            </form>
        </div>
        <p>Welcome, <?php echo htmlspecialchars($username ?? 'User'); ?>!</p>

        <?php
        // Admin-specific view components will be added here later.
        if (isset($role_id) && $role_id == 1):
        ?>
        <div class="admin-section mt-4 p-3 bg-light rounded">
            <h3>Admin Overview</h3>
            <p>Admin statistics will be re-implemented here in a future step.</p>
        </div>
        <?php endif; ?>

        <div class="user-section mt-4">
            <h3 class="mb-3">Document Management</h3>
            <div class="upload-form p-4 mb-4 border rounded">
                <h4>Upload New Document</h4>
                <p>The document upload form will be re-implemented here soon.</p>
            </div>

            <div class="document-list mt-4">
                <h4>Your Documents</h4>
                <p>Your uploaded documents will be listed here soon.</p>
            </div>
        </div>
    </div>
</body>
</html>

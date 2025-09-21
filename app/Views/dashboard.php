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

        <?php if (isset($pending_approvals) && count($pending_approvals) > 0): ?>
        <div class="pending-approvals-section mt-4 p-3 bg-white rounded border">
            <h4 class="mb-3">Documents Awaiting Your Approval</h4>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Document Title</th>
                            <th>From</th>
                            <th>Received On</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pending_approvals as $approval): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($approval['title']); ?></td>
                            <td><?php echo htmlspecialchars($approval['sender_name']); ?></td>
                            <td><?php echo date('M d, Y H:i', strtotime($approval['request_date'])); ?></td>
                            <td>
                                <a href="/approvals?id=<?php echo $approval['approval_id']; ?>" class="btn btn-sm btn-warning">Review</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <div class="user-section mt-4">
            <div class="upload-form p-4 mb-4 border rounded">
                <h4>Upload New Document</h4>
                <form action="/documents" method="post" enctype="multipart/form-data">
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
                <?php if (isset($documents) && count($documents) > 0): ?>
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
                            <?php foreach ($documents as $doc): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($doc['title']); ?></td>
                                <td><?php echo htmlspecialchars($doc['description']); ?></td>
                                <td><?php echo date('M d, Y H:i', strtotime($doc['upload_date'])); ?></td>
                                <td class="d-flex">
                                    <a href="/download?file=<?php echo urlencode($doc['file_path']); ?>" class="btn btn-sm btn-success mr-2" target="_blank" rel="noopener noreferrer">View</a>
                                    <a href="/approvals/create?document_id=<?php echo $doc['id']; ?>" class="btn btn-sm btn-primary">Send for Approval</a>
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send for Approval - Document Management System</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-5">Send Document for Approval</h1>

        <div class="card mt-4">
            <div class="card-header">
                Document Details
            </div>
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($document['title']); ?></h5>
                <p class="card-text"><?php echo htmlspecialchars($document['description']); ?></p>
            </div>
        </div>

        <form action="/approvals" method="POST" class="mt-4">
            <input type="hidden" name="document_id" value="<?php echo $document['id']; ?>">

            <div class="form-group">
                <label for="approver_id">Select Approver</label>
                <select name="approver_id" id="approver_id" class="form-control" required>
                    <option value="">-- Please select a user --</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?php echo $user['id']; ?>">
                            <?php echo htmlspecialchars($user['username']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <a href="/dashboard" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Send Request</button>
        </form>
    </div>
</body>
</html>

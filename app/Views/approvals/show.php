<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Document - Document Management System</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-5">Review Document</h1>

        <div class="card mt-4">
            <div class="card-header">
                Approval Request Details
            </div>
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($approval['title']); ?></h5>
                <p class="card-text"><strong>Description:</strong> <?php echo htmlspecialchars($approval['description']); ?></p>
                <p class="card-text"><small class="text-muted">Sent by <?php echo htmlspecialchars($approval['sender_name']); ?> on <?php echo date('M d, Y', strtotime($approval['request_date'])); ?></small></p>
                <a href="/download?file=<?php echo urlencode($approval['file_path']); ?>" class="btn btn-primary" target="_blank">View Document</a>
            </div>
        </div>

        <form action="/approvals/update" method="POST" class="mt-4">
            <input type="hidden" name="approval_id" value="<?php echo $approval['approval_id']; ?>">

            <div class="form-group">
                <label for="comments">Comments (Optional)</label>
                <textarea name="comments" id="comments" class="form-control" rows="4" placeholder="Provide feedback here..."></textarea>
            </div>

            <div class="d-flex justify-content-between">
                <a href="/dashboard" class="btn btn-secondary">Back to Dashboard</a>
                <div>
                    <button type="submit" name="action" value="rejected" class="btn btn-danger">Reject</button>
                    <button type="submit" name="action" value="approved" class="btn btn-success">Approve</button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>

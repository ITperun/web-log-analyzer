<?php

require_once 'analyzer.php';

$result = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $file = $_FILES['logfile'] ?? null;

    if ($file === null || $file['error'] !== UPLOAD_ERR_OK) {
        $error = 'Failed to upload file.';
    } elseif ($file['size'] > 10 * 1024 * 1024) {
        $error = 'File size exceeds the 10MB limit.';
    } else {
        $result = analyzeLog($file['tmp_name']);
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Web Log Analyzer</title>
</head>
<body>
    <h1>Web Log Analyzer</h1>

    <?php if ($error !== null): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <input type="file" name="logfile" accept=".log,.txt" required>
        <button type="submit">Analyze</button>
    </form>

    <?php if ($result !== null): ?>
        <p>Total lines: <?= $result['total'] ?>, skipped: <?= $result['skipped'] ?></p>
        <!-- Tables with results will be there. -->
    <?php endif; ?>
</body>
</html>
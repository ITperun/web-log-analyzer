<?php

require_once 'analyzer.php';

$suspicious = [];
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
        $suspicious = findSuspiciousIps($result['auth_failures']);
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Web Log Analyzer</title>
    <link rel="stylesheet" href="style.css">
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
        <p>Lines total: <?= $result['total'] ?>, skipped: <?= $result['skipped'] ?></p>

        <h2>Top IP by request count</h2>
        <table border="1" cellpadding="6">
            <tr>
                <th>IP</th>
                <th>Requests</th>
                <th>Failed Logins</th>
            </tr>
            <?php foreach (array_slice($result['by_ip'], 0, 10, true) as $ip => $count): ?>
                <tr class="<?= isset($suspicious[$ip]) ? 'suspicious' : '' ?>">
                    <td><?= htmlspecialchars($ip) ?></td>
                    <td><?= $count ?></td>
                    <td><?= $result['auth_failures'][$ip] ?? 0 ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <h2>Response Codes</h2>
        <table border="1" cellpadding="6">
            <tr>
                <th>Code</th>
                <th>Count</th>
            </tr>
            <?php foreach ($result['by_status'] as $status => $count): ?>
                <tr>
                    <td><?= $status ?></td>
                    <td><?= $count ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <h2>Suspicious IPs</h2>
        <?php if (empty($suspicious)): ?>
            <p>No suspicious addresses found.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($suspicious as $ip => $count): ?>
                    <li><?= htmlspecialchars($ip) ?>: <?= $count ?> failed logins</li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <h2>Night Visits by Admin</h2>
        <?php if (empty($result['night_admin'])): ?>
            <p>No night visits by admin found.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($result['night_admin'] as $entry): ?>
                    <li><?= htmlspecialchars($entry['time']) ?> from IP <?= htmlspecialchars($entry['ip']) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>
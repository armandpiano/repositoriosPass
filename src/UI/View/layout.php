<?php

declare(strict_types=1);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'App', ENT_QUOTES, 'UTF-8'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        body { background: radial-gradient(circle at top left, #0ea5e9, #1e3a8a 45%, #0f172a 100%); min-height: 100vh; }
        .glass-card { background: rgba(255,255,255,.12); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,.2); box-shadow: 0 20px 45px rgba(0,0,0,.25); }
        .table-card { border-radius: 1rem; overflow: hidden; }
        .modal-doc .modal-dialog { max-width: 1100px; }
        .doc-content { max-height: 70vh; overflow-y: auto; }
        .brand-pill { background: rgba(255,255,255,.2); border-radius: 999px; padding: .4rem .9rem; }
    </style>
</head>
<body>
<?php echo $content ?? ''; ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<?php echo $scripts ?? ''; ?>
</body>
</html>

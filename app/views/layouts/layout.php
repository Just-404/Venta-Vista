<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventas Catálogo</title>

    <link rel="stylesheet" href="<?= BASE_URL ?>css/common.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/app.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/sidebar.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/header.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
</head>
<body>
<div class="app-wrapper">

    <?php require __DIR__ . '/sidebar.php'; ?>

    <div class="main-area">

        <?php require __DIR__ . '/header.php'; ?>

        <main class="content">

            <?php if (!empty($flash)): ?>
                <div id="contenedor-toast">
            <div class="toast <?= $flash['tipo'] ?>">
                 <?= htmlspecialchars($flash['mensaje']) ?>
            </div>
           
        </div>
            <?php endif; ?>

            <?php require $content; ?>

        </main>

        <?php require __DIR__ . '/footer.php'; ?>

    </div>

</div>

<script src="<?= BASE_URL ?>public/js/app.js"></script>
</body>
</html>
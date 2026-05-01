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
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/configuracion.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/configuracion.css">
    <script>window.APP_BASE = '<?= BASE_URL ?>';</script>

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
<script>
document.addEventListener("DOMContentLoaded", function () {
    const toast = document.querySelector("#contenedor-toast .toast");

    if (toast) {
        setTimeout(() => {
            toast.style.opacity = "0";
            toast.style.transform = "translateX(50px)";

            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 3000);
    }
});
</script>
<script src="<?= BASE_URL ?>js/app.js"></script>
<script src="<?= BASE_URL ?>js/validaciones.js"></script>
</body>
</html>

<?php
$productos = $productos ?? [];
$usuario = $usuario ?? null;
$carrito = $carrito ?? [];
$categorias = $categorias ?? [];
?>

<div class="page-header">
    <div>
        <h1 class="page-titulo">Catálogo de Productos</h1>
        <p class="page-sub"><?= count($productos) ?> productos registrados</p>
    </div>
    <?php if ($usuario['rol'] != 3): ?>
        <a href="<?= BASE_URL ?>productos/crear" class="btn btn-primario">+ Nuevo producto</a>
    <?php endif; ?>
</div>

<div class="panel">
    <div class="panel-header">
<<<<<<< HEAD
        <img src="<?= BASE_URL ?>images/icons/search-icon.png" class="icon" alt="logo sistema">
        <input class="input-buscar" type="text" id="buscador" placeholder="Buscar producto...">
=======
        <input class="input-buscar" type="text" id="buscador" placeholder="🔍 Buscar producto..." style="width: 67%;">
        <select id="filtro-categoria" class="select-form" style="width: 30%;">
            <option value="">Todas las categorías</option>
            <?php foreach ($categorias as $categoria): ?>
                <option value="<?= htmlspecialchars($categoria['nombre']) ?>"><?= $categoria['nombre'] ?></option>
            <?php endforeach; ?>
        </select>
>>>>>>> develop-v1.3
    </div>
    <?php if (empty($productos)): ?>
        <p class="panel-empty">No hay productos disponibles.</p>
    <?php endif; ?>
    <div class="catalogo-grid">
        <!-- Tarjeta de producto -->
        <?php foreach ($productos as $producto): ?>
            <?php if ($usuario['rol'] == 3 && !$producto['activo']) continue; ?>
            <div class="producto-card">
                <span class="imagen"><img src="<?= htmlspecialchars($producto['imagenes']) ?>"
                        alt="<?= htmlspecialchars($producto['nombre']) ?>" onclick="mostrarImagen(this)"
                        onerror="this.onerror=null; this.src='https://community.softr.io/uploads/db9110/original/2X/7/74e6e7e382d0ff5d7773ca9a87e6f6f8817a68a6.jpeg';"></span>
                <span class="categoria"><?= htmlspecialchars($producto['categoria']) ?></span>
                <h3 class="nombre"><?= htmlspecialchars($producto['nombre']) ?></h3>
                <?php if ($producto['descuento'] > 0): ?>
                    <span class="descuento">-<?= $producto['descuento'] ?>%</span>
                <?php endif; ?>
                <div class="calificacion">
                </div>
                <div class="precio">
                    <span class="precio-actual">RD$
                        <?= number_format($producto['precio'] * (1 - $producto['descuento'] / 100), 2) ?></span>
                    <?php if ($producto['descuento'] > 0): ?>
                        <span class="precio-anterior">RD$ <?= number_format($producto['precio'], 2) ?></span>
                    <?php endif; ?>
                </div>
                <span class="stock">Stock: <?= $producto['stock'] ?></span>
                <?php if ($usuario['rol'] == 3): ?>
                    <div class="acciones-carrito">
                        <form action="<?= BASE_URL ?>/carrito/agregar" method="post" class="form-carrito">
                            <input type="hidden" name="idProducto" value="<?= $producto['idProducto'] ?>">
                            <input class="input-form" type="number" name="cantidad" value="1" min="1"
                                max="<?= $producto['stock'] ?>" required>
                            <button type="submit" class="btn btn-primario"> Agregar <span
                                    class="carrito-blanco">🛒</span></button>
                        </form>
                    </div>
                <?php endif; ?>
                <a href="<?= BASE_URL ?>productos/ver?id=<?= $producto['idProducto'] ?>" class="btn btn-secundario">Ver
                    Detalles</a>
            </div>
        <?php endforeach; ?>
    </div>

</div>

<!-- Modal oculto -->
<div id="modalImagen" class="modal-catalogo">
    <span class="cerrar" onclick="cerrarModal()">&times;</span>
    <img class="modal-contenido" id="imagenGrande" src="">
</div>


<script>
    const buscador = document.getElementById('buscador');
    const filtroCategoria = document.getElementById('filtro-categoria');
    const cards = document.querySelectorAll('.producto-card');

    function filtrarProductos() {
        const query = buscador.value.toLowerCase();
        const categoriaSeleccionada = filtroCategoria.value.toLowerCase();

        cards.forEach(card => {
            const nombre = card.querySelector('.nombre').textContent.toLowerCase();
            const categoria = card.querySelector('.categoria').textContent.toLowerCase();

            const coincideBusqueda = nombre.includes(query) || categoria.includes(query);
            const coincideCategoria = categoriaSeleccionada === '' || categoria === categoriaSeleccionada.toLowerCase();

            card.style.display = (coincideBusqueda && coincideCategoria) ? '' : 'none';
        });
    }

    buscador.addEventListener('input', filtrarProductos);
    filtroCategoria.addEventListener('change', filtrarProductos);

    function mostrarImagen(img) {
        const modal = document.getElementById("modalImagen");
        const imagenGrande = document.getElementById("imagenGrande");
        imagenGrande.src = img.src;
        modal.style.display = "flex";
    }

    function cerrarModal() {
        const modal = document.getElementById("modalImagen");
        modal.style.display = "none";
        document.getElementById("imagenGrande").src = ""; // limpiar al cerrar
    }

</script>
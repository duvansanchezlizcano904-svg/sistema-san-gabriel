/**
 * Quesos San Gabriel — JavaScript Principal
 */

document.addEventListener('DOMContentLoaded', function () {

    // ── 1. Sidebar toggle móvil ──────────────────────────────
    const sidebarToggle  = document.getElementById('sidebarToggle');
    const sidebar        = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    if (sidebarToggle && sidebar && sidebarOverlay) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('show');
            sidebarOverlay.classList.toggle('show');
        });
        sidebarOverlay.addEventListener('click', () => {
            sidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
        });
    }

    // ── 2. Confirmación antes de eliminar ────────────────────
    document.querySelectorAll('.form-eliminar-producto').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const nombre = this.getAttribute('data-nombre') || 'este queso';
            if (confirm(`¿Eliminar permanentemente "${nombre}"? Esta acción no se puede deshacer.`)) {
                this.submit();
            }
        });
    });

    // ── 3. Punto de Venta Admin (carrito dinámico) ───────────
    const tablaItems       = document.getElementById('tablaItemsVenta');
    const selectProducto   = document.getElementById('selectProductoVenta');
    const inputCantidad    = document.getElementById('inputCantidadVenta');
    const btnAgregar       = document.getElementById('btnAgregarProducto');
    const totalSpan        = document.getElementById('totalVentaSpan');
    const infoStock        = document.getElementById('infoStockDisponible');
    const formVenta        = document.getElementById('formVenta');

    if (selectProducto && btnAgregar && tablaItems) {
        let itemsCarrito = [];

        selectProducto.addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            if (opt.value) {
                const stock = parseInt(opt.dataset.stock) || 0;
                const precio = parseFloat(opt.dataset.precio) || 0;
                const unidad = opt.dataset.unidad || '';
                infoStock.innerHTML = `Disponible: <strong class="text-primary">${stock} ${unidad}</strong> | Precio: <strong>$ ${fmt(precio)}</strong>`;
                inputCantidad.max   = stock;
                inputCantidad.value = 1;
                inputCantidad.focus();
            } else {
                infoStock.textContent = 'Selecciona un queso para ver disponibilidad.';
                inputCantidad.value   = 1;
            }
        });

        btnAgregar.addEventListener('click', function () {
            const opt = selectProducto.options[selectProducto.selectedIndex];
            if (!opt.value) { alert('Selecciona un queso de la lista.'); return; }

            const id       = parseInt(opt.value);
            const nombre   = opt.dataset.nombre;
            const stockMax = parseInt(opt.dataset.stock) || 0;
            const precio   = parseFloat(opt.dataset.precio) || 0;
            const unidad   = opt.dataset.unidad || '';
            const cantidad = parseInt(inputCantidad.value) || 0;

            if (cantidad <= 0) { alert('La cantidad debe ser al menos 1.'); return; }

            const idx = itemsCarrito.findIndex(i => i.id === id);
            const cantActual = idx !== -1 ? itemsCarrito[idx].cantidad : 0;
            const nuevaTotal = cantActual + cantidad;

            if (nuevaTotal > stockMax) {
                alert(`Stock insuficiente para "${nombre}". Disponible: ${stockMax}, en carrito: ${cantActual}.`);
                return;
            }

            if (idx !== -1) {
                itemsCarrito[idx].cantidad = nuevaTotal;
                itemsCarrito[idx].subtotal = nuevaTotal * precio;
            } else {
                itemsCarrito.push({ id, nombre, unidad, stockMax, precio, cantidad, subtotal: cantidad * precio });
            }

            selectProducto.value  = '';
            infoStock.textContent = 'Selecciona un queso para ver disponibilidad.';
            inputCantidad.value   = 1;
            renderTabla();
        });

        function renderTabla() {
            const tbody = tablaItems.querySelector('tbody');
            tbody.innerHTML = '';

            if (itemsCarrito.length === 0) {
                tbody.innerHTML = `
                    <tr id="filaSinProductos">
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="bi bi-cart-x fs-3 d-block mb-1"></i>
                            Agrega quesos desde el panel izquierdo.
                        </td>
                    </tr>`;
                if (totalSpan) totalSpan.textContent = '$ 0,00';
                return;
            }

            let granTotal = 0;
            itemsCarrito.forEach((item, index) => {
                granTotal += item.subtotal;
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="fw-semibold text-dark">
                        ${item.nombre}
                        <input type="hidden" name="producto_id[]" value="${item.id}">
                    </td>
                    <td><span class="badge bg-light text-secondary border">${item.unidad}</span></td>
                    <td style="width:140px">
                        <input type="number" name="cantidad[]"
                               class="form-control form-control-sm text-center input-mod-cant"
                               value="${item.cantidad}" min="1" max="${item.stockMax}" data-index="${index}">
                    </td>
                    <td>$ ${fmt(item.precio)}</td>
                    <td class="fw-bold text-primary">$ ${fmt(item.subtotal)}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger btn-del-item" data-index="${index}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>`;
                tbody.appendChild(tr);
            });

            if (totalSpan) totalSpan.textContent = `$ ${fmt(granTotal)}`;

            document.querySelectorAll('.input-mod-cant').forEach(inp => {
                inp.addEventListener('change', function () {
                    const i = parseInt(this.dataset.index);
                    let v = parseInt(this.value) || 1;
                    v = Math.min(Math.max(v, 1), itemsCarrito[i].stockMax);
                    itemsCarrito[i].cantidad = v;
                    itemsCarrito[i].subtotal = v * itemsCarrito[i].precio;
                    renderTabla();
                });
            });

            document.querySelectorAll('.btn-del-item').forEach(btn => {
                btn.addEventListener('click', function () {
                    itemsCarrito.splice(parseInt(this.dataset.index), 1);
                    renderTabla();
                });
            });
        }

        if (formVenta) {
            formVenta.addEventListener('submit', function (e) {
                if (itemsCarrito.length === 0) {
                    e.preventDefault();
                    alert('Agrega al menos un queso antes de registrar la venta.');
                }
            });
        }
    }

    // ── 4. Helper formato moneda ─────────────────────────────
    function fmt(num) {
        return Number(num).toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
});

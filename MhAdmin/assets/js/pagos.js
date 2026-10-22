/**
 * ============================================================
 *  pagos.js  -  Carrito y botones de pago
 * ============================================================
 *  - Muestra el carrito agrupado por producto (con + / - / quitar)
 *  - Cada botón [data-pasarela] envía el carrito a pago_iniciar.php,
 *    que calcula el total real y devuelve la URL de la pasarela.
 *  - El navegador va directo a pagar (Wompi, Mercado Pago, PayPal o WhatsApp).
 */
(function ($) {
    const formatoCop = new Intl.NumberFormat('es-CO', { maximumFractionDigits: 0 });

    function leerCarrito() {
        try { return JSON.parse(localStorage.getItem('productos') || '[]'); } catch (e) { return []; }
    }
    function guardarCarrito(items) {
        localStorage.setItem('productos', JSON.stringify(items));
    }
    function escapar(texto) {
        return $('<div>').text(texto).html();
    }
    function mostrarMensaje(texto) {
        $('#mensajePago').text(texto).show();
    }

    let ultimaRespuesta = { datos: [], total: 0 };

    window.mostrarCarrito = function () {
        const items = leerCarrito();
        if (!items.length) {
            pintar({ datos: [], total: 0 });
            return;
        }
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            dataType: 'json',
            data: { action: 'buscar', data: items },
            success: pintar,
            error: function () { mostrarMensaje('No se pudo cargar el carrito.'); }
        });
    };

    function pintar(res) {
        ultimaRespuesta = res;
        let html = '';
        res.datos.forEach(function (p) {
            html += '<tr>' +
                '<td>' + escapar(p.nombre) + '</td>' +
                '<td>$' + formatoCop.format(p.precio) + '</td>' +
                '<td class="text-center text-nowrap">' +
                '<button class="btn btn-sm btn-outline-secondary cant-btn" data-accion="menos" data-id="' + p.id + '">−</button> ' +
                '<span class="mx-2">' + p.cantidad + '</span>' +
                '<button class="btn btn-sm btn-outline-secondary cant-btn" data-accion="mas" data-id="' + p.id + '">+</button>' +
                '</td>' +
                '<td>$' + formatoCop.format(p.subtotal) + '</td>' +
                '<td><button class="btn btn-sm btn-link text-danger" data-accion="quitar" data-id="' + p.id + '">Quitar</button></td>' +
                '</tr>';
        });
        if (!html) {
            html = '<tr><td colspan="5" class="text-center text-muted">Tu carrito está vacío. <a href="./">Ver productos</a></td></tr>';
        }
        $('#tblCarrito').html(html);
        $('#total_pagar').text('$' + formatoCop.format(res.total));
        $('#totalUsd').text((res.total / window.TIENDA.tasaUsd).toFixed(2));
        $('#botonesPago [data-pasarela]').prop('disabled', !res.datos.length);
        if (typeof mostrar === 'function') { mostrar(); } // contador de scripts.js
    }

    // + / - / quitar
    $(document).on('click', '#tblCarrito [data-accion]', function () {
        const id = Number($(this).data('id'));
        const accion = $(this).data('accion');
        let items = leerCarrito();
        if (accion === 'mas') {
            items.push({ id: id });
        } else if (accion === 'menos') {
            const i = items.findIndex(function (it) { return Number(it.id) === id; });
            if (i >= 0) { items.splice(i, 1); }
        } else {
            items = items.filter(function (it) { return Number(it.id) !== id; });
        }
        guardarCarrito(items);
        window.mostrarCarrito();
    });

    // Botones de pago
    $(document).on('click', '#botonesPago [data-pasarela]', function () {
        const $boton = $(this);
        const pasarela = $boton.data('pasarela');
        const items = leerCarrito();
        $('#mensajePago').hide();

        if (!items.length) {
            mostrarMensaje('Tu carrito está vacío. Agrega productos antes de pagar.');
            return;
        }

        const htmlOriginal = $boton.html();
        $('#botonesPago [data-pasarela]').prop('disabled', true);
        $boton.html('Conectando con la pasarela...');

        function restaurar() {
            $boton.html(htmlOriginal);
            $('#botonesPago [data-pasarela]').prop('disabled', false);
        }

        $.ajax({
            url: 'pago_iniciar.php',
            type: 'POST',
            dataType: 'json',
            data: { pasarela: pasarela, data: items },
            success: function (res) {
                if (!res.ok) {
                    mostrarMensaje(res.mensaje || 'No fue posible iniciar el pago.');
                    restaurar();
                    return;
                }
                // WhatsApp y vistas dentro de un iframe se abren en otra pestaña
                if (pasarela === 'transferencia' || window.self !== window.top) {
                    window.open(res.url, '_blank');
                    restaurar();
                } else {
                    window.location.href = res.url;
                }
            },
            error: function (xhr) {
                const res = xhr.responseJSON;
                mostrarMensaje((res && res.mensaje) || 'Error de conexión. Intenta nuevamente.');
                restaurar();
            }
        });
    });

    // Resumen descargable (para pago por transferencia)
    $(document).on('click', '#btnDescargarFactura', function () {
        const res = ultimaRespuesta;
        if (!res.datos.length) {
            mostrarMensaje('Tu carrito está vacío.');
            return;
        }
        let txt = 'Resumen del pedido - MH Globos y Detalles\n';
        txt += '----------------------------------------\n';
        res.datos.forEach(function (p) {
            txt += p.cantidad + ' x ' + p.nombre + '  $' + formatoCop.format(p.precio) +
                '  = $' + formatoCop.format(p.subtotal) + '\n';
        });
        txt += '----------------------------------------\n';
        txt += 'Total a pagar: $' + formatoCop.format(res.total) + ' COP\n\n';
        txt += 'Pago por transferencia: ' + window.TIENDA.cuentaBanco + '\n';
        txt += 'Envía tu comprobante al WhatsApp +' + window.TIENDA.whatsapp + '\n';

        const enlace = document.createElement('a');
        enlace.href = URL.createObjectURL(new Blob([txt], { type: 'text/plain' }));
        enlace.download = 'resumen-pedido.txt';
        enlace.click();
    });

    $(function () {
        $('#btnVaciar').on('click', function () { window.mostrarCarrito(); });
        window.mostrarCarrito();
    });
})(jQuery);

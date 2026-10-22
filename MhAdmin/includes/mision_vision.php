<?php
/**
 * ============================================================
 *  mision_vision.php  -  Sección de Misión y Visión
 * ============================================================
 * Se incluye al inicio de:
 *   - index.php            (tienda, la ven los usuarios)
 *   - admin/productos.php  (panel, la ven los administradores)
 *
 * Los estilos usan el prefijo "mv-" para no chocar con Bootstrap 4
 * (panel admin) ni con Bootstrap 5 (tienda).
 * Para cambiar los textos solo edita este archivo.
 */
?>
<style>
    .mv-seccion {
        padding: 48px 16px;
        background: #fff5fb;
    }

    .mv-contenedor {
        max-width: 1100px;
        margin: 0 auto;
    }

    .mv-titulo {
        text-align: center;
        font-weight: 800;
        font-size: 28px;
        color: #212529;
        margin: 0 0 8px;
    }

    .mv-subtitulo {
        text-align: center;
        color: #6c757d;
        margin: 0 0 32px;
    }

    .mv-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 24px;
    }

    .mv-tarjeta {
        flex: 1 1 300px;
        background: #ffffff;
        border-radius: 12px;
        border-top: 5px solid #ed9fd8;
        padding: 28px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
    }

    .mv-tarjeta h3 {
        font-size: 22px;
        font-weight: 700;
        color: #212529;
        margin: 0 0 12px;
    }

    .mv-tarjeta p {
        color: #495057;
        line-height: 1.7;
        margin: 0;
    }

    .mv-etiqueta {
        display: inline-block;
        background: #ed9fd8;
        color: #000;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
        border-radius: 20px;
        padding: 4px 12px;
        margin-bottom: 12px;
    }
</style>

<section class="mv-seccion" aria-labelledby="mv-titulo">
    <div class="mv-contenedor">
        <h2 class="mv-titulo" id="mv-titulo">Quiénes Somos</h2>
        <p class="mv-subtitulo">MH Globos y Detalles: creamos momentos inolvidables</p>

        <div class="mv-grid">
            <article class="mv-tarjeta">
                <span class="mv-etiqueta">Nuestra razón de ser</span>
                <h3>Misión</h3>
                <p>
                    En MH Globos y Detalles diseñamos y entregamos detalles, anchetas y decoraciones con globos
                    hechos con amor y creatividad, para ayudar a nuestros clientes a expresar sus sentimientos y
                    celebrar sus momentos más especiales. Nos comprometemos a ofrecer productos de calidad,
                    atención personalizada, precios justos y entregas puntuales, haciendo de cada pedido una
                    experiencia única y memorable.
                </p>
            </article>

            <article class="mv-tarjeta">
                <span class="mv-etiqueta">Hacia dónde vamos</span>
                <h3>Visión</h3>
                <p>
                    Para el año 2030 ser la tienda virtual de detalles y decoración con globos líder y más
                    recomendada de la región, reconocida por la originalidad de nuestros diseños, la excelencia
                    en el servicio y la innovación en nuestros canales de venta y pago en línea, llevando alegría
                    a cada hogar y evento de Colombia.
                </p>
            </article>
        </div>
    </div>
</section>

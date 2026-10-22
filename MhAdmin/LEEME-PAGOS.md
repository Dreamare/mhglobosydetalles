# Pasarela de pagos – MH Globos y Detalles

La tienda (carpeta `MhAdmin`) ahora permite pagar desde el carrito con:

| Botón | Pasarela | Medios de pago |
|---|---|---|
| PSE · Nequi · Bancolombia · Tarjeta | **Wompi** | PSE, Nequi, Botón Bancolombia, Daviplata, tarjetas, efectivo en corresponsales |
| Pagar con Mercado Pago | **Mercado Pago** | Tarjetas, PSE, Efecty, saldo Mercado Pago |
| Pagar con PayPal | **PayPal** | Cuenta PayPal y tarjetas internacionales (cobra en USD) |
| Transferencia + WhatsApp | Manual | Abre WhatsApp con el pedido para enviar el comprobante |

Cada botón aparece **solo cuando su pasarela tiene llaves configuradas**.

## 1. Poner a correr el sitio (XAMPP)

1. Copia la carpeta `integracion-pasarela-de-pagos` dentro de `C:\xampp\htdocs\`.
2. En el panel de XAMPP inicia **Apache** y **MySQL**.
3. Abre `http://localhost/phpmyadmin` → pestaña **Importar** → selecciona
   `MhAdmin/db/instalar_tienda.sql` → **Continuar**.
   (Crea las bases `card` y `usuarios` con productos de ejemplo. Es seguro reimportarlo.)
4. Abre `http://localhost/integracion-pasarela-de-pagos/MhAdmin/login.php`
   - Cliente de prueba: `cliente` / `cliente123`
   - Panel admin (`.../MhAdmin/admin/`): `admin` / `admin123`

Requisitos de PHP (XAMPP ya los trae): PHP 7.4+ con las extensiones **mysqli** y **curl** activas.

## 2. Activar las pasarelas

Edita **solo** `MhAdmin/config/pagos.php` y reemplaza los textos `REEMPLAZA_...`.

**Wompi** – https://comercios.wompi.co → Desarrolladores
- Llave pública, secreto de Integridad y secreto de Eventos.
- En "URL de Eventos" registra `https://TU-DOMINIO/webhook_wompi.php`.

**Mercado Pago** – https://www.mercadopago.com.co/developers → Tus integraciones → Credenciales
- Copia el **Access Token** (de prueba: `TEST-...`; real: `APP_USR-...`).
- Opcional: en Webhooks activa el evento "Pagos" y copia la clave secreta en `MP_WEBHOOK_SECRET`.

**PayPal** – https://developer.paypal.com → Apps & Credentials
- Copia **Client ID** y **Secret**. Deja `PAYPAL_MODO` en `sandbox` para pruebas y cámbialo a `live` para cobrar.
- PayPal no acepta pesos colombianos: ajusta `PAYPAL_TASA_COP_POR_USD`.

**Datos de la tienda**: número de WhatsApp y cuenta bancaria están al inicio del mismo archivo.

## 3. Probar sin cobrar dinero real

- **Wompi** (llaves `pub_test_`): tarjeta `4242 4242 4242 4242` (aprobada), `4111 1111 1111 1111` (rechazada);
  Nequi de prueba `3991111111` (aprobada). PSE en sandbox permite elegir el resultado.
- **Mercado Pago**: usa las tarjetas de prueba de tu panel de desarrollador.
- **PayPal**: entra con una cuenta "Personal" de sandbox (Sandbox → Accounts).

## 4. Pasar a producción

1. Sube el sitio a un hosting con **HTTPS**.
2. En `config/pagos.php` pon `TIENDA_URL_BASE` (ej. `https://www.mhglobdetalle.com`) y las llaves reales
   (`pub_prod_`/`prod_integrity_`/`prod_events_`, `APP_USR-...`, `PAYPAL_MODO = 'live'`).
3. Registra los webhooks con tu dominio real.
4. Cambia las claves de prueba (`admin123`, `cliente123`) y la contraseña de MySQL en `config/conexion.php`.

> En localhost los pagos funcionan, pero las pasarelas no pueden enviar webhooks a tu PC.
> El estado igual se actualiza cuando el cliente vuelve a `respuesta_pago.php`.
> Si quieres probar webhooks en local, expón el sitio con una herramienta como ngrok.

## Cómo funciona (para el desarrollador)

```
carrito.php ──(botón)──> pago_iniciar.php ──> lib/pagos.php calcula total con precios de la BD
                                │                y crea el pedido (tabla `pedidos`, estado PENDIENTE)
                                └──> URL de Wompi / Mercado Pago / PayPal / WhatsApp
Cliente paga ──> respuesta_pago.php (consulta el estado real en la API y actualiza el pedido)
Pasarela ──> webhook_wompi.php / webhook_mercadopago.php (confirmaciones tardías, ej. PSE)
```

Seguridad incluida: precios siempre desde la BD, firma de integridad Wompi, validación de firma
en webhooks, verificación de que el monto pagado coincida con el pedido, consultas SQL preparadas,
y un pedido aprobado nunca vuelve a "pendiente".

Los pedidos quedan en la tabla `card.pedidos` (consúltalos en phpMyAdmin).

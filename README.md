Extensión LibreDTE para OpenCart
================================

Esta extensión permite integrar OpenCart con la aplicación web de LibreDTE.

Funcionalidades implementadas:

- API para obtener datos de los items desde la página de emisión de LibreDTE.
- Enlaces directos a la aplicación de LibreDTE utilizando preautenticación.
- Generación de factura (33 o 34) desde la página de orden de compra de OpenCart.
- Acceso desde la página de la orden de compra a la página del DTE en LibreDTE.

API
---

URL items:

    https://example.com/index.php?route=libredte/product&column=sku&product_id=

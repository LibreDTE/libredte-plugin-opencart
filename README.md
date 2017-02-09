Plugin LibreDTE para OpenCart
=============================

Este plugin permite integrar OpenCart con la aplicación web de LibreDTE.

Funcionalidades implementadas:

- API para obtener datos de los items desde la página de emisión de LibreDTE.
- Enlaces directos a la aplicación de LibreDTE utilizando preautenticación.
- Generación de factura (33 o 34) desde la página de orden de compra de OpenCart.
- Acceso desde la página de la orden de compra a la página del DTE en LibreDTE.

Licencia
--------

Este código está liberado bajo la licencia de software libre [AGPL](http://www.gnu.org/licenses/agpl-3.0.en.html).
Para detalles sobre cómo se puede utilizar, modificar y/o distribuir este plugin revisar los términos de la licencia.
También tiene detalles, en español, sobre esto en los [términos y condiciones](https://wiki.libredte.cl/doku.php/terminos) de LibreDTE.

API
---

URL items:

    https://example.com/index.php?route=libredte/product&column=sku&product_id=

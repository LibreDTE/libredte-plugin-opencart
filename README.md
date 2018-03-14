Plugin LibreDTE para OpenCart
=============================

Este plugin permite integrar OpenCart 3 con la aplicación web de LibreDTE.

El plugin ha sido probado con Opencart 3.0.2.0

Si desea una versión compatible con OpenCart 2 revisar [aquí](https://github.com/LibreDTE/libredte-plugin-opencart/releases/tag/v2.0.0-alpha).

Funcionalidades implementadas:

- API para obtener datos de los items desde la página de emisión de LibreDTE.
- Enlaces directos a la aplicación de LibreDTE utilizando preautenticación.
- Generación de factura o boleta desde la página de orden de compra de OpenCart.
- Acceso desde la página de la orden de compra a la página del DTE en LibreDTE.
- Envío por correo electrónico al emitir el documento en OpenCart.

Licencia
--------

Este código está liberado bajo la licencia de software libre [AGPL](http://www.gnu.org/licenses/agpl-3.0.en.html).
Para detalles sobre cómo se puede utilizar, modificar y/o distribuir este plugin revisar los términos de la licencia.
También tiene detalles, en español, sobre esto en los [términos y condiciones](https://wiki.libredte.cl/doku.php/terminos) de LibreDTE.

API
---

### URL items

Para configurar la lectura de códigos desde la plataforma LibreDTE, por ejemplo, usando el SKU del producto, se debe configurar la URL:

    http://www.mitienda.cl/index.php?route=libredte/product&codigo=

Por ejemplo:

    http://altronicschile.cl/index.php?route=libredte/product&codigo=

La columna a usar, en este caso el ejemplo se indica SKU, se asigna en la configuración del plugin en OpenCart.

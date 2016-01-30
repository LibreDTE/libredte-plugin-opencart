BEGIN;

-- índices
ALTER TABLE oc_product DROP INDEX IF EXISTS oc_product_model_idx;
CREATE INDEX oc_product_model_idx ON oc_product (model);
ALTER TABLE oc_product DROP INDEX IF EXISTS oc_product_sku_idx;
CREATE INDEX oc_product_sku_idx ON oc_product (sku);

-- configuración extensión LibreDTE
INSERT INTO oc_setting (`code`, `key`, `value`) VALUES
    ('libredte', 'libredte_url', 'https://libredte.sasco.cl'),
    ('libredte', 'libredte_contribuyente', ''),
    ('libredte', 'libredte_preauth_hash', ''),
    ('libredte', 'libredte_producto_codigo', 'sku'),
    ('libredte', 'libredte_cliente_rut', 1),
    ('libredte', 'libredte_cliente_giro', 2)
;

COMMIT;

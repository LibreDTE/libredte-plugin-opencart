BEGIN;

ALTER TABLE oc_product DROP INDEX IF EXISTS oc_product_model_idx;
CREATE INDEX oc_product_model_idx ON oc_product (model);

ALTER TABLE oc_product DROP INDEX IF EXISTS oc_product_sku_idx;
CREATE INDEX oc_product_sku_idx ON oc_product (sku);

COMMIT;

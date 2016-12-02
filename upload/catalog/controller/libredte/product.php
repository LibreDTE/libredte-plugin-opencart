<?php

/**
 * LibreDTE
 * Copyright (C) SASCO SpA (https://sasco.cl)
 *
 * Este programa es software libre: usted puede redistribuirlo y/o
 * modificarlo bajo los términos de la Licencia Pública General Affero de GNU
 * publicada por la Fundación para el Software Libre, ya sea la versión
 * 3 de la Licencia, o (a su elección) cualquier versión posterior de la
 * misma.
 *
 * Este programa se distribuye con la esperanza de que sea útil, pero
 * SIN GARANTÍA ALGUNA; ni siquiera la garantía implícita
 * MERCANTIL o de APTITUD PARA UN PROPÓSITO DETERMINADO.
 * Consulte los detalles de la Licencia Pública General Affero de GNU para
 * obtener una información más detallada.
 *
 * Debería haber recibido una copia de la Licencia Pública General Affero de GNU
 * junto a este programa.
 * En caso contrario, consulte <http://www.gnu.org/licenses/agpl.html>.
 */

/**
 * Controlador para trabajar con los productos de OpenCart
 * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
 * @version 2016-01-23
 */
class ControllerLibredteProduct extends Controller
{

    /**
     * Acción que permite obtener los datos de un item (producto) para poder
     * consumir desde la aplicación web de LibreDTE
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2016-12-02
     */
    public function index()
    {
        $item = [];
        // solo procesar si es una consulta por POST
        if ($this->request->server['REQUEST_METHOD'] == 'GET') {
            // columna que se usará para identificar al producto
            if (isset($this->request->get['column'])) {
                $column = $this->request->get['column'];
            } else {
                $column = 'product_id';
            }
            // recuperar ID del producto
            if (isset($this->request->get['product_id'])) {
                if ($column != 'product_id') {
                    $this->load->model('libredte/product');
                    $product_id = $this->model_libredte_product->getProductId(
                        $this->request->get['product_id'], $column
                    );
                } else {
                    $product_id = (int)$this->request->get['product_id'];
                }
            } else {
                $product_id = 0;
            }
            // obtener datos del producto
            $this->load->model('catalog/product');
            $product_info = $this->model_catalog_product->getProduct($product_id);
            if ($product_info) {
                $item = [
                    'TpoCodigo' => 'INT1',
                    'VlrCodigo' => substr($this->request->get['product_id'], 0, 35),
                    'NmbItem' => substr($product_info['name'], 0, 80),
                    'DscItem' => substr($product_info['meta_description'], 0, 1000),
                    'IndExe' => $product_info['tax_class_id'] ? 0 : 1,
                    'UnmdItem' => substr('', 0, 4),
                    'PrcItem' => round($product_info['price']),
                    'ValorDR' => $product_info['special'] ? round($product_info['price']-$product_info['special']) : 0,
                    'TpoValor' => '$',
                ];
            }
        }
        // enviar respuesta al cliente
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($item, JSON_PRETTY_PRINT));
    }

}

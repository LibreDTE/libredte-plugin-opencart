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
 * @author Pablo Estremadoyro
 * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
 * @version 2018-03-07
 */
class ControllerLibredteProduct extends Controller
{

    /**
     * Acción que permite obtener los datos de un item (producto) para poder
     * consumir desde la aplicación web de LibreDTE
     * @author Pablo Estremadoyro
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2018-03-07
     */
    public function index()
    {
        $item = [];
        // solo procesar si es una consulta por POST
        if ($this->request->server['REQUEST_METHOD'] == 'GET') {
            // columna que se usará para identificar al producto
            $product_code = $this->config->get('module_libredte_producto_codigo');
            $result = $this->db->query("SELECT `product_id` FROM `".DB_PREFIX."product` WHERE " . $this->db->escape($product_code) . " = '" . $this->db->escape($this->request->get['codigo']) . "'");
            if ($result->num_rows) {
                $product_id = $result->row['product_id'];
            }
            // obtener datos del producto
            $this->load->model('catalog/product');
            $product_info = $this->model_catalog_product->getProduct($product_id);
            if ($product_info) {
                $item = [
                    'TpoCodigo' => 'INT1',
                    'VlrCodigo' => substr($this->request->get['codigo'], 0, 35),
                    'NmbItem' => substr($product_info['name'], 0, 80),
                    'DscItem' => '',
                    'IndExe' => $product_info['tax_class_id'] ? 0 : 0,
                    'UnmdItem' => 'ud.',
                    'PrcItem' => round($product_info['price'] / 1.19),
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

<?php

/**
 * LibreDTE
 * Copyright (C) SASCO SpA (https://sasco.cl)
 *
 * Este programa es software libre: usted puede redistribuirlo y/o
 * modificarlo bajo los términos de la Licencia Pública General GNU
 * publicada por la Fundación para el Software Libre, ya sea la versión
 * 3 de la Licencia, o (a su elección) cualquier versión posterior de la
 * misma.
 *
 * Este programa se distribuye con la esperanza de que sea útil, pero
 * SIN GARANTÍA ALGUNA; ni siquiera la garantía implícita
 * MERCANTIL o de APTITUD PARA UN PROPÓSITO DETERMINADO.
 * Consulte los detalles de la Licencia Pública General GNU para obtener
 * una información más detallada.
 *
 * Debería haber recibido una copia de la Licencia Pública General GNU
 * junto a este programa.
 * En caso contrario, consulte <http://www.gnu.org/licenses/gpl.html>.
 */

/**
 * Modelo para trabajar con las órdenes de compra de OpenCart
 * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
 * @version 2016-01-30
 */
class ModelLibredteOrder extends Model
{

    /**
     * Constructor del controlador
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2016-01-26
     */
    public function __construct($registry)
    {
        $this->registry = $registry;
        $this->registry->set('libredte', new Libredte($this->registry));
    }

    /**
     * Método que crea la factura en LibreDTE
     * @param order_id ID de la orden que se quiere generar su factura
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2016-01-30
     */
    public function createInvoiceNo($order_id)
    {
        $dte = $this->getDte($order_id);
        if (!$dte)
            return false;
        $order_info = $this->model_sale_order->getOrder($order_id);
        $libredte_info = $this->model_setting_setting->getSetting(
            'libredte', $order_info['store_id']
        );
        // emitir dte temporal
        $response = $this->libredte->post(
            $libredte_info['libredte_url'].'/api/dte/documentos/emitir',
            $libredte_info['libredte_preauth_hash'],
            $dte
        );
        if ($response['status']['code']!=200) {
            $this->log->write($response['body']);
            return false;
        }
        $dte_tmp = $response['body'];
        // generar dte definitivo y enviar al sii
        $response = $this->libredte->post(
            $libredte_info['libredte_url'].'/api/dte/documentos/generar',
            $libredte_info['libredte_preauth_hash'],
            $dte_tmp
        );
        if ($response['status']['code']!=200) {
            $this->log->write($response['body']);
            return false;
        }
        $invoice_prefix = 'T'.$response['body']['dte'].'F';
        $invoice_no = $response['body']['folio'];
        $this->db->query("UPDATE `" . DB_PREFIX . "order` SET invoice_no = '" . (int)$invoice_no . "', invoice_prefix = '" . $this->db->escape($invoice_prefix) . "' WHERE order_id = '" . (int)$order_id . "'");
        return $invoice_prefix.$invoice_no;
    }

    /**
     * Método que crea el arreglo con los datos del DTE según especificación de
     * LibreDTE (mismo esquema que el SII)
     * @param order_id ID de la orden que se quiere obtener sus datos
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2016-01-30
     */
    public function getDte($order_id, $TipoDTE = 33)
    {
        $this->load->model('sale/order');
        $order_info = $this->model_sale_order->getOrder($order_id);
        if (!$order_info or $order_info['invoice_no'])
            return false;
        $this->load->model('setting/setting');
        $libredte_info = $this->model_setting_setting->getSetting(
            'libredte', $order_info['store_id']
        );
        $custom_field_rut = $libredte_info['libredte_cliente_rut'];
        $custom_field_giro = $libredte_info['libredte_cliente_giro'];
        $product_code = $libredte_info['libredte_producto_codigo'];
        if (empty($order_info['custom_field'][$custom_field_rut]) or empty($order_info['custom_field'][$custom_field_giro]))
            return false;
        if (!$this->libredte->checkRut($order_info['custom_field'][$custom_field_rut]))
            return false;
        // crear arreglo con detalles de productos y/o servicios
        $this->load->model('libredte/product');
        $products = $this->model_sale_order->getOrderProducts($order_id);
        $Detalle = [];
        foreach ($products as $product) {
            $product_info = $this->model_libredte_product->getProduct($product['product_id']);
            $price = $product_info['price'];
            $discount = $product_info['price'] - $product_info['special'];
            if ($product['price']!=($price-$discount)) {
                $price = $product['price'];
                $discount = 0;
            }
            $Detalle[] = [
                'CdgItem' => $product_info[$product_code] ? [
                    'TpoCodigo' => 'INT1',
                    'VlrCodigo' => substr($product_info[$product_code], 0, 35),
                ] : false,
                'IndExe' => $product_info['tax_class_id'] ? false : 1,
                'NmbItem' => substr($product['name'], 0, 80),
                'DscItem' => substr($product_info['meta_description'], 0, 1000),
                'QtyItem' => $product['quantity'],
                'UnmdItem' => false,
                'PrcItem' => round($price),
                'DescuentoMonto' => $discount ? round($discount) : false
            ];
        }
        if (empty($Detalle))
            return false;
        // entregar arreglo con datos del DTE
        return [
            'Encabezado' => [
                'IdDoc' => [
                    'TipoDTE' => $TipoDTE,
                    'Folio' => 0,
                    'FchEmis' => date('Y-m-d'),
                ],
                'Emisor' => [
                    'RUTEmisor' => $libredte_info['libredte_contribuyente'].'-'.$this->libredte->dv($libredte_info['libredte_contribuyente']),
                ],
                'Receptor' => [
                    'RUTRecep' => $order_info['custom_field'][$custom_field_rut],
                    'RznSocRecep' => substr($order_info['customer'], 0, 100),
                    'GiroRecep' => substr($order_info['custom_field'][$custom_field_giro], 0, 40),
                    'Contacto' => substr($order_info['telephone'], 0, 80),
                    'CorreoRecep' => substr($order_info['email'], 0, 80),
                    'DirRecep' => substr($order_info['payment_address_1'].(!empty($order_info['payment_address_2'])?(', '.$order_info['payment_address_2']):''), 0, 70),
                    'CmnaRecep' => substr($order_info['payment_city'], 0, 20),
                ],
            ],
            'Detalle' => $Detalle,
        ];
    }

}

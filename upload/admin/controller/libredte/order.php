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
 * Controlador para ordenes de compra
 * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
 * @version 2016-01-26
 */
class ControllerLibredteOrder extends Controller
{

    private $error = [];

    /**
     * Acción que permite ir a la página de la factura de la orden en LibreDTE
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2016-01-30
     */
    public function invoice()
    {
        $this->load->language('libredte/order');
        if (!$this->user->hasPermission('access', 'libredte/order')) {
            $this->error['error'] = $this->language->get('error_permission');
            return;
        }
        $order_id = !empty($this->request->get['order_id']) ? $this->request->get['order_id'] : 0;
        $this->load->model('sale/order');
        $order_info = $this->model_sale_order->getOrder($order_id);
        if ($order_info and $order_info['invoice_no']) {
            $factura = str_replace('F', 'F'.$order_info['invoice_no'], $order_info['invoice_prefix']);
            list($dte_tipo, $dte_folio) = explode('F', substr($factura, 1));
            $url = $this->url->link(
                'extension/libredte/go',
                [
                    'user_token' => $this->session->data['user_token'],
                    'url' => base64_encode('/dte/dte_emitidos/ver/'.$dte_tipo.'/'.$dte_folio),
                ],
                'SSL'
            );
            header('location: '.str_replace('&amp;', '&', $url));
            exit;
        } else {
            $this->load->language('error/not_found');
            $this->document->setTitle($this->language->get('heading_title'));
            $data['breadcrumbs'] = array();
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL')
            );
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('error/not_found', 'user_token=' . $this->session->data['user_token'], 'SSL')
            );
            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');
            $this->response->setOutput($this->load->view('error/not_found', $data));
        }
    }

}

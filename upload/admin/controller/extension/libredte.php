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
 * Controlador para configurar la extensión Libredte
 * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
 * @version 2016-01-26
 */
class ControllerExtensionLibredte extends Controller
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
     * Acción que muestra el panel de administración de la extensión
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2016-01-26
     */
    public function index()
    {
        $this->load->model('setting/setting');
        $this->load->language('extension/libredte');
        $this->document->setTitle($this->language->get('heading_title'));
        $data['heading_title'] = $this->language->get('heading_title');
        $data['button_save'] = $this->language->get('button_save');
        // breadcrumbs
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/libredte', 'token=' . $this->session->data['token'], 'SSL')
        );
        // token para enlaces
        $data['token'] = $this->session->data['token'];
        // cabecera, menú y pie de página
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        // si se envío el formulario se procesa
        if (!empty($this->request->post)) {
            // verificar que campos mínimos estén completos
            if (empty($this->request->post['libredte_url']) or empty($this->request->post['libredte_contribuyente']) or empty($this->request->post['libredte_preauth_hash'])) {
                $data['error_warning'] = 'Falta completar campos en la configuración';
            }
            // verificar formato del rut
            else if (!$this->libredte->checkRut($this->request->post['libredte_contribuyente'])) {
                $data['error_warning'] = 'RUT del contribuyente es incorrecto';
            }
            // guardar en base de datos
            else {
                $settings = array_merge(
                    $this->request->post,
                    [
                        'libredte_contribuyente' => $this->libredte->checkRut($this->request->post['libredte_contribuyente'])
                    ]
                );
                $this->model_setting_setting->editSetting(
                    'libredte', $settings, (int)$this->config->get('config_store_id')
                );
                $data['success'] = 'Se ha guardado la configuración de la extensión';
            }
            // guardar datos para ser mostrados en la vista
            $libredte_info = $this->request->post;
        }
        // asignar configuración de la base de datos
        else {
            $libredte_info = $this->model_setting_setting->getSetting(
                'libredte', (int)$this->config->get('config_store_id')
            );
            $libredte_contribuyente = $libredte_info['libredte_contribuyente'];
            $libredte_info['libredte_contribuyente'] = number_format(
                $libredte_info['libredte_contribuyente'], 0, ',', '.'
            ).'-'.$this->libredte->dv($libredte_info['libredte_contribuyente']);
        }
        // variables para la vista
        foreach ($libredte_info as $key => $value) {
            $data[$key] = $value;
        }
        $data['producto_codigos'] = ['sku', 'model'];
        // enlaces a LibreDTE
        $enlaces = [
            'dte' => '/dte',
            'emitir' => '/dte/documentos/emitir',
            'temporales' => '/dte/dte_tmps',
            'emitidos' => '/dte/dte_emitidos/listar',
            'recibidos' => '/dte/dte_recibidos/listar',
            'intercambio' => '/dte/dte_intercambios',
            'ventas' => '/dte/dte_ventas',
            'compras' => '/dte/dte_compras',
            'folios' => '/dte/admin/dte_folios',
            'firma' => '/dte/admin/firma_electronicas',
            'contribuyente' => '/dte/contribuyentes/modificar/'.(isset($libredte_contribuyente)?$libredte_contribuyente:''),
            'perfil' => '/usuarios/perfil',
        ];
        foreach ($enlaces as $key => $enlace) {
            $data['enlace_'.$key] = $this->url->link(
                'extension/libredte/go',
                [
                    'token' => $this->session->data['token'],
                    'url' => base64_encode($enlace),
                ],
                'SSL'
            );
        }
        // cargar vista
        $this->response->setOutput($this->load->view('extension/libredte.tpl', $data));
    }

    /**
     * Acción para dirigir al usuario a una página en la aplicación de LibreDTE
     * Utiliza preautenticación y selecciona automáticamente al contribuyente
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2016-01-26
     */
    public function go()
    {
        $this->load->model('setting/setting');
        $libredte_info = $this->model_setting_setting->getSetting(
            'libredte', (int)$this->config->get('config_store_id')
        );
        if (!empty($libredte_info['libredte_url']) and !empty($libredte_info['libredte_contribuyente']) and !empty($libredte_info['libredte_preauth_hash'])) {
            $url = !empty($this->request->get['url']) ? $this->request->get['url'] : base64_encode('/dte');
            $token = $libredte_info['libredte_preauth_hash'];
            $url = base64_encode('/dte/contribuyentes/seleccionar/'.$libredte_info['libredte_contribuyente'].'/'.$url);
            header('location: '.$libredte_info['libredte_url'].'/usuarios/preauth/'.$token.'/0/'.$url);
            exit;
        }
        // debe configurar antes
        else {
            $this->load->language('error/not_found');
            $this->document->setTitle($this->language->get('heading_title'));
            $data['heading_title'] = $this->language->get('heading_title');
            $data['text_not_found'] = $this->language->get('text_not_found');
            $data['breadcrumbs'] = array();
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
            );
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('error/not_found', 'token=' . $this->session->data['token'], 'SSL')
            );
            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');
            $this->response->setOutput($this->load->view('error/not_found.tpl', $data));
        }
    }

}

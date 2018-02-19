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
class ControllerExtensionModuleLibredte extends Controller
{

    /**
     * Constructor del controlador
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2016-01-26
     */
	 /*
    public function __construct($registry)
    {
        $this->registry = $registry;
        $this->registry->set('libredte', new Libredte($this->registry));
    }
	*/
    /**
     * Acción que muestra el panel de administración de la extensión
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2016-01-26
     */
    public function index()
    {
        $this->load->model('setting/setting');
		$this->load->model('setting/module');
        $this->load->language('extension/module/libredte');
        $this->document->setTitle($this->language->get('heading_title'));
		
		
			if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if (!isset($this->request->get['module_id'])) {
				$this->model_setting_module->addModule('module_libredte', $this->request->post);
			} else {
				$this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		if (isset($this->error['width'])) {
			$data['error_width'] = $this->error['width'];
		} else {
			$data['error_width'] = '';
		}

		if (isset($this->error['height'])) {
			$data['error_height'] = $this->error['height'];
		} else {
			$data['error_height'] = '';
		}
        // breadcrumbs
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/libredte', 'user_token=' . $this->session->data['user_token'], true)
        );
		
		
		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link('extension/module/libredte', 'user_token=' . $this->session->data['user_token'], true);
		} else {
			$data['action'] = $this->url->link('extension/module/libredte', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
		}

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$module_info = $this->model_setting_module->getModule($this->request->get['module_id']);
		}

		
		
		
		
        // token para enlaces
        $data['user_token'] = $this->session->data['user_token'];
        // cabecera, menú y pie de página
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
		$data['error_warning'] = '';
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
                    'module_libredte', $settings, (int)$this->config->get('config_store_id')
                );
                $data['success'] = 'Se ha guardado la configuración de la extensión';
            }
            // guardar datos para ser mostrados en la vista
            $libredte_info = $this->request->post;
        }
        // asignar configuración de la base de datos
        else {
            $libredte_info = $this->model_setting_setting->getSetting(
                'module_libredte', (int)$this->config->get('config_store_id')
            );
			
			if (!empty($libredte_info['libredte_contribuyente'])){
            $libredte_contribuyente = $libredte_info['libredte_contribuyente'];
            $libredte_info['libredte_contribuyente'] = number_format(
                $libredte_info['libredte_contribuyente'], 0, ',', '.'
            ).'-'.$this->libredte->dv($libredte_info['libredte_contribuyente']);
			}
        }
		
		if (empty($data['error_warning'])){
		$data['empty_error_warning'] = true;
		}
		else
		{
		$data['empty_error_warning'] = false;	
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
                    'user_token' => $this->session->data['user_token'],
                    'url' => base64_encode($enlace),
                ],
                true
            );
        }
		
		
		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($module_info)) {
			$data['status'] = $module_info['status'];
		} else {
			$data['status'] = '';
		}
		
		
        // cargar vista
        $this->response->setOutput($this->load->view('extension/module/libredte', $data));
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
            'module_libredte', (int)$this->config->get('config_store_id')
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
            $data['breadcrumbs'] = array();
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
            );
					$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
			);

		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/libredte', 'user_token=' . $this->session->data['user_token'], true)
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/libredte', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
			);
		}
 
            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');
            $this->response->setOutput($this->load->view('error/not_found', $data));
        }
    }
	
    public function install() {
		      $this->load->model('setting/setting');
		$this->model_setting_setting->editSetting('module_libredte', array("module_libredte_status" => 1));
    
    }

    public function uninstall() {
         $this->load->model('setting/setting');
        $this->model_setting_setting->deleteSetting('module_libredte');
    }


	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/libredte')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}	
	
	
}

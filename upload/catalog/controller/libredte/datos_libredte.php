<?php
class ControllerLibredteDatosLibredte extends Controller {
	public function index() {
		
	}

	public function save() {
		
		$json = array();
	
		$json['tipo'] = $_POST['tipo'];
		$this->session->data['libredte_frontend_tipo'] = $_POST['tipo'];
		
		if($_POST['tipo'] == "fe") {
			$json['rut'] = $_POST['rut'];
			$this->session->data['libredte_frontend_rut'] = $_POST['rut'];
			
			$json['razonsocial'] = $_POST['razonsocial'];
			$this->session->data['libredte_frontend_razonsocial'] = $_POST['razonsocial'];
			
			$json['giro'] = $_POST['giro'];
			$this->session->data['libredte_frontend_giro'] = $_POST['giro'];
		}
				
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
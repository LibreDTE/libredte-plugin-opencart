<?php

class ControllerExtensionModuleLibredte extends Controller {
	
	public function index() {
		$ruta = $this->request->get['route'];
				
		$data['heading_title'] = 'Opciones de Facturación';
		if($ruta == "checkout/checkout")
		{		
			$this->load->language('checkout/checkout');
			
			$data['logged'] = $this->customer->isLogged();
					
			$data['text_checkout_option'] = sprintf($this->language->get('text_checkout_option'), 1);
			$data['text_checkout_payment_address'] = sprintf($this->language->get('text_checkout_payment_address'), 1);
					
			return $this->load->view('extension/module/libredte', $data);
		}
				
	}
	
}
?>
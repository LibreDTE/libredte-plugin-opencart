<?php
class ControllerLibredteContribuyente extends Controller
{

      public function __construct($registry)
    {
        $this->registry = $registry;
        $this->registry->set('libredte', new Libredte($this->registry));
    }
    /**
     * Método que obtiene la Razón Social a Partir del Rut
     */
    public function index()
    {
      
$libredte_info['module_libredte_url'] = $this->config->get('module_libredte_url');
$libredte_info['module_libredte_preauth_hash'] = $this->config->get('module_libredte_preauth_hash');
		
$rut = $this->request->get['rut'];		
$rut = str_replace('.', '', $rut);		
      
// consultar situación tributaria del contribuyente al SII
$datos = $this->libredte->get($libredte_info['module_libredte_url'] . '/api/dte/contribuyentes/info/'.$rut, $libredte_info['module_libredte_preauth_hash']);

if ($datos['status']['code'] == "200") {        
// enviar respuesta al cliente
$this->response->addHeader('Content-Type: text/plain');
$this->response->setOutput($datos['body']['razon_social']);           
}      
else if ($datos['status']['code'] == "404") {  
// enviar respuesta al cliente
//$datos['body']['razon_social'] = 'Debe Ingresar Razón Social de la Empresa';
$this->response->addHeader('Content-Type: text/plain');
$this->response->setOutput(''); 
}
else {
// enviar respuesta al cliente
$this->response->addHeader('Content-Type: text/plain');
$this->response->setOutput('');        
  
}


    }

}
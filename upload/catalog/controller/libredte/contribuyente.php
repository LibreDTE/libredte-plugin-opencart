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
 * Controlador para trabajar con los datos de un contribuyente
 * @author Pablo Estremadoyro
 * @version 2018-03-07
 */
class ControllerLibredteContribuyente extends Controller
{

    public function __construct($registry)
    {
        $this->registry = $registry;
        $this->registry->set('libredte', new Libredte($this->registry));
    }

    /**
     * Método que obtiene la razón social a partir del RUT
     * @author Pablo Estremadoyro
     * @version 2018-03-07
     */
    public function index()
    {
        $libredte_info['module_libredte_url'] = $this->config->get('module_libredte_url');
        $libredte_info['module_libredte_preauth_hash'] = $this->config->get('module_libredte_preauth_hash');
        $rut = $this->request->get['rut'];
        $rut = str_replace('.', '', $rut);
        // consultar situación tributaria del contribuyente al SII
        $datos = $this->libredte->get($libredte_info['module_libredte_url'] . '/api/dte/contribuyentes/info/'.$rut, $libredte_info['module_libredte_preauth_hash']);
        // enviar respuesta al cliente según estado determinado
        if ($datos['status']['code'] == 200) {
            $rsocial = mb_substr($datos['body']['razon_social'], 0, 50);
            $this->response->addHeader('Content-Type: text/plain');
            $this->response->setOutput($rsocial);
        }
        else {
            $this->response->addHeader('Content-Type: text/plain');
            $this->response->setOutput('');
        }
    }

}

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
 * Clase con funcionalidades varias para inegrar con LibreDTE
 * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
 * @version 2016-01-26
 */
class Libredte
{

    /**
     * Método que consume un servicio web de LibreDTE a través de POST
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2016-01-26
     */
    public function post($url, $hash = null, $data = null)
    {
        return $this->consume($url, $hash, $data, 'post');
    }

    /**
     * Método que consume un servicio web de LibreDTE a través de GET
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2016-01-26
     */
    public function get($url, $hash = null, $data = null)
    {
        return $this->consume($url, $hash, $data, 'get');
    }

    /**
     * Método que consume un servicio web de LibreDTE a través de cierto método
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2016-01-26
     */
    private function consume($url, $hash, $data, $method)
    {
        if (!class_exists('\sowerphp\core\Network_Http_Rest')) {
            require_once('rest.php');
            require_once('socket.php');
        }
        $rest = new \sowerphp\core\Network_Http_Rest();
        $rest->setAuth($hash);
        return $rest->$method($url, $data);
    }

    /**
     * Método que valida el RUT ingresado
     * @param rut RUT con guión (puntos son opcionales)
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2016-01-26
     */
    public function checkRut($rut)
    {
        if (!strpos($rut, '-'))
            return false;
        list($rut, $dv) = explode('-', str_replace('.', '', $rut));
		$dv = strtoupper($dv);
        if (!is_numeric($rut))
            return false;
        $real_dv = $this->dv($rut);
        return $dv == $real_dv ? $rut : false;
    }

    /**
     * Calcula el dígito verificador de un RUT
     * @param r RUT al que se calculará el dígito verificador
     * @return Dígito verificar
     * @author Desconocido
     * @version 2010-05-23
     */
    public function dv($r)
    {
    $r = str_replace('.', '', $r);
    $r = str_replace(',', '', $r);
	$factor = 2;
    $suma = 0;
    for($i = strlen($r) - 1; $i >= 0; $i--) {
        $suma += $factor * $r[$i];
        $factor = $factor % 7 == 0 ? 2 : $factor + 1;
    }
    $dv = 11 - $suma % 11;
    $dv = $dv == 11 ? 0 : ($dv == 10 ? "K" : $dv);
    return $dv;
    }

}

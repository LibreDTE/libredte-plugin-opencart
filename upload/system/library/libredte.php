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
            require_once('Rest.php');
            require_once('Socket.php');
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
        if (!is_numeric($rut) or !is_numeric($dv))
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
        $s=1;
        for ($m=0;$r!=0;$r/=10)
            $s=($s+$r%10*(9-$m++%6))%11;
        return strtoupper(chr($s?$s+47:75));
    }

}

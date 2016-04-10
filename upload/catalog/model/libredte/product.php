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
 * Modelo para trabajar con los productos de OpenCart
 * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
 * @version 2016-01-23
 */
class ModelLibredteProduct extends Model
{

    /**
     * Método que obtiene el ID del producto a partir del modelo
     * @param value Valor de la columna por la que se está filtrando
     * @param column Columna por la que se desea filtrar
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2016-01-23
     */
    public function getProductId($value, $column = 'model')
    {
        if (!in_array($column, ['model', 'sku', 'alias']))
            return false;
        if ($column=='alias') {
            $query = $this->db->query('
                SELECT SUBSTR(query, 12) AS product_id
                FROM oc_url_alias
                WHERE
                    keyword = \'' . $this->db->escape($value) . '\'
                    AND query LIKE \'product_id=%\'
            ');
        }
        else {
            $query = $this->db->query('
                SELECT product_id
                FROM ' . DB_PREFIX . 'product
                WHERE ' . $this->db->escape($column) . ' = \'' . $this->db->escape($value) . '\'
            ');
        }
        return $query->num_rows == 1 ? (int)$query->row['product_id'] : false;
    }

}

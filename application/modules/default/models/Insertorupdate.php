<?php

class Default_Model_Insertorupdate extends Zend_Db_Table_Abstract
{
    protected $_primary  = 'id';
    protected $_name     = 'main_hierarchylevels';
    

    /**
     * @param array $insertData
     * @param array $updateData
     * @return int
     * @throws Zend_Db_Adapter_Exception
     */
    public function insertOrUpdate(array $insertData, array $updateData)
    {
        $db    = $this->getAdapter();
        $table = ($this->_schema ? $this->_schema . '.' : '') . $this->_name;

        // extract and quote col names from the array keys
        $i           = 0;
        $bind        = array();
        $insert_cols = array(); $insert_vals = array();
        $update_cols = array(); $update_vals = array();
        foreach (array('insert', 'update') as $type) {
            $data = ${"{$type}Data"};
            $cols = array();
            $vals = array();
            foreach ($data as $col => $val) {
                $cols[] = $db->quoteIdentifier($col, true);
                if ($val instanceof Zend_Db_Expr) {
                    $vals[] = $val->__toString();
                } else {
                    if ($db->supportsParameters('positional')) {
                        $vals[] = '?';
                        $bind[] = $val;
                    } else {
                        if ($db->supportsParameters('named')) {
                            $bind[':col' . $i] = $val;
                            $vals[] = ':col'.$i;
                            $i++;
                        } else {
                            /** @see Zend_Db_Adapter_Exception */
                            require_once 'Zend/Db/Adapter/Exception.php';
                            throw new Zend_Db_Adapter_Exception(get_class($db) ." doesn't support positional or named binding");
                        }
                    }
                }
            }
            ${"{$type}_cols"} = $cols; unset($cols);
            ${"{$type}_vals"} = $vals; unset($vals);
        }

        // build the statement
        $set = array();
        foreach ($update_cols as $i => $col) {
            $set[] = sprintf('%s = %s', $col, $update_vals[$i]);
        }

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s) ON DUPLICATE KEY UPDATE %s;',
                $db->quoteIdentifier($table, true),
                implode(', ', $insert_cols),
                implode(', ', $insert_vals),
                implode(', ', $set)
        );

        // execute the statement and return the number of affected rows
        if ($db->supportsParameters('positional')) {
            $bind = array_values($bind);
        }
        $stmt   = $db->query($sql, $bind);
        $result = $stmt->rowCount();
        return $result;
    }
}
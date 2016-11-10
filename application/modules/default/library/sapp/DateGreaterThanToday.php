<?php

class sapp_DateGreaterThanToday extends Zend_Validate_Abstract
{
    const DATE_INVALID = 'dateInvalid';

    protected $_messageTemplates = array(
        self::DATE_INVALID => "Date should be greater than current date."
    );

    public function isValid($value)
    {
        $this->_setValue($value);

        $today = date('Y-m-d');
        $final_val = sapp_Global::change_date($value, 'database');

        // expecting $value to be YYYY-MM-DD
        if ($final_val <= $today)
		{
            $this->_error(self::DATE_INVALID);
            return false;
        }

        return true;
    }
}
?>
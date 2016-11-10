<?php

class sapp_Htmlhelper 
{    
    /**
     * This function will help in service request view page.
     * @param string $class    = css class to div tag
     * @param string $heading  = heading of the div
     * @param string $value    = value to display.
     */
    public static function request_view_helper($class,$heading,$value)
    {
?>
        <div class="<?php echo $class;?>">
            <label><?php echo $heading;?> <b>:</b> </label>
            <span><?php echo $value; ?></span>
        </div>
<?php 
    }
    
	public static function getExtension($fileName)
	{
		$i = strrpos($fileName,".");
		if (!$i) { return ""; }
		$l = strlen($fileName) - $i;
		$extension = substr($fileName,$i+1,$l);
		return $extension;
	}
}//end of class

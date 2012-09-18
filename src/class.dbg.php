<?php
/**
 * Debug library for PHP
 *
 * @author jds
 * 
 * @TODO	Adjust server level error reporting
 * @TODO	Breakpoints
 * @TODO	Add ability to silence output except for unit tests and failures
 */
 
class dbg
{
    function __construct() {
    	dbg::setNoCache();
    }
    public function msg($message, $die=false, $method='', $exception=false, $file='', $line='')
    {
    	return dbg::dump($message, $die, $method, $exception, $file, $line, false);
    }
    public function dump($var, $die=true, $label='', $exception=false, $file='', $line='', $export=true)
    {
    	if($export)
    		$var = var_export($var,true);
    	$output = "<div class='err'>";
        $label=='' ? print '' : $output .= "<span class='errLabel'>$label</span>: ";
        $output .= "<span class='errDesc'>$var";
        $file=='' ? print '' : $output .= "in file $file";
        $line=='' ? print '' : $output .= "on line $line";
        $output .= "</span></div>";
        
        print $output;
        
        if($exception) throw new Exception ($var);
        
        if($die)
        	die();
        else
       		return $output;
    }
    public function test($term, $die=true) {
    	assert_options(ASSERT_ACTIVE, true);
    	assert_options(ASSERT_WARNING, false);
    	assert_options(ASSERT_BAIL, false);
    	assert_options(ASSERT_QUIET_EVAL, false);
    	 
    	
    	if(assert($term)) {
    			dbg::msg("assertion passed");
    		return true;
    	} else {
    			dbg::msg("assertion failed");
    		if($die)
    			 die();
    		else
    			return false;
    	}
    }
    public function setNoCache () {
    	print "<META HTTP-EQUIV='CACHE-CONTROL' CONTENT='NO-CACHE'>\n<META HTTP-EQUIV='PRAGMA' CONTENT='NO-CACHE'>";
    }
    public function randData($type) {
    	$dataTypes = array('String','Array','Int','Bool','Float','NULL');
    	
    	$func = 'rand'.ucfirst(strtolower($type));
    	
    	
    	if(in_array(ucfirst(strtolower($type)), $dataTypes))
    		return dbg::$func();
    	else
    		die();
    }
    private function randArray($max=100) {
    	$array = array();
    	$arrayLen = rand()%$max;
    	
    	for ($count=0;$count<$arrayLen;$count++) {
    			array_push($array,dbg::randSign()*rand());
    	}
    	return $array;
    }
    private function randInt($max=PHP_INT_MAX) {
		return dbg::randSign()*rand()%$max;
    }
    private function randFloat($max=0) {
    	if($max == 0)
    		$max = mt_getrandmax();
    	return dbg::randSign()*mt_rand() / $max * mt_rand();
    }
    private function randSign() {
    	return pow(-1, rand(0,1));
    }
    private function randBool() {
    	return (bool) rand(0,1);
    }
    private function randString($max=100) {
    	$stringLen = rand()%$max;
    	$string = "";
    	
   		for( $i = 0; $i < $stringLen; $i++ ) {
   			$string .= chr(rand()%255);
   		}
    	
  		return $string;
    }

}

if(isset($_REQUEST['unit'])) {
	require_once('unit.php');
}
?>

<?php

namespace seago\devtools;

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
	private $files;
	private $dirs;
	private $autoloadArray;
	
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
	public function autoload($class) {
		if(file_exists('autoload.json')) {
		 	$this->autoloadArray = json_decode(file_get_contents('autoload.json'), true);
		} else {
			$fileArray= explode("/",$_SERVER['SCRIPT_FILENAME']);
			array_pop($fileArray);
		 	$runPath = implode("/",$fileArray);
			$searchArray = $this->getPath();
			$pathArray=array();
			
		 	foreach($searchArray as $searchLoc) {
		 		$this->searchForClasses($searchLoc,$runPath);
		 	}
		 	
		 	file_put_contents('autoload.json',json_encode($this->autoloadArray));
		}
		
		dbg::dump($this->autoloadArray,false);
		/*
		 * @TODO add ability to check autoload.json for validity and kick off scan on failure
		 */
		require_once($this->autoloadArray[$class]);
	}
	public function dumpDir($searchLoc)
	{
		$dir_test = opendir($searchLoc);
		while (false !== ($entry = readdir($dir_test))) {
			if ($entry=='.' || $entry=='..') {
				//dbg::msg("$entry is either . or ..");
			} else if (!is_dir($searchLoc."/".$entry)) {
				dbg::msg("$searchLoc/$entry is a file");
				$this->files++;
			} else {
				//dbg::msg("$searchLoc/$entry is a directory");
				$this->dirs++;
				$this->dumpDir($searchLoc."/".$entry);
			}
		}
	}
    
	private function searchForClasses($searchLoc,$runPath) {
		$dir = opendir($searchLoc);
		while (false !== ($entry = readdir($dir))) {
			if($entry=='.' || $entry=='..') {
				//dbg::msg("$entry is . or ..");
			} else if (is_dir($searchLoc."/".$entry)) {
				//dbg::msg("$searchLoc/$entry is a directory");
				$this->searchForClasses($searchLoc."/".$entry,$runPath);
			} else {
				//dbg::msg("$searchLoc/$entry is a file");
				if(substr($entry,strlen($entry)-4)==".php") {
					$code = file_get_contents($searchLoc.'/'.$entry);
					//dbg::dump($code);
					if(preg_match_all('/class [a-zA-Z0-9_ ]*\r\n{/',$code,$classes)) {
						//dbg::dump($classes);
						foreach($classes as $file) {
							foreach($file as $instance) {
								// remove class prefix and \r\n{ postfix to get class name
								$class = substr($instance,6,strlen($instance)-6-3);
								//dbg::dump($this->getRelPath($runPath,$searchLoc).$class.".php");
								$this->autoloadArray[$class]=$this->getRelPath($runPath,$searchLoc).$class.".php";
								//return $pathArray;
								//file_put_contents('autoload.json',json_encode($pathArray));
							}
						}
					}
				}
			}
		}
	}
	private function getPath()
	{
		$array = explode("/",$_SERVER['SCRIPT_FILENAME']);
		$filename = array_pop($array);
		if(array_pop($array)=='src') {
			$libPath =implode("/",$array)."/lib";
			$srcPath = implode("/",$array)."/src";
		}
		return array('lib'=>$libPath,'src'=>$srcPath);
	}
	private function getRelPath($run, $search)
	{
		if($run==$search) {
			//dbg::msg("same directory");
			return '';
		}
		else {
			$runArray = explode("/",$run);
			$searchArray = explode("/",$search);
			sizeof($runArray)>=sizeof($searchArray) ? $count = sizeof($searchArray) : $count = sizeof($runArray);
			//dbg::msg("Count: $count;RunSize: ".sizeof($runArray).";SearchSize: ".sizeof($searchArray).";");
			for($i=0;$i<$count;$i++) {
				if($runArray[$i]!=$searchArray[$i])
					$delta = $i;
			}
			
			if(sizeof($runArray)>=sizeof($searchArray)) {
				$relPath = array('..');
				for($i=sizeof($searchArray)-1;$i<=sizeof($runArray)-1;$i++)
					array_push($relPath,$runArray[$i]);
			} else {
				$relPath = array('..');
				for($i=sizeof($runArray)-1;$i<=sizeof($searchArray)-1;$i++) {
					array_push($relPath,$searchArray[$i]);
				}
			}
			return implode("/",$relPath)."/";
		}
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
	//require_once('unit.php');
	$dbg = new dbg();
	$dbg->autoload("dbg");
}
?>

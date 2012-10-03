<?php
/**
 * unit.php: Unit Tests for the Debug library
 * 
 * @TODO	Comment Unit Tests in accordance with PHPDoc standard
 */

namespace seago\devtools;
require_once('../lib/lib.autoload/src/autoload.php');

class dbgTest extends unit {

	public function __construct() {
		$this->unit = new unit(new dbg);
	}
/*
	private $unit;
	private $methods;
	private $depends;
	private $passed;
	
	public function __construct() {	
		print "<style>.errLabel{color:blue;}.errDesc{color:black;}.function{text-align:center;border:2px solid;border-radius:5px;-moz-border-radius:25px; /* Firefox 3.6 and earlier }</style><div>Testing dbg</div>";
		$this->unit = new dbg();
		$this->methods = get_class_methods($this->unit);
		$this->passed = array();
		$this->buildDepends();
		foreach($this->methods AS $method) {
			if(!in_array($method,$this->passed)) {
				if(in_array($method,$this->depends))
				{
					foreach($this->depends[$method] AS $dependancy) {
						if(!in_array($dependancy,$this->passed)) {
							print "<div class='function'>";
							$result = $this->$dependsTest();
							$result ? print "<div>".$dependsTest."() passed all unit tests</div></div><div>&nbsp;</div>" : die($this->dependsTest."() failed.");
						}
					}
				}
				$methodTest = $method."Test";
				print "<div class='function'>";
				$result = $this->$methodTest();
				$result ? print "<div>".$methodTest."() passed all unit tests</div></div><div>&nbsp;</div>" : die($methodTest."() failed.");
			}
			array_push($this->passed, $method);
			
		}
	}
	public function __destruct() {
		$pass=NULL;
		foreach($this->methods AS $method) {
			if($pass==NULL)
				$pass = dbg::test(!in_array($method."Test",$this->passed),"$method did not pass Unit tests.");
			else
				$pass = $pass && dbg::test(!in_array($method."Test",$this->passed),"$method did not pass Unit tests.");
		}
		$pass ? print "<div>dbg passed all unit tests</div><div>&nbsp;</div>": "";
	}
	private function buildDepends() {
		$unitTags = $this->unit->getUnitTags($_SERVER['SCRIPT_FILENAME']);
		//dbg::dump($unitTags);
		foreach($unitTags AS $method=>$comments) {
			//$depends = array();
			$temp = array();
			foreach($comments AS $comment) {
				foreach($comment AS $tag=>$value) {
					if($tag=='@depends') {
						array_push($temp,$value);
					}
				}
			}
			//dbg::dump($method);
			$this->depends[$method]=$temp;
		}
		return true;
	}
	*/
	public function __constructTest() {
		$pass = $this->test(is_array($this->unit->commentTags),"dbg.commentTags is not an array");
		return $pass;
	}
	/**
	 * @getComments	true
	 * @depends	__constructTest
	 */
	public function getCommentTagsTest() {
		$test = $this->unit->getCommentTags($_SERVER['SCRIPT_FILENAME']);
		$isArray = $this->test(is_array($test),"getCommentTags did not return an array.");
		foreach($test["getCommentTagsTest"] AS $comment) {
			foreach($comment AS $tag=>$value) {
				if($tag=='@getComments' && $value==true)
					$valueFound = true;
			}
		}
		$valueFound = $this->test($valueFound, "@getComments was not found in unit->getCommentsTags array.");
		return ($isArray && $valueFound);
		
	}
	/**
	 * @depends	__constructTest
	 */
	public function randDataTest() {
		$data = array (
				"array" => dbg::randData('array'),
				"string" => dbg::randData('string'),
				"integer" => dbg::randData('integer'),
				"bool" => dbg::randData('bool'),
				"double" => dbg::randData('double')
		);
		$pass=NULL;
		foreach($data AS $type => $value) {
			$func = 'is_'.$type;
			//dbg::dump("$func($value): ".$func($value), false);
			//dbg::dump(gettype($value),false);
			if($pass==NULL)
				$pass = dbg::test($func($value), "$value is of type ".gettype($value));
			else
				$pass = $pass && dbg::test($func($value), "$value is of type ".gettype($value));
		}
		return $pass;
	}
	/**
	 * @depends	__constructTest
	 */
	public function msgTest() {
		return true;
	}
	/**
	 * @depends	__constructTest
	 */
	public function dumpTest() {
		return true;
	}
	/**
	 * @depends	__constructTest
	 */
	public function testTest() {
		return true;
	}
	/**
	 * @depends	__constructTest
	 */
	public function setNoCacheTest() {
		return true;
	}
	/**
	 * @depends	__constructTest
	 */
	public function getUnitTagsTest() {
		return true;
	}
}

new dbgTest();
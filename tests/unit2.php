<?php
/**
 * Do not scan
 * @TODO	rewrite UnitTest class to resemble PHPUnit tests
 * @TODO	Comment Unit Tests in accordance with PHPDoc standard
 * @expectedExceptionMessage	Test Exception
 */

namespace seago\devtools;
require_once('../src/dbg.php');

class dbgTest extends dbg {

	private $unit;
	private $depends;
	private $passed;
	private $methodList;
	
	public function __construct() {
		print "<style>.errLabel{color:blue;}.errDesc{color:black;}.function{text-align:center;border:2px solid;border-radius:5px;-moz-border-radius:25px; /* Firefox 3.6 and earlier */}</style>";
		$this->unit = new dbg();
		$this->methodList = get_class_methods($this->unit);
		$this->passed = array();
		$this->depends = $this->buildDepends();
		foreach($this->methodList AS $method) {
			if(!in_array($method,$this->passed)) {
				if(in_array($method,$this->depends))
				{
					foreach($this->depends[$method] AS $dependancy) {
						if(!in_array($dependancy,$this->passed)) {
							print "<div class='function'>";
							$this->$dependsTest();
							print "<div>".$dependsTest."() passed all unit tests</div></div><div>&nbsp;</div>";
						}
					}
				}
				$methodTest = $method."Test";
				print "<div class='function'>";
				$this->$methodTest();
				print "<div>".$methodTest."() passed all unit tests</div></div><div>&nbsp;</div>";
			}
			array_push($this->passed, $method);
			
		}
		//$this->randDataTest();
	}
	public function __destruct() {
		print "<div>dbg passed all unit tests</div><div>&nbsp;</div>";
	}
	private function buildDepends() {
		$unitTags = $this->unit->getUnitTags($_SERVER['SCRIPT_FILENAME']);
	
		foreach($unitTags AS $method=>$comments) {
			$depends = array();
			$temp = array();
			foreach($comments AS $comment) {
				foreach($comment AS $tag=>$value) {
					if($tag=='@depends') {
						//	dbg::dump($value);
						array_push($temp,$value);
					}
				}
			}
			$depends[$method]=$temp;
		}
		return $depends;
	}
	
	public function __constructTest() {
		print "<div class='function'>";
		$this->test(is_array($this->unit->commentTags),"dbg.commentTags is not an array");
		print "<div>".__METHOD__."() passed all unit tests</div></div><div>&nbsp;</div>";
		
	}
	/**
	 *	@depends __constructTest 
	 */
	public function getCommentTagsTest() {
		print "<div class='function'>";
		$this->unit->getCommentTags($_SERVER['SCRIPT_FILENAME']);
		$this->test(true);
		print "<div>".__METHOD__."() passed all unit tests</div></div><div>&nbsp;</div>";
	}
	public function randDataTest() {
		print "<div class='function'>";
		$data = array (
				"array" => dbg::randData('array'),
				"string" => dbg::randData('string'),
				"int" => dbg::randData('int'),
				"bool" => dbg::randData('bool'),
				"float" => dbg::randData('float')
		);
		foreach($data AS $type => $value) {
			$func = 'is_'.$type;
			$this->test($func($value),"testing randData($type)");
		}
		print "<div>".__METHOD__."() passed all unit tests</div></div><div>&nbsp;</div>";
	}
	public function msgTest() {
		return true;
	}
	public function dumpTest() {
		return true;
	}
	public function testTest() {
		return true;
	}
	public function setNoCacheTest() {
		return true;
	}
	public function getUnitTagsTest() {
		return true;
	}
}

new dbgTest();
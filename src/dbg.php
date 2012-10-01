<?php
/**
 * Lib.dbg: Debug library for PHP
 * 
 * @name		lib.dbg
 * @package		seago\devtools
 * @author 		Jeremy Seago <seagoj@gmail.com>
 * @copyright 	Copyright (c) 2012, Jeremy Seago
 * @license		http://opensource.org/licenses/mit-license.php
 * @version 	1.0
 * @link		https://github.com/seagoj/lib.dbg
 *
 * @TODO	Comment dbg class in accordance to PHPDoc standard
 * @TODO	Looking into adding detailed info to message on test() failure
 * @TODO	Adjust server level error reporting
 * @TODO	Breakpoints
 * @TODO	Add ability to silence output except for unit tests and failures
 */

namespace seago\devtools;

/**
 * dbg Class
 * Library of debug tools for PHP development
 *
 * @package		seago\devtools
 * @subpackage	dbg
 * @author		Jeremy Seago	<seagoj@gmail.com>
 * @version		1.0
 * @access		public
 * @public		__construct()
 * @public		msg(string,[bool],[string],[bool],[string],[int])
 * @public		dump(string,[bool],[string],[bool],[string],[int],[bool])
 * @public		test(bool,[bool])
 * @public		setNoCache();
 * @public		randData(string)
 */
class dbg
{
	protected $commentTags;
	
    public function __construct()
    {
    	$this->commentTags = array();
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
    public function test($term, $failMsg='', $die=true)
    {
        assert_options(ASSERT_ACTIVE, true);
        assert_options(ASSERT_WARNING, false);
        assert_options(ASSERT_BAIL, false);
        assert_options(ASSERT_QUIET_EVAL, false);

        if (assert($term)) {
                //dbg::msg($msg);
            return true;
        } else {
                dbg::msg("assertion failed: $failMsg");
            if($die)
                 die("Assertion failed");
            else
                return false;
        }
    }
    public function setNoCache ()
    {
        print "<META HTTP-EQUIV='CACHE-CONTROL' CONTENT='NO-CACHE'>\n<META HTTP-EQUIV='PRAGMA' CONTENT='NO-CACHE'>";
    }
    public function randData($type)
    {
    	$data = new randData();
    	$dataTypes = array('String','Array','Int','Bool','Float','NULL');
    	$func = 'rand'.ucfirst(strtolower($type));
    	
    	if(in_array(ucfirst(strtolower($type)), $dataTypes))
    		return $data->$func();
    	else
    		die();
    }
	
    public function getCommentTags($filename,$type=''){
    	$this->scanCommentsForTags($filename,$type);
    	return $this->commentTags;
    }
    public function getUnitTags($filename) {
    	$this->scanCommentsForTags($filename);
    	$unitTags = array(
    			'@expectedException',
    			'@expectedExceptionCode',
    			'@expectedExceptionMessage',
    			'@dataProvider',
    			'@depends',
    			);
    	$return = array();
    	foreach($this->commentTags AS $method=>$values) {
    		//dbg::dump($this->commentTags);
    		foreach($values AS $value) {
    			//dbg::dump($values);
    			foreach($value AS $tag=>$comment) {
    				if(in_array($tag,$unitTags)){
						$return = array($method=>array(array("$tag"=>"$comment")));
    				}
    			}
    		}
    	}
    	return $return;
    } 
	private function scanCommentsForTags($filename='dbg.php', $type='') {
		$source = file_get_contents($filename);
		
		$tokens = token_get_all($source);
		$comment = array(
				T_COMMENT,      // All comments since PHP5
				T_DOC_COMMENT	// PHPDoc comments	
		);
		$commentTags = array();
		$fuctionTags = array();
		$commentsFound = false;
		$functionFound = false;
		//dbg::dump($tokens);
		foreach( $tokens as $token ) {
			
			if(in_array($token[0],$comment)) {
				preg_match_all('/@[a-zA-Z0-9 \t_]*/',$token[1],$matches);
				foreach($matches[0] AS $match) {
					if(strpos($match,"\t")&&(strpos($match,"\t")<strpos($match," "))) {
						if(strpos($match,"\t")>0) {
							$pivot = strpos($match,"\t");
							//dbg::msg("tab");
						} else
							dbg::dump("not found");
					} else if(strpos($match," ")>0) {
						$pivot = strpos($match," ");
						//dbg::msg("space");
					} else
						dbg::dump("not found");
					
					$tag = substr($match,0,$pivot);
					$value = substr($match,$pivot+1);
					array_push($commentTags, array($tag=>$value));
					$commentsFound = true;
				}
			} else if(($token[0]==T_FUNCTION ||$token[0]==T_CLASS)&& $commentsFound) {
				$functionFound = true;
				$commentsFound = false;
			} else if($token[0]==307 && $functionFound) {
				while($tag = array_pop($commentTags)) {
					//dbg::dump($tag,false);
					$this->commentTags[$token[1]] = array($tag);
				}
				$functionFound = false;
			}
		}
		//dbg::dump($this->commentTags);
	}
}

class randData
{
	public function __construct() {}
	
	public function randArray($max=100)
	{
		$array = array();
		$arrayLen = rand()%$max;
	
		for ($count=0;$count<$arrayLen;$count++) {
			array_push($array,randData::randSign()*rand());
		}
	
		return $array;
	}
	public function randInt($max=PHP_INT_MAX)
	{
		return randData::randSign()*rand()%$max;
	}
	public function randFloat($max=0)
	{
		if($max == 0)
			$max = mt_getrandmax();
	
		return randData::randSign()*mt_rand() / $max * mt_rand();
	}
	public function randSign()
	{
		return pow(-1, rand(0,1));
	}
	public function randBool()
	{
		return (bool) rand(0,1);
	}
	public function randString($max=100)
	{
		$stringLen = rand()%$max;
		$string = "";
	
		for ($i = 0; $i < $stringLen; $i++) {
			$string .= chr(rand()%255);
		}
	
		return $string;
	}
}

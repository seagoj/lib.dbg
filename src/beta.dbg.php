<?php

class beta {
    private $code;
    private $vars;

    function __construct($page) {
        //require_once($page);
        $this->code = file_get_contents($page);
        $this->getVars();
        $this->getVals();
    }
    function __destruct() {}

    private function getVars() {
        $sections = explode('$',$this->code);
        $variables = array();
        foreach($sections as $section)
        {
            $length = strlen($section);
            $invalid = false;
            $i=0;
            $variable = '';
            
            while(!$invalid) {
                $char = substr($section, $i++, 1);
                if(ctype_alnum($char) || $char=='_')
                    $variable .= $char;
                else
                    $invalid=true;
            }
            if(!in_array($variable, $variables, true) && strlen($variable)>0)
                array_push($variables, $variable);
        }
        $this->vars = $variables;
    }
    private function getVals() {
    }
    public function setBreak($vars)
    {
        var_dump($vars);
        die();
    }

}

//$debug = new beta('class.dbg.php');
?>

<?php
/**
 * Description of class
 *
 * @author jds
 */
 
class dbg
{
    function __construct()
    {}
    public function msg($message, $method='', $exception=false, $file='', $line='')
    {
        print "<div class='err'>";
        $method=='' ? print '' : print "<span style='color:red;'>$method</span>: ";
        print "<span style='color:black'>$message";
        $file=='' ? print '' : print "in file $file";
        $line=='' ? print '' : print "on line $line";
        print "</span></div>";
        if($exception) throw Exception ($msg);
    }
    public function vardump($var, $label='')
    {
        $dump = var_export($var, true);
        print "<div class='err'>";
        $label=='' ? print '' : print "<span style='color:red;'>$label</span>: ";
        print "<span style='color:black;'>$dump</span></div>";
    }
    public function test($term, $method='', $fail='false') {
        assert_options(ASSERT_ACTIVE, true);
        assert_options(ASSERT_WARNING, true);
        assert_options(ASSERT_BAIL, false);
        assert_options(ASSERT_QUIET_EVAL, false);
        assert_options(ASSERT_CALLBACK, 'testFail');

        assert($term);
    }
    private function testFail() {
        dbg::msg("Assertion Failed");
    }
    public function setNoCache() {
        print "<META HTTP-EQUIV='CACHE-CONTROL' CONTENT='NO-CACHE'>\n<META HTTP-EQUIV='PRAGMA' CONTENT='NO-CACHE'>";
    }
}
?>

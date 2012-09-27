<?php
require_once('class.dbg.php');
dbg::setNoCache();

// Test Data
$data = array (
			"array" => dbg::randData('array'),
			"string" => dbg::randData('string'),
			"int" => dbg::randData('int'),
			"bool" => dbg::randData('bool'),
			"float" => dbg::randData('float')
		);

print "<style>.errLabel{color:blue;}.errDesc{color:black;}.function{text-align:center;border:2px solid;border-radius:5px;-moz-border-radius:25px; /* Firefox 3.6 and earlier */}</style>";

/*TESTING: dbg::randData()**********************************************/
print "<div class='function'>";
foreach($data as $type=>$value) {
	print "<div class='test'>";
	dbg::msg("dbg::randData($type): ");
	$func = "is_".$type;
	dbg::test($func(dbg::randData($type)));
	print "</div>";
}
dbg::msg("dbg::randData() passed all unit tests");
print "</div><div>&nbsp;</div>";
/**********************************************************************/

/*TESTING: dbg::test()**************************************************/
print "<div class='function'>";
dbg::msg("dbg::test(true): ");
assert(dbg::test(true,false)) or die ();
dbg::msg("dbg::test(false): ");
assert(!dbg::test(false,false)) or die();
dbg::msg("dbg::test() passed all unit tests");
print "</div><div>&nbsp;</div>";
/**********************************************************************/

/*TESTING: dbg::msg()**************************************************/
print "<div class='function'>";
foreach($data as $type=>$value) {
	print "<div class='test'>";
	if($type == 'array')
		$value = var_export($value,true);
	dbg::test(dbg::msg($value,false,"dbg::msg(($type) $value)")=="<div class='err'><span class='errLabel'>dbg::msg(($type) $value)</span>: <span class='errDesc'>$value</span></div>");
	print "</div>";
}
print "<div>dbg::msg() passed all unit tests</div></div><div>&nbsp;</div>";
/**********************************************************************/

/*TESTING: dbg::dump()*************************************************/
print "<div class='function'>";
foreach($data as $type=>$value) {
	print "<div class='test'>";
	$value = var_export($value,true);
	dbg::test(dbg::dump($data[$type],false,"dbg::dump(($type) $value)")=="<div class='err'><span class='errLabel'>dbg::dump(($type) $value)</span>: <span class='errDesc'>$value</span></div>");
	print "</div>";
}
print "<div>dbg::dump() passed all unit tests</div></div><div>&nbsp;</div>";
/**********************************************************************/


print "</div><div>class.dbg passed all unit tests</div><div>&nbsp;</div>";
?>
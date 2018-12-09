<?php

include "template.php";

$tpl = new Template();
$tpl->load("index.tpl");

$langs[] = "lang.php";
$lang = $tpl->loadLanguage($langs);

$tpl->assign( "website_title", "MyHomepage" );
$tpl->assign( "time", date("H:i") );
$tpl->assign( "test", $lang['test'] );

$tpl->display();

?>
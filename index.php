<?php
require_once dirname(__FILE__).'/AutoloadPackage.php';

$savant = new \PEAR2\Templates\Savant\Main();

echo $savant->render('I did it. Do you think I\'ve gone too far?');
?>

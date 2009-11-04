<?php
require_once 'AutoloadPackage.php';

$savant = new \pear2\Templates\Savant\Main();

echo $savant->render('I did it. Do you think I\'ve gone too far?');
?>
<?php 

include ('include/header.php');




$html = file_get_contents("pages/index.html");
$html = str_replace('{menufarmacia}', $menufarmacia, $html);

echo $html;

?>



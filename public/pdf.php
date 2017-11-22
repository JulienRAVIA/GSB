<?php
require_once '../vendor/autoload.php';
use App\Utils\Session;

$mpdf = new \Mpdf\Mpdf();
$source = file_get_contents('http://gsb.ppe/frais/pdf/a17/201708');
$mpdf->WriteHTML($source);
$mpdf->CSSselectMedia='mpdf';
$mpdf->Output();
// var_dump($source);
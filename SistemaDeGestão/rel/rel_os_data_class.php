<?php
require_once '../dompdf/autoload.inc.php';
use Dompdf\Dompdf;

$dataInicial = $_POST['txtdataInicial'];
$dataFinal = $_POST['txtdataFinal'];
$status = $_POST['status'];

if($dataInicial == ''){
    $dataInicial = Date('Y-m-d');
}
if($dataFinal == ''){
    $dataFinal = Date('Y-m-d');
}

// Caminho correto para seu projeto:
$html = file_get_contents("http://localhost/sistema_esof/rel/rel_os_data.php?dataInicial=".$dataInicial."&dataFinal=".$dataFinal."&status=".$status);

// Teste de depuração
// echo $html; exit();

$pdf = new Dompdf();
$pdf->set_option('isRemoteEnabled', true);
$pdf->setPaper('A4', 'portrait');
$pdf->loadHtml($html);
$pdf->render();
$pdf->stream('relatorioOSData.pdf', array("Attachment" => false));
?>

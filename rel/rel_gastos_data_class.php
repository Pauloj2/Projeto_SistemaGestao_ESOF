<?php
require_once '../dompdf/autoload.inc.php';
use Dompdf\Dompdf;

$dataInicial = $_POST['txtdataInicial'] ?? date('Y-m-d');
$dataFinal = $_POST['txtdataFinal'] ?? date('Y-m-d');
$status = $_POST['status'] ?? 'Todas';

// Passa os dados como se fossem GET
$_GET['dataInicial'] = $dataInicial;
$_GET['dataFinal'] = $dataFinal;
$_GET['status'] = $status;

// Usa include para capturar HTML
ob_start();
include '../rel/rel_gastos_data.php';
$html = ob_get_clean();

$pdf = new Dompdf();
$pdf->set_option('isRemoteEnabled', true); // se tiver imagens externas
$pdf->setPaper('A4', 'portrait');
$pdf->loadHtml($html);
$pdf->render();
$pdf->stream('relatorioGastosData.pdf', array("Attachment" => false));
?>

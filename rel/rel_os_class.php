<?php
require_once '../dompdf/autoload.inc.php';
use Dompdf\Dompdf;

$id = $_GET['id'];
$id_orc = $_GET['id_orc'];


if (!$id) {
    die("ID não especificado.");
}

// Caminho correto para seu projeto:
$html = file_get_contents("http://localhost/sistema_esof/rel/rel_os.php?id=".$id."&id_orc=".$id_orc);

// Teste de depuração
// echo $html; exit();

$pdf = new Dompdf();
$pdf->set_option('isRemoteEnabled', true);
$pdf->setPaper('A4', 'portrait');
$pdf->loadHtml($html);
$pdf->render();
$pdf->stream('relatorioOS.pdf', array("Attachment" => false));
?>



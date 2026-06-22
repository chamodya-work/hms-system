<?php
require_once __DIR__ . '/vendor/autoload.php';
include("connection/connect.php");

function printMaintenanceReport($content)
{
    $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
    $fontDirs = $defaultConfig['fontDir'];

    $fontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
    $fontData = $fontConfig['fontdata'];

    $mpdf = new \Mpdf\Mpdf([
        'fontDir' => array_merge($fontDirs, [
            __DIR__ . '/fonts', // your font folder
        ]),
        'fontdata' => $fontData + [
            'notosinhala' => [
                'R' => 'NotoSansSinhala-Regular.ttf',
                'B' => 'NotoSansSinhala-Regular.ttf',
            ]
        ],
        'default_font' => 'notosinhala'
    ]);

    $stylesheet = "
        body { font-family: notosinhala; }
        table { border-collapse: collapse; width:100%; }
        th { background-color: #333; color: #fff; padding:8px; }
        td { text-align: center; padding:6px; border:1px solid #ddd; }
    ";

    $content = mb_convert_encoding($content, 'UTF-8', 'auto');

    $mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);
    $mpdf->WriteHTML($content);

    $mpdf->Output("report.pdf", "I");
}

if (isset($_POST['export_pdf'])) {
    $html = htmlspecialchars_decode($_POST['html']);
    printMaintenanceReport($html);
    exit;
}

if (isset($_POST['review_request'])) {
    
    $rep_id = $_POST['rep_id'];
    $review = $_POST['review'];

    $query = "UPDATE repairs SET review = '$review' WHERE rep_id = '$rep_id'";
    $result = $conn->query($query);
    // go back to the repair-student-view.php page after submitting the review
    echo "<script>window.location='repair-student-view.php';</script>";

    // Process the review submission (e.g., update the database)
}
?>
<?php
 
require_once 'vendor/autoload.php';
$defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
$fontData = $defaultFontConfig['fontdata'];

$mpdf = new \Mpdf\Mpdf(['tempDir' =>  __DIR__ .'/tmp',
    'fontdata' => $fontData + [
            'sarabun' => [ 
                'R' => 'THSarabunNew.ttf',
                'I' => 'THSarabunNew Italic.ttf',
                'B' =>  'THSarabunNew Bold.ttf',
                'BI' => "THSarabunNew BoldItalic.ttf",
            ]
        ],
]);
?>
 
<?php
$mpdf->AddPage('P','','','','',10,10,10,10,10,10);

$html .= "<style> body { font-family: 'Sarabun'; font-size: 19px;} .tbl-collaps {border-collapse: collapse;} </style>";
$html .= $htmlPDF;

// $stylesheet = file_get_contents(base_url('asset/bootstrap-3.3.7-dist/css/bootstrap.min.css'));
// $mpdf->WriteHTML($stylesheet,1);
$mpdf->WriteHTML($html);
$mpdf->Output();

/* $yearthai = date('Y') + 543;
$year = substr($yearthai,2);
echo "year : ".$yearthai." = ".$year; */
?>
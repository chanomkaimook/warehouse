<?php
require_once 'vendor/autoload.php';
/* $defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
$fontData = $defaultFontConfig['fontdata']; */

/* $mpdf = new \Mpdf\Mpdf(['tempDir' =>  __DIR__ .'/tmp',
    'fontdata' => $fontData + [
            'sarabun' => [ 
                'R' => 'THSarabunNew.ttf',
                'I' => 'THSarabunNew Italic.ttf',
                'B' =>  'THSarabunNew Bold.ttf',
                'BI' => "THSarabunNew BoldItalic.ttf",
            ]
        ],
]); */
$mpdf = new \Mpdf\Mpdf();

// $mpdf->AddPage('P','','','','',10,10,10,10,10,10);

$stylesheet  = file_get_contents('test.php');
// $html  = $mpdf->importPage('test.php');

$html .= "<style> body { font-family: 'Garuda'; font-size: 19px;} .tbl-collaps {border-collapse: collapse;} </style>";
// $html .= "<p>Hello lorism สวัสดี</p>";
$html .= "<div class='contrainer'><div class='row d-flex'><div class='col-md-6'> test </div><div class='col-md-6'> test 99 นะครับ </div></div></div>";




// $mpdf->SetHTMLHeader('เอกสารรับเข้า');
// $mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);
$mpdf->WriteHTML($html);

$mpdf->Output();

?>
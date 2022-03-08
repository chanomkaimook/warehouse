<?php
// include composer autoload
require_once 'vendor/autoload.php';
 
// เรียกใช้งานสร้าง qrcode โดยสร้าง qrcode 
// ข้อควม http://www.ninenik.com
// บันทึกเป็นไฟล์ ชื่อ myqrcode.png ไว้ในโฟลเดอร์ images / picqrcode / myqrcode.png 
// กำหนด Error Correction ของ QRcode เท่ากับ L  (มีค่า L,M,Q และ H)
// กำหนด ขนาด pixel เท่ากับ 4
// กำหนดความหนาของกรอบ เท่ากับ 2
\PHPQRCode\QRcode::png("http://www.farmchokchaisport.com?bib=1111&event=cnr", "temp/myqrcode.png", 'L', 4, 2);
?>
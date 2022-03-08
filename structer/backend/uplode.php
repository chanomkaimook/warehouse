<?php
    $config['upload_path'] = './upload/';  // โฟลเดอร์ ตำแหน่งเดียวกับ root ของโปรเจ็ค
    $config['allowed_types'] = 'gif|jpg|png'; // ปรเเภทไฟล์ 
    $config['max_size']     = '0';  // ขนาดไฟล์ (kb)  0 คือไม่จำกัด ขึ้นกับกำหนดใน php.ini ปกติไม่เกิน 2MB
    $config['max_width'] = '1024';  // ความกว้างรูปไม่เกิน
    $config['max_height'] = '768'; // ความสูงรูปไม่เกิน
    $config['file_name'] = 'mypicture';  // ชื่อไฟล์ ถ้าไม่กำหนดจะเป็นตามชื่อเพิม
?>
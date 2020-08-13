<?php
ob_start();
$counter = $_POST['counter_id'];
sleep(1);
check($counter);
ob_flush();
ob_end_flush();
function check($number){ 
    if($number % 2 == 0){ 
        echo $number . "<BR>";  
    } 
    else{ 
        echo $number . "Certificate generation failed<BR>";  
    } 
} 
?>
<?php
ini_set("display_errors","1");
ERROR_REPORTING(E_ALL);
require_once 'fpdf.php';
session_start();

function generateCertificate ($id, $name, $subject, $by, $on) {
	#header ("Content-type: image/png");
	$tmpPNGFile = "certs/" . $id . "_certificate.png";
	$tmpPDFFile = "certs/" . $id . "_certificate.pdf";
	// Set the environment variable for GD
	putenv('GDFONTPATH=' . realpath('.'));
	// Name the font to be used (note the lack of the .ttf extension)
	#$font = 'arial.ttf';
	$font = 'MonospaceTypewriter.ttf';
	$bigsize = 40;
	$smallsize = 30;
	$pad_length = 41;
	$pad_length_small = 53;
	$pad_type = STR_PAD_BOTH;
	$pad_string = " ";
	
	#Elements to embed
	#$name = "BIJU ABRAHAM";
	#$name = "01234567890123456789012345678901234567890123";
	$name = str_pad ( $name , $pad_length , $pad_string , $pad_type);
	
	#$subject = "Orthodontics Dental Class";
	#$subject = "01234567890123456789012345678901234567890123";
	$subject = str_pad ( $subject , $pad_length , $pad_string , $pad_type);
	
	#$by = "By " . "Dr. Joe Antony";
	#$by = "012345678901234567890123456789012345678901234567890123456789";
	$by = str_pad ( $by , $pad_length_small , $pad_string , $pad_type);
	
	#$on = "On " . "August 8, 2020";
	#$on = "012345678901234567890123456789012345678901234567890123456789";
	$on = str_pad ( $on , $pad_length_small , $pad_string , $pad_type);
	
	# Image handling
	$img_handle = imageCreateFromPNG("certbgA4.png");
	$blue = imagecolorallocate($img_handle, 0, 0, 255);
	$black = imagecolorallocate($img_handle, 0, 0, 0);
	$red = imagecolorallocate($img_handle, 200, 45, 10);
	$color = ImageColorAllocate ($img_handle, 0, 0, 0);
	#imagettftext ( $image , $size , $angle , int $x , int $y , int $color , string $fontfile , string $text ) : array
	imagettftext ( $img_handle , $bigsize , 0 , 0 , 680 , $blue , $font , $name );
	imagettftext ( $img_handle , $bigsize , 0 , 0 , 880 , $red , $font , $subject );
	imagettftext ( $img_handle , $smallsize , 0 , 0 , 940 , $black , $font , $by );
	imagettftext ( $img_handle , $smallsize , 0 , 0 , 1000 , $black , $font , $on );
	#Destroy
	ImagePng ($img_handle, $tmpPNGFile);
	#Convert
	$pdf = new FPDF();
	$pdf->AddPage('P','A4');
	$pdf->SetAuthor('Online Dentistry');
	$pdf->SetTitle('Online Dentistry Certificate');
	$pdf->Image($tmpPNGFile,5,0,200);
	$pdf->Output($tmpPDFFile,'F');
	$pdf->close();
	
	#Destroy
	
	ImageDestroy ($img_handle);
	if (file_exists ( $tmpPDFFile )) {
		return TRUE;
	 } else {
		return FALSE;
	 }
}



?>
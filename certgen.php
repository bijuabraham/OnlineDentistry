<?php
ini_set("display_errors","0");
ERROR_REPORTING(E_ALL);
require_once 'fpdf.php';
session_start();
putenv('GDFONTPATH=' . realpath('.'));
define('FPDF_FONTPATH',realpath('.'));
function generateCertificate ($id, $name, $subject, $by, $on) {
	$tmpPDFFile = "certs/" . $id . "_certificate.pdf";
	// Set the environment variable for GD
	// Name the font to be used (note the lack of the .ttf extension)
	#$font = 'arial.ttf';
	$font = 'monospacetypewriter';
	$bigsize = 20;
	$smallsize = 16;
	$pad_length = 43;
	$pad_length_small = 52;
	$pad_type = STR_PAD_BOTH;
	$pad_string = " ";
	
	#Elements to embed
	#$name = "BIJU ABRAHAM";
	#$name = "0123456789012345678901234567890123456789012";
	$name = str_pad ( $name , $pad_length , $pad_string , $pad_type);
	
	#$subject = "Orthodontics Dental Class";
	#$subject = "0123456789012345678901234567890123456789012";
	$subject = str_pad ( $subject , $pad_length , $pad_string , $pad_type);
	
	#$by = "By " . "Dr. Joe Antony";
	#$by = "0123456789012345678901234567890123456789012345678901";
	$by = str_pad ( $by , $pad_length_small , $pad_string , $pad_type);
	
	#$on = "On " . "August 8, 2020";
	#$on = "0123456789012345678901234567890123456789012345678901";
	$on = str_pad ( $on , $pad_length_small , $pad_string , $pad_type);
	
    # PDF handling
    $pdf = new FPDF();
	$pdf->AddPage('P','A4');
	$pdf->SetAuthor('Online Dentistry');
    $pdf->SetTitle('Online Dentistry Certificate');
    $pdf->Image("certbgA4.png",5,0,200);
    $pdf->AddFont($font);
    $pdf->SetFont($font, "", $bigsize);
    $pdf->SetTextColor(0,0,255); #blue
    $pdf->SetXY(18, 105);
    $pdf->Write(1,$name);
    $pdf->SetTextColor(200,45,10); #red
    $pdf->SetXY(18, 135);
    $pdf->Write(1,$subject);
    $pdf->SetFont($font, "", $smallsize);
    $pdf->SetTextColor(0,0,0); #black
    $pdf->SetXY(18, 150);
    $pdf->Write(1,$by);
    $pdf->SetXY(18, 160);
    $pdf->Write(1,$on);
	$pdf->Output($tmpPDFFile,'F');
    $pdf->close();
	if (file_exists ( $tmpPDFFile )) {
		return TRUE;
	 } else {
		return FALSE;
	 }
}



?>
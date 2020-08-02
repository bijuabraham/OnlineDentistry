<?php
ini_set("display_errors","1");
ERROR_REPORTING(E_ALL);
require_once 'mailer.php';
require_once 'certgen.php';
require_once 'od_utils.php';
od_authenticate();
od_header();
od_top_menu();
generateCertificate ("Biju Abraham", "Orthodontics Cource", "Dr. Binu Abraham", "August 8, 2020");
?>
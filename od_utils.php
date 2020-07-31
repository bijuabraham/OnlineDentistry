<?php
/*
 * File: od_utils.php
 * version: bijuabraham
 */

include ('od_config.php');
include ('od_dbutils.php');

/*
 * check if user is an admin user
 */
function user_admin($user)
{
    return ($user === ADMIN || $user === ADMIN1 || $user === TEMPORARY);
}

/*
 * Check user credentials and allow or disallow access.
 * If access is denied, redirect to login page
 */
function od_authenticate() 
{
    if (!isset($_SESSION['user'])) {
        $page = $_SERVER['REQUEST_URI'];
        if (!isset($page) || trim($page) === '') {
            header("Location:login.php");
        } else {
            header("Location:login.php?ret=$page");
        }
        exit();
    }
}

/*
 * display a login form in-place and
 * Check user credentials and allow or disallow access.
 */
function od_login_form($redir_page) 
{
    if (!isset($_SESSION['user'])) {
        ?>
    <link href="my_style.css" rel="stylesheet" type="text/css" />
    <div id="centerbox">
        <?php
        if (isset($_SESSION['LOGIN_ERRMSG'])) {
            echo "<p><strong>" . $_SESSION['LOGIN_ERRMSG'] . "</strong></p>";
        }
        ?>
      <p><em>Please login with your Online Dentistry Admin credentials</em></p>
      <form name="loginform" method="post" action="login-check.php">
        <input type="hidden" name="redir_page" value='<?php echo "$redir_page";?>'/> 
        <table width="300" border="0" cellpadding="2" cellspacing="0">
        <tr>
          <td><b>Email</b></td>
          <td><input name="email" type="text" id="email"/></td>
        </tr>
        <tr>
          <td><b>Password</b></td>
          <td><input name="pwd" type="password" id="pwd"/></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td><input type="submit" name="Submit" value="Login" /></td>
        </tr>
        <tr>
          <?php
          unset($_SESSION['LOGIN_ERRMSG']);
          echo "<td colspan='2'><a href='$redir_page'>"
               . "Proceed without Portal signon</a></td>";
          ?>
        </tr>
        </table>
      </form>
    </div>
<?php
    } 
}

function wp_header()
{
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Online Dentistry Admin Portal</title>
</style>
<body>
<?php
}

function wp_footer()
{
?>
            <div id="footerline"></div>
            <div id="footer">
            <?php
                if (isset($_SESSION['user'])) {
                    if (user_admin($_SESSION['user'])) {
                        echo "<a href='bdwa.php'>Birthdays/Wedding Ann</a>";
                        echo " | <a href='admin.php'>Admin Page</a><br><br>";
                    }
                    echo "Logged as " . $_SESSION['user'] 
                         .  " | <a href='settings.php'>Settings</a>"
                         .  " | <a href='logout.php'>Logout</a>";
                }
            ?>
            </div>
        </div>
    </div> 
</body>
</html>
<?php
}


function od_header()
{
    //od_header_old();
    //wp_header();
    od_simple_header();
}

function od_footer()
{
    //od_footer_old();
    //wp_footer();
    od_simple_footer();
}

function new_wp_header()
{
?>
<body class="page-template page-template-full-width page-template-full-width-php page page-id-113">

<div class="header">

        <div class="header-inner">

                        <div class="logo">

                                                <a href="http://sfmtc.org/">

                                                                                                           <img src="http://sfmtc.org/wp-content/uploads/2019/01/webtoplogo.png" / >

                                                                                                                                                               </a>

                                                                                                                                                                                </div><!-- logo -->

                                                                                                                                                                                                                                  <div class="toggle">

                                                                                                                                                                                                                                                  <a class="toggleMenu" href="#">Menu</a>

                                                                                                                                                                                                                                                                  </div><!-- toggle -->

                                                                                                                                                                                                                                                                                  <div class="nav">

                                                                                                                                                                                                                                                                                                      <div class="menu-top-menu-container"><ul id="menu-top-menu" class="menu"><li id="menu-item-69" class="menu-item menu-item-type-taxonomy menu-item-object-category menu-item-has-children menu-item-69"><a href="http://sfmtc.org/?cat=5">About Us</a>
                                                                                                                                                                                                                                                                                                      <ul class="sub-menu">
                                                                                                                                                                                                                                                                                                        <li id="menu-item-73" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-73"><a href="http://sfmtc.org/?page_id=70">What We Believe</a></li>
                                                                                                                                                                                                                                                                                                            <li id="menu-item-75" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-75"><a href="http://sfmtc.org/?page_id=72">Mar Thoma Church History</a></li>
                                                                                                                                                                                                                                                                                                                <li id="menu-item-76" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-76"><a href="https://www.marthomanae.org/website/">Our Diocese</a></li>
                                                                                                                                                                                                                                                                                                                    <li id="menu-item-79" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-79"><a href="http://sfmtc.org/?page_id=77">Our Vicar</a></li>
                                                                                                                                                                                                                                                                                                                        <li id="menu-item-84" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-84"><a href="http://sfmtc.org/?page_id=81">Membership</a></li>
                                                                                                                                                                                                                                                                                                                        </ul>
                                                                                                                                                                                                                                                                                                                        </li>
                                                                                                                                                                                                                                                                                                                        <li id="menu-item-68" class="menu-item menu-item-type-taxonomy menu-item-object-category menu-item-has-children menu-item-68"><a href="http://sfmtc.org/?cat=4">Ministries</a>
                                                                                                                                                                                                                                                                                                                        <ul class="sub-menu">
                                                                                                                                                                                                                                                                                                                            <li id="menu-item-63" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-63"><a href="http://sfmtc.org/?page_id=34">Choir</a></li>
                                                                                                                                                                                                                                                                                                                                <li id="menu-item-66" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-66"><a href="http://sfmtc.org/?page_id=64">Edavaka Mission</a></li>
                                                                                                                                                                                                                                                                                                                                </ul>
                                                                                                                                                                                                                                                                                                                                </li>
                                                                                                                                                                                                                                                                                                                                <li id="menu-item-103" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-103"><a href="http://sfmtc.org/?page_id=49">Photo Gallery</a></li>
                                                                                                                                                                                                                                                                                                                                <li id="menu-item-89" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-89"><a href="http://sfmtc.org/?calendar=86">Calendar</a></li>
                                                                                                                                                                                                                                                                                                                                <li id="menu-item-111" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-111"><a href="http://sfmtc.org/portal">Portal</a></li>
                                                                                                                                                                                                                                                                                                                                </ul></div>                </div><!-- nav --><div class="clear"></div>

                                                                                                                                                                                                                                                                                                                                                    </div><!-- header-inner -->

                                                                                                                                                                                                                                                                                                                                                    </div><!-- header -->

                                                                                                                                                                                                                                                                                                                                                            

                                                                                                                                                                                                                                                                                                                                                                        <div class="innerbanner">                 

                                                                                                                                                                                                                                                                                                                                                                                                <img src="http://sfmtc.org/wp-content/uploads/2019/01/cropped-A-2.jpg" width="1400" height="272" alt="" />

                                                                                                                                                                                                                                                                                                                                                                                                                                         

                                                                                                                                                                                                                                                                                                                                                                                                                                             </div>  

                                                                                                                                                                                                                                                                                                                                                                                                                                             <div class="content-area">

                                                                                                                                                                                                                                                                                                                                                                                                                                                 <div class="middle-align">

                                                                                                                                                                                                                                                                                                                                                                                                                                                         <div class="site-main" id="sitefull">

<?php
}


function od_simple_header()
{
?>
<!DOCTYPE html>
<html lang="en-US">
    <head>
    <link href="styles.css" rel="stylesheet" type="text/css" />
    <link href="my_style.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    </head>
    <body>
    <div id="container">
        <div id="content">
<?php
}
function od_simple_footer()
{
?>
        </div>
        <div id="footerline"></div>
        <div id="footer">
            <?php
                if (isset($_SESSION['user'])) {
                    if (user_admin($_SESSION['user'])) {
                        echo "<a href='bdwa.php'>Birthdays/Wedding Ann</a>";
                        echo " | <a href='admin.php'>Admin Page</a><br><br>";
                    }
                    echo "Logged as " . $_SESSION['user'] ;
                }
            ?>

        </div>    
    </div>
    </body>
    </html>
<?php
}

/*
 * allow callers external to portal php files
 */
function od_ext_header($title)
{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="portal/my_style.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<title><?php echo $title ?></title>
</head>
<body>
<div id="container">
<div id="content">
<?php
}

function od_ext_footer()
{
?>
        </div>
        <div id="footerline"></div>
        <div id="footer"> </div>    
    </div>
    </body>
    </html>
<?php
}
/*
 * navigation menu - displayed as tabs thru the ID "tablist"
 */
function od_top_menu()
{
}
function od_old_top_menu()
{
?>
    <ul id = "tablist">
    <li id = "current"><a href="index.php">Home</a></li>        
    <li><a href="myinfo.php">My Details</a></li>        
    <li><a href="myaccount.php">My Accounts</a></li>        
    <li><a href="mydues.php">My Dues</a></li>        
    <li><a href="myreports.php">My Reports</a></li>        
    <li><a href="photo.php">Photo Gallery</a></li>        
    <li><a href="video.php">Video Gallery</a></li>        
    <li><a href="search.php">Search Member</a></li>        
    <li><a href="settings.php">Settings</a></li>        
    <li><a href="logout.php">Logout</a></li>        
    </ul>
    <p class="clear"></p>
    <br><br>
<?php
}

/*
 * Picasa Access functions
 */
function od_picasa_photo_get($albumid, $env, $size)
{
    /*
    require_once 'ZendPicasa.php';
    $client = getOAuthHttpClient();
    $photos = new Zend_Gdata_Photos($client);
    $query = new Zend_Gdata_Photos_AlbumQuery();
    $user = "default";
    $query->setUser($user);
    $query->setAlbumId($albumid);
    //$query->setThumbsize('72, 144, 160');
    $albumFeed = $photos->getAlbumFeed($query);
    $noimage_url ="";
    foreach ($albumFeed as $entry) {
        if (($entry->getTitle() == "$env.JPG") 
            || ($entry->getTitle() == "$env.jpg")) {
            $thumb = $entry->getMediaGroup()->getThumbnail();
            if ($size == "small") {
                $index = 0;
            } else if ($size == "medium") {
                $index = 1;
            } else {
                $index = 2;
            }
            //print "found $env image at " . $thumb[$index]->getUrl() . "<br>";
            return array($thumb[$index]->getUrl(), $entry->getGphotoId());
        } else if ($entry->getTitle() == "noimage.jpg") {
            $noimage_url = $entry->getMediaGroup()->getThumbnail()[1]->getUrl();
            //print "did not find $env image<br>";
        }
    }
    return array($noimage_url, 0);
    */

}

use Google\Auth\Credentials\UserRefreshCredentials;
use Google\Photos\Library\V1\PhotosLibraryClient;
use Google\Photos\Library\V1\PhotosLibraryResourceFactory;
function od_photos_get_env_urls($env_array, $num, $msize)
{
    $url_array = array();
    if ($msize == "thumb") {
        $size = "=w320-h214";
    } else {
        $size = $msize;
    }
    $pquery = "SELECT photo_id from env_photo_ids WHERE env IN ('"
              . implode("', '", $env_array) . "')";
    $presult = db_fetch_column("CCDB", $pquery);
    $pnum = db_num_rows($presult);
    if ($pnum != 0) {
        $gParams = $presult;
        require 'vendor/autoload.php';

        try {
            $clientSecretJson = json_decode(
                file_get_contents('./data/client_secret.json'),
                true
            )['web'];
            $authCredentials = new UserRefreshCredentials(
                              ['https://www.googleapis.com/auth/photoslibrary'],
                                                         $clientSecretJson);

            $photosLibraryClient = new PhotosLibraryClient(
                                           ['credentials' => $authCredentials]);
            $response = $photosLibraryClient->batchGetMediaItems($gParams);
            foreach ($response->getMediaItemResults() as $itemResult) {
                $mediaItem = $itemResult->getMediaItem();
                if (!(is_null($mediaItem))) {
                    list($env, $dummy) = explode(".", $mediaItem->getFilename());
                    $url_array[$env] = $mediaItem->getBaseUrl() . $size;
                }
            }

        } catch (\Google\ApiCore\ApiException $exception) {
            print "API Exception: $exception";
        } catch (\Google\ApiCore\ValidationException $e) {
            // Error during client creation
            print "API Validation Exception: $exception";
        }
    }

    return $url_array;
}

function od_photos_get_all_albums($msize)
{
    require 'vendor/autoload.php';
    $album_array = array();
    $i = 0;
    if ($msize == "thumb") {
        $size = "=w240-h160-c";
    } else {
        $size = $msize;
    }
    try {
        $clientSecretJson = json_decode(
            file_get_contents('./data/client_secret.json'),
            true
        )['web'];
        $authCredentials = new UserRefreshCredentials(
                          ['https://www.googleapis.com/auth/photoslibrary'],
                                                     $clientSecretJson);

        $photosLibraryClient = new PhotosLibraryClient(
                                       ['credentials' => $authCredentials]);

        $response = $photosLibraryClient->listAlbums();
        foreach ($response->iterateAllElements() as $album) {
            // Get some properties of an album
            $album_array[$i]['album_id']  = $album->getId();
            $album_array[$i]['title'] = $album->getTitle();
            $album_array[$i]['url'] = $album->getCoverPhotoBaseUrl() . $size;
            $album_array[$i]['num_items'] = $album->getMediaItemsCount();
            $i++;
        }
    } catch (\Google\ApiCore\ApiException $e) {
        // Handle error
        print "Error in GPhoto Album retrieval<br>";
    }
    return $album_array;
}

function od_photos_get_album_feed($albumid, $msize)
{
    require 'vendor/autoload.php';
    $image_array = array();
    $i = 0;
    if ($msize == "thumb") {
        $size = "=w240-h160-c";
    } else {
        $size = $msize;
    }
    try {
        $clientSecretJson = json_decode(
            file_get_contents('./data/client_secret.json'),
            true
        )['web'];
        $authCredentials = new UserRefreshCredentials(
                          ['https://www.googleapis.com/auth/photoslibrary'],
                                                     $clientSecretJson);

        $photosLibraryClient = new PhotosLibraryClient(
                                       ['credentials' => $authCredentials]);

        $response = $photosLibraryClient->searchMediaItems(
                                       ['albumId' => $albumid]);
        foreach ($response->iterateAllElements() as $item) {
            $image_array[$i]['title'] = $item->getDescription();
            $image_array[$i]['url'] = $item->getBaseUrl() . $size;
            $i++;
        }
    } catch (\Google\ApiCore\ApiException $e) {
        // Handle error
        print "Error in GPhoto Album retrieval<br>";
    }
    return $image_array;


}

function dnld_headers()
{
    // output headers so that the file is downloaded rather than displayed
    header('HTTP/1.1 200 OK');
    header('Cache-Control: no-cache, must-revalidate');
    header("Pragma: no-cache");
    header("Expires: 0");
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=data.csv');
}

// CSV download function which dumps a mysql table given the query result
function od_dnld_table($result_rows, $hdr_array)
{
    dnld_headers();
    // create a file pointer connected to the output stream
    $output = fopen('php://output', 'w');

    $hdr = 1;
    $total_amount = 0;
    foreach ($result_rows as $row) {
        if ($hdr) {
        // output the column headings
            if (empty($hdr_array)) {
                fputcsv($output, array_keys($row));
            } else {
                fputcsv($output, $hdr_array);
            }
            $hdr = 0;
        }
        $row_array = array();
        foreach ($row as $key => $value) {
            $row_array[] = $value;
        }
        fputcsv($output, $row_array);
    }
    fclose($output);
}

function od_user_check($envelope, $linked, $user)
{
    if (!$envelope) {
        print "ENV $envelope LINK: $linked You account is not yet approved by the Church Officials.\nPlease contact <A href=\"mailto:webadmin@marthomasf.org\">webadmin@marthomasf.org</A> if you have registered more than 3 days back.";
        return false;
    }
    if (!$linked) {
        print "LINK $envelope  LINK: $linked You account is not yet approved by the Church Officials.\nPlease contact <A href=\"mailto:webadmin@marthomasf.org\">webadmin@marthomasf.org</A> if you have registered more than 3 days back.";
        return false;
    }
    if ($envelope == 999 && $user=="guest@marthomasf.org") {
        print "Guests do not have access to this page !!";
        return false;
    }
    if ($envelope && $linked && $user != "guest@marthomasf.org") {
        return true;
    } 
    return false;
}

function od_user_or_guest_check($envelope, $linked)
{
    if (!$envelope) {
        print "ENV $envelope LINK: $linked You account is not yet approved by the Church Officials.\nPlease contact <A href=\"mailto:webadmin@marthomasf.org\">webadmin@marthomasf.org</A> if you have registered more than 3 days back.";
        return false;
    }
    if (!$linked) {
        print "LINK $envelope  LINK: $linked You account is not yet approved by the Church Officials.\nPlease contact <A href=\"mailto:webadmin@marthomasf.org\">webadmin@marthomasf.org</A> if you have registered more than 3 days back.";
        return false;
    }
    if ($envelope && $linked) {
        return true;
    } 
    return false;
}
?>

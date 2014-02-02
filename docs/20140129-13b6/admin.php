<?php
require_once( dirname(__FILE__).'/form.lib.php' );

define( 'PHPFMG_USER', "baollantines@yahoo.es" ); // must be a email address. for sending password to you.
define( 'PHPFMG_PW', "a2ea0c" );

?>
<?php
/**
 * GNU Library or Lesser General Public License version 2.0 (LGPLv2)
*/

# main
# ------------------------------------------------------
error_reporting( E_ERROR ) ;
phpfmg_admin_main();
# ------------------------------------------------------




function phpfmg_admin_main(){
    $mod  = isset($_REQUEST['mod'])  ? $_REQUEST['mod']  : '';
    $func = isset($_REQUEST['func']) ? $_REQUEST['func'] : '';
    $function = "phpfmg_{$mod}_{$func}";
    if( !function_exists($function) ){
        phpfmg_admin_default();
        exit;
    };

    // no login required modules
    $public_modules   = false !== strpos('|captcha|', "|{$mod}|", "|ajax|");
    $public_functions = false !== strpos('|phpfmg_ajax_submit||phpfmg_mail_request_password||phpfmg_filman_download||phpfmg_image_processing||phpfmg_dd_lookup|', "|{$function}|") ;   
    if( $public_modules || $public_functions ) { 
        $function();
        exit;
    };
    
    return phpfmg_user_isLogin() ? $function() : phpfmg_admin_default();
}

function phpfmg_ajax_submit(){
    $phpfmg_send = phpfmg_sendmail( $GLOBALS['form_mail'] );
    $isHideForm  = isset($phpfmg_send['isHideForm']) ? $phpfmg_send['isHideForm'] : false;

    $response = array(
        'ok' => $isHideForm,
        'error_fields' => isset($phpfmg_send['error']) ? $phpfmg_send['error']['fields'] : '',
        'OneEntry' => isset($GLOBALS['OneEntry']) ? $GLOBALS['OneEntry'] : '',
    );
    
    @header("Content-Type:text/html; charset=$charset");
    echo "<html><body><script>
    var response = " . json_encode( $response ) . ";
    try{
        parent.fmgHandler.onResponse( response );
    }catch(E){};
    \n\n";
    echo "\n\n</script></body></html>";

}


function phpfmg_admin_default(){
    if( phpfmg_user_login() ){
        phpfmg_admin_panel();
    };
}



function phpfmg_admin_panel()
{    
    phpfmg_admin_header();
    phpfmg_writable_check();
?>    
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td valign=top style="padding-left:280px;">

<style type="text/css">
    .fmg_title{
        font-size: 16px;
        font-weight: bold;
        padding: 10px;
    }
    
    .fmg_sep{
        width:32px;
    }
    
    .fmg_text{
        line-height: 150%;
        vertical-align: top;
        padding-left:28px;
    }

</style>

<script type="text/javascript">
    function deleteAll(n){
        if( confirm("Are you sure you want to delete?" ) ){
            location.href = "admin.php?mod=log&func=delete&file=" + n ;
        };
        return false ;
    }
</script>


<div class="fmg_title">
    1. Email Traffics
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=1">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=1">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_EMAILS_LOGFILE) ){
            echo '<a href="#" onclick="return deleteAll(1);">delete all</a>';
        };
    ?>
</div>


<div class="fmg_title">
    2. Form Data
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=2">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=2">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_SAVE_FILE) ){
            echo '<a href="#" onclick="return deleteAll(2);">delete all</a>';
        };
    ?>
</div>

<div class="fmg_title">
    3. Form Generator
</div>
<div class="fmg_text">
    <a href="http://www.formmail-maker.com/generator.php" onclick="document.frmFormMail.submit(); return false;" title="<?php echo htmlspecialchars(PHPFMG_SUBJECT);?>">Edit Form</a> &nbsp;&nbsp;
    <a href="http://www.formmail-maker.com/generator.php" >New Form</a>
</div>
    <form name="frmFormMail" action='http://www.formmail-maker.com/generator.php' method='post' enctype='multipart/form-data'>
    <input type="hidden" name="uuid" value="<?php echo PHPFMG_ID; ?>">
    <input type="hidden" name="external_ini" value="<?php echo function_exists('phpfmg_formini') ?  phpfmg_formini() : ""; ?>">
    </form>

		</td>
	</tr>
</table>

<?php
    phpfmg_admin_footer();
}



function phpfmg_admin_header( $title = '' ){
    header( "Content-Type: text/html; charset=" . PHPFMG_CHARSET );
?>
<html>
<head>
    <title><?php echo '' == $title ? '' : $title . ' | ' ; ?>PHP FormMail Admin Panel </title>
    <meta name="keywords" content="PHP FormMail Generator, PHP HTML form, send html email with attachment, PHP web form,  Free Form, Form Builder, Form Creator, phpFormMailGen, Customized Web Forms, phpFormMailGenerator,formmail.php, formmail.pl, formMail Generator, ASP Formmail, ASP form, PHP Form, Generator, phpFormGen, phpFormGenerator, anti-spam, web hosting">
    <meta name="description" content="PHP formMail Generator - A tool to ceate ready-to-use web forms in a flash. Validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. ">
    <meta name="generator" content="PHP Mail Form Generator, phpfmg.sourceforge.net">

    <style type='text/css'>
    body, td, label, div, span{
        font-family : Verdana, Arial, Helvetica, sans-serif;
        font-size : 12px;
    }
    </style>
</head>
<body  marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">

<table cellspacing=0 cellpadding=0 border=0 width="100%">
    <td nowrap align=center style="background-color:#024e7b;padding:10px;font-size:18px;color:#ffffff;font-weight:bold;width:250px;" >
        Form Admin Panel
    </td>
    <td style="padding-left:30px;background-color:#86BC1B;width:100%;font-weight:bold;" >
        &nbsp;
<?php
    if( phpfmg_user_isLogin() ){
        echo '<a href="admin.php" style="color:#ffffff;">Main Menu</a> &nbsp;&nbsp;' ;
        echo '<a href="admin.php?mod=user&func=logout" style="color:#ffffff;">Logout</a>' ;
    }; 
?>
    </td>
</table>

<div style="padding-top:28px;">

<?php
    
}


function phpfmg_admin_footer(){
?>

</div>

<div style="color:#cccccc;text-decoration:none;padding:18px;font-weight:bold;">
	:: <a href="http://phpfmg.sourceforge.net" target="_blank" title="Free Mailform Maker: Create read-to-use Web Forms in a flash. Including validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. " style="color:#cccccc;font-weight:bold;text-decoration:none;">PHP FormMail Generator</a> ::
</div>

</body>
</html>
<?php
}


function phpfmg_image_processing(){
    $img = new phpfmgImage();
    $img->out_processing_gif();
}


# phpfmg module : captcha
# ------------------------------------------------------
function phpfmg_captcha_get(){
    $img = new phpfmgImage();
    $img->out();
    //$_SESSION[PHPFMG_ID.'fmgCaptchCode'] = $img->text ;
    $_SESSION[ phpfmg_captcha_name() ] = $img->text ;
}



function phpfmg_captcha_generate_images(){
    for( $i = 0; $i < 50; $i ++ ){
        $file = "$i.png";
        $img = new phpfmgImage();
        $img->out($file);
        $data = base64_encode( file_get_contents($file) );
        echo "'{$img->text}' => '{$data}',\n" ;
        unlink( $file );
    };
}


function phpfmg_dd_lookup(){
    $paraOk = ( isset($_REQUEST['n']) && isset($_REQUEST['lookup']) && isset($_REQUEST['field_name']) );
    if( !$paraOk )
        return;
        
    $base64 = phpfmg_dependent_dropdown_data();
    $data = @unserialize( base64_decode($base64) );
    if( !is_array($data) ){
        return ;
    };
    
    
    foreach( $data as $field ){
        if( $field['name'] == $_REQUEST['field_name'] ){
            $nColumn = intval($_REQUEST['n']);
            $lookup  = $_REQUEST['lookup']; // $lookup is an array
            $dd      = new DependantDropdown(); 
            echo $dd->lookupFieldColumn( $field, $nColumn, $lookup );
            return;
        };
    };
    
    return;
}


function phpfmg_filman_download(){
    if( !isset($_REQUEST['filelink']) )
        return ;
        
    $info =  @unserialize(base64_decode($_REQUEST['filelink']));
    if( !isset($info['recordID']) ){
        return ;
    };
    
    $file = PHPFMG_SAVE_ATTACHMENTS_DIR . $info['recordID'] . '-' . $info['filename'];
    phpfmg_util_download( $file, $info['filename'] );
}


class phpfmgDataManager
{
    var $dataFile = '';
    var $columns = '';
    var $records = '';
    
    function phpfmgDataManager(){
        $this->dataFile = PHPFMG_SAVE_FILE; 
    }
    
    function parseFile(){
        $fp = @fopen($this->dataFile, 'rb');
        if( !$fp ) return false;
        
        $i = 0 ;
        $phpExitLine = 1; // first line is php code
        $colsLine = 2 ; // second line is column headers
        $this->columns = array();
        $this->records = array();
        $sep = chr(0x09);
        while( !feof($fp) ) { 
            $line = fgets($fp);
            $line = trim($line);
            if( empty($line) ) continue;
            $line = $this->line2display($line);
            $i ++ ;
            switch( $i ){
                case $phpExitLine:
                    continue;
                    break;
                case $colsLine :
                    $this->columns = explode($sep,$line);
                    break;
                default:
                    $this->records[] = explode( $sep, phpfmg_data2record( $line, false ) );
            };
        }; 
        fclose ($fp);
    }
    
    function displayRecords(){
        $this->parseFile();
        echo "<table border=1 style='width=95%;border-collapse: collapse;border-color:#cccccc;' >";
        echo "<tr><td>&nbsp;</td><td><b>" . join( "</b></td><td>&nbsp;<b>", $this->columns ) . "</b></td></tr>\n";
        $i = 1;
        foreach( $this->records as $r ){
            echo "<tr><td align=right>{$i}&nbsp;</td><td>" . join( "</td><td>&nbsp;", $r ) . "</td></tr>\n";
            $i++;
        };
        echo "</table>\n";
    }
    
    function line2display( $line ){
        $line = str_replace( array('"' . chr(0x09) . '"', '""'),  array(chr(0x09),'"'),  $line );
        $line = substr( $line, 1, -1 ); // chop first " and last "
        return $line;
    }
    
}
# end of class



# ------------------------------------------------------
class phpfmgImage
{
    var $im = null;
    var $width = 73 ;
    var $height = 33 ;
    var $text = '' ; 
    var $line_distance = 8;
    var $text_len = 4 ;

    function phpfmgImage( $text = '', $len = 4 ){
        $this->text_len = $len ;
        $this->text = '' == $text ? $this->uniqid( $this->text_len ) : $text ;
        $this->text = strtoupper( substr( $this->text, 0, $this->text_len ) );
    }
    
    function create(){
        $this->im = imagecreate( $this->width, $this->height );
        $bgcolor   = imagecolorallocate($this->im, 255, 255, 255);
        $textcolor = imagecolorallocate($this->im, 0, 0, 0);
        $this->drawLines();
        imagestring($this->im, 5, 20, 9, $this->text, $textcolor);
    }
    
    function drawLines(){
        $linecolor = imagecolorallocate($this->im, 210, 210, 210);
    
        //vertical lines
        for($x = 0; $x < $this->width; $x += $this->line_distance) {
          imageline($this->im, $x, 0, $x, $this->height, $linecolor);
        };
    
        //horizontal lines
        for($y = 0; $y < $this->height; $y += $this->line_distance) {
          imageline($this->im, 0, $y, $this->width, $y, $linecolor);
        };
    }
    
    function out( $filename = '' ){
        if( function_exists('imageline') ){
            $this->create();
            if( '' == $filename ) header("Content-type: image/png");
            ( '' == $filename ) ? imagepng( $this->im ) : imagepng( $this->im, $filename );
            imagedestroy( $this->im ); 
        }else{
            $this->out_predefined_image(); 
        };
    }

    function uniqid( $len = 0 ){
        $md5 = md5( uniqid(rand()) );
        return $len > 0 ? substr($md5,0,$len) : $md5 ;
    }
    
    function out_predefined_image(){
        header("Content-type: image/png");
        $data = $this->getImage(); 
        echo base64_decode($data);
    }
    
    // Use predefined captcha random images if web server doens't have GD graphics library installed  
    function getImage(){
        $images = array(
			'6B76' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7WANEQ1hDA6Y6IImJTBFpZWgICAhAEgtoEWl0aAh0EEAWawCqa3R0QHZfZNTUsFVLV6ZmIbkvBGTeFEZU81qB5gUwOoigiTk6oIqB3MLawICiF+zmBgYUNw9U+FERYnEfAOXszJaKxCpuAAAAAElFTkSuQmCC',
			'34E5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7RAMYWllDHUMDkMQCpjBMZW1gdEBR2coQiiE2hdEVKObqgOS+lVFLly4NXRkVhey+KSKtrEBaBMU80VBXDDGgW4B2IIsB3QLSG4DsPoibHaY6DILwoyLE4j4AdTnKNqqXD84AAAAASUVORK5CYII=',
			'051F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7GB1EQxmmMIaGIImxBog0MIQwOiCrE5ki0sCIJhbQKhIC1AsTAzspaunUpaumrQzNQnJfQCtDo8MUdL2YYkA7MMRYA1hbGdDEGB2ALgl1RBEbqPCjIsTiPgDg68jAAvmg9wAAAABJRU5ErkJggg==',
			'64CE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WAMYWhlCHUMDkMREpjBMZXQIdEBWF9DCEMraIIgq1sDoytrACBMDOykyaunSpatWhmYhuS9kikgrkjqI3lbRUFcMMYZWdDuAbmlFdws2Nw9U+FERYnEfACejyc7c3fmoAAAAAElFTkSuQmCC',
			'33E8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWElEQVR4nGNYhQEaGAYTpIn7RANYQ1hDHaY6IIkFTBFpZW1gCAhAVtnK0OjawOgggiw2hQFZHdhJK6NWhS0NXTU1C9l9qOpwm4dFDJtbsLl5oMKPihCL+wA3PstrNPQZewAAAABJRU5ErkJggg==',
			'7151' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QkMZAlhDHVpRRFsZA1gbGKaiirGCxEJRxKYA9U5lgOmFuClqVdTSzKylyO5jdGAIYGgIQLEDaBaGmAhQjBVNLAAoxujogCbGGgp0SWjAIAg/KkIs7gMAj0zJc9yJgWwAAAAASUVORK5CYII=',
			'230A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7WANYQximMLQii4lMEWllCGWY6oAkFtDK0Ojo6BAQgKy7laGVtSHQQQTZfdNWhS1dFZk1Ddl9ASjqwJDRgaHRtSEwNATZLQ0gOxxR1Ik0gNzCiCIWGgpyM6rYQIUfFSEW9wEAgbbKsaYC0ZkAAAAASUVORK5CYII=',
			'09D1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDGVqRxVgDWFtZGx2mIouJTBFpdG0ICEUWC2gFi8H0gp0UtXTp0lQgiey+gFbGQCR1UDGGRnQxkSksGGJQt6CIQd0cGjAIwo+KEIv7APQ3zN1KxVPMAAAAAElFTkSuQmCC',
			'69D2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDGaY6IImJTGFtZW10CAhAEgtoEWl0bQh0EEEWawCJAUkk90VGLV2auioKCBHuC5nCGAhU14hsR0ArA0hvKwOKGAtIbAoDFrdgupkxNGQQhB8VIRb3AQCDB84Aybfs6QAAAABJRU5ErkJggg==',
			'4D4B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpI37poiGMDQ6hjogi4WItDK0OjoEIIkxhog0Okx1dBBBEmOdAhQLhKsDO2natGkrMzMzQ7OQ3BcAVOfaiGpeaChQLDQQxTwGkHmNjuhirQxoerG6eaDCj3oQi/sAGwjNCxt/Wh4AAAAASUVORK5CYII=',
			'A325' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nM2QMQ6AIAwAy9Af8KAyuJeEOvADfwEDP8AnOMgrJU4lOmpCu12uyaXQHpNgpv2lzxAGECOsGLItxjnSnq2Ql+QHxgUKJL+Q6otHW9u5xaj6bq+bVt2KQKY6su5lYkMj6y0EzAPDgMI7TfC/D/el7wK5gctswizIygAAAABJRU5ErkJggg==',
			'F1C7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QkMZAhhCHUNDkMQCGhgDGB0CGkRQxFgDWBsE0MQYgGJgGu6+0KhVUUtXrVqZheQ+qLpWBky9UzDFBALQxRgdAh1QxVhDgW5GERuo8KMixOI+AIWWyqZ16Q+3AAAAAElFTkSuQmCC',
			'9A0E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WAMYAhimMIYGIImJTGEMYQhldEBWF9DK2sro6IgmJtLo2hAIEwM7adrUaStTV0WGZiG5j9UVRR0EtoqGoosJAM1zRLNDZIpIowOaW1gDgGJobh6o8KMixOI+AJDVyk5sbsIpAAAAAElFTkSuQmCC',
			'ABE5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDHUMDkMRYA0RaWYEyyOpEpog0uqKJBbSC1bk6ILkvaunUsKWhK6OikNwHUQc0A0lvaCjIPFQxoDqwHSIYdjAEBKCIgdzsMNVhEIQfFSEW9wEAF9LLyX9eDB4AAAAASUVORK5CYII=',
			'549A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nGNYhQEaGAYTpIn7QkMYWhlCgRhJLKCBYSqjo8NUB1SxUNaGgIAAJLHAAEZX1oZABxEk94VNW7p0ZWZk1jRk97WKtDKEwNVBxURDHRoCQ0OQ7WhlaGVsQFUnMgUo5uiIIsYaAHIzI6p5AxR+VIRY3AcAMhXLI9kihMEAAAAASUVORK5CYII=',
			'C9DF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7WEMYQ1hDGUNDkMREWllbWRsdHZDVBTSKNLo2BKKKNaCIgZ0UtWrp0tRVkaFZSO4LaGAMxNTLgGleIwuGGDa3QN2MIjZQ4UdFiMV9AEM6y1GNNILUAAAAAElFTkSuQmCC',
			'CDD4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7WENEQ1hDGRoCkMREWkVaWRsdGpHFAhpFGl0bAlpRxBrAYlMCkNwXtWraytRVUVFRSO6DqAt0wNQbGBqCaQc2t6CIYXPzQIUfFSEW9wEAMi3QPLN9Y1IAAAAASUVORK5CYII=',
			'AEA6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7GB1EQxmmMEx1QBJjDRBpYAhlCAhAEhOZItLA6OjoIIAkFtAq0sDaEOiA7L6opVPDlq6KTM1Cch9UHYp5oaFAsdBABxEs5mGKBaDoDWgVDQWKobh5oMKPihCL+wAP3syiNrgTNAAAAABJRU5ErkJggg==',
			'DC80' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7QgMYQxlCGVqRxQKmsDY6OjpMdUAWaxVpcG0ICAhAE2MEKhRBcl/U0mmrVoWuzJqG5D40dXAx1oZADDEMO7C4BZubByr8qAixuA8ApnTOLc23bS8AAAAASUVORK5CYII=',
			'E003' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWUlEQVR4nGNYhQEaGAYTpIn7QkMYAhimMIQ6IIkFNDCGMIQyOgSgiLG2Mjo6NIigiIk0ugLJACT3hUZNW5m6KmppFpL70NShiIkQtAPTLdjcPFDhR0WIxX0A21PNjV+7R7gAAAAASUVORK5CYII=',
			'BF83' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7QgNEQx1CGUIdkMQCpog0MDo6OgQgi7WKNLA2BDSIYKhzaAhAcl9o1NSwVaGrlmYhuQ9NHW7zcNqB6pbQAKAKNDcPVPhREWJxHwCPw84ZlNuWpwAAAABJRU5ErkJggg==',
			'9D8D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7WANEQxhCGUMdkMREpoi0Mjo6OgQgiQW0ijS6NgQ6iKCJOQLViSC5b9rUaSuzQldmTUNyH6srijoIxGKeABYxbG7B5uaBCj8qQizuAwBaFct6rShx7gAAAABJRU5ErkJggg==',
			'088E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWElEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGUMDkMRYA1hbGR0dHZDViUwRaXRtCEQRC2hFUQd2UtTSlWGrQleGZiG5D00dVAzTPGx2YHMLNjcPVPhREWJxHwBvrMlAmQskXAAAAABJRU5ErkJggg==',
			'012C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7GB0YAhhCGaYGIImxBjAGMDo6BIggiYlMYQ1gbQh0YEESC2gF6gWKIbsvaumqqFUrM7OQ3QdW18rowICudwqqmMgUoFgAI4odrGARBhS3MDqwhrKGBqC4eaDCj4oQi/sAmKPHqEPSMWQAAAAASUVORK5CYII=',
			'A86C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGaYGIImxBrC2Mjo6BIggiYlMEWl0bXB0YEESC2hlbWUFmoDsvqilK8OWTl2Zhew+sDpHRwdke0NDQeYFoogFtELE0O1Ad0tAK6abByr8qAixuA8AG2rLqSO/aaAAAAAASUVORK5CYII=',
			'DB26' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QgNEQxhCGaY6IIkFTBFpZXR0CAhAFmsVaXRtCHQQQBVrZQCKIbsvaunUsFUrM1OzkNwHVtfKiGGewxRGBxF0sQA0MZBbHBhQ9ILczBoagOLmgQo/KkIs7gMAlinNR1GkKLQAAAAASUVORK5CYII=',
			'1B81' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7GB1EQxhCGVqRxVgdRFoZHR2mIouJOog0ujYEhKLqBauD6QU7aWXW1LBVoauWIrsPTR1MDGQeMWIYekVDwG4ODRgE4UdFiMV9AHb/yV3kaTCaAAAAAElFTkSuQmCC',
			'156F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7GB1EQxlCGUNDkMRYHUQaGB0dHZDViQLFWBtQxRgdREJYgSSy+1ZmTV26dOrK0Cwk9wFVNLo6ousFijUEopuHRYy1FcMtIYwhQDejiA1U+FERYnEfAHduxs1YVC3bAAAAAElFTkSuQmCC',
			'BF05' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QgNEQx2mMIYGIIkFTBFpYAhldEBWF9Aq0sDo6IgqBlTH2hDo6oDkvtCoqWFLV0VGRSG5D6IuoEEEzTxsYiA7RDDcwhCA7L7QAKDYFIapDoMg/KgIsbgPANNQzNl0iv+VAAAAAElFTkSuQmCC',
			'DD9A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QgNEQxhCGVqRxQKmiLQyOjpMdUAWaxVpdG0ICAjAEAt0EEFyX9TSaSszMyOzpiG5D6TOIQSuDiHWEBgagibm2ICmDuwWRxQxiJsZUcQGKvyoCLG4DwCzK84GhPYZ8AAAAABJRU5ErkJggg==',
			'B101' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QgMYAhimMLQiiwVMYQxgCGWYiiLWyhrA6OgQiqqOIYAVKIPsvtCoVVFLQQjJfWjqoOZhFwPa0YpuB9AtKGKhAayhQDeHBgyC8KMixOI+AN1lyzHZ7T/9AAAAAElFTkSuQmCC',
			'DD3C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWElEQVR4nGNYhQEaGAYTpIn7QgNEQxhDGaYGIIkFTBFpZW10CBBBFmsVaXRoCHRgQRdrdHRAdl/U0mkrs6auzEJ2H5o6FPOwiaHYgcUt2Nw8UOFHRYjFfQBZEc62tw+2AAAAAABJRU5ErkJggg==',
			'6690' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGVqRxUSmsLYyOjpMdUASC2gRaWRtCAgIQBZrEGlgbQh0EEFyX2TUtLCVmZFZ05DcFzJFtJUhBK4OordVpNGhAVPMEc0ObG7B5uaBCj8qQizuAwAj9MxA6iM/8AAAAABJRU5ErkJggg==',
			'C4C9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WEMYWhlCHaY6IImJtDJMZXQICAhAEgtoZAhlbRB0EEEWa2B0ZW1ghImBnRS1aunSpUAqDMl9AUATWRsYpqLqFQ11BdmFagdQnQCKHUC3tKK7BZubByr8qAixuA8Au/fL6fDvELAAAAAASUVORK5CYII=',
			'CA4B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WEMYAhgaHUMdkMREWhlDGFodHQKQxAIaWVsZpjo6iCCLNYg0OgTC1YGdFLVq2srMzMzQLCT3gdS5NqKZ1yAa6hoaiGpeI9C8RlQ7RFohYsh6WUPAYihuHqjwoyLE4j4AhLHNjiijvdgAAAAASUVORK5CYII=',
			'9D60' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7WANEQxhCGVqRxUSmiLQyOjpMdUASC2gVaXRtcAgIwBBjdBBBct+0qdNWpk5dmTUNyX2srkB1jo4wdRAI1huIIiYAFgtAsQObW7C5eaDCj4oQi/sA0FPMqMT/ld4AAAAASUVORK5CYII=',
			'45D7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpI37poiGsoYyhoYgi4WINLA2OjSIIIkxgsQaAlDEWKeIhIDEApDcN23a1KVLV0WtzEJyX8AUhkbXhoBWZHtDQ8FiU1DdIgISC0AVY21lbXR0QBVjDAG6GVVsoMKPehCL+wDuUcyzAAsKYgAAAABJRU5ErkJggg==',
			'78A5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7QkMZQximMIYGIIu2srYyhDI6oKhsFWl0dHREFZvC2sraEOjqgOy+qJVhS1dFRkUhuY/RAaQuoEEESS9rg0ijayiqmAhIrCHQAVksoAGsNyAARYwxBCg21WEQhB8VIRb3AQBtSswbbLliiAAAAABJRU5ErkJggg==',
			'450A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpI37poiGMkxhaEURCxFpYAhlmOqAJMYIFGN0dAgIQBJjnSISwtoQ6CCC5L5p06YuXboqMmsakvsCpjA0uiLUgWFoKFgsNATFLSKNjo6OKOoYprC2MoQyookxhgAxqthAhR/1IBb3AQAX7MtLuFBwFgAAAABJRU5ErkJggg==',
			'4B62' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpI37poiGMIQyTHVAFgsRaWV0dAgIQBJjDBFpdG1wdBBBEmOdItLKCqRFkNw3bdrUsKVTV62KQnJfAEido0Mjsh2hoSDzAlpR3QIWm4ImBnYLppsZQ0MGQ/hRD2JxHwBqI8x5RltYgwAAAABJRU5ErkJggg==',
			'CD0B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7WENEQximMIY6IImJtIq0MoQyOgQgiQU0ijQ6Ojo6iCCLNYg0ujYEwtSBnRS1atrK1FWRoVlI7kNThyImQsAObG7B5uaBCj8qQizuAwCzTMycG1CvnwAAAABJRU5ErkJggg==',
			'E5D0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QkNEQ1lDGVqRxQIaRBpYGx2mOqCLNQQEBKCKhbA2BDqIILkvNGrq0qWrIrOmIbkPqKfRFaEOj5gIUAzdDtZWdLeEhjCGoLt5oMKPihCL+wDkd85qhKG4OAAAAABJRU5ErkJggg==',
			'AA9B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7GB0YAhhCGUMdkMRYAxhDGB0dHQKQxESmsLayNgQ6iCCJBbSKNLoCxQKQ3Be1dNrKzMzI0Cwk94HUOYQEopgXGioKtBPTPEdsYmhuAZuH5uaBCj8qQizuAwDwS8ySTqRa8gAAAABJRU5ErkJggg==',
			'DDFF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAUElEQVR4nGNYhQEaGAYTpIn7QgNEQ1hDA0NDkMQCpoi0sjYwOiCrC2gVaXTFLwZ2UtTSaStTQ1eGZiG5jwi9uMWwuAXsZjSxgQo/KkIs7gMAqrbLlzAVNXQAAAAASUVORK5CYII=',
			'6BD8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7WANEQ1hDGaY6IImJTBFpZW10CAhAEgtoEWl0bQh0EEEWawCqawiAqQM7KTJqatjSVVFTs5DcFzIFRR1EbysW87CIYXMLNjcPVPhREWJxHwBuqc4IRuLF8wAAAABJRU5ErkJggg==',
			'0695' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGUMDkMRYA1hbGR0dHZDViUwRaWRtCEQRC2gVaQCKuToguS9q6bSwlZmRUVFI7gtoFW1lCAloEEHV2+jQgCoGssMRaIcIhlscApDdB3Ezw1SHQRB+VIRY3AcAB5nKuIivvBEAAAAASUVORK5CYII=',
			'47AE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpI37poiGOkxhDA1AFgthaHQIZXRAVscIFHN0dEQRY53C0MraEAgTAztp2rRV05auigzNQnJfwBSGACR1YBgKNJ81FFWMYQprA7o6hikiuMRQ3TxQ4Uc9iMV9ANUHyn4kLGrcAAAAAElFTkSuQmCC',
			'1D43' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7GB1EQxgaHUIdkMRYHURaGVodHQKQxEQdRBodpjo0iKDoBYoFOjQEILlvZda0lZmZWUuzkNwHUufaCFeHEAsNwDSvEcOOVoZGNLeEYLp5oMKPihCL+wC7J8vlmzGs8gAAAABJRU5ErkJggg==',
			'6B68' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7WANEQxhCGaY6IImJTBFpZXR0CAhAEgtoEWl0bXB0EEEWaxBpZW1ggKkDOykyamrY0qmrpmYhuS8EaB4runmtIPMCUc3DIobNLdjcPFDhR0WIxX0AIRLNAiBFp0sAAAAASUVORK5CYII=',
			'4C23' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpI37pjCGMgChA7JYCGujo6OjQwCSGGOISINrQ0CDCJIY6xQQL6AhAMl906ZNW7VqZdbSLCT3BYDUtTI0IJsXGgoUm8KAYh4DUJ1DALoY0C0OjChuAbmZNTQA1c0DFX7Ug1jcBwDNIMy3AIpemQAAAABJRU5ErkJggg==',
			'9D0D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7WANEQximMIY6IImJTBFpZQhldAhAEgtoFWl0dHR0EEETc20IhImBnTRt6rSVqasis6YhuY/VFUUdBLZiiglgsQObW7C5eaDCj4oQi/sAjjbLntjGqMYAAAAASUVORK5CYII=',
			'DBB6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWUlEQVR4nGNYhQEaGAYTpIn7QgNEQ1hDGaY6IIkFTBFpZW10CAhAFmsVaXRtCHQQQBUDqnN0QHZf1NKpYUtDV6ZmIbkPqg6reSKExLC4BZubByr8qAixuA8ANFXOqXu6vGwAAAAASUVORK5CYII=',
			'6D4D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WANEQxgaHUMdkMREpoi0MrQ6OgQgiQW0iDQ6THV0EEEWawCKBcLFwE6KjJq2MjMzM2sakvtCpog0ujai6W0FioUGYog5oKkDu6UR1S3Y3DxQ4UdFiMV9AK6YzWtKwvoaAAAAAElFTkSuQmCC',
			'75AF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QkNFQxmmMIaGIIu2ijQwhDI6MKCJMTo6oopNEQlhbQiEiUHcFDV16dJVkaFZSO4DmtToilAHhqwNQLFQVDGRBhEMdQENrK2sGGKMIehiAxV+VIRY3AcAjULKUqs8zeAAAAAASUVORK5CYII=',
			'1834' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7GB0YQxhDGRoCkMRYHVhbWRsdGpHFRB1EGh0aAloDUPSytjI0OkwJQHLfyqyVYaumroqKQnIfRJ2jA6pekHmBoSEYYgEN6HawgkWR3BKC6eaBCj8qQizuAwDWx8wD/tRpLQAAAABJRU5ErkJggg==',
			'F094' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QkMZAhhCGRoCkMQCGhhDGB0dGlHFWFtZGwJaUcVEGl0bAqYEILkvNGrayszMqKgoJPeB1DmEBDqg63VoCAwNQbODEUhicQuaGKabByr8qAixuA8ATSHOwq4I4ZEAAAAASUVORK5CYII=',
			'1BF0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDA1qRxVgdRFpZGximOiCJiTqINLo2MAQEoOgFqQOSSO5bmTU1bGnoyqxpSO5DUwcTA5qHTQybHWhuCQG6uYEBxc0DFX5UhFjcBwAULMjvXruDvwAAAABJRU5ErkJggg==',
			'A699' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGaY6IImxBrC2Mjo6BAQgiYlMEWlkbQh0EEESC2gVaUASAzspaum0sJWZUVFhSO4LaBVtZQgJmIqsNzRUpNGhIaABzbxGx4YANDsw3RLQiunmgQo/KkIs7gMA1T3MUOv7gd4AAAAASUVORK5CYII=',
			'B650' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDHVqRxQKmsLayNjBMdUAWaxVpBIoFBKCoE2lgncroIILkvtCoaWFLMzOzpiG5L2CKKND8QJg6uHkOWMRcGwLQ7GBtZXR0QHELyM0MoQwobh6o8KMixOI+AJGuzV7NuXe+AAAAAElFTkSuQmCC',
			'871A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WANEQx2mMLQii4lMYWh0CGGY6oAkFtDK0OgYwhAQgKqulWEKo4MIkvuWRq2atmrayqxpSO4DqgtAUgc1j9EBKBYagiLG2oCuTmSKCIYYa4BIA2OoI4rYQIUfFSEW9wEAMwPLL/1OuDoAAAAASUVORK5CYII=',
			'9B98' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WANEQxhCGaY6IImJTBFpZXR0CAhAEgtoFWl0bQh0EEEVa2VtCICpAztp2tSpYSszo6ZmIbmP1VWklSEkAMU8BqB5DmjmCQDFHNHEsLkFm5sHKvyoCLG4DwBn8MxPmkJdgAAAAABJRU5ErkJggg==',
			'A2D9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nM2QvRGAIAxGQ5ENcJ809PHOWDBNLNgARqBhSjmriJZ6mq97l593gXYphT/lFT9HbkGBQoYhY8KNmA3z2W9BZ/KGcQLLDqVYW60txtX49b6MysXOigB3pud9jjobbqCOLpwmCYPzV/97MDd+O0V7zVpUiUGyAAAAAElFTkSuQmCC',
			'9D99' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WANEQxhCGaY6IImJTBFpZXR0CAhAEgtoFWl0bQh0EMEtBnbStKnTVmZmRkWFIbmP1VWk0SEkYCqyXgagXoeGgAZkMQGgmGNDAIod2NyCzc0DFX5UhFjcBwCjD8yIHPsmhAAAAABJRU5ErkJggg==',
			'94CF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WAMYWhlCHUNDkMREpjBMZXQIdEBWFwBUxdogiCbG6MrawAgTAztp2tSlS5euWhmaheQ+VleRViR1ENgqGuqKJibQytCKbgfQLa3oboG6GdW8AQo/KkIs7gMAF3/IvRsfcz0AAAAASUVORK5CYII=',
			'8897' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGUNDkMREprC2Mjo6NIggiQW0ijS6NgSgiIHUsQLFApDctzRqZdjKzKiVWUjuA6ljCAloZUAzz6EhYAq6mGNDQAADhlscHbC4GUVsoMKPihCL+wBAk8wMQrEfcwAAAABJRU5ErkJggg==',
			'0128' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7GB0YAhhCGaY6IImxBjAGMDo6BAQgiYlMYQ1gbQh0EEESC2gF6m0IgKkDOylq6aqoVSuzpmYhuQ+sDogD0PVOYUQxT2QKUCwAVYwVLIKql9GBNZQ1NADFzQMVflSEWNwHABv+yN6/CwNtAAAAAElFTkSuQmCC',
			'3E89' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7RANEQxlCGaY6IIkFTBFpYHR0CAhAVtkq0sDaEOgggiwGVucIEwM7aWXU1LBVoauiwpDdBzFvqgiGeQENWMRQ7MDmFmxuHqjwoyLE4j4Ar+TLER/SzLcAAAAASUVORK5CYII=',
			'5162' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nM3QsQ2AIBCF4UfhBgwEGzwTz8JpzoINwA1smFKizRktNeGu+0LCn0N9jKKn/aVPJhCCEoxRHV0M5M0GDhqDNzYSzaDe9M1bXfZS62L7UnsXw2r/OE2ZbAsvy9Z8xtlirZUIxMnUwf0+3Je+A9I6yiOsvlYTAAAAAElFTkSuQmCC',
			'D99C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QgMYQxhCGaYGIIkFTGFtZXR0CBBBFmsVaXRtCHRgwSKG7L6opUuXZmZGZiG7L6CVMdAhBK4OKsbQ6NCALsbS6IhuBxa3YHPzQIUfFSEW9wEASgHNCvyxbMAAAAAASUVORK5CYII=',
			'73D1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QkNZQ1hDGVpRRFtFWlkbHaaiijE0ujYEhKKITWFoZW0IgOmFuClqVdjSVVFLkd3H6ICiDgxZG8DmoYiJYBELaAC7BU0M7ObQgEEQflSEWNwHALuJzNB8lCktAAAAAElFTkSuQmCC',
			'1047' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7GB0YAhgaHUNDkMRYHRhDGFodGkSQxEQdWFsZpqKKMTqINDoEOjQEILlvZda0lZmZWSuzkNwHUufa6NDKgKbXNTRgCqoY0I5GhwBUMaBbGh0dkMVEQ8BuRhEbqPCjIsTiPgAJe8l+7dSomQAAAABJRU5ErkJggg==',
			'28A2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nM2QsQ3DMAwEqYIbcCCpSP8GRBeZhiq4gZINXNhTWlBFIykTwPzuwAcPpONjjO6Uv/gxUqVOrxyYdHZSAgKDSyulZIltZ2eDSfR77+t2PEeCH+ZeizdSlvZQ+MXFBjP0yMRmF5Gppsq2aL3B/36YL34ng1bMo1SDrm8AAAAASUVORK5CYII=',
			'FF1F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAT0lEQVR4nGNYhQEaGAYTpIn7QkNFQx2mMIaGIIkFNIg0MIQwOjCgiTFiEWOYAhcDOyk0amrYqmkrQ7OQ3IemjipijKGOKGIDFX5UhFjcBwAAlspqUMAVNgAAAABJRU5ErkJggg==',
			'D951' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDHVqRxQKmsLayNjBMRRFrFWl0bWAIxRCbygDTC3ZS1NKlS1Mzs5Yiuy+glTHQAUii6mVoxBRjAdoRgOEWRkdU94HcDHRJaMAgCD8qQizuAwB7wM4N/9h7VQAAAABJRU5ErkJggg==',
			'FF80' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAV0lEQVR4nGNYhQEaGAYTpIn7QkNFQx1CGVqRxQIaRBoYHR2mOqCJsTYEBARgqHN0EEFyX2jU1LBVoSuzpiG5D00dknmBWMSw2YHpFgY0Nw9U+FERYnEfAE+1zSXg3qujAAAAAElFTkSuQmCC',
			'E7C5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkNEQx1CHUMDkMSA7EZHh0AHBjQx1wZBdLFW1gZGVwck94VGrZq2dNXKqCgk9wHVBbACaREUvYwOmGKsQCjogComAlQZEIDsvtAQoIpQh6kOgyD8qAixuA8AiFDMfKjE8X8AAAAASUVORK5CYII=',
			'CA8B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WEMYAhhCGUMdkMREWhlDGB0dHQKQxAIaWVtZGwIdRJDFGkQaHRHqwE6KWjVtZVboytAsJPehqYOKiYa6opvXKNKILibSiqmXNUSk0QHNzQMVflSEWNwHAO1czE0Xh0xZAAAAAElFTkSuQmCC',
			'3175' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7RAMYAlhDA0MDkMQCpjAGMDQEOqCobGXFFJvCEMDQ6OjqgOS+lVGrolYtXRkVhew+kLopDA0iKOYBxQIwxRgdGB1EUNwCdF8DQwCy+0SBLgaKTXUYBOFHRYjFfQBLockBv0ST9QAAAABJRU5ErkJggg==',
			'68B8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDGaY6IImJTGFtZW10CAhAEgtoEWl0bQh0EEEWa0BRB3ZSZNTKsKWhq6ZmIbkvBJt5rVjMwyKGzS3Y3DxQ4UdFiMV9ANxrzZUqv3F0AAAAAElFTkSuQmCC',
			'EF9E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWElEQVR4nGNYhQEaGAYTpIn7QkNEQx1CGUMDkMQCGkQaGB0dHRjQxFgbAvGJgZ0UGjU1bGVmZGgWkvtA6hhCMPUyYDGPEZsYmltCQ4B60dw8UOFHRYjFfQDI3MsL6b9+DwAAAABJRU5ErkJggg==',
			'36BE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWUlEQVR4nGNYhQEaGAYTpIn7RAMYQ1hDGUMDkMQCprC2sjY6OqCobBVpZG0IRBWbItKApA7spJVR08KWhq4MzUJ23xRRrOa5opuHRQybW7C5eaDCj4oQi/sA5hnKbpsMoAQAAAAASUVORK5CYII=',
			'A38D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7GB1YQxhCGUMdkMRYA0RaGR0dHQKQxESmMDS6NgQ6iCCJBbQygNWJILkvaumqsFWhK7OmIbkPTR0YhoZiNQ+LGKZbAlox3TxQ4UdFiMV9ALsiy1xUCoMsAAAAAElFTkSuQmCC',
			'E01A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QkMYAhimMLQiiwU0MIYwhDBMdUARY20FigYEoIiJNDpMYXQQQXJfaNS0lVkghOQ+NHXIYqEhaHYwYKgD2oomBnIzY6gjithAhR8VIRb3AQDDfMvFbtYcfwAAAABJRU5ErkJggg==',
			'AC61' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7GB0YQxlCGVqRxVgDWBsdHR2mIouJTBFpcG1wCEUWC2gVaWBtgOsFOylq6bRVS6euWorsPrA6RwcUO0JDQXoDWtHNc8UQA7sFTQzs5tCAQRB+VIRY3AcADx3NP1WdIa0AAAAASUVORK5CYII=',
			'8C09' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WAMYQxmmMEx1QBITmcLa6BDKEBCAJBbQKtLg6OjoIIKiTqSBtSEQJgZ20tKoaauWroqKCkNyH0RdwFQRNPOAYg3oYkAr0OzAdAs2Nw9U+FERYnEfANF5zMJ4xEzuAAAAAElFTkSuQmCC',
			'92A7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7WAMYQximMIaGIImJTGFtZQgF0khiAa0ijY6ODmhiDI2uDQFAiHDftKmrli5dFbUyC8l9rK4MU1hBqpFtbmUIYA0NmIIsJtDK6ABUF8CA6pYG1oZAB1Q3i4a6ookNVPhREWJxHwBGNMwdFIGkMAAAAABJRU5ErkJggg==',
			'C7EE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7WENEQ11DHUMDkMREWhkaXRsYHZDVBTRiEWtgaGVFiIGdFLVq1bSloStDs5DcB1QXwIqhl9EBQ6yRtQFdTKRVBEOMNQQohubmgQo/KkIs7gMAVC7JwDQx5LgAAAAASUVORK5CYII=',
			'3CAB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7RAMYQxmmMIY6IIkFTGFtdAhldAhAVtkq0uDo6Ogggiw2RaSBtSEQpg7spJVR01YtXRUZmoXsPlR1cPNYQwNRzQOKuTagioHc4oqmF+RmoHkobh6o8KMixOI+AFhWzIcJCy+fAAAAAElFTkSuQmCC',
			'CEA1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7WENEQxmmMLQii4m0ijQwhDJMRRYLaBRpYHR0CEURaxBpYG0IgOkFOylq1dSwpauiliK7D00dQiwUTawRUx3ILehiIDcDxUIDBkH4URFicR8AyKzM81jzl1kAAAAASUVORK5CYII=',
			'1BEB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWElEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDHUMdkMRYHURaWYEyAUhiog4ija5AMREUvSjqwE5amTU1bGnoytAsJPcxYjGPEbt5hOyAuCUE080DFX5UhFjcBwAa7cgvKY76pgAAAABJRU5ErkJggg==',
			'0DDE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVklEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDGUMDkMRYA0RaWRsdHZDViUwRaXRtCEQRC2hFEQM7KWrptJWpqyJDs5Dch6YOpxg2O7C5BZubByr8qAixuA8AFSvLVFTRglcAAAAASUVORK5CYII=',
			'0109' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7GB0YAhimMEx1QBJjDWAMYAhlCAhAEhOZAhR1dHQQQRILaGUIYG0IhImBnRS1FIyiwpDcB1EXMBVTb0CDCIodDEA7HFDsYA1gwHALowNrKLqbByr8qAixuA8AFinI/BW36IAAAAAASUVORK5CYII=',
			'276B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WANEQx1CGUMdkMREpjA0Ojo6OgQgiQW0MjS6Njg6iCDrbmVoZW1ghKmDuGnaqmlLp64MzUJ2XwBDACuaeYwOjA6sDYEo5rGCIaqYCBAyoukNDQWqQHPzQIUfFSEW9wEAXyfKnrun1B4AAAAASUVORK5CYII=',
			'082D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGUMdkMRYA1hbGR0dHQKQxESmiDS6NgQ6iCCJBbSytjIgxMBOilq6MmzVysysaUjuA6trZUTTK9LoMAVVDGSHQwCqGNgtDowobgG5mTU0EMXNAxV+VIRY3AcAm47KGLtbdAUAAAAASUVORK5CYII=',
			'E219' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7QkMYQximMEx1QBILaGBtZQhhCAhAERNpdAxhdBBBEWNodJgCFwM7KTRq1dJV01ZFhSG5D6huCsgONL0BQLEGVDFGB6AYmh2sIN0obgkNEQ11DHVAcfNAhR8VIRb3AQCFXMyK6H9ATAAAAABJRU5ErkJggg==',
			'B3A0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QgNYQximMLQiiwVMEWllCGWY6oAs1srQ6OjoEBCAoo6hlbUh0EEEyX2hUavClq6KzJqG5D40dXDzXEOxiDUEoNkhAtQbgOIWkJtZQaoHQfhREWJxHwCW7s5dl79Y3gAAAABJRU5ErkJggg==',
			'389F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7RAMYQxhCGUNDkMQCprC2Mjo6OqCobBVpdG0IRBUDqmNFiIGdtDJqZdjKzMjQLGT3AdUxhARimOeAbh5QzBFNDJtboG5G1TtA4UdFiMV9AMrAyWD8w8CDAAAAAElFTkSuQmCC',
			'5642' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeUlEQVR4nM2QsQ2AMAwETZENzD5OQW+kJAUbwBRx4Q0CO8CUhM4ISpDir3yy5NPD8ZgMLeUXvxi6AEIrGcbZKSgx3xgKrJ7QsJHrNlJG45e2Le3zckzWT3t1QmJ/gKIMkdW6cGX1qliGpboIsWWOL2cfQwP9fZgXvxPXw81ldujWFQAAAABJRU5ErkJggg==',
			'507B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7QkMYAlhDA0MdkMQCGhhDGBoCHQJQxFhbQWIiSGKBASKNDo2OMHVgJ4VNm7Yya+nK0Cxk97UC1U1hRDEPLBbAiGJeQCtrK6MDqpjIFMYQ1gZUvawBQDc3MKK4eaDCj4oQi/sAci3LOTkN45AAAAAASUVORK5CYII=',
			'D2A3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7QgMYQximMIQ6IIkFTGFtZQhldAhAFmsVaXR0dGgQQRFjaHRtCGgIQHJf1NJVS5cCySwk9wHVTWFFqIOJBbCGBqCZx+gAUieC6pYG1oZAFLeEBoiGAu1FcfNAhR8VIRb3AQDGN887zl6wUAAAAABJRU5ErkJggg=='        
        );
        $this->text = array_rand( $images );
        return $images[ $this->text ] ;    
    }
    
    function out_processing_gif(){
        $image = dirname(__FILE__) . '/processing.gif';
        $base64_image = "R0lGODlhFAAUALMIAPh2AP+TMsZiALlcAKNOAOp4ANVqAP+PFv///wAAAAAAAAAAAAAAAAAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh+QQFCgAIACwAAAAAFAAUAAAEUxDJSau9iBDMtebTMEjehgTBJYqkiaLWOlZvGs8WDO6UIPCHw8TnAwWDEuKPcxQml0Ynj2cwYACAS7VqwWItWyuiUJB4s2AxmWxGg9bl6YQtl0cAACH5BAUKAAgALAEAAQASABIAAAROEMkpx6A4W5upENUmEQT2feFIltMJYivbvhnZ3Z1h4FMQIDodz+cL7nDEn5CH8DGZhcLtcMBEoxkqlXKVIgAAibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkphaA4W5upMdUmDQP2feFIltMJYivbvhnZ3V1R4BNBIDodz+cL7nDEn5CH8DGZAMAtEMBEoxkqlXKVIg4HibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpjaE4W5tpKdUmCQL2feFIltMJYivbvhnZ3R0A4NMwIDodz+cL7nDEn5CH8DGZh8ONQMBEoxkqlXKVIgIBibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpS6E4W5spANUmGQb2feFIltMJYivbvhnZ3d1x4JMgIDodz+cL7nDEn5CH8DGZgcBtMMBEoxkqlXKVIggEibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpAaA4W5vpOdUmFQX2feFIltMJYivbvhnZ3V0Q4JNhIDodz+cL7nDEn5CH8DGZBMJNIMBEoxkqlXKVIgYDibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpz6E4W5tpCNUmAQD2feFIltMJYivbvhnZ3R1B4FNRIDodz+cL7nDEn5CH8DGZg8HNYMBEoxkqlXKVIgQCibbK9YLBYvLtHH5K0J0IACH5BAkKAAgALAEAAQASABIAAAROEMkpQ6A4W5spIdUmHQf2feFIltMJYivbvhnZ3d0w4BMAIDodz+cL7nDEn5CH8DGZAsGtUMBEoxkqlXKVIgwGibbK9YLBYvLtHH5K0J0IADs=";
        $binary = is_file($image) ? join("",file($image)) : base64_decode($base64_image); 
        header("Cache-Control: post-check=0, pre-check=0, max-age=0, no-store, no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: image/gif");
        echo $binary;
    }

}
# end of class phpfmgImage
# ------------------------------------------------------
# end of module : captcha


# module user
# ------------------------------------------------------
function phpfmg_user_isLogin(){
    return ( isset($_SESSION['authenticated']) && true === $_SESSION['authenticated'] );
}


function phpfmg_user_logout(){
    session_destroy();
    header("Location: admin.php");
}

function phpfmg_user_login()
{
    if( phpfmg_user_isLogin() ){
        return true ;
    };
    
    $sErr = "" ;
    if( 'Y' == $_POST['formmail_submit'] ){
        if(
            defined( 'PHPFMG_USER' ) && strtolower(PHPFMG_USER) == strtolower($_POST['Username']) &&
            defined( 'PHPFMG_PW' )   && strtolower(PHPFMG_PW) == strtolower($_POST['Password']) 
        ){
             $_SESSION['authenticated'] = true ;
             return true ;
             
        }else{
            $sErr = 'Login failed. Please try again.';
        }
    };
    
    // show login form 
    phpfmg_admin_header();
?>
<form name="frmFormMail" action="" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:380px;height:260px;">
<fieldset style="padding:18px;" >
<table cellspacing='3' cellpadding='3' border='0' >
	<tr>
		<td class="form_field" valign='top' align='right'>Email :</td>
		<td class="form_text">
            <input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" class='text_box' >
		</td>
	</tr>

	<tr>
		<td class="form_field" valign='top' align='right'>Password :</td>
		<td class="form_text">
            <input type="password" name="Password"  value="" class='text_box'>
		</td>
	</tr>

	<tr><td colspan=3 align='center'>
        <input type='submit' value='Login'><br><br>
        <?php if( $sErr ) echo "<span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
        <a href="admin.php?mod=mail&func=request_password">I forgot my password</a>   
    </td></tr>
</table>
</fieldset>
</div>
<script type="text/javascript">
    document.frmFormMail.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();
}


function phpfmg_mail_request_password(){
    $sErr = '';
    if( $_POST['formmail_submit'] == 'Y' ){
        if( strtoupper(trim($_POST['Username'])) == strtoupper(trim(PHPFMG_USER)) ){
            phpfmg_mail_password();
            exit;
        }else{
            $sErr = "Failed to verify your email.";
        };
    };
    
    $n1 = strpos(PHPFMG_USER,'@');
    $n2 = strrpos(PHPFMG_USER,'.');
    $email = substr(PHPFMG_USER,0,1) . str_repeat('*',$n1-1) . 
            '@' . substr(PHPFMG_USER,$n1+1,1) . str_repeat('*',$n2-$n1-2) . 
            '.' . substr(PHPFMG_USER,$n2+1,1) . str_repeat('*',strlen(PHPFMG_USER)-$n2-2) ;


    phpfmg_admin_header("Request Password of Email Form Admin Panel");
?>
<form name="frmRequestPassword" action="admin.php?mod=mail&func=request_password" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:580px;height:260px;text-align:left;">
<fieldset style="padding:18px;" >
<legend>Request Password</legend>
Enter Email Address <b><?php echo strtoupper($email) ;?></b>:<br />
<input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" style="width:380px;">
<input type='submit' value='Verify'><br>
The password will be sent to this email address. 
<?php if( $sErr ) echo "<br /><br /><span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
</fieldset>
</div>
<script type="text/javascript">
    document.frmRequestPassword.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();    
}


function phpfmg_mail_password(){
    phpfmg_admin_header();
    if( defined( 'PHPFMG_USER' ) && defined( 'PHPFMG_PW' ) ){
        $body = "Here is the password for your form admin panel:\n\nUsername: " . PHPFMG_USER . "\nPassword: " . PHPFMG_PW . "\n\n" ;
        if( 'html' == PHPFMG_MAIL_TYPE )
            $body = nl2br($body);
        mailAttachments( PHPFMG_USER, "Password for Your Form Admin Panel", $body, PHPFMG_USER, 'You', "You <" . PHPFMG_USER . ">" );
        echo "<center>Your password has been sent.<br><br><a href='admin.php'>Click here to login again</a></center>";
    };   
    phpfmg_admin_footer();
}


function phpfmg_writable_check(){
 
    if( is_writable( dirname(PHPFMG_SAVE_FILE) ) && is_writable( dirname(PHPFMG_EMAILS_LOGFILE) )  ){
        return ;
    };
?>
<style type="text/css">
    .fmg_warning{
        background-color: #F4F6E5;
        border: 1px dashed #ff0000;
        padding: 16px;
        color : black;
        margin: 10px;
        line-height: 180%;
        width:80%;
    }
    
    .fmg_warning_title{
        font-weight: bold;
    }

</style>
<br><br>
<div class="fmg_warning">
    <div class="fmg_warning_title">Your form data or email traffic log is NOT saving.</div>
    The form data (<?php echo PHPFMG_SAVE_FILE ?>) and email traffic log (<?php echo PHPFMG_EMAILS_LOGFILE?>) will be created automatically when the form is submitted. 
    However, the script doesn't have writable permission to create those files. In order to save your valuable information, please set the directory to writable.
     If you don't know how to do it, please ask for help from your web Administrator or Technical Support of your hosting company.   
</div>
<br><br>
<?php
}


function phpfmg_log_view(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    
    phpfmg_admin_header();
   
    $file = $files[$n];
    if( is_file($file) ){
        if( 1== $n ){
            echo "<pre>\n";
            echo join("",file($file) );
            echo "</pre>\n";
        }else{
            $man = new phpfmgDataManager();
            $man->displayRecords();
        };
     

    }else{
        echo "<b>No form data found.</b>";
    };
    phpfmg_admin_footer();
}


function phpfmg_log_download(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );

    $file = $files[$n];
    if( is_file($file) ){
        phpfmg_util_download( $file, PHPFMG_SAVE_FILE == $file ? 'form-data.csv' : 'email-traffics.txt', true, 1 ); // skip the first line
    }else{
        phpfmg_admin_header();
        echo "<b>No email traffic log found.</b>";
        phpfmg_admin_footer();
    };

}


function phpfmg_log_delete(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    phpfmg_admin_header();

    $file = $files[$n];
    if( is_file($file) ){
        echo unlink($file) ? "It has been deleted!" : "Failed to delete!" ;
    };
    phpfmg_admin_footer();
}


function phpfmg_util_download($file, $filename='', $toCSV = false, $skipN = 0 ){
    if (!is_file($file)) return false ;

    set_time_limit(0);


    $buffer = "";
    $i = 0 ;
    $fp = @fopen($file, 'rb');
    while( !feof($fp)) { 
        $i ++ ;
        $line = fgets($fp);
        if($i > $skipN){ // skip lines
            if( $toCSV ){ 
              $line = str_replace( chr(0x09), ',', $line );
              $buffer .= phpfmg_data2record( $line, false );
            }else{
                $buffer .= $line;
            };
        }; 
    }; 
    fclose ($fp);
  

    
    /*
        If the Content-Length is NOT THE SAME SIZE as the real conent output, Windows+IIS might be hung!!
    */
    $len = strlen($buffer);
    $filename = basename( '' == $filename ? $file : $filename );
    $file_extension = strtolower(substr(strrchr($filename,"."),1));

    switch( $file_extension ) {
        case "pdf": $ctype="application/pdf"; break;
        case "exe": $ctype="application/octet-stream"; break;
        case "zip": $ctype="application/zip"; break;
        case "doc": $ctype="application/msword"; break;
        case "xls": $ctype="application/vnd.ms-excel"; break;
        case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
        case "gif": $ctype="image/gif"; break;
        case "png": $ctype="image/png"; break;
        case "jpeg":
        case "jpg": $ctype="image/jpg"; break;
        case "mp3": $ctype="audio/mpeg"; break;
        case "wav": $ctype="audio/x-wav"; break;
        case "mpeg":
        case "mpg":
        case "mpe": $ctype="video/mpeg"; break;
        case "mov": $ctype="video/quicktime"; break;
        case "avi": $ctype="video/x-msvideo"; break;
        //The following are for extensions that shouldn't be downloaded (sensitive stuff, like php files)
        case "php":
        case "htm":
        case "html": 
                $ctype="text/plain"; break;
        default: 
            $ctype="application/x-download";
    }
                                            

    //Begin writing headers
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public"); 
    header("Content-Description: File Transfer");
    //Use the switch-generated Content-Type
    header("Content-Type: $ctype");
    //Force the download
    header("Content-Disposition: attachment; filename=".$filename.";" );
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".$len);
    
    while (@ob_end_clean()); // no output buffering !
    flush();
    echo $buffer ;
    
    return true;
 
    
}
?>
<?php
require('excel.php');
?>

<?php
require_once( dirname(__FILE__).'/form.lib.php' );

define( 'PHPFMG_USER', "baollantines@yahoo.es" ); // must be a email address. for sending password to you.
define( 'PHPFMG_PW', "11e7ba" );

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
			'10FF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAV0lEQVR4nGNYhQEaGAYTpIn7GB0YAlhDA0NDkMRYHRhDWEEySGKiDqyt6GKMDiKNrggxsJNWZk1bmRq6MjQLyX1o6vCIYbMDi1tCgG5GExuo8KMixOI+ACzfxakRquGgAAAAAElFTkSuQmCC',
			'99E2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDHaY6IImJTGFtZW1gCAhAEgtoFWl0bWB0EMEQA6pHct+0qUuXpoauWhWF5D5WV8ZAoLpGZDsYWhlAeluR3SLQygISm8KAxS2YbnYMDRkE4UdFiMV9AM6xy7l+r9dLAAAAAElFTkSuQmCC',
			'4AC0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpI37pjAEMIQ6tKKIhTCGMDoETHVAEmMMYW1lbRAICEASY50i0ujawOggguS+adOmrUxdtTJrGpL7AlDVgWFoqGgouhgDWB2qHSAxRzS3gMQc0N08UOFHPYjFfQAehsyIJVOrTgAAAABJRU5ErkJggg==',
			'B934' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QgMYQxhDGRoCkMQCprC2sjY6NKKItYo0OgBJVHVAsUaHKQFI7guNWro0a+qqqCgk9wVMYQx0aHR0QDWPAWheYGgIihgLyA5sbkERw+bmgQo/KkIs7gMAxP/QqTptMtMAAAAASUVORK5CYII=',
			'C2C2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7WEMYQxhCHaY6IImJtLK2MjoEBAQgiQU0ijS6Ngg6iCCLNTAAxYDqkdwXtWrV0qUQGu4+oLoprEC1Dqh6A4BirQwodjA6sDYITGFAdUsDyC2obhYNdQh1DA0ZBOFHRYjFfQDPpsyRypI5EgAAAABJRU5ErkJggg==',
			'9C02' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WAMYQxmmMEx1QBITmcLa6BDKEBCAJBbQKtLg6OjoIIImxtoQ0CCC5L5pU6etWroqCggR7mN1BatrRLaDAaK3FdktAmA7HKYwYHELppsZQ0MGQfhREWJxHwDR3cyOI0klcwAAAABJRU5ErkJggg==',
			'F9BC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDGaYGIIkFNLC2sjY6BIigiIk0ujYEOrCgizU6OiC7LzRq6dLU0JVZyO4LaGAMRFIHFWMAm4cqxoLFDmxuwXTzQIUfFSEW9wEA24zNj1EKZp0AAAAASUVORK5CYII=',
			'5D4D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QkNEQxgaHUMdkMQCGkRaGVodHQJQxRodpjo6iCCJBQYAxQLhYmAnhU2btjIzMzNrGrL7WkUaXRtR9YLFQgNRxAKAYg5o6kSmAN3SiOoW1gBMNw9U+FERYnEfAKJRzTobHtgnAAAAAElFTkSuQmCC',
			'9490' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WAMYWhlCgRhJTGQKw1RGR4epDkhiAUBVrA0BAQEoYoyurA2BDiJI7ps2denSlZmRWdOQ3MfqKtLKEAJXB4GtoqEODahiAq0MrYxodgDd0oruFmxuHqjwoyLE4j4AFhXLSlna5PIAAAAASUVORK5CYII=',
			'0ABB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7GB0YAlhDGUMdkMRYAxhDWBsdHQKQxESmsLayNgQ6iCCJBbSKNLoi1IGdFLV02srU0JWhWUjuQ1MHFRMNdUUzT2QKUB2aGGsApl5GB6AYmpsHKvyoCLG4DwAHx8xi5ZDZKQAAAABJRU5ErkJggg==',
			'941B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WAMYWhmmMIY6IImJTGGYyhDC6BCAJBbQyhDKCBQTQRFjdAXqhakDO2na1KVLV01bGZqF5D5WV5FWJHUQ2Coa6jAF1TyBVrBbUMSAbsHQC3IzY6gjipsHKvyoCLG4DwCJSModyBsx2gAAAABJRU5ErkJggg==',
			'2123' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7WAMYAhhCGUIdkMREpjAGMDo6OgQgiQW0sgawNgQ0iCDrbgXqBYoFILtv2qqoVSuzlmYhuw9kRytDA7J5jA5AsSkMKOaxQlSiiAHZAYwOjChuCQ1lBcIAFDcPVPhREWJxHwC+D8lezYQviQAAAABJRU5ErkJggg==',
			'A415' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nM2QsRGAMAhFP4UbxH1o0lNIk2ls2CC6QQozpTkrLrHUu0D3uA/vQB1qx0z9ix8xDJlUHFsEB7Y2cSxkKHVMjGLLRnZ+qZRSzysl5ycW2o22w2VVV+WOiT0uPDKIdIyUD57gfx/2i98NIVbLIDhEIE8AAAAASUVORK5CYII=',
			'6A3B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WAMYAhhDGUMdkMREpjCGsDY6OgQgiQW0sLYyNAQ6iCCLNYg0OiDUgZ0UGTVtZdbUlaFZSO4LmYKiDqK3VRRoJ5p5rUB1aGIiQL2uaHpZA0QaHdHcPFDhR0WIxX0AWZjNb7DMRsMAAAAASUVORK5CYII=',
			'1717' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7GB1EQx2mMIaGIImxOjA0OoQwNIggiYkCxRzRxBgdGFoZpjA0BCC5b2XWqmlACKQQ7gOqCwCqa0W1Fyg6BaQbWYy1ASgSgCoGtHEKUC2yW0JEGhhDHVHEBir8qAixuA8AsSTIV28dluAAAAAASUVORK5CYII=',
			'2CA7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7WAMYQxmmMIaGIImJTGFtdAgF0khiAa0iDY6ODihiDEAx1oYAIERy37Rpq5auilqZhey+ALC6VmR7GR2AYqEBU1DcAjTdtSEgAFkMqKrRtSHQAVksNJQxlBVNbKDCj4oQi/sAvLjMmcDAn3sAAAAASUVORK5CYII=',
			'80B6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7WAMYAlhDGaY6IImJTGEMYW10CAhAEgtoZW1lbQh0EEBRJ9Lo2ujogOy+pVHTVqaGrkzNQnIfVB2aeUAxoHkiWOwQIeAWbG4eqPCjIsTiPgBsEMxzIkOORwAAAABJRU5ErkJggg==',
			'595F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QkMYQ1hDHUNDkMQCGlhbWRsYHRhQxEQaXdHEAgOAYlPhYmAnhU1bujQ1MzM0C9l9rYyBDg2BKHoZWhka0cUCWlmAdqCKiUxhbWV0dEQRYw1gDGEIRXXLQIUfFSEW9wEAw+bJ+eShxSIAAAAASUVORK5CYII=',
			'F190' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QkMZAhhCGVqRxQIaGAMYHR2mOqCIsQawNgQEBKCIMQDFAh1EkNwXGrUqamVmZNY0JPeB1DGEwNUhxBowxRix2IHFLaHobh6o8KMixOI+AKNgywM8XKaiAAAAAElFTkSuQmCC',
			'3D81' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7RANEQxhCGVqRxQKmiLQyOjpMRVHZKtLo2hAQiiI2RaTR0dEBphfspJVR01Zmha5aiuI+VHXI5hEUg7oFRQzq5tCAQRB+VIRY3AcAl5nMmVKlrokAAAAASUVORK5CYII=',
			'BAA7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QgMYAhimMIaGIIkFTGEMYQhlaBBBFmtlbWV0dEAVmyLS6NoQAIQI94VGTVuZuipqZRaS+6DqWhlQzBMNdQ0NmIIqBlYXwIBhR6ADqpsxxQYq/KgIsbgPAC4NzttP5/C2AAAAAElFTkSuQmCC',
			'151D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7GB1EQxmmMIY6IImxOog0MIQwOgQgiYkCxRiBYiIoekVCgHphYmAnrcyaunTVtJVZ05Dcx+jA0OgwBV0vNjERLGKsrSA7UNwSAnRJqCOKmwcq/KgIsbgPAJ+Mx+h3OOXCAAAAAElFTkSuQmCC',
			'0CCA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7GB0YQxlCHVqRxVgDWBsdHQKmOiCJiUwRaXBtEAgIQBILaBVpYAWaIILkvqil01YtXbUyaxqS+9DUIYuFhmDYIYiiDuKWQBQxiJsdUcQGKvyoCLG4DwBzNctrUDe5cAAAAABJRU5ErkJggg==',
			'1741' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7GB1EQx0aHVqRxVgdGIAiDlORxURBYlMdQlH1MrQyBML1gp20MmvVtJWZWUuR3QdUF8CKZgejA6MDa2gAmhhrAwOGOhEMMdEQsFhowCAIPypCLO4DALUkyi1frEfWAAAAAElFTkSuQmCC',
			'2C6D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WAMYQxlCGUMdkMREprA2Ojo6OgQgiQW0ijS4Njg6iCDrBoqxNjDCxCBumjZt1dKpK7OmIbsvAKjOEVUvSBdrQyCKGGsDyA5UMaAqDLeEhmK6eaDCj4oQi/sAx5bLFJvnsUkAAAAASUVORK5CYII=',
			'4E41' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpI37poiGMjQ6tKKIhYg0MLQ6TEUWYwSJTXUIRRZjnQIUC4TrBTtp2rSpYSszs5Yiuy8AqI4VzY7QUKBYaACqvSDz0N2CVQzs5tCAwRB+1INY3AcAU6rMeRZrlrcAAAAASUVORK5CYII=',
			'B2A1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QgMYQximMLQiiwVMYW1lCGWYiiLWKtLo6OgQiqqOodEVKIPsvtCoVUuXropaiuw+oLoprAh1UPMYAlhD0cUYHTDUAXWii4UGiIYC7Q0NGAThR0WIxX0AjRrOTlBhzXsAAAAASUVORK5CYII=',
			'7A54' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7QkMZAlhDHRoCkEVbGUNYGxgaUcVYW4FirShiU0QaXacyTAlAdl/UtJWpmVlRUUjuY3QQaXRoCHRA1svaIAq0NTA0BElMpAFoHtAlyOoCgGKOjg4YYg6hDKhuHqDwoyLE4j4ATkLOTY1FTpUAAAAASUVORK5CYII=',
			'81D1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7WAMYAlhDGVqRxUSmMAawNjpMRRYLaGUNYG0ICEVVxwASg+kFO2lp1KqopSCE5D40dVDziBMD6210QBEDuiQU6ObQgEEQflSEWNwHAJ49ywV4jDwYAAAAAElFTkSuQmCC',
			'4973' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nM3QwQ2AMAhAUZrIBh0IN8CkXaJT0EM3IN2gF6fU9CJVjxqF2z+QF2C9jMCf9h2fuoCRI9kWsIAsxKa54DMJizcNdW+9Hr5aW0ttbcn4WN1CCmLvxQiZGIZ7oFOe6dywoLjB0s0Co/mr/z23N74Nb8/NC/d4JQkAAAAASUVORK5CYII=',
			'3CAF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7RAMYQxmmMIaGIIkFTGFtdAhldEBR2SrS4OjoiCo2RaSBtSEQJgZ20sqoaauWrooMzUJ2H6o6uHmsoZhirmjqQG5BFwO5GcO8AQo/KkIs7gMAU9nKytIHalIAAAAASUVORK5CYII=',
			'2EDB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7WANEQ1lDGUMdkMREpog0sDY6OgQgiQW0AsUaAh1EkHVDxQKQ3TdtatjSVZGhWcjuC0BRB4aMDpjmsTZgiok0YLolNBTTzQMVflSEWNwHAALEy0BQoXIlAAAAAElFTkSuQmCC',
			'C40B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WEMYWhmmMIY6IImJtDJMZQhldAhAEgtoBIo4OjqIIIs1MLqyNgTC1IGdFLVq6dKlqyJDs5DcFwA0EUkdVEw01BUoJoJqRyu6HUC3tKK7BZubByr8qAixuA8A9OTLQbYqZz0AAAAASUVORK5CYII=',
			'82B8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDGaY6IImJTGFtZW10CAhAEgtoFWl0bQh0EEFRx9DoilAHdtLSqFVLl4aumpqF5D6guimY5jEEsKKZF9DK6IAuBnRLA7pe1gDRUFc0Nw9U+FERYnEfAI34zUJljIPVAAAAAElFTkSuQmCC',
			'5952' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAfElEQVR4nM2QsQ3DMAwEScDcQAPRRfoXIKVI78KZgi64gZwN3HjKqGQQlzZgfvXX/IG0/53RnXKJXy1cpOqqgcHExQj4YWl5GGsKLKOzlSwFv+dn26b5vb+in3NWwxI3yKl3eHSBD30DLbLUxHlURCbgQpVrucH/TsyB3xclpsy/BBDDdgAAAABJRU5ErkJggg==',
			'3109' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7RAMYAhimMEx1QBILmMIYwBDKEBCArLKVNYDR0dFBBFlsCkMAa0MgTAzspJVRq6KWroqKCkN2H1hdwFQUva1gsQZ0MUZHBxQ7AoB60d0iGsAaiu7mgQo/KkIs7gMAggjJTrc1An8AAAAASUVORK5CYII=',
			'CD4B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7WENEQxgaHUMdkMREWkVaGVodHQKQxAIaRRodpjo6iCCLNQDFAuHqwE6KWjVtZWZmZmgWkvtA6lwb0cwDiYUGopoHsqMR1Q6wW9D0YnPzQIUfFSEW9wEAFoDNueGVddAAAAAASUVORK5CYII=',
			'C738' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WENEQx1DGaY6IImJtDI0ujY6BAQgiQU0MjQ6NAQ6iCCLNTAAVcLVgZ0UtWrVtFVTV03NQnIfUD6AAd28BkYHBnTzGlkb0MVEWkUaWNH0soaINDCiuXmgwo+KEIv7ADnlza9HZxReAAAAAElFTkSuQmCC',
			'4865' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpI37pjCGMIQyhgYgi4WwtjI6Ojogq2MMEWl0bUAVY53C2srawOjqgOS+adNWhi2dujIqCsl9ASB1jg4NIkh6Q0NB5gWgiDFMAYkFOqCKgdziEIDiPrCbGaY6DIbwox7E4j4An/bLRvC8MRAAAAAASUVORK5CYII=',
			'199C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGaYGIImxOrC2Mjo6BIggiYk6iDS6NgQ6sKDohYghu29l1tKlmZmRWcjuA9oR6BACVwcVY2h0aEAXY2l0xLADi1tCMN08UOFHRYjFfQBxd8hsxrry0wAAAABJRU5ErkJggg==',
			'F67D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDA0MdkMQCGlhbGRoCHQJQxEQaQWIiqGINDI2OMDGwk0KjpoWtWroyaxqS+wIaRFsZpjCi6210CMAUc3RAF2NtZW1gRHML0M0NjChuHqjwoyLE4j4Am8XMZGjjRqUAAAAASUVORK5CYII=',
			'E155' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QkMYAlhDHUMDkMQCGhgDWBsYHRhQxFixiAH1TmV0dUByX2jUqqilmZlRUUjuA6kDkSJoerGJsTYEOqCLMTo6BCC7LzSENZQhlGGqwyAIPypCLO4DAHx9yjWMYK6wAAAAAElFTkSuQmCC',
			'B389' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QgNYQxhCGaY6IIkFTBFpZXR0CAhAFmtlaHRtCHQQQVHHAFTnCBMDOyk0alXYqtBVUWFI7oOoc5gqgmFeQAMWMTQ7MN2Czc0DFX5UhFjcBwBUsM0z9CuBWAAAAABJRU5ErkJggg==',
			'AEA7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7GB1EQxmmMIaGIImxBog0MIQyNIggiYlMEWlgdHRAEQtoFWlgbQgAQoT7opZODVu6KmplFpL7oOpake0NDQWKhQZMYcA0LwBTLNABVUw0FF1soMKPihCL+wB1Lsyttcjh+AAAAABJRU5ErkJggg==',
			'B533' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QgNEQxlDGUIdkMQCpog0sDY6OgQgi7WKgMgGEVR1IQyNDg0BSO4LjZq6dNXUVUuzkNwXMAWoCqEOah5EpwiqHZhiU1hb0d0SGsAYgu7mgQo/KkIs7gMATCTPh1PEy7UAAAAASUVORK5CYII=',
			'5BAB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QkNEQximMIY6IIkFNIi0MoQyOgSgijU6Ojo6iCCJBQaItLI2BMLUgZ0UNm1q2NJVkaFZyO5rRVEHE2t0DQ1EMS8AJNaAKiYyBVMva4BoCFAMxc0DFX5UhFjcBwCtC8yx/X/zrwAAAABJRU5ErkJggg==',
			'42D3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpI37pjCGsIYyhDogi4WwtrI2OjoEIIkxhog0ujYENIggibFOYQCLBSC5b9q0VUuXropamoXkvoApDFNYEerAMDSUIYAVzTygWxwwxVgb0N3CMEU01BXdzQMVftSDWNwHALD1zXiLCmM/AAAAAElFTkSuQmCC',
			'5DEF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWUlEQVR4nGNYhQEaGAYTpIn7QkNEQ1hDHUNDkMQCGkRaWRsYHRhQxRpd0cQCA1DEwE4KmzZtZWroytAsZPe1YurFJhaARUxkCqZbWAPAbkY1b4DCj4oQi/sA4QLKFDA1jPIAAAAASUVORK5CYII=',
			'E8D4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7QkMYQ1hDGRoCkMQCGlhbWRsdGlHFRBpdGwJaMdQ1BEwJQHJfaNTKsKWroqKikNwHURfogGleYGgIph3Y3IIihs3NAxV+VIRY3AcA/PvQF1D04Q4AAAAASUVORK5CYII=',
			'D345' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QgNYQxgaHUMDkMQCpoi0MrQ6OiCrC2hlaHSYiiHWyhDo6OqA5L6opavCVmZmRkUhuQ+kjrXRoUEEzTxXoK3oYg6Njg4i6G5pdAhAdh/EzQ5THQZB+FERYnEfAGRFziS0K/zSAAAAAElFTkSuQmCC',
			'A671' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDA1qRxVgDWIH8gKnIYiJTRBqBYqHIYgGtIg0MjQ4wvWAnRS2dFrZqKRAiuS+gVbSVYQoDih2hoSKNDgGoYkDzGh0d0MVYW1kb0MWAbm5gCA0YBOFHRYjFfQCJYcyhSWlNpQAAAABJRU5ErkJggg==',
			'2DA0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7WANEQximMLQii4lMEWllCGWY6oAkFtAq0ujo6BAQgKwbKObaEOggguy+adNWpq6KzJqG7L4AFHVgyOgAFAtFFWNtAKkLQLFDpEGklbUhAMUtoaGiIUAxFDcPVPhREWJxHwAyQs0729nECgAAAABJRU5ErkJggg==',
			'182C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGaYGIImxOrC2Mjo6BIggiYk6iDS6NgQ6sKDoZW1lAIohu29l1sqwVSszs5DdB1bXyuiAaq9Io8MULGIBjBh2AFWhuiWEMYQ1NADFzQMVflSEWNwHAJjfx7YkzoEUAAAAAElFTkSuQmCC',
			'5C05' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QkMYQxmmMIYGIIkFNLA2OoQyOjCgiIk0ODo6oogFBog0sDYEujoguS9s2rRVS1dFRkUhu68VpA5kApJuLGIBrRA7kMVEpoDcwhCA7D7WAJCbGaY6DILwoyLE4j4AHbnMKkNKPxcAAAAASUVORK5CYII=',
			'EF10' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QkNEQx2mMLQiiwU0iDQwhDBMdUATYwxhCAhAVzeF0UEEyX2hUVPDVk1bmTUNyX1o6giIYbMD1S2hIUC3hDqguHmgwo+KEIv7ANI1zMS0cAdxAAAAAElFTkSuQmCC',
			'F21E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QkMZQximMIYGIIkFNLC2MoQwOjCgiIk0OmKIMTQ6TIGLgZ0UGrVq6appK0OzkNwHVDeFYQqG3gBMMSAfQ4y1AVNMNNQRCJHdPFDhR0WIxX0AOAPKrok1ddgAAAAASUVORK5CYII=',
			'2AA3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WAMYAhimMIQ6IImJTGEMYQhldAhAEgtoZW1ldHRoEEHW3SrS6NoQ0BCA7L5p01amropamoXsvgAUdWDI6CAa6hoagGIeawNEHbKYCFgsEMUtoaFgdShuHqjwoyLE4j4AltnN1RJuhW0AAAAASUVORK5CYII=',
			'F348' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QkNZQxgaHaY6IIkFNIi0MrQ6BASgiIFUOTqIoIq1MgTC1YGdFBq1KmxlZtbULCT3gdSxNmKa5xoaiG5eo0Mjuh1At2DoxXTzQIUfFSEW9wEAHAPOmOhp3wIAAAAASUVORK5CYII=',
			'FDFD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAU0lEQVR4nGNYhQEaGAYTpIn7QkNFQ1hDA0MdkMQCGkRaWRsYHQJQxRpdgWIiuMXATgqNmrYyNXRl1jQk9xGhF58YFrcA3dzAiOLmgQo/KkIs7gMA3njMvJbE/5UAAAAASUVORK5CYII=',
			'33AC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7RANYQximMEwNQBILmCLSyhDKECCCrLKVodHR0dGBBVlsCkMra0OgA7L7VkatClu6KjILxX2o6uDmuYZiEQOqQ7YD5BbWhgAUt4DcDBRDcfNAhR8VIRb3AQAwQ8uh7g+iqQAAAABJRU5ErkJggg==',
			'4F57' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpI37poiGuoY6hoYgi4WINLACaREkMUYsYqxTgGJTGRoCkNw3bdrUsKWZWSuzkNwXMAWkK6AV2d7QULDYFFS3gOwICEAXY3R0dEAXYwhlRBUbqPCjHsTiPgBu9stulgMm+QAAAABJRU5ErkJggg==',
			'E4C8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QkMYWhlCHaY6IIkFNDBMZXQICAhAFQtlbRB0EEERY3RlbWCAqQM7KTRq6dKlq1ZNzUJyX0CDSCuSOqiYaKhrAyOaeQytmHYwtKK7BZubByr8qAixuA8AGmHM8+ucMCwAAAAASUVORK5CYII=',
			'0AB7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7GB0YAlhDGUNDkMRYAxhDWBsdGkSQxESmsLayNgSgiAW0ijS6AtUFILkvaum0lamhq1ZmIbkPqq6VAUWvaKhrQ8AUBhQ7gOoaAgIYUNwC0uvogOpmoFgoI4rYQIUfFSEW9wEA5AfM1fkJu4gAAAAASUVORK5CYII=',
			'D9B8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDGaY6IIkFTGFtZW10CAhAFmsVaXRtCHQQQRdDqAM7KWrp0qWpoaumZiG5L6CVMdAVwzwGLOaxYIphcQs2Nw9U+FERYnEfAOmezyYr14E+AAAAAElFTkSuQmCC',
			'7C95' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7QkMZQxmAOABZtJW10dHR0QFFZatIg2tDIKrYFJEG1oZAVwdk90VNW7UyMzIqCsl9jA4iDQwhAQ0iSHpZwTxUMREgdATagSwW0AByi0NAAIoYyM0MUx0GQfhREWJxHwBY3cvJ2tCpeAAAAABJRU5ErkJggg==',
			'CCDC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7WEMYQ1lDGaYGIImJtLI2ujY6BIggiQU0ijS4NgQ6sCCLNYg0sALFkN0XtWraqqWrIrOQ3YemDrcYFjuwuQWbmwcq/KgIsbgPAGKEzSWHE48xAAAAAElFTkSuQmCC',
			'1B80' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7GB1EQxhCGVqRxVgdRFoZHR2mOiCJiTqINLo2BAQEoOgFqXN0EEFy38qsqWGrQldmTUNyH5o6mBjQvEAsYtjsQHNLCKabByr8qAixuA8AeKfJU/e7E3gAAAAASUVORK5CYII=',
			'57E8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkNEQ11DHaY6IIkFNDA0ujYwBARgiDE6iCCJBQYwtLIi1IGdFDZt1bSloaumZiG7r5UhgBXNPIZWRgdWNPMCgKahi4lMEWlA18saABRDc/NAhR8VIRb3AQASKMvPVPxOuwAAAABJRU5ErkJggg==',
			'1DD8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDGaY6IImxOoi0sjY6BAQgiYk6iDS6NgQ6iKDoBYkFwNSBnbQya9rK1FVRU7OQ3IemDkkMm3kYYphuCcF080CFHxUhFvcBAAFpy01oy/p6AAAAAElFTkSuQmCC',
			'47D0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpI37poiGuoYytKKIhTA0ujY6THVAEmMEiTUEBAQgibFOYWhlbQh0EEFy37Rpq6YtXRWZNQ3JfQFTGAKQ1IFhaCijA7oYwxTWBlY0OximiDSworkFLIbu5oEKP+pBLO4DAPOezMouzzU2AAAAAElFTkSuQmCC',
			'A2B9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDGaY6IImxBrC2sjY6BAQgiYlMEWl0bQh0EEESC2hlaHRtdISJgZ0UtXTV0qWhq6LCkNwHVDcFaN5UZL2hoQwBrA0BDajmMToAxdDsYG1Ad0tAq2ioK5qbByr8qAixuA8AGFDNN62ztSgAAAAASUVORK5CYII=',
			'6CEF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7WAMYQ1lDHUNDkMREprA2ujYwOiCrC2gRacAQaxBpYEWIgZ0UGTVt1dLQlaFZSO4LmYKiDqK3FbsYuh3Y3AJ1M4rYQIUfFSEW9wEAjcfJ9Ucdjq0AAAAASUVORK5CYII=',
			'50A7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7QkMYAhimMIaGIIkFNDCGMIQyNIigiLG2Mjo6oIgFBog0ugJlApDcFzZt2srUVVErs5Dd1wpW14piM0gsNGAKslhAK2sra0NAALKYyBTGENaGQAdkMdYAhgB0sYEKPypCLO4DAJjSzGBVnKHFAAAAAElFTkSuQmCC',
			'17A9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7GB1EQx2mMEx1QBJjdWBodAhlCAhAEhMFijk6OjqIoOhlaGVtCISJgZ20MmvVtKWroqLCkNwHVBfA2hAwFVUvowNraEADqhhrA1Admh0iIDFUt4SAxVDcPFDhR0WIxX0A/vfJ0g5/UEMAAAAASUVORK5CYII=',
			'BCCA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QgMYQxlCHVqRxQKmsDY6OgRMdUAWaxVpcG0QCAhAUSfSwNrA6CCC5L7QqGmrlq5amTUNyX1o6uDmAcVCQzDsEERVB3ZLIIoYxM2OKGIDFX5UhFjcBwAKyc15ezg4IgAAAABJRU5ErkJggg==',
			'8602' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WAMYQximMEx1QBITmcLayhDKEBCAJBbQKtLI6OjoIIKiTqSBtSGgQQTJfUujpoUtXRUFhAj3iUwRbQWqa3RAM88VSDKgiQGtmMKAxS2YbmYMDRkE4UdFiMV9AF+ezF7kDmZTAAAAAElFTkSuQmCC',
			'A5FE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7GB1EQ1lDA0MDkMRYA0QaWIEyyOpEpmCKBbSKhCCJgZ0UtXTq0qWhK0OzkNwX0MrQ6IqmNzQUUwxoHhYx1lZMexlB9qK4eaDCj4oQi/sALKTKDFKUgXIAAAAASUVORK5CYII=',
			'EA7C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkMYAlhDA6YGIIkFNDCGAMkAERQx1laGhkAHFhQxkUaHRkcHZPeFRk1bmbV0ZRay+8DqpjA6MKDoFQ11CEAXEwGaxohhh2sDA4pbQkPAYihuHqjwoyLE4j4AT93NInme8OIAAAAASUVORK5CYII=',
			'0815' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7GB0YQximMIYGIImxBrC2MoQwOiCrE5ki0uiIJhbQClQ3hdHVAcl9UUtXhq2atjIqCsl9EHVAM1D0ijQ6oImB7HCYwugggu6WKQwByO4DuZkx1GGqwyAIPypCLO4DAMDzyonETYPfAAAAAElFTkSuQmCC',
			'FE57' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QkNFQ1lDHUNDkMQCGkQaWIG0CDFiU0E0wn2hUVPDlmZmrcxCcl8AWFdAKwOaXiA5BV2MtSEgAF2M0dHRAVVMNJQhlBFFbKDCj4oQi/sA58bMkTleGy4AAAAASUVORK5CYII=',
			'2366' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WANYQxhCGaY6IImJTBFpZXR0CAhAEgtoZWh0bXB0EEDW3crQytrA6IDivmmrwpZOXZmahey+AKA6R0cU84C6gOYFOoggu6UBU0ykAdMtoaGYbh6o8KMixOI+AKU0ywgyZ7viAAAAAElFTkSuQmCC',
			'6E36' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7WANEQxlDGaY6IImJTBFpYG10CAhAEgtoEQGSgQ4CyGINQLFGRwdk90VGTQ1bNXVlahaS+0KmgNWhmtcKMU+EgBg2t2Bz80CFHxUhFvcBAKpZzKE/mc1uAAAAAElFTkSuQmCC',
			'10B1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7GB0YAlhDGVqRxVgdGENYGx2mIouJOrC2sjYEhKLqFWl0bXSA6QU7aWXWtJWpoauWIrsPTR1CrCEATQxsB5oY2C0oYqIhYDeHBgyC8KMixOI+AOTkya6XylUZAAAAAElFTkSuQmCC',
			'B3E6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QgNYQ1hDHaY6IIkFTBFpZW1gCAhAFmtlaHRtYHQQQFHHAFTH6IDsvtCoVWFLQ1emZiG5D6oOq3kihMSwuAWbmwcq/KgIsbgPAJ9FzJSp6sfVAAAAAElFTkSuQmCC',
			'597E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7QkMYQ1hDA0MDkMQCGlhbGRoCHRhQxEQaHdDEAgOAYo2OMDGwk8KmLV2atXRlaBay+1oZAx2mMKLoZWhlaHQIQBULaGUBmoYqJjKFtZW1AVWMNQDo5gZGFDcPVPhREWJxHwCi2MqLnihMcwAAAABJRU5ErkJggg==',
			'929E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGUMDkMREprC2Mjo6OiCrC2gVaXRtCEQTY0AWAztp2tRVS1dmRoZmIbmP1ZVhCkMIql6GVoYABjTzBFoZHRjRxIBuaUB3C2uAaKgDmpsHKvyoCLG4DwDpjMlwhP5r2gAAAABJRU5ErkJggg==',
			'5304' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7QkNYQximMDQEIIkFNIi0MoQyNKKKMTQ6Ojq0IosFBjC0sjYETAlAcl/YtFVhS1dFRUUhu68VpC7QAVkvUKzRtSEwNATZjlawHShuEZkCdguKGGsAppsHKvyoCLG4DwBtpM3lbb/7zAAAAABJRU5ErkJggg==',
			'9517' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7WANEQxmmMIaGIImJTBFpYAgB0khiAa0iDYyYYiEMU4A0kvumTZ26dNW0VSuzkNzH6srQ6DCFoRXF5law2BRkMYFWEZBYAAOKW1hbge5zQHUzYwhjqCOK2ECFHxUhFvcBACa2yykF00PtAAAAAElFTkSuQmCC',
			'4A19' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpI37pjAEAPFUB2SxEMYQhhCGgAAkMcYQ1lbGEEYHESQx1ikijQ5T4GJgJ02bNm1l1rRVUWFI7gsAq2OYiqw3NFQ0FCjWIILiFrA6ByxiKG4BiTmGOqC6eaDCj3oQi/sAWnPMDqLzJO8AAAAASUVORK5CYII=',
			'374D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7RANEQx0aHUMdkMQCpjA0OrQ6OgQgq2wFik11dBBBFpsCFA2Ei4GdtDJq1bSVmZlZ05DdN4UhgLURTW8rowNraCCaGGsDA5q6gCkiYDFkt4gGgMVQ3DxQ4UdFiMV9AOpVy9fWFiVJAAAAAElFTkSuQmCC',
			'DA86' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QgMYAhhCGaY6IIkFTGEMYXR0CAhAFmtlbWVtCHQQQBETaXR0dHRAdl/U0mkrs0JXpmYhuQ+qDs080VBXoHkiaOZhiE0B6UV1S2iASKMDmpsHKvyoCLG4DwD43s31TXppAAAAAABJRU5ErkJggg==',
			'0216' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nM2QsRGAMAhFoUhvEfchG1DExt7GKZIiG6AbWJgppSRqqafQvePDO6BeKsGf+hU/JIwgsJBhjl2BCMyGefE5RKTOMC6QSZCs37jVra77NBs/nRMQbPYpY2Xkmxu668TUJWm6ySL1Qxiocf7qfw/2jd8BEqzKk4Q32PwAAAAASUVORK5CYII=',
			'A8CE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7GB0YQxhCHUMDkMRYA1hbGR0CHZDViUwRaXRtEEQRC2hlbWUFmoDsvqilK8OWrloZmoXkPjR1YBgaCjKPEc087HaguyWgFdPNAxV+VIRY3AcArtvKekGyESsAAAAASUVORK5CYII=',
			'1B62' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7GB1EQxhCGaY6IImxOoi0Mjo6BAQgiYk6iDS6Njg6iKDoFWllBdIiSO5bmTU1bOnUVauikNwHVufo0OiAqhdoXkArA6bYFDQxsFuQxURDQG5mDA0ZBOFHRYjFfQAho8nJitUFewAAAABJRU5ErkJggg==',
			'0122' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7GB0YAhhCGaY6IImxBjAGMDo6BAQgiYlMYQ1gbQh0EEESC2gF6m0IaBBBcl/U0lVRq1ZmAQmE+8DqWhkaHdD1TgGKotgBFAOJoriFIQDsRhQ3s4ayhgaGhgyC8KMixOI+AD6jyOwn4KpRAAAAAElFTkSuQmCC',
			'297B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nM2QwQmAMAxFGzAbdKC4QQpmiU6RHrpBcYMedEorIjToUdH82yP5POLWy6j7U17xQ4YJJQh1zBfMTgNxxzj7RI35/npnaTz3Dqe51lgXib0fQ6ACpg/IJWIwfahDa7PMK2ZUeyvSnBWM81f/ezA3fhsyicsgBZEUKgAAAABJRU5ErkJggg==',
			'FE17' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7QkNFQxmmMIaGIIkFNIg0MIQwgEgUMUYsYgxTQDTCfaFRU8NWTVu1MgvJfVB1rQyYeqdgEQvAFGN0QBUTDWUMdUQRG6jwoyLE4j4AVO7MMMLKMrQAAAAASUVORK5CYII=',
			'F578' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QkNFQ1lDA6Y6IIkFNIiAyIAADLFABxFUsRCGRgeYOrCTQqOmLl21dNXULCT3AeUbHaYwoJkH0smIbl6jowO6GGsrawO6XsYQoBiKmwcq/KgIsbgPAIzGzembdyztAAAAAElFTkSuQmCC',
			'A4E5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7GB0YWllDHUMDkMRYAximsoJkkMREpjCEoosFtDK6AsVcHZDcF7UUCEJXRkUhuS+gVaSVFWQGkt7QUNFQVzSxgFagW4B2YIoxBASgi4U6THUYBOFHRYjFfQAOPsr6JZayTwAAAABJRU5ErkJggg==',
			'34A6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7RAMYWhmmMEx1QBILAPIZQhkCApBVtjKEMjo6Ogggi01hdGVtCHRAdt/KqKVLl66KTM1Cdt8UkVagOjTzRENdQwMdRFDtAKlDEQO6BSgWgKIX5GagGIqbByr8qAixuA8AcG/L5N01iYUAAAAASUVORK5CYII='        
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


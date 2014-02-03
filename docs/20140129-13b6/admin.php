<?php
require_once( dirname(__FILE__).'/form.lib.php' );

define( 'PHPFMG_USER', "baollantines@yahoo.es" ); // must be a email address. for sending password to you.
define( 'PHPFMG_PW', "2318a3" );

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
			'D3C0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7QgNYQxhCHVqRxQKmiLQyOgRMdUAWa2VodG0QCAhAFWtlbWB0EEFyX9TSVWFLV63MmobkPjR1SOZhE0OzA4tbsLl5oMKPihCL+wABeM2mp0S3QwAAAABJRU5ErkJggg==',
			'A470' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7GB0YWllDA1qRxVgDGKYyNARMdUASE5nCEAoUCwhAEgtoZXRlaHR0EEFyX9TSpUtXLV2ZNQ3JfQGtIq0MUxhh6sAwNFQ01CEAVQzoilage9DsALqvgQHFLVAxFDcPVPhREWJxHwA6Z8xR9Yp4agAAAABJRU5ErkJggg==',
			'7D54' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7QkNFQ1hDHRoCkEVbRVpZGxga0cQaXYEkitgUoNhUhikByO6LmrYyNTMrKgrJfYwOIo0ODYEOyHpZG8BioSFIYiINIDsCUNwS0CDSyujogCYmGsIQyoDq5gEKPypCLO4DAOACznj7i6VbAAAAAElFTkSuQmCC',
			'B32F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QgNYQxhCGUNDkMQCpoi0Mjo6OiCrC2hlaHRtCEQVm8LQyoAQAzspNGpV2KqVmaFZSO4Dq2tlxDDPYQoWsQA0MZBbHFDFQG5mDUV1y0CFHxUhFvcBAE+ayoHlRfpIAAAAAElFTkSuQmCC',
			'931B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WANYQximMIY6IImJTBFpZQhhdAhAEgtoZWh0BIqJoIq1AvXC1IGdNG3qqrBV01aGZiG5j9UVRR0EAs1zmIJqngAWMbBb0PSC3MwY6oji5oEKPypCLO4DAAjYyn03ktCsAAAAAElFTkSuQmCC',
			'7F0F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QkNFQx2mMIaGIIu2ijQwhDI6MKCJMTo6oopNEWlgbQiEiUHcFDU1bOmqyNAsJPcxOqCoA0PWBkwxkQZMOwIaMN0CFpuC5r4BCj8qQizuAwCCQ8k0oOjRqAAAAABJRU5ErkJggg==',
			'63AB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WANYQximMIY6IImJTBFpZQhldAhAEgtoYWh0dHR0EEEWa2BoZW0IhKkDOykyalXY0lWRoVlI7guZgqIOoreVodE1NBDVPJBYA6oYyC3oekFuBoqhuHmgwo+KEIv7AC9OzHM614H1AAAAAElFTkSuQmCC',
			'4C7A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpI37pjCGsoYGtKKIhbA2OjQETHVAEmMMEWkAigUEIImxThFpYGh0dBBBct+0adNWrVq6MmsakvsCQOqmMMLUgWFoKJAXwBgaguIWkQZHB1R1DFNYG10b0MWAbkYXG6jwox7E4j4ASAzL8osbnNIAAAAASUVORK5CYII=',
			'43C5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpI37prCGMIQ6hgYgi4WItDI6BDogq2MMYWh0bRBEEWOdwtDK2sDo6oDkvmnTVoUtXbUyKgrJfQFgdQwNIkh6Q0NB5qGKMUyB2IEqBnJLQACK+8BudpjqMBjCj3oQi/sAd0DLGTbDCPUAAAAASUVORK5CYII=',
			'432C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nM2QwQ2AIAxFP4lswEB48F4TcAedAg5sUNlBphRuJXrUaP/tpc1/KcplAv6Ud/xYO3jsJJkzSY2WjGDKIU5htoNgmpFQmfTLuSzlWDfpR20vKSt7vUe03DNwZaS6DrBpl51Lc9aeeuev/vdcbvxOvF/KR5+chlAAAAAASUVORK5CYII=',
			'E48A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QkMYWhlCgRhJLKCBYSqjo8NUB1SxUNaGgIAAFDFGV0ZHRwcRJPeFRi1duip0ZdY0JPcFNIi0IqmDiomGujYEhoag2tHK2hCIpo4BQy/EzYwoYgMVflSEWNwHAALZy/P6/F6aAAAAAElFTkSuQmCC',
			'CA94' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7WEMYAhhCGRoCkMREWhlDGB0dGpHFAhpZW1kbAlpRxBpEGl0bAqYEILkvatW0lZmZUVFRSO4DqXMICXRA1Ssa6tAQGBqCYodIoyNQBtUtQDFHBxQx1hCgeWhuHqjwoyLE4j4AVuLPCJaRZp4AAAAASUVORK5CYII=',
			'F325' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkNZQxhCGUMDkMQCGkRaGR0dHRhQxBgaXRsC0cVaGRoCXR2Q3BcatSps1crMqCgk94HVAVWKoJnnMAWLWACjgwi6WxwYAlDdxxrCGhow1WEQhB8VIRb3AQACicw1hO7xAwAAAABJRU5ErkJggg==',
			'C03A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7WEMYAhhDGVqRxURaGUNYGx2mOiCJBTSyAtUEBAQgizWINDo0OjqIILkvatW0lVlTV2ZNQ3IfmjqEWENgaAiGHYEo6iBuQdULcTMjithAhR8VIRb3AQDFQcyATL63cwAAAABJRU5ErkJggg==',
			'F64A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkMZQxgaHVqRxQIaWFsZWh2mOqCIiTQyTHUICEAVa2AIdHQQQXJfaNS0sJWZmVnTkNwX0CDaytoIVwc3zzU0MDQETcwBQx3QLRhiIDejig1U+FERYnEfAA/nzaI77BZVAAAAAElFTkSuQmCC',
			'229D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGUMdkMREprC2Mjo6OgQgiQW0ijS6NgQ6iCDrbmVAFoO4adqqpSszI7OmIbsvgGEKQwiqXkYHoCiaeaxAUUY0MRGQKJpbQkNFQx3Q3DxQ4UdFiMV9AOQvyk3Hx2ogAAAAAElFTkSuQmCC',
			'3356' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7RANYQ1hDHaY6IIkFTBFpZW1gCAhAVtnK0OjawOgggCw2haGVdSqjA7L7VkatCluamZmahew+oDqGhkAM8xwaAh1EMOxAFQO5hdHRAUUvyM0MoQwobh6o8KMixOI+ABmey1/RA43UAAAAAElFTkSuQmCC',
			'0C2C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7GB0YQxlCGaYGIImxBrA2Ojo6BIggiYlMEWlwbQh0YEESC2gFqQh0QHZf1NJpq1atzMxCdh9YXSujAwO63imoYiA7HAIYUewAu8WBAcUtIDezhgaguHmgwo+KEIv7AFMcyqA8iViwAAAAAElFTkSuQmCC',
			'F5C1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QkNFQxlCHVqRxQIaRBoYHQKmoouxNgiEoomFsDYwwPSCnRQaNXXp0lWrliK7L6CBodEVoQ6PmAhQTABNjLUV6BY0McYQoJtDAwZB+FERYnEfALaZzYiLkoY2AAAAAElFTkSuQmCC',
			'D23F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QgMYQxhDGUNDkMQCprC2sjY6OiCrC2gVaXRoCEQTY2h0QKgDOylq6aqlq6auDM1Cch9Q3RQGDPMYAhgwzGN0wBCbwtqA7pbQANFQx1BGFLGBCj8qQizuAwBWTcwgy5vQ7AAAAABJRU5ErkJggg==',
			'471A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nM2QsRGAMAgAoXCDuA/ZgAKWcIpYuAFmBzOl0YpoSj3luz+4+wPKbRL8iXf6bFQyWBonMJPASs5hdVGA2bnhuDOk4PpyLpVtyq6PDdjtnagiVafStAzpugcWug41tu6r/z1Hp28HerbKs7jfBVQAAAAASUVORK5CYII=',
			'B98A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QgMYQxhCGVqRxQKmsLYyOjpMdUAWaxVpdG0ICAhAUSfS6Ojo6CCC5L7QqKVLs0JXZk1Dcl/AFMZAJHVQ8xiA5gWGhqCIsYDEUNWB3YKqF+JmRhSxgQo/KkIs7gMAbOXNA+EX5OgAAAAASUVORK5CYII=',
			'83AA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WANYQximMLQii4lMEWllCGWY6oAkFtDK0Ojo6BAQgKKOoZW1IdBBBMl9S6NWhS1dFZk1Dcl9aOrg5rmGBoaGoIuhqQO5BV0vyM3oYgMVflSEWNwHAGtZzHerUOMpAAAAAElFTkSuQmCC',
			'EB36' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAV0lEQVR4nGNYhQEaGAYTpIn7QkNEQxhDGaY6IIkFNIi0sjY6BASgijU6NAQ6CKCpY2h0dEB2X2jU1LBVU1emZiG5D6oOq3kihMUw3ILNzQMVflSEWNwHALQczkSrbK+QAAAAAElFTkSuQmCC',
			'86F1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDA1qRxUSmsLayNjBMRRYLaBVpBIqFoqoTaQCKwfSCnbQ0alrY0tBVS5HdJzJFtBVJHdw8VyLEoG5BEQO7GeiWgEEQflSEWNwHAFpfy5wvsGE0AAAAAElFTkSuQmCC',
			'209D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WAMYAhhCGUMdkMREpjCGMDo6OgQgiQW0srayNgQ6iCDrbhVpdEWIQdw0bdrKzMzIrGnI7gsQaXQIQdXL6AAUQzOPtYG1lRFNTKQB0y2hoZhuHqjwoyLE4j4AhebKESa6a0gAAAAASUVORK5CYII=',
			'22B3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDGUIdkMREprC2sjY6OgQgiQW0ijS6NgQ0iCDrbmVodG10aAhAdt+0VUuXhq5amoXsvgCGKawIdWDI6MAQwIpmHitQFF1MBCiK7pbQUNFQVzQ3D1T4URFicR8ALzLM9Ia1nggAAAAASUVORK5CYII=',
			'6877' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDA0NDkMREprC2MjQENIggiQW0iDQ6oIs1ANWBRRHui4xaGbZq6aqVWUjuCwGZN4WhFdnegFageQFAUTQxRweGAAY0t7A2MDpguBlNbKDCj4oQi/sA5jjMUSrj36QAAAAASUVORK5CYII=',
			'B1BD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QgMYAlhDGUMdkMQCpjAGsDY6OgQgi7WyBrA2BDqIoKhjAKsTQXJfaNSqqKWhK7OmIbkPTR3UPAZM87CJQfUiuyUU6GJ0Nw9U+FERYnEfANEcyxx8YmJdAAAAAElFTkSuQmCC',
			'E255' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QkMYQ1hDHUMDkMQCGlhbWRsYHRhQxEQaXTHEGBpdpzK6OiC5LzRq1dKlmZlRUUjuA6qbAjYBVW8AphijA2tDoAOqGNAljg4ByO4LDRENdQhlmOowCMKPihCL+wBPdMxpJHoRVgAAAABJRU5ErkJggg==',
			'DC56' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QgMYQ1lDHaY6IIkFTGFtdG1gCAhAFmsVaXBtYHQQQBNjncrogOy+qKXTVi3NzEzNQnIfSB1DQyCGeUAxBxEMO9DEgG5xdHRA0QtyM0MoA4qbByr8qAixuA8AZanN+CEu9XgAAAAASUVORK5CYII=',
			'FCCB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QkMZQxlCHUMdkMQCGlgbHR0CHQJQxEQaXBsEHUTQxFgbGGHqwE4KjZq2aumqlaFZSO5DU4cihm4eph3Y3ILp5oEKPypCLO4DAGPAzSsRHPcMAAAAAElFTkSuQmCC',
			'5D22' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nGNYhQEaGAYTpIn7QkNEQxhCGaY6IIkFNIi0Mjo6BASgijW6NgQ6iCCJBQaINDqAZJDcFzZt2sqslVmropDd1wpU18rQiGwHWGwKQyuyWwJAYgEMU5DFRKYA3eLAEIAsxhogGsIaGhgaMgjCj4oQi/sAboTM7PJNJAkAAAAASUVORK5CYII=',
			'B3BF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWElEQVR4nGNYhQEaGAYTpIn7QgNYQ1hDGUNDkMQCpoi0sjY6OiCrC2hlaHRtCEQVm8KArA7spNCoVWFLQ1eGZiG5D00dbvOw2oHpFqibUcQGKvyoCLG4DwDtt8vjbmA66gAAAABJRU5ErkJggg==',
			'4CB8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpI37pjCGsoYyTHVAFgthbXRtdAgIQBJjDBFpcG0IdBBBEmOdItLAilAHdtK0adNWLQ1dNTULyX0BqOrAMDQUKIZmHsMUTDsYpmC6BaubByr8qAexuA8AvS3NirDKfmsAAAAASUVORK5CYII=',
			'7399' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QkNZQxhCGaY6IIu2irQyOjoEBKCIMTS6NgQ6iCCLTWFoZUWIQdwUtSpsZWZUVBiS+xgdgLpDAqYi62VtYGh0aAhoQBYDshsdGwJQ7ACqwHBLQAMWNw9Q+FERYnEfAEJWy6BfU7HCAAAAAElFTkSuQmCC',
			'BE78' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QgNEQ1lDA6Y6IIkFTBEBkQEByGKtILFABxF0dY0OMHVgJ4VGTQ1btXTV1Cwk94HVTWHANC+AEdU8oBijAyOGHawNqHrBbm5gQHHzQIUfFSEW9wEAG8jNeSK8EiYAAAAASUVORK5CYII=',
			'E361' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7QkNYQxhCGVqRxQIaRFoZHR2moooxNLo2OISiibWyNsD1gp0UGrUqbOnUVUuR3QdW5+jQimleABFiYLegiEHdHBowCMKPihCL+wBTqc01coKolwAAAABJRU5ErkJggg==',
			'C90B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7WEMYQximMIY6IImJtLK2MoQyOgQgiQU0ijQ6Ojo6iCCLNYg0ujYEwtSBnRS1aunS1FWRoVlI7gtoYAxEUgcVYwDrRTGvkQXDDmxuwebmgQo/KkIs7gMA8DHL+izU43EAAAAASUVORK5CYII=',
			'A587' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nM2QvRGAMAhGocgGuE9S2FOExhGcAotsoCNYmClNOjgt9ZSve8fPO6BeSuFPecUP4yAgKNmwwKSYopJhtJIGZce4UO59bPymfdur1GM2flxgSSkWe1cEllF5Bb+vM/YsFGzDnmFuzo599b8Hc+N3Ah8EzD/GRYMhAAAAAElFTkSuQmCC',
			'D2BE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDGUMDkMQCprC2sjY6OiCrC2gVaXRtCEQTY2h0RagDOylq6aqlS0NXhmYhuQ+obgqmeQwBrBjmMTpgiE1hbUDXGxogGuqK5uaBCj8qQizuAwB5kMxdwrRytAAAAABJRU5ErkJggg==',
			'F1FF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAATUlEQVR4nGNYhQEaGAYTpIn7QkMZAlhDA0NDkMQCGhgDWBsYHRhQxFixiDEgi4GdFBq1Kmpp6MrQLCT3oamjghhrKLrYQIUfFSEW9wEABgTH7gH1eKkAAAAASUVORK5CYII=',
			'70F1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QkMZAlhDA1pRRFsZQ1gbGKaiirG2AsVCUcSmiDS6AmVQ3Bc1bWVq6KqlyO5jdEBRB4asDZhiIg1gO1DEAhrAbkETA7oZ6JaAQRB+VIRY3AcAZPLK37jDb+gAAAAASUVORK5CYII=',
			'B785' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nM2QsRGAMAhFocgGcR8o0mNBk2lI4QbRHcyUpiRqqXeB7t0H3gHtUQYz9S9+KouSoopjUqEwM/mcbFCSrSOrsCFzIuenuR1Nz5ydX88JMlkc9iEFkxsLFvqNgdVofVa8n0pPKOw0wf8+7Be/C7zEzK9pQu43AAAAAElFTkSuQmCC',
			'043D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7GB0YWhlDGUMdkMRYAximsjY6OgQgiYlMYQhlaAh0EEESC2hldGUAqhNBcl/U0qVLV01dmTUNyX0BrSKtSOqgYqJAO1HNA9rRim4H0C2t6G7B5uaBCj8qQizuAwDEzssf2+y4oAAAAABJRU5ErkJggg==',
			'3576' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7RANEQ1lDA6Y6IIkFTBEBkQEByCpbQWKBDgLIYlNEQhgaHR2Q3bcyaurSVUtXpmYhu28KQ6PDFEY084BiAYwOIqh2AE1DFQuYwtrK2sCAolc0gDEEKIbi5oEKPypCLO4DAOcpy8kCULOrAAAAAElFTkSuQmCC',
			'4E8C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpI37poiGMoQyTA1AFgsRaWB0dAgQQRJjBIqxNgQ6sCCJsU4BqXN0QHbftGlTw1aFrsxCdl8AqjowDA2FmIfqFkw7GKZgugWrmwcq/KgHsbgPAIL4yi5UmzEBAAAAAElFTkSuQmCC',
			'6186' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7WAMYAhhCGaY6IImJTGEMYHR0CAhAEgtoYQ1gbQh0EEAWa2AAqnN0QHZfZNSqqFWhK1OzkNwXMgWsDtW8VgaweSIExETAelHdAnRJKLqbByr8qAixuA8AujzJgW4mgckAAAAASUVORK5CYII=',
			'C596' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WENEQxlCGaY6IImJtIo0MDo6BAQgiQU0ijSwNgQ6CCCLNYiEgMSQ3Re1aurSlZmRqVlI7gOa0+gQEohqHkgMqFcE1Y5GRzQxkVbWVnS3sIYwhqC7eaDCj4oQi/sAOv3MTc++sSYAAAAASUVORK5CYII=',
			'ACE3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7GB0YQ1lDHUIdkMRYA1gbXYEyAUhiIlNEGlxBNJJYQKtIAyuIRnJf1NJpq5aGrlqaheQ+NHVgGBoKEUM3D9MOTLcEtGK6eaDCj4oQi/sAXanNaYbNoiIAAAAASUVORK5CYII=',
			'3203' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7RAMYQximMIQ6IIkFTGFtZQhldAhAVtkq0ujo6NAggiw2haHRtSGgIQDJfSujVi1duipqaRay+6YwTGFFqIOaxxAAEkMxr5XRgRHNDqBbGtDdIhogGuqA5uaBCj8qQizuAwB8xMxkQQSmRQAAAABJRU5ErkJggg==',
			'B3C1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7QgNYQxhCHVqRxQKmiLQyOgRMRRFrZWh0bRAIRVXH0MrawADTC3ZSaNSqsKWrVi1Fdh+aOiTzsIkJYHMLihjUzaEBgyD8qAixuA8AvrLNfsbTG/sAAAAASUVORK5CYII=',
			'6BE6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7WANEQ1hDHaY6IImJTBFpZW1gCAhAEgtoEWl0bWB0EEAWawCpY3RAdl9k1NSwpaErU7OQ3BcCNo8R1bxWiHkiBMSwuQWbmwcq/KgIsbgPALPxy95PcUusAAAAAElFTkSuQmCC',
			'B5EE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QgNEQ1lDHUMDkMQCpog0sDYwOiCrC2jFIjZFJARJDOyk0KipS5eGrgzNQnJfwBSGRlcM87CJiWCKTWFtRbc3NIAxBN3NAxV+VIRY3AcAuE7LCWDNuyIAAAAASUVORK5CYII=',
			'E1A6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QkMYAhimMEx1QBILaGAMYAhlCAhAEWMNYHR0dBBAEWMIYG0IdEB2X2jUqqilqyJTs5DcB1WHZh5QLDTQQQSLeZhiASh6Q0NYQ4FiKG4eqPCjIsTiPgDRtMtc/b0VXQAAAABJRU5ErkJggg==',
			'BC37' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QgMYQ0EwBEksYApro2ujQ4MIslirSINDQwCq2BQgrxEkinBfaNS0VaumrlqZheQ+qLpWBjTzQDLoYkDTAhgw3OLogMXNKGIDFX5UhFjcBwDdfc7iAH0gTgAAAABJRU5ErkJggg==',
			'9B0A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WANEQximMLQii4lMEWllCGWY6oAkFtAq0ujo6BAQgCrWytoQ6CCC5L5pU6eGLV0VmTUNyX2srijqIBBonmtDYGgIkpgA2A5HFHUQtzCiiEHcjCo2UOFHRYjFfQB8uMtowKYbqwAAAABJRU5ErkJggg==',
			'726D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QkMZQxhCGUMdkEVbWVsZHR0dAlDERBpdGxwdRJDFpjAAxRhhYhA3Ra1aunTqyqxpSO5jdGCYwuqIqpe1gSGAtSEQRUwEqBJdLACoEt0tAQ2ioQ7obh6g8KMixOI+AC8xyqtefZFdAAAAAElFTkSuQmCC',
			'87A2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nM2QwQ2AIAxF66Eb1H3KBjWRC9PAgQ3QDbgwpeUkjR41of/28tO+FNpjIsyUX/xQVs8FDh4YFUjsQWRgkiE555hsL2OUSINfDe2sLWhuP+2J9hKbfQuj162GYdReAXODOhPr3Nnm9wn+92Fe/C6cHs1f0K1eNgAAAABJRU5ErkJggg==',
			'9507' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7WANEQxmmMIaGIImJTBFpYAgF0khiAa0iDYyODuhiIawNAUCIcN+0qVOXLl0VtTILyX2srgyNrkDVKDa3gsWmIIsJtIo0Ojo6BDCguIW1lSGU0QHVzYwhQDejiA1U+FERYnEfALMxy4aL2kqnAAAAAElFTkSuQmCC',
			'7D39' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkNFQxhDGaY6IIu2irSyNjoEBKCKNTo0BDqIIItNAYo1OsLEIG6KmrYya+qqqDAk9zE6gNQ5TEXWy9oAMi+gAVlMBCKGYgdQBYZbAhqwuHmAwo+KEIv7AM0jzaWBlTXAAAAAAElFTkSuQmCC',
			'B118' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QgMYAhimMEx1QBILmMIYwBDCEBCALNbKGsAYwugggqIOrBemDuyk0KhVUaumrZqaheQ+NHVQ80BiaOZhE8OiNzSANZQx1AHFzQMVflSEWNwHAJrZywC9ZRvdAAAAAElFTkSuQmCC',
			'0BE9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDHaY6IImxBoi0sjYwBAQgiYlMEWl0BaoWQRILaAWpg4uBnRS1dGrY0tBVUWFI7oOoY5iKphdoHtBcDDsYUOzA5hZsbh6o8KMixOI+ANuuyzWV1cBPAAAAAElFTkSuQmCC',
			'0466' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7GB0YWhlCGaY6IImxBjBMZXR0CAhAEhOZwhDK2uDoIIAkFtDK6MoKMgHJfVFLgWDqytQsJPcFtIq0sjo6opgX0Coa6toQ6CCCakcrK5oY0C2t6G7B5uaBCj8qQizuAwAxssq3PHyn0gAAAABJRU5ErkJggg==',
			'EC13' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QkMYQxmmMIQ6IIkFNLA2OoQwOgSgiIk0OIYwAElUMaDehgAk94VGTVu1atqqpVlI7kNThyKGbp4DhhjQLVNQ3QJyM2OoA4qbByr8qAixuA8AlKPOMHmEptQAAAAASUVORK5CYII=',
			'F6E0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDHVqRxQIaWFtZGximOqCIiTQCxQICUMUaWBsYHUSQ3BcaNS1saejKrGlI7gtoEG1FUgc3zxWrGLod2NyC6eaBCj8qQizuAwCiB8yo6mpPjAAAAABJRU5ErkJggg==',
			'3564' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7RANEQxlCGRoCkMQCpog0MDo6NCKLMbSKNLA2OLSiiE0RCWEFkgFI7lsZNXXp0qmroqKQ3TeFodHV0dEB1TygWENgaAiqHUCxADS3sLYC3YIiJhrAGILu5oEKPypCLO4DAA8bzcYeViBEAAAAAElFTkSuQmCC',
			'03DD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7GB1YQ1hDGUMdkMRYA0RaWRsdHQKQxESmMDS6NgQ6iCCJBbQytLIixMBOilq6KmzpqsisaUjuQ1MHE8MwD5sd2NyCzc0DFX5UhFjcBwBB5ct+In2y6gAAAABJRU5ErkJggg==',
			'A212' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nM2QsQ3AIAwEn4INyD5mAxemyTRQsAHJBjRMGSuVUVImEv7C0vllv4zxqIyV9Es+R07QcJBhnn2FgNmw0EKJ4igYxhWFms5Mvr2PPk7tJp/6mqrYGymBlVVM+xzdzon5rIRntqWokgX+96Fe8l39xcxKcjIdjQAAAABJRU5ErkJggg==',
			'F985' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkMZQxhCGUMDkMQCGlhbGR0dHRhQxEQaXRsCMcQcHR1dHZDcFxq1dGlW6MqoKCT3BTQwBgKNaxBB0csANC8ATYwFbIcIhlscAlDdB3Izw1SHQRB+VIRY3AcAvfbM295d1dkAAAAASUVORK5CYII=',
			'0631' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7GB0YQxhDGVqRxVgDWFtZGx2mIouJTBFpZGgICEUWC2gVaWBodIDpBTspaum0sFVTVy1Fdl9Aq2grkjqY3kYHIIluB7oY1C0oYlA3hwYMgvCjIsTiPgBMFMxrnCIKdQAAAABJRU5ErkJggg==',
			'0B7D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDA0MdkMRYA0RaGRoCHQKQxESmiDQ6AMVEkMQCWoHqGh1hYmAnRS2dGrZq6cqsaUjuA6ubwoiut9EhAFUMZIejA6oYyC2sQFciuwXs5gZGFDcPVPhREWJxHwAG2ssOBZ6/BAAAAABJRU5ErkJggg==',
			'9390' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7WANYQxhCGVqRxUSmiLQyOjpMdUASC2hlaHRtCAgIQBVrZW0IdBBBct+0qavCVmZGZk1Dch+rK9D8ELg6CASa59CAKiYAFHNEswObW7C5eaDCj4oQi/sAlZbLqpTxV2sAAAAASUVORK5CYII=',
			'52D4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nM2QMQ6AIAxFy9Ab4H1Y2EsCDpymDNyAeAMWTmndijpqtH976U9fCuMyDH/KK34pmogJmBQjxorFlZnZ4pmqZoHgYI2U37qN3kfOWftVaMjB6a4wEpaivlGNQ7mk96w0xWViSEvyJ+ev/vdgbvx2vO/O4JouatcAAAAASUVORK5CYII=',
			'CA80' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WEMYAhhCGVqRxURaGUMYHR2mOiCJBTSytrI2BAQEIIs1iDQ6Ojo6iCC5L2rVtJVZoSuzpiG5D00dVEw01LUhEFWsUaTRFc0OkVaQXlS3sIaINDqguXmgwo+KEIv7AN+kzRJwvNQCAAAAAElFTkSuQmCC',
			'04FF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7GB0YWllDA0NDkMRYAximsoJkkMREpjCEoosFtDK6IomBnRS1FAhCV4ZmIbkvoFWkFVOvaKgrph0Y6oBuwRADuxlNbKDCj4oQi/sAE7rH/M2uwngAAAAASUVORK5CYII=',
			'667D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDA0MdkMREprC2MjQEOgQgiQW0iDSCxESQxRqAvEZHmBjYSZFR08JWLV2ZNQ3JfSFTRFsZpjCi6m0VaXQIwBRzdEAVA7mFtYERxS1gNzcworh5oMKPihCL+wBwFctuq8P2bQAAAABJRU5ErkJggg==',
			'EC6E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QkMYQxlCGUMDkMQCGlgbHR0dHRhQxEQaXBswxVgbGGFiYCeFRk1btXTqytAsJPeB1WExj7UhEIsd6GKYbsHm5oEKPypCLO4DAJDJy7UonFG2AAAAAElFTkSuQmCC',
			'52D0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QkMYQ1hDGVqRxQIaWFtZGx2mOqCIiTS6NgQEBCCJBQYwAMUCHUSQ3Bc2bdXSpasis6Yhu6+VYQorQh1MLABdLKCV0YEVzQ4RoE50t7AGiIa6orl5oMKPihCL+wCH3M0ZOksgmwAAAABJRU5ErkJggg==',
			'FA7D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QkMZAlhDA0MdkMQCGhhDGBoCHQJQxFhbQWIiKGIijQ6NjjAxsJNCo6atzFq6MmsakvvA6qYwoukVDXUIQBcTAZqGKebawIjmFrAYipsHKvyoCLG4DwB3vM1O+qMPUAAAAABJRU5ErkJggg==',
			'A33B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7GB1YQxhDGUMdkMRYA0RaWRsdHQKQxESmMDQ6NAQ6iCCJBbQytDIg1IGdFLV0VdiqqStDs5Dch6YODENDsZqHRQzTLQGtmG4eqPCjIsTiPgCPN8zMQ12eMwAAAABJRU5ErkJggg==',
			'6E7E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7WANEQ1lDA0MDkMREpogAyUAHZHUBLVjEGoBijY4wMbCTIqOmhq1aujI0C8l9ISDzpjCi6m0FigVgijE6oIqB3MLagCoGdnMDI4qbByr8qAixuA8AujzJ/ajtYc8AAAAASUVORK5CYII=',
			'C431' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WEMYWhlDGVqRxURaGaayNjpMRRYLaGQIBZKhKGINjK4MjQ4wvWAnRa1aunTV1FVLkd0XADIRoQ4qJhrq0BDQimYHkB+A7pZWVjS9UDeHBgyC8KMixOI+AD+DzSXazKR4AAAAAElFTkSuQmCC',
			'1534' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7GB1EQxlDGRoCkMRYHUQaWBsdGpHFRIFiQFWtASh6RUIYGh2mBCC5b2XW1KWrpq6KikJyH6MDUFWjowOqXqBYQ2BoCKp5QLGABlR1rK2sYFEkt4QwhqC7eaDCj4oQi/sA81bMHR55wIoAAAAASUVORK5CYII=',
			'DB31' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QgNEQxhDGVqRxQKmiLSyNjpMRRFrFWl0aAgIRRNrZWh0gOkFOylq6dSwVVNXLUV2H5o6ZPMIi0HcgiIGdXNowCAIPypCLO4DAN2azzRCPNVyAAAAAElFTkSuQmCC',
			'DA94' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QgMYAhhCGRoCkMQCpjCGMDo6NKKItbK2sgJJVDGRRleg6gAk90UtnbYyMzMqKgrJfSB1DiGBDqh6RUMdGgJDQ9DMcwS6BNUtQDFHBxSx0ACgeWhuHqjwoyLE4j4A62XQSMX4P7YAAAAASUVORK5CYII=',
			'CC68' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7WEMYQxlCGaY6IImJtLI2Ojo6BAQgiQU0ijS4Njg6iCCLNYg0sDYwwNSBnRS1atqqpVNXTc1Cch9YHbp5YL2BqOaB7UAVw+YWbG4eqPCjIsTiPgCYOc1VrYNzhAAAAABJRU5ErkJggg==',
			'2126' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WAMYAhhCGaY6IImJTGEMYHR0CAhAEgtoZQ1gbQh0EEDW3QrUCxRDcd+0VVGrVmamZiG7D2RHKyOKeYwOQLEpjA4iyG4Bq0QVA7IDQGqR9YaGsgJhAIqbByr8qAixuA8ATGDIPOFWeAcAAAAASUVORK5CYII=',
			'7BC2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkNFQxhCHaY6IIu2irQyOgQEBKCKNbo2CDqIIItNEWllBdIiyO6Lmhq2FEQhuY/RAayuEdkO1gaQeQytyG4RAYsJTEEWC2iAuAVVDORmx9CQQRB+VIRY3AcAYOPMZfYTtyAAAAAASUVORK5CYII=',
			'5E51' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QkNEQ1lDHVqRxQIaRBpYGximYhELRRYLDACKTWWA6QU7KWza1LClmVlLUdzXKgIyAcUObGIBrSA7UMVEpog0MDqiuo81QDQU6JLQgEEQflSEWNwHAMQgy8biFOGUAAAAAElFTkSuQmCC',
			'673F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WANEQx1DGUNDkMREpjA0ujY6OiCrC2hhaHRoCEQVa2BoZUCoAzspMmrVtFVTV4ZmIbkvZApDAAO6ea2MQD6aea2sDehiIlNEGljR9LIGiDQwhjKiiA1U+FERYnEfANjmyuB5T9WkAAAAAElFTkSuQmCC',
			'5756' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7QkNEQ11DHaY6IIkFNDA0ujYwBARgiDE6CCCJBQYwtLJOZXRAdl/YtFXTlmZmpmYhu6+VIQCkGtk8hlaQvkAHEWQ7WlkbWNHERKaINDA6OqDoZQ0AqghlQHHzQIUfFSEW9wEA9HnLwyjgIXYAAAAASUVORK5CYII=',
			'7369' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QkNZQxhCGaY6IIu2irQyOjoEBKCIMTS6Njg6iCCLTWFoZW1ghIlB3BS1Kmzp1FVRYUjuY3QAqnN0mIqsl7UBZF5AA7KYCEQMxQ6gCgy3BDRgcfMAhR8VIRb3AQA+O8ujS8A/zAAAAABJRU5ErkJggg==',
			'3211' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7RAMYQximMLQiiwVMYW1lCGGYiqKyVaTRMYQhFEVsCkOjA0Iv2Ekro1YtXTUNiJHdNwUMW1HNYwjAFGN0wOKWBnQx0QDRUMdQh9CAQRB+VIRY3AcA+CzLTJLff1AAAAAASUVORK5CYII=',
			'04D1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7GB0YWllDGVqRxVgDGKayNjpMRRYTmcIQytoQEIosFtDK6AoUg+kFOylqKRAASWT3BbSKtCKpg4qJhrqiiQHtwFAHdEsr0C0oYlA3hwYMgvCjIsTiPgD46swk4+VVywAAAABJRU5ErkJggg==',
			'7BA1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QkNFQximMLSiiLaKtDKEMkxFE2t0dHQIRRGbItLK2hAA0wtxU9TUsKWropYiu4/RAUUdGLI2iDS6hqKKiYDE0NQFNGDqDWgQDQGKhQYMgvCjIsTiPgDK1M0U6b0QQAAAAABJRU5ErkJggg==',
			'5795' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nGNYhQEaGAYTpIn7QkNEQx1CGUMDkMSA7EZHR0cHBjQx14ZAFLHAAIZW1oZAVwck94VNWzVtZWZkVBSy+1oZAhhCAhpEkG1uZXQAmYosFgA0jRFoB7KYyBSRBkZHhwBk97EGAFWEMkx1GAThR0WIxX0AfUHLiCQEhIAAAAAASUVORK5CYII=',
			'08B1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDGVqRxVgDWFtZGx2mIouJTBFpdG0ICEUWC2gFq4PpBTspaunKsKWhq5Yiuw9NHVQMbF4rFjuwuQVFDOrm0IBBEH5UhFjcBwBwT8yAK6E2ugAAAABJRU5ErkJggg==',
			'8558' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7WANEQ1lDHaY6IImJTBFpYG1gCAhAEgtoBYkxOoigqgthnQpXB3bS0qipS5dmZk3NQnKfyBSGRoeGADTzQGKBKOYB7Wh0RRMTmcLayujogKKXNYAxhCGUAcXNAxV+VIRY3AcAs8TMp5MDC3UAAAAASUVORK5CYII=',
			'6911' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7WAMYQximMLQii4lMYW1lCGGYiiwW0CLS6BjCEIoi1iDS6IDQC3ZSZNTSpVnTVi1Fdl/IFMZABzQ7AloZGjHFWDDEwG5BEwO5mTHUITRgEIQfFSEW9wEAMnLMVS+mFmUAAAAASUVORK5CYII='        
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
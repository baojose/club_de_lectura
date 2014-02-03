<!--modal Dialog    (la cajita con formulario). Tambien tiene el css del formulario -->
<link rel="stylesheet" type="text/css" href="css/modaldialog.css" />

<?php
// php anyadido por dan
include("wrapper.html");
?>
<?php

// if the from is loaded from WordPress form loader plugin, 
// the phpfmg_display_form() will be called by the loader 
if( !defined('FormmailMakerFormLoader') ){
    # This block must be placed at the very top of page.
    # --------------------------------------------------
	require_once( dirname(__FILE__).'/form.lib.php' );
    phpfmg_display_form();
    # --------------------------------------------------
};


function phpfmg_form( $sErr = false ){
		$style=" class='form_text' ";

?>


<div id="openModal" class="modalDialog">

    <div>
        <a href="#close" title="Close" class="close">X</a>

            <div id='frmFormMailContainer'>
            
            <p class="encabezado">Club de Lectura Alfaguara</p>
            <p class="encabezado2">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>

            <form name="frmFormMail" id="frmFormMail" target="submitToFrame" action='<?php echo PHPFMG_ADMIN_URL . '' ; ?>' method='post' enctype='multipart/form-data' onsubmit='return fmgHandler.onSubmit(this);'>

            <input type='hidden' name='formmail_submit' value='Y'>
            <input type='hidden' name='mod' value='ajax'>
            <input type='hidden' name='func' value='submit'>
                        
                        
            <ol class='phpfmg_form' >

            <li class='field_block' id='field_0_div'><div class='col_label'>
            	<label class='form_field'>Nombre</label> <label class='form_required' >*</label> </div>
            	<div class='col_field'>
            	<input type="text" name="field_0"  id="field_0" value="<?php  phpfmg_hsc("field_0", ""); ?>" class='text_box'>
	<div id='field_0_tip' class='instruction'>Necesitamos tu Nombre</div>
            	</div>
            </li>

            <li class='field_block' id='field_1_div'><div class='col_label'>
            	<label class='form_field'>Apellidos</label> <label class='form_required' >*</label> </div>
            	<div class='col_field'>
            	<input type="text" name="field_1"  id="field_1" value="<?php  phpfmg_hsc("field_1", ""); ?>" class='text_box'>
	<div id='field_1_tip' class='instruction'>También necesitamos tus apellidos</div>
            	</div>
            </li>

            <li class='field_block' id='field_2_div'><div class='col_label'>
            	<label class='form_field'>eMail</label> <label class='form_required' >*</label> </div>
            	<div class='col_field'>
            	<input type="text" name="field_2"  id="field_2" value="<?php  phpfmg_hsc("field_2", ""); ?>" class='text_box'>
	<div id='field_2_tip' class='instruction'>Por favor introduce una dirección de correo válida</div>
            	</div>
            </li>

            <li class='field_block' id='field_3_div'><div class='col_label'>
	<label class='form_field'>País</label> <label class='form_required' >*</label> </div>
	<div class='col_field'>
	<?php phpfmg_dropdown( 'field_3', "España|Argentina|Bolivia|Brasil|Chile|Colombia|Costa Rica|Cuba|Ecuador|El Salvador|Guatemala|Haití|Honduras|México|Nicaragua|Panamá|Paraguay|Perú|República Dominicana|Uruguay|Venezuela", true );?>
	<div id='field_3_tip' class='instruction'>Necesitamos saber cual es tu país de origen</div>
	</div>
</li>

<li class='field_block' id='field_4_div'><div class='col_label'>
	<label class='form_field'>Ciudad, Localidad, Municipio</label> <label class='form_required' >*</label> </div>
	<div class='col_field'>
	<input type="text" name="field_4"  id="field_4" value="<?php  phpfmg_hsc("field_4", ""); ?>" class='text_box'>
	<div id='field_4_tip' class='instruction'>Introduce tu ciudad de origen</div>
	</div>
</li>

<li class='field_block' id='field_5_div'><div class='col_label'>
            	<label class='form_field'>Debes aceptar las condiciones</label> <label class='form_required' >*</label> </div>
            	<div class='col_field'>
	<?php phpfmg_checkboxes( 'field_5', "Acepto las condiciones" );?>
	<div id='field_5_tip' class='instruction'>Debes aceptar las condiciones</div>
            	</div>
            </li>

            <!-- 
            <li class='field_block' id='phpfmg_captcha_div'>
            	<div class='col_label'><label class='form_field'>Security Code:</label> <label class='form_required' >*</label> </div><div class='col_field'>
            	<?php phpfmg_show_captcha(); ?>
            	</div>
            </li>
             -->

                        <li>
                        <div class='col_label'>&nbsp;</div>
                        <div class='form_submit_block col_field'>
            	
            				
                            <input type='submit' value='Enviar' class='form_button'>

            				<div id='err_required' class="form_error" style='display:none;'>
            				    <label class='form_error_title'>Ya falta menos para entrar al Club de Lectura Alfaguara, completa los datos que faltan.</label>
            				</div>
            				


                            <span id='phpfmg_processing' style='display:none;'>
                                <img id='phpfmg_processing_gif' src='<?php echo PHPFMG_ADMIN_URL . '?mod=image&amp;func=processing' ;?>' border=0 alt='Processing...'> <label id='phpfmg_processing_dots'></label>
                            </span>
                        </div>
                        </li>
                        
            </ol>
            </form>

            <iframe name="submitToFrame" id="submitToFrame" src="javascript:false" style="position:absolute;top:-10000px;left:-10000px;" /></iframe>

            </div> 
            <!-- end of form container -->


            <!-- [Your confirmation message goes here] -->
            <div id='thank_you_msg' style='display:none;'>
Hemos recibido tus datos, bienvenido al Club de Lectura Alfaguara.
            </div>

                        
                        






<?php
			
    phpfmg_javascript($sErr);

} 
# end of form




function phpfmg_form_css(){
    $formOnly = isset($GLOBALS['formOnly']) && true === $GLOBALS['formOnly'];
?>
<style type='text/css'>
<?php 
if( !$formOnly ){
    echo"
    /*
body{
    margin-left: 18px;
    margin-top: 18px;
}

body{
    font-family : Verdana, Arial, Helvetica, sans-serif;
    font-size : 13px;
    color : #474747;
    background-color: transparent;
}

select, option{
    font-size:13px;
}
*/
";
}; // if
?>


<?php phpfmg_text_align();?>    



</style>

<?php
}

?>

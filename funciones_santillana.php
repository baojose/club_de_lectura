<?php
$debug=0;
$path_csv = "../results/"; // path de los dos csvs que se generan
?>


<?php




//  gestor GET

//if ( $_GET ){
//	echo "<h1>hat gets</h1>";
//}

//if(isset($_GET['error'])) {
//	// si aqui llega un get, TIENDE Q SER BORRADO
 //  $clean_url = '';
//   // code to create a clean url. 
 //  // After that $clean_url will contain the new redirect url 
 //  header("Location: $clean_url");
 //  exit();
//}
?>

<?php
// Gestor POST
// test page that accepts HTTP POST request only. for demo purpose
// accepts 'nombre' parameter, and records nombre and 
// browser user-agent to text files
// 


// compruebo que vienen nombre y correo_electronico
// SED
//  ALGO PASA CON ESTE QUE NO LLEGA 
// if (isset( $_POST['nombre']) and isset( $_POST['correo_electronico'])) 

// if (1) 
if (isset( $_POST['nombre']) and isset( $_POST['correo_electronico']) and isset( $_POST['curso_hijo'])  and isset( $_POST['pregunta1']) and isset( $_POST['pregunta2']) and isset( $_POST['pregunta3']) and isset( $_POST['pregunta4']) and isset( $_POST['pregunta5']) and isset( $_POST['pregunta6']) and isset( $_POST['pregunta7']) and isset( $_POST['pregunta8']) and isset( $_POST['pregunta9']) and isset( $_POST['pregunta10']) and isset( $_POST['otros2']) and isset( $_POST['otros9']) ) 
{
	// VOLCAR CONTENIDOS DE TEXTAREAS
	// si la pregunta2.9 = otros  => se vuelva el contenido del textarea
	if($_POST['pregunta2'] == "otros2"){
		$_POST['pregunta2'] = $_POST['otros2'];	// el contenido del textarea se vuelca en pregunta
	}
	if($_POST['pregunta9'] == "otros9"){
		$_POST['pregunta9'] = $_POST['otros9'];	// el contenido del textarea se vuelca en pregunta
	}

	// DETERMINAR CUAL FORM HACE PETICION
	$form = '';
	if(isset($_POST['mlofwlfn'])){ // Renovar
		if ($_POST['mlofwlfn'] == "lfi40fosj"){
			if($_POST['pregunta10'] == "otros10"){
				$_POST['pregunta10'] = $_POST['otros10'];	// el contenido del textarea se vuelca en pregunta
			}
			$form = "si";
		}
	}
	if(isset($_POST['yhgkyttn'])){ // NoRenovar
		if(isset($_POST['pregunta11'])){ // no puedo validar pregunta11 antes por que renovar no la tiene
			if ($_POST['yhgkyttn'] == "niikmhfdj"){
				if($_POST['pregunta11'] == "otros11"){
					$_POST['pregunta11'] = $_POST['otros11'];	// el contenido del textarea se vuelca en pregunta
				}
				$form = "no";
			}
		}
	}
	// si la pregunta2.9.11 = otros


	if(preg_match("/(si|no)/", $form)){



	if(0){
		$_POST['nombre'] = "de;;;bug_no;;m;;bre;";
		$_POST['correo_electronico'] = "debug_correo";
		$_POST['curso_hijo'] = "debug_curso_hijo";
		$_POST['pregunta1'] ="debug1";
		$_POST['pregunta2'] ="debug2";
		$_POST['pregunta3'] ="debug3";
		$_POST['pregunta4']  ="debug4";
		$_POST['pregunta5']  ="debug5";
		$_POST['pregunta6']  ="debug6";
		$_POST['pregunta7']  ="debug7";
		$_POST['pregunta8']  ="debug8";
		$_POST['pregunta9']  ="debug9";
		$_POST['pregunta10'] ="debug10";
		$_POST['pregunta11'] ="debug11";
	}

  $nombre = quitarPyC($_POST['nombre'] )			;	
  $correo_electronico = quitarPyC($_POST['correo_electronico'])		; 
  $curso_hijo = quitarPyC($_POST['curso_hijo'] )			;	
  $pregunta1 =  quitarPyC($_POST['pregunta1'] );	
  $pregunta2 =  quitarPyC($_POST['pregunta2'] );	
  $pregunta3 =  quitarPyC($_POST['pregunta3'] );	
  $pregunta4 = quitarPyC( $_POST['pregunta4']); 
  $pregunta5 = quitarPyC( $_POST['pregunta5']); 
  $pregunta6 =  quitarPyC($_POST['pregunta6'] ); 
  $pregunta7 = quitarPyC( $_POST['pregunta7']); 
  $pregunta8 = quitarPyC( $_POST['pregunta8']); 
  $pregunta9 = quitarPyC($_POST['pregunta9'] ); 
  $pregunta10 = quitarPyC($_POST['pregunta10']); 
  if($form=="no"){
  	$pregunta11 = quitarPyC($_POST['pregunta11']);
  }


	$respuesta_via_get = ''; // sitio donde guardar respuesta hacia anterior pagina
	// VALIDACION PHP
	if(esVacio($nombre)){
		print "<h1>nombre vacio</h1>";
		$respuesta_via_get .= "WBnombre_vacio";
	}

	$correo_electronico=$_POST['correo_electronico'];
	if(esVacio($correo_electronico)){
		// esCorreoElectronico
		print "<h1>correo_electronico vacio</h1>";
		$respuesta_via_get .= "WBcorreo_electronico_vacio";
	} 
	// si hay algun otros seleccionado el contenido de la pregunta debe ser su textarea

	if($respuesta_via_get == ''){ // no hay errores de validacion 


		// ABRIR EL FICHERO
		// abro fichero para concatenar
		$text=$nombre.";".$correo_electronico.";".$curso_hijo.";".$pregunta1.";".$pregunta2.";".$pregunta3.";".$pregunta4.";".$pregunta5.";".$pregunta6.";".$pregunta7.";".$pregunta8.";".$pregunta9.";".$pregunta10.";";
		$nombre_output;
		if($form == "si"){
			$nombre_output =  "renueva.csv"; // mirar esto http://www.webmaster-talk.com/php-forum/221234-php-multiple-users-problem.html
		}	
		else if ($form == "no"){
			$nombre_output =  "norenueva.csv"; // mirar esto http://www.webmaster-talk.com/php-forum/221234-php-multiple-users-problem.html
			$text .= ";".$pregunta11.";";
		}


    $fh = fopen($path_csv.$nombre_output, "a+"); // mirar esto http://www.webmaster-talk.com/php-forum/221234-php-multiple-users-problem.html
    if (flock($fh, LOCK_EX)) { // sacado de http://www.tuxradar.com/practicalphp/8/11/0
		// header ya viene en target.txt
		$text.="\n";

		fwrite($fh,$text);
        flock($fh, LOCK_UN);
		fclose($fh);
    	
    }

	

			// Redireccionar a pagina de agradecimiento
		if($debug == 0){
			header('Location: ../agradecimiento.php');
		}

	}
	else{
		// Datos no validos
		if($debug == 0){
				header('Location: '.$_SERVER['HTTP_REFERER'].'?error='.$error_via_get);
			}
		}

	} // cierroif(preg_match("/(si|no)/", $form))
	else{
		// no han mandado el valor hidden
		if($debug == 0){
			header('Location: '.$_SERVER['HTTP_REFERER'].'?error=faltan_valores');
		}
	}
} 
 else {
	// no han enviado todos los valores
	if($debug == 0){
		header('Location: '.$_SERVER['HTTP_REFERER']);
	}

}

// VALIDACIONES
function esVacio($algo){
	return(preg_match("/^\s+$/",$algo));
}

// quitar los puntos y comas
function quitarPyC($con){
	$sin1 = trim( $con );
	$sin2 = strip_tags( $sin1 );

	$sin3 = preg_replace( "/;/", "", $sin2 );
	return($sin3);
}


?>

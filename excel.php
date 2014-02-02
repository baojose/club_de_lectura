<?php

// echo "<script>confirm('ejecutando js escrito desde excel.php');</script>"; // funciona!
$debug=0;
$path_csv = "./"; // path de los dos csvs que se generan
if (isset( $_POST['field_0'])) 
{
$nombre = quitarPyC($_POST['field_0'] )			;	
$nombre = quitarPyC($_POST['field_1'] )			;	
$nombre = quitarPyC($_POST['field_2'] )			;	
$nombre = quitarPyC($_POST['field_355'] )			;	
	// echo "<script>confirm('pasas aunque POST no exista');</script>"; // funciona!
	// ABRIR EL FICHERO
	// abro fichero para concatenar
	$text=$nombre.";";
	$nombre_output =  "club_de_lectura.csv";


if(!file_exists($path_csv.$nombre_output))
  {
  	// mail(); <<<<< no se puede.
  	// escribir(PORSIACA.txt);
	echo "<script>confirm('HA ocurrido error, NO ESTAS DADO DE ALTA');</script>"; // funciona!
  	die("File not found");
  }
else
  {

	    $fh = fopen($path_csv.$nombre_output, "a+"); // mirar esto http://www.webmaster-talk.com/php-forum/221234-php-multiple-users-problem.html


	    if (flock($fh, LOCK_EX)) { // sacado de http://www.tuxradar.com/practicalphp/8/11/0
			// header ya viene en target.txt
			$text.="\n";

			fwrite($fh,$text);
	        flock($fh, LOCK_UN);
			fclose($fh);
	    	
    }
    }
};

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


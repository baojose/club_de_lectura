<?php
function esNombreValido(){
	return true;
}
function esApellidoValido(){
	return true;
}
function esCorreoValido(){
	return true;
}
function esPaisValido(){
	return true;
}
function esCiudadValido(){
	return true;
}


// VALIDACIONES
function esVacio( $algo ) {
	return preg_match( "/^\s+$/", $algo );
}

// quitar los puntos y comas
function quitarPyC( $con ) {
	$sin1 = trim( $con );
	$sin2 = strip_tags( $sin1 );

	$sin3 = preg_replace( "/;/", "", $sin2 );
	return $sin3;
}


?>

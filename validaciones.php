<?php
// VALIDACIONES
function esVacio( $algo ) {
	if($algo == ""){
		return true;
	}
	return false;
	// return preg_match( "/^\s+$/", $algo );  // no pongo esta porque  tengo que mimetizar lo que hace la libreria
}

// quitar los puntos y comas
function quitarPyC( $con ) {
	$sin1 = trim( $con );
	$sin2 = strip_tags( $sin1 );

	$sin3 = preg_replace( "/;/", "", $sin2 );
	return $sin3;
}




function esNombreValido($valor){
	if(esVacio($valor)){
		return false;
	}
		return true;
}
function esApellidoValido($valor){
	if(esVacio($valor)){
		return false;
	}
		return true;
}
function esEmailValido($valor){
	if (filter_var($valor, FILTER_VALIDATE_EMAIL)) {
		return true;
	}
		return false;
}
function esPaisValido($valor){
	if(esVacio($valor)){
		return false;
	}
		return true;
}
function esCiudadValido($valor){
	if(esVacio($valor)){
		return false;
	}
		return true;
}




?>

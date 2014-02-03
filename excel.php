<?php
// decide si escribe en csv
// echo "<script>confirm('ejecutando js escrito desde excel.php');</script>"; // funciona!
$debug=0;
$path_csv = "./"; // path de los dos csvs que se generan
	$nombre = quitarPyC( $_POST['field_0'] );
	$apellidos = quitarPyC( $_POST['field_1'] );
	$email = quitarPyC( $_POST['field_2'] );
	$pais = quitarPyC( $_POST['field_3'] );
	$ciudad = quitarPyC( $_POST['field_4'] );
	// echo "<script>confirm('pasas aunque POST no exista');</script>"; // funciona!
	// ABRIR EL FICHERO
	// abro fichero para concatenar
	$text=$nombre.";".$apellidos.";".$email.";".$pais.";".$ciudad;
	$nombre_output =  "club_de_lectura.csv";


	if ( !file_exists( $path_csv.$nombre_output ) ) {
		// mail(); <<<<< no se puede.
		// escribir(PORSIACA.txt);
		echo "<script>confirm('HA ocurrido error, NO ESTAS DADO DE ALTA');</script>"; // funciona!
		die( "File not found" );
	}
	else {

		$fh = fopen( $path_csv.$nombre_output, "a+" ); // mirar esto http://www.webmaster-talk.com/php-forum/221234-php-multiple-users-problem.html


		if ( flock( $fh, LOCK_EX ) ) { // sacado de http://www.tuxradar.com/practicalphp/8/11/0
			// header ya viene en target.txt
			$text.="\n";

			fwrite( $fh, $text );
			flock( $fh, LOCK_UN );
			fclose( $fh );

		}
	}





?>

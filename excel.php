<?php
// decide si escribe en csv
// echo "<script>confirm('ejecutando js escrito desde excel.php');</script>"; // funciona!
$debug=0;
$path_csv = "./"; // path de los dos csvs que se generan

	// echo "<script>confirm('pasas aunque POST no exista');</script>"; // funciona!
	// ABRIR EL FICHERO
	// abro fichero para concatenar
	
	$text=$nombre.";".$apellidos.";".$email.";".$pais.";".$ciudad;
	if($debug == 1){
	$text=$text."\ndebug nombre=[".$_POST['field_0']."] debug apellidos=[".$_POST['field_1']."] debug email=[".$_POST['field_2']."] debug pais=[".$_POST['field_3']."] debug ciudad=[".$_POST['field_4']."] debug Checked=[".$_POST['Checkbox01_field_5']."]\n"; // DEBUG
		
	}
	
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

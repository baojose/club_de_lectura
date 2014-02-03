<?php

require('../validacion_php/form_validations.php');     // segun veo deveulve false cuando email esta OK (mi no entender)
$form_validations = new form_validations;
$fallos_totales = 0;


##### validar Emails que ESTAN CORRECTOS
$a_mails_ok = Array("fkjds@gmail.com","alsdfasd@sakdf.com");
foreach ($a_mails_ok as $key => $email) {
	print "probando este MAIL (deberia serlo) [".$email."]\t";
	$response_check_mail = $form_validations->check_email($email);
	if($response_check_mail === false){ // segun veo deveulve false cuando email esta OK (mi no entender)
		print "EXITO";
	}
	else {
		global $fallos_totales;
		$fallos_totales++;
		print "FALLO";
	}
	print "\n";
}
##### validar Emails que ESTAN CORRECTOS
$a_mails_ok = Array("muchos@@arrobass@gmail.com","sinningunarroba.com");
foreach ($a_mails_ok as $key => $email) {
	print "probando este MAIL (no deberia serlo) [".$email."]\t";
	$response_check_mail = $form_validations->check_email($email);
	if($response_check_mail === true){ // segun veo deveulve false cuando email esta OK (mi no entender)
		print "EXITO";
	}
	else {
		global $fallos_totales;
		$fallos_totales++;
		print "FALLO";
	}
	print "\n";
}

//         function testvalidateEmail() {
// 			$dummy_mail_ok = "sltjaa@ltjl.com";
// 			// $this->assertTrue($response_check_mail, true);
//             $this->assertEqual(1 + 1, 2);
//         }

    // function validateEmail() {
    // 	$dummy_mail_ok = "sltjaa@ltjl.com";
    // 	$response_check_mail = $form_validations->check_email("sltjaaltjl.com");
    //     $this->assertTrue($response_check_mail, true);
    // }


print "\n==============\t";
if($fallos_totales == 0){
	print "EXITO TOTAL 0 fallos";
}
else{
	print "FRACASO NUMERO FALLOS ENCONTRADOS =[".$fallos_totales."]";
}
print "\n";

?>
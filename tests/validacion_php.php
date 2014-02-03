<?php

require '../validacion_php/form_validations.php';     // segun veo deveulve false cuando email esta OK (mi no entender)
require_once 'simpletest/autorun.php';  // http://simpletest.org/

// $a_mails_ok = Array("fkjds@gmail.com","alsdfasd@sakdf.com");
$form_validations = new form_validations;
$response_check_mail = $form_validations->check_email( "sltjaal@dj.com" );
var_dump( $response_check_mail );


class form_validations_test extends UnitTestCase {
	function mitestvalidateEmail( $response_check_mail="toe" ) {
		echo "recibido en mitestvalidateEmail=[".$response_check_mail."]";
		// //    $dummy_mail_ok = "sltjaa@ltjl.com";
		//    $this->assertFalse($response_check_mail, true);
		// //             $this->assertEqual(1 + 1, 2);
	}
}



// function validateEmail() {
//  $dummy_mail_ok = "sltjaa@ltjl.com";
//  $response_check_mail = $form_validations->check_email("sltjaaltjl.com");
//     $this->assertTrue($response_check_mail, true);
// }
$form_validations_test = new form_validations_test;
// var_dump($form_validations_test);
// $result_form_validations_test = $form_validations_test->mitestvalidateEmails("sltjaal@dj.com");


//### PRUEBAS CON OBJETOS
class dummy {
	function dummymethod() {
		echo "\ndummy XXXXXXXXXXXXXXXXXXXXXXXmethod\n";
	}
}
$dummy = new dummy;
// $dummy->dummymethod(); // FUNCIONA. "tirando" de $dummy

$extends_from_dummy = new extendsFromDummy();
// $extends_from_dummy->dummymethod();   //  FUNCIONA. Este metodo lo ha "extendido" de dummy
$extends_from_dummy->nuevoMethod();  //  FUNCIONA. Este metodo es propio de esta clase. Sin envio de dato
$extends_from_dummy->nuevoMethod( "envio esto" );  //  FUNCIONA. Este metodo es propio de esta clase. Con envio de dato

class extendsFromDummy extends dummy{
	function nuevoMethod( $recibiendo_dato= "recibiendo_dato_POR_DEFECTO" ) {
		echo "\ntoe ZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZ recibiendo_dato=[".$recibiendo_dato."]\n";
	}
}
//### FIN DE  PRUEBAS CON OBJETOS
?>
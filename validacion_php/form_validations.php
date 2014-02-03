<?php

class form_validations {
	public function check_numeric($value,$detail1 = NULL,$detail2 = NULL) {
		/**
		 * Check numeric values only
		 */
		if (preg_match ( '/^[1-9][0-9]*$/', $value )) {
			return true;
		} else {
			echo $this->form_error($detail1,$detail2);
			return false;
		}
	
	}
	/**
	 * Check alphabets only in string
	 */
	public function check_alphabets($value,$detail1 = NULL,$detail2 = NULL) {
		
		if (preg_match ( '/^[a-zA-Z]+$/', $value )) {
			return true;
		} else {
			echo $this->form_error($detail1,$detail2);
			return false;
		}
	
	}
	/**
	 * Check alphabets and numeric in string
	 */
	public function check_alphanumeric($value,$detail1 = NULL,$detail2 = NULL) {
		
		if (preg_match ( '/^([0-9a-zA-Z ])+$/', trim ( $value ) ))
		return true;
		
		else
		{
			echo $this->form_error($detail1,$detail2);
			return false;
		}
	}
	/**
	 * Check email validate or not
	 */
	public function check_email($value,$detail1 = NULL,$detail2 = NULL) {
		
		if (! filter_var ( $value, FILTER_VALIDATE_EMAIL )) {
			return true;
		} else {
			echo $this->form_error($detail1,$detail2);
			return false;
		}
	}
	/**
	 * Check special characters only in string
	 */
	public function check_specialcharacters($value,$detail1 = NULL,$detail2 = NULL) {
		
		if (preg_match ( '/[\'\/~`\!@#\$%\^&\*\(\)_\-\+=\{\}\[\]\|;:"\<\>,\.\?\\\]/', $value )) {
			return true;
		} else {
			echo $this->form_error($detail1,$detail2);
			return false;
		}
	}
	/**
	 * Check spaces only in string
	 */
	public function check_space($value,$detail1 = NULL,$detail2 = NULL) {
		
		if (preg_match ( '/\\s/', $value )) {
			return true;
		} else {
			echo $this->form_error($detail1,$detail2);
			return false;
		}
	}
	
	/**
	 * Removes spaces from string and returns same string
	 */
	public function removespace($value) {
		
		return preg_replace ( '/\s+/', '', $value );
	}
	
	/**
	 * 
	 * @param sting type $value
	 */
	public function trim($value) {
		return trim ( $value );
	}
	
	/**
	 * @param array type $value
	 * @param optional $display, must be set when data needs to be displayed
	 * @return true or false
	 */
	public function emptyfields($value, $display = NULL) {
		$newarray = array ();
		foreach ( $value as $key => $val ) {
			if (empty ( $val ))
				
				$newarray [] = $key;
		
		}
		if ($display != NULL && count($newarray)!=0)
			echo $this->form_warning ( $newarray, 'Following fields are empty ! <br>please correct them', 'array' );
		if (count ( $newarray ) != 0)
			return true;
		else
			return false;
	
	}
	
	/**
	 * 
	 * @param Shows warning alert for a given field or array of fields (fields will be displayed in alert)
	 * @param Alert is in yellow color
	 * @param type is null by default (optional), must be set when first parameter is an array
	 * @return Html code
	 */
	
	public function form_warning($detail1, $detail2, $type = NULL) {
		
		$value1 =  $detail2;
		
		if ($type != NULL) {
			foreach ( $detail1 as $arr ) {
				$value2 .=   $arr;
			}
		} else
			$value2 =  $detail1;		
	
		
		return $value1 . $value2;
	}
	
	/**
	 * 
	 * @param Shows error in alert format
	 * @param $detail1 field and $detail2 description
	 */
	public function form_error($detail1=NULL, $detail2=NULL) {
	
		if($detail1!=NULL || $detail2!=NULL)
		{
		$value = $detail1.",".$detail2;
		return $value;
		}
	}
	
	public function form_success($detail1, $detail2) {
		
		$value = $detail1.",".$detail2;
		return $value;
		}

}
?>

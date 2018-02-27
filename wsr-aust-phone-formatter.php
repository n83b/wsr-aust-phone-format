<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Attempts to output phone numbers as:
 * landline: +61 x xxxx xxxx 
 * mobile: +61 4xx xxx xxx
 */
class WSR_phone_format{

	private $formatted_number;
	
	function __construct($number){
		$number = $this->strip_non_digits($number);
		$number = $this->strip_international_code($number);

		$number = $this->parse_no_area_code($number);
		$number = $this->parse_area_code($number, '02');
		$number = $this->parse_area_code($number, '03');
		$number = $this->parse_area_code($number, '04');
		$number = $this->parse_area_code($number, '07');
		$number = $this->parse_area_code($number, '08');

		$this->formatted_number = $number;
	}



	// --------------------------------------------------------------------



	public function get_formatted_number(){
		return $this->formatted_number;
	}



	// --------------------------------------------------------------------



	private function strip_non_digits($number){
		return preg_replace("/[^0-9,.]/", "", $number );
	}



	// --------------------------------------------------------------------
	


	private function strip_international_code($number){
		//if internation followed by a 4
		if(substr($number, 0, 3) == '614'){
			$number = substr($number, 2);
			$number = '0' . $number;
		}

		//else if just international
		if(substr($number, 0, 2) == '61')
			$number = substr($number, 2);

		return $number;
	}



	// --------------------------------------------------------------------


	
	private function parse_no_area_code($number){
		if(substr($number, 0, 1) != '0')
			$number = $this->format_landline_no_area($number);

		return $number;
	}



	// --------------------------------------------------------------------



	private function parse_area_code($number, $areaCode){
		if(substr($number, 0, 2) == $areaCode) {
			$number = $this->format_number($number, $areaCode);
		}

		return $number;
	}



	// --------------------------------------------------------------------



	private function format_number($number, $areaCode){
		//if mobile
		if ($areaCode == '04'){
			$number = $this->format_mobile($number);
		}

		//if landline
		else{
			$number = $this->format_landline($number);
		}

		return $number;
	}



	// --------------------------------------------------------------------



	private function format_mobile($number){
		//remove zero and whitespace
		$number = $this->strip_number($number);

		//format to correct spacing
		$number = preg_replace("/^1?(\d{3})(\d{3})(\d{3})$/", "$1 $2 $3", $number);

		//add +61 to number
		return $this->add_six_one($number);
	}



	// --------------------------------------------------------------------



	private function format_landline($number){
		//remove zero and whitespace
		$number = $this->strip_number($number);

		//format to correct spacing
		$number = preg_replace("/^1?(\d{1})(\d{4})(\d{4})$/", "$1 $2 $3", $number);

		//add +61 to number
		return $this->add_six_one($number);
	}



	// --------------------------------------------------------------------
	


	private function format_landline_no_area($number){
		//remove white space
		$number = $this->strip_number($number);

		//format to correct spacing
		$number = preg_replace("/^1?(\d{4})(\d{4})$/", "$1 $2", $number);

		return $number;
		//add +61 to number
		//return $this->add_six_one($number);
	}



	// --------------------------------------------------------------------



	private function add_six_one($number){
		return '+61 ' . $number;
	}



	// --------------------------------------------------------------------



	private function strip_number($number){
		//trim the zero from the number
		if(substr($number, 0, 1) == '0')
			$number = substr($number, 1);

		//remove all white space
		$number = str_replace(' ', '', $number);

		return $number;
	}


}
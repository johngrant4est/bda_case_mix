<?php

/**
 * @author J Grant Forrest jgrant@lunaria.co.uk
 * @company Lunaria Ltd www.lunaria.co.uk
 * @copyright 2016
 */

  class BDAWeighting {
    public $weighted_score;
 	public $communication_arr = array(2,4,8);
	public $cooperation_arr = array(3,6,12);
	public $medical_arr = array(2,6,12);
	public $oral_risk_arr = array(3,6,12);
	public $access_arr = array(2,4,8);
	public $legal_and_ethical_arr = array(2,4,8);
    public $temp_array = array();  
    function __construct($criterion, $score) {
      switch ($criterion) {
        case "" :
      }    
    }

  }

?>
<?php
App::uses('AppModel', 'Model');

class ElectricityPostcodeDistributor2 extends AppModel {
	public $useTable = 'electricity_postcode_distributors2';
	public $actsAs = array('Containable');
}
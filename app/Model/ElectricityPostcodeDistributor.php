<?php
App::uses('AppModel', 'Model');

class ElectricityPostcodeDistributor extends AppModel {
	public $useTable = 'electricity_postcode_distributors';
	public $actsAs = array('Containable');
}
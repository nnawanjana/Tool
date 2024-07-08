<?php
App::uses('AppModel', 'Model');

class GasPostcodeDistributor extends AppModel {
	public $useTable = 'gas_postcode_distributors';
	public $actsAs = array('Containable');
}
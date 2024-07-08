<?php
App::uses('AppModel', 'Model');

class SolarRebateScheme extends AppModel {
	public $useTable = 'solar_rebate_schemes';
	public $actsAs = array('Containable');
}
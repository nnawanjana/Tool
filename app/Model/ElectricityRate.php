<?php
App::uses('AppModel', 'Model');

class ElectricityRate extends AppModel {
	public $useTable = 'electricity_rates';
	public $actsAs = array('Containable');
}
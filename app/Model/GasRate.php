<?php
App::uses('AppModel', 'Model');

class GasRate extends AppModel {
	public $useTable = 'gas_rates';
	public $actsAs = array('Containable');
}
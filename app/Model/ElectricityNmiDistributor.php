<?php
App::uses('AppModel', 'Model');

class ElectricityNmiDistributor extends AppModel {
	public $useTable = 'electricity_nmi_distributors';
	public $actsAs = array('Containable');
}
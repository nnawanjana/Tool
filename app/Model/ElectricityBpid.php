<?php
App::uses('AppModel', 'Model');

class ElectricityBpid extends AppModel {
	public $useTable = 'electricity_bpid';
	public $actsAs = array('Containable');
}
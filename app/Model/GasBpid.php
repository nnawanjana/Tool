<?php
App::uses('AppModel', 'Model');

class GasBpid extends AppModel {
	public $useTable = 'gas_bpid';
	public $actsAs = array('Containable');
}
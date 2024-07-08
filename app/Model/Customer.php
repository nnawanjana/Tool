<?php
App::uses('AppModel', 'Model');

class Customer extends AppModel {
	public $useTable = 'customers';
	public $actsAs = array('Containable');
}
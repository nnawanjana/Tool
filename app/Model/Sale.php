<?php
App::uses('AppModel', 'Model');

class Sale extends AppModel {
	public $useTable = 'sales';
	public $actsAs = array('Containable');
}
<?php
App::uses('AppModel', 'Model');

class Holiday extends AppModel {
	public $useTable = 'holidays';
	public $actsAs = array('Containable');
}
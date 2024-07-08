<?php
App::uses('AppModel', 'Model');

class Plan extends AppModel {
	public $useTable = 'plans';
	public $actsAs = array('Containable');
}
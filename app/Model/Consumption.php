<?php
App::uses('AppModel', 'Model');

class Consumption extends AppModel {
	public $useTable = 'consumptions';
	public $actsAs = array('Containable');
}
<?php
App::uses('AppModel', 'Model');

class StreetType extends AppModel {
	public $useTable = 'street_types';
	public $actsAs = array('Containable');
}
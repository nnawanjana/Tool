<?php
App::uses('AppModel', 'Model');

class Location extends AppModel {
	public $useTable = 'postcode_suburb';
	public $actsAs = array('Containable');
}
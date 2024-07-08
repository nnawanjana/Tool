<?php
App::uses('AppModel', 'Model');

class DmoVdo extends AppModel {
	public $useTable = 'dmo_vdo';
	public $actsAs = array('Containable');
}
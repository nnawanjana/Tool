<?php
App::uses('AppModel', 'Model');

class TermCondition extends AppModel {
	public $useTable = 'terms_conditions';
	public $actsAs = array('Containable');
}
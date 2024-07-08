<?php
App::uses('AppModel', 'Model');

class LeadType extends AppModel {
	public $useTable = 'lead_types';
	public $actsAs = array('Containable');
}
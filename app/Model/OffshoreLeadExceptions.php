<?php
App::uses('AppModel', 'Model');

class OffshoreLeadExceptions extends AppModel {
	public $useTable = 'offshore_lead_exceptions';
	public $actsAs = array('Containable');
}
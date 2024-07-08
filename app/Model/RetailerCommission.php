<?php
App::uses('AppModel', 'Model');

class RetailerCommission extends AppModel {
	public $useTable = 'retailer_commissions';
	public $actsAs = array('Containable');
}
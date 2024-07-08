<?php
App::uses('AppModel', 'Model');

class OriginLpg extends AppModel {
	public $useTable = 'origin_lpg';
	public $actsAs = array('Containable');
}
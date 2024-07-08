<?php
App::uses('AppModel', 'Model');

class Tariff extends AppModel {
	public $useTable = 'tariffs';
	public $actsAs = array('Containable');
}
<?php
App::uses('AppModel', 'Model');

class Product extends AppModel {
	public $useTable = 'products';
	public $actsAs = array('Containable');
}
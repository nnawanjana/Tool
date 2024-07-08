<?php
App::uses('AppModel', 'Model');

class Pdf extends AppModel {
	public $useTable = 'pdfs';
	public $actsAs = array('Containable');
}
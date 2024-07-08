<?php
App::uses('AppModel', 'Model');

class Submission extends AppModel {
	public $useTable = 'submissions';
	public $actsAs = array('Containable');
}
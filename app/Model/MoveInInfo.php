<?php
App::uses('AppModel', 'Model');

class MoveInInfo extends AppModel {
    public $useTable = 'move_in_info';
    public $actsAs = array('Containable');
}
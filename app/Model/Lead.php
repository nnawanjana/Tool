<?php
App::uses('AppModel', 'Model');

class Lead extends AppModel {
    public $useTable = 'leads';
    public $useDbConfig = 'velocify_middleware';
    public $actsAs = array('Containable');
}

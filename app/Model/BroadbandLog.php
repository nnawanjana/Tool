<?php
App::uses('AppModel', 'Model');

class BroadbandLog extends AppModel {
    public $useTable = 'broadband_logs';
    public $actsAs = array('Containable');
}
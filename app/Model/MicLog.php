<?php
App::uses('AppModel', 'Model');

class MicLog extends AppModel {
    public $useTable = 'mic_logs';
    public $actsAs = array('Containable');
}
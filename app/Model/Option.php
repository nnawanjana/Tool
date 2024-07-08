<?php
App::uses('AppModel', 'Model');

class Option extends AppModel {
    public $useTable = 'options';
    public $actsAs = array('Containable');
}
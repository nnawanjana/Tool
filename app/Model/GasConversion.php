<?php
App::uses('AppModel', 'Model');

class GasConversion extends AppModel {
    public $useTable = 'gas_conversion';
    public $actsAs = array('Containable');
}
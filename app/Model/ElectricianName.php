<?php
App::uses('AppModel', 'Model');

class ElectricianName extends AppModel
{
    public $useTable = 'electrician_name';
    public $actsAs = array('Containable');
}
<?php
App::uses('AppModel', 'Model');

class LeadAgent extends AppModel {
    public $useTable = 'lead_agents';
    public $actsAs = array('Containable');
}
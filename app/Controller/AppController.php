<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Controller', 'Controller');
App::uses('CakeEmail', 'Network/Email');
App::import('Lib', 'Utilities');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

	public $uses = array('User');
	public $helpers = array('App');
	
	public $components = array(
		'Session',
		'Auth',
		'Cookie'
	);
	
	public function beforeFilter() {
		$user = $this->Auth->user();
		$this->current_user = $user; 
		$this->set('current_user', $user);
		
		if (isset($this->params['prefix']) && $this->params['prefix'] == 'admin') {
			if (empty($this->current_user) || $this->current_user['User']['role'] != USER_ADMIN) {
				if ($this->current_user['User']['id'] > 0) {
					$this->redirect('/');
				}
				else {
					$this->redirect('/users/login');
				}
			}
			$this->layout = 'admin';
		}
		$this->set('controller', $this->params['controller']); 
		$this->set('action', $this->params['action']); 
	}
}

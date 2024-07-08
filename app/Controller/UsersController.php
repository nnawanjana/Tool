<?php
App::uses('AppController', 'Controller');

class UsersController extends AppController {

	public $uses = array('User');
	public $helpers = array('Html', 'Timezone');
	
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('login', 'forgot_password');
	}
	
	public function admin_index() {
		$this->paginate = array(
			'User' => array(
				'order' => 'User.role DESC, User.name asc',
				'limit' => 50
			)
		);
		$this->User->recursive = 0;
		$this->set('users', $this->paginate());
	}

	public function admin_view($id = null) {
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}
		$options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
		$this->set('user', $this->User->find('first', $options));
	}

	public function admin_add() {
		if ($this->request->is('post')) {
			$this->User->create();
			if (empty($this->request->data['User']['phone'])) {
				unset($this->request->data['User']['phone']);
			}
			if (empty($this->request->data['User']['website'])) {
				unset($this->request->data['User']['website']);
			}
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved'), 'flash_success');
				$this->redirect(array('action' => 'index'));
			} 
			else {
				$this->Session->setFlash(__('There was a problem: please review your errors below and try again.'), 'flash_error');
			}
		}
	}

	public function admin_edit($id = null) {
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if (empty($this->request->data['User']['password'])) {
				unset($this->request->data['User']['password']);
			}
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved'), 'flash_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('There was a problem: please review your errors below and try again.'), 'flash_error');
			}
		} else {
			$options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
			$this->request->data = $this->User->find('first', $options);
		}
	}

	public function admin_delete($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		$this->request->onlyAllow('post', 'delete');
		
		if ($this->User->delete()) {
			$this->Session->setFlash(__('This user has been successfully deleted.'), 'flash_success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('User was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
	
	public function forgot_password() {
		if ($this->request->is('post') || $this->request->is('put')) {
			$user = $this->User->fromEmail($this->request->data['User']['email']);
			
			if ($user) {
				$password = Utils::rand(8);
				$this->User->save(array('User' => array(
					'id' => $user['User']['id'],
					'password_temp' => $password
				)), true, array('password_temp'));
				
				$email = new CakeEmail();
				$email->viewVars(array(
					'password' => $password,
					'user' => $user
				));
				$email->from('no-reply@dealexpert.com.au')
					->template('forgot_password')
    				->emailFormat('html')
				    ->to($user['User']['email'])
				    ->subject('Temporary password')
				    ->send();
				$this->Session->setFlash('We\'ve sent you an email with a new temporary password.', 'flash_success');
				$this->redirect(array('action' => 'login'));
			}
			else {
				$this->setFlash('We could not find that user account.'); 
			}
		}
	}
	
	public function login() {
		if ($this->request->is('post')) {
			$user = $this->User->fromEmail($this->data['User']['email']);
			if (!$user) {
				$this->Session->setFlash('That email address does not exist.', 'flash_error');
			}
			else {
				// todo: status check
				$hashed = $this->Auth->password($this->data['User']['password']);
				if ($hashed == $user['User']['password'] || (!empty($user['User']['temp_password']) && $hashed == $user['User']['temp_password'])) {
					$user = array('User' => array(
						'id' => $user['User']['id'],
						'email' => $user['User']['email'],
						'role' => $user['User']['role'],
						'name' => $user['User']['name'],
						'timezone' => $user['User']['timezone'],
						'agent_id' => $user['User']['agent_id'],
					));
					if ($this->Auth->login($user)) {
						$authed = $this->Auth->user();
						$this->User->save(array('User' => array(
							'id' => $user['User']['id'],
							'login' => date('Y-m-d H:i:m')
						)), true, array('login'));
						
						$redirect_url = $this->Auth->redirectUrl();
						if (empty($redirect_url) && $user['User']['role'] == USER_ADMIN) {
							$this->redirect('/'); 
						}
						$this->Session->setFlash('You are now logged in.', 'flash_success');
						$this->redirect($redirect_url);
					}
				}
				else {
					$this->Session->setFlash('That password is incorrect.', 'flash_error');
				}
			}
		}
	}
	
	public function logout() {
		$this->Session->destroy();
		$this->redirect(array('action' => 'login'));
	}
}

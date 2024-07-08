<?php
App::uses('AppModel', 'Model');
/**
 * User Model
 *
 */
class User extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';

/**
 * Validation rules
 *
 * @var array
 */
	public $actsAs = array('Containable');
	public $validate = array(
		'password' => array(
			'rule' => array('minLength', 5),
			'message' => 'Passwords must be between at least 5 characters long.'
		),
		'email' => array(
			'email' => array(
				'rule' => array('email'),
				'allowEmpty' => false,
				'required' => false,
				'message' => 'This is an invalid email address.'
			),
			'email2' => array(
				'rule' => 'isUnique',
				'message' => 'That email has already been registered.'
			)
		),
	);
	
	public function fromId($id) {
		$user = $this->find('first', array(
			'conditions' => array(
				'User.id' => $id
			)
		));
		
		return $user;
	}
	
	public function fromEmail($email) {
		return $this->find('first', array(
			'fields' => array('*'),
			'conditions' => array(
				'User.email' => $email
			)
		));
	}
	
	public function beforeSave($options = array()) {
		if (isset($this->data[$this->alias]['password']) && !empty($this->data[$this->alias]['password'])) {
			$this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
		}
		if (isset($this->data[$this->alias]['temp_password']) && !empty($this->data[$this->alias]['temp_password'])) {
			$this->data[$this->alias]['temp_password'] = AuthComponent::password($this->data[$this->alias]['temp_password']);
		}
		
		// creating a new user
		if (!isset($this->data[$this->alias]['id'])) {
			
			if (!isset($this->data[$this->alias]['role'])) {
				// first user created needs to be an admin
				$count = $this->find('count', array(
					'conditions' => array(
						'User.role' => USER_ADMIN
					)
				));
				$this->data[$this->alias]['role'] = $count == 0 ? USER_ADMIN : 0;
			}
		}
		return true;
	}
}

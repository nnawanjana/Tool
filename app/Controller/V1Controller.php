<?php

App::uses('AppController', 'Controller');

class V1Controller extends AppController {
	public $uses = array('Plan', 'Location', 'Submission');
	public $helpers = array('Html', 'Icon');
	
	public function beforeFilter() {
    	
		parent::beforeFilter();
		
		$this->Auth->allow();
		
		$this->layout = 'v1';
		
		if (!in_array($this->request->clientIp(), unserialize(STAFF_IPS))) {
			$this->redirect( MAIN_SITE );
		}
		$this->redirect( '/v4/' );
	}
	
	public function index() {
		$this->redirect( '/compare/1' );
	}
	
	public function compare($step = 1) {
		if (!in_array($step, array(1, 2, 3))) {
			$step = 1;
		}
		$states_arr = unserialize(AU_STATES);
        $payment_options_arr = unserialize(AU_PAYMENTS);
        $step1 = array();
		$step2 = array();
		$plans = array();
		$available_retailers = array();
		$available_discount_type = array();
		$available_contract_length = array();
		$available_payment_options = array();
		$filters = array();
		$view_top_picks = 0;
		$top_picks = array();
		$conversion_tracked = 0;
		switch ($step) {
			case 1:
				$this->set('title_for_layout', 'Step 1 - About You');
				if (isset($this->request->query) && !empty($this->request->query)) {
					if (isset($this->request->query['sid'])) {
						$this->Session->write('User.sid', $this->request->query['sid']);
					}
					if (isset($this->request->query['postcode'])) {
						$this->Session->write('User.postcode', $this->request->query['postcode']);
					}
					if (isset($this->request->query['state'])) {
						$this->Session->write('User.state', $this->request->query['state']);
					}
					if (isset($this->request->query['suburb'])) {
						$this->Session->write('User.suburb', $this->request->query['suburb']);
					}
				}
				if ($this->Session->check('User.step1')) {
					$step1 = $this->Session->read('User.step1');
				}
			break;
			case 2:
				$this->set('title_for_layout', 'Step 2 - Product Options');
				if ($this->Session->check('User.step1')) {
					$step1 = $this->Session->read('User.step1');
				} else {
					$this->redirect( '/compare/1' );
				}
				if ($this->Session->check('User.step2')) {
					$step2 = $this->Session->read('User.step2');
				}
			break;
			case 3:
				$this->set('title_for_layout', 'Step 3 - See Your Results');
				if ($this->Session->check('User.step1')) {
					$step1 = $this->Session->read('User.step1');
				} else {
					$this->redirect( '/compare/1' );
				}
				if ($this->Session->check('User.step2')) {
					$step2 = $this->Session->read('User.step2');
				} else {
					$this->redirect( '/compare/2' );
				}
				$filters = array(
						'retailer' => array(),
						'discount_type' => array(),
						'contract_length' => array(),
						'payment_options' => array(),
						'plan_type' => $step1['plan_type'],
						'customer_type' => $step1['customer_type'],
					);
				$conditions['Plan.status'] = 'Active';
				$conditions['Plan.solar_specific_plan !='] = 'Solar Only';
				if (isset($_COOKIE['top_picks']) && $_COOKIE['top_picks']) {
					$top_picks = explode(',', $_COOKIE['top_picks']);
				}
				if (isset($this->request->query['view_top_picks']) && $this->request->query['view_top_picks'] == 1 && !empty($top_picks)) {
					$conditions['Plan.id'] = $top_picks;
					$view_top_picks = 1;
				} else {
					$conditions['Plan.state'] = $states_arr[$this->Session->read('User.state')];
					$conditions['Plan.package'] = $step1['plan_type'];
					$conditions['Plan.res_sme'] = $step1['customer_type'];
					$conditions['Plan.version'] = array('All', '1');
					if ($this->request->is('put') || $this->request->is('post')) {
						$conditions['Plan.package'] = $filters['plan_type'] = (isset($this->request->data['plan_type'])) ? $this->request->data['plan_type'] : $step1['plan_type'];
						$conditions['Plan.res_sme'] = $filters['customer_type'] = (isset($this->request->data['customer_type'])) ? $this->request->data['customer_type'] : $step1['customer_type'];
						if (isset($this->request->data['retailer']) && !empty($this->request->data['retailer']) && !in_array('all', $this->request->data['retailer'])) {
							$conditions['Plan.retailer'] = $this->request->data['retailer'];
							$filters['retailer'] = $this->request->data['retailer'];
						}
						if (isset($this->request->data['discount_type']) && !empty($this->request->data['discount_type'])  && !in_array('all', $this->request->data['discount_type'])) {
							$discount_type_or = array();
							if (in_array('Pay On Time', $this->request->data['discount_type'])) {
								$discount_type_or['or']['Plan.discount_pay_on_time_description !='] = '';
							}
							if (in_array('Guaranteed', $this->request->data['discount_type'])) {
								$discount_type_or['or']['Plan.discount_guaranteed_description !='] = '';
							}
							if (in_array('Direct Debit', $this->request->data['discount_type'])) {
								$discount_type_or['or']['Plan.discount_direct_debit_description !='] = '';
							}
							$conditions[] = $discount_type_or;
							$filters['discount_type'] = $this->request->data['discount_type'];
						}
						if (isset($this->request->data['contract_length']) && !empty($this->request->data['contract_length'])  && !in_array('all', $this->request->data['contract_length'])) {
							$conditions['Plan.contract_length'] = $this->request->data['contract_length'];
							$filters['contract_length'] = $this->request->data['contract_length'];
						}
						if (isset($this->request->data['payment_options']) && !empty($this->request->data['payment_options'])  && !in_array('all', $this->request->data['payment_options'])) {
							$payment_options_or = array();
							foreach ($this->request->data['payment_options'] as $value) {
								$payment_options_or['or']["Plan.{$value}"] = 'Yes';
							}
							$conditions[] = $payment_options_or;
							$filters['payment_options'] = $this->request->data['payment_options'];
						}
					}
				}
				$order = array();
				if ($step2['conditional_discount'] == 'Yes') {
					$order[] = 'Plan.conditional_discount DESC';
				}
				if ($step2['rate_freeze'] == 'Yes') {
					$order[] = 'Plan.rate_freeze DESC';
				}
				if ($step2['no_contract_plan'] == 'Yes') {
					$order[] = 'Plan.no_contract_plan DESC';
				}
				if ($step2['bill_smoothing'] == 'Yes') {
					$order[] = 'Plan.bill_smoothing DESC';
				}
				if ($step2['online_account_management'] == 'Yes') {
					$order[] = 'Plan.online_account_management DESC';
				}
				if ($step2['energy_monitoring_tools'] == 'Yes') {
					$order[] = 'Plan.energy_monitoring_tools DESC';
				}
				if ($step2['membership_reward_programs'] == 'Yes') {
					$order[] = 'Plan.membership_reward_programs DESC';
				}
				if ($step2['renewable_energy'] == 'Yes') {
					$order[] = 'Plan.renewable_energy DESC';
				}
				$order[] = 'rand()';
				if ($view_top_picks == 0) {
					$available_plans = $this->Plan->find('all', array(
						'conditions' => array(
							'Plan.status' => 'Active',
							'Plan.state' => $conditions['Plan.state'],
							'Plan.package' => $conditions['Plan.package'],
							'Plan.res_sme' => $conditions['Plan.res_sme'],
							'Plan.version' => $conditions['Plan.version']
						),
						'order' => $order
					));
					if (!empty($available_plans)) {
						foreach ($available_plans as $plan) {
							if (!in_array($plan['Plan']['retailer'], $available_retailers)) {
								$available_retailers[] = $plan['Plan']['retailer'];
							}
							
							if ($plan['Plan']['discount_pay_on_time_description'] && !in_array('Pay On Time', $available_discount_type)) {
								$available_discount_type[] = 'Pay On Time';
							}
							if ($plan['Plan']['discount_guaranteed_description'] && !in_array('Guaranteed', $available_discount_type)) {
								$available_discount_type[] = 'Guaranteed';
							}
							if ($plan['Plan']['discount_direct_debit_description'] && !in_array('Direct Debit', $available_discount_type)) {
								$available_discount_type[] = 'Direct Debit';
							}
							if (!in_array($plan['Plan']['contract_length'], $available_contract_length)) {
								$available_contract_length[] = $plan['Plan']['contract_length'];
							}
							foreach ($payment_options_arr as $key => $value) {
								if ($plan['Plan'][$key] == 'Yes' && !in_array($value, $available_payment_options)) {
                            		$available_payment_options[] = $value;
								}
							}
						}
					}
				}
				$plans = $this->Plan->find('all', array(
					'conditions' => $conditions,
					'order' => $order
				));
			break;
		}
		$sid = $this->Session->read('User.sid');
		$postcode = $this->Session->read('User.postcode');
		$state = $this->Session->read('User.state');
		$suburb = $this->Session->read('User.suburb');
		$conversion_tracked = ($this->Session->read('User.conversion_tracked')) ? 1 : 0;
		$this->set(compact('step', 'sid', 'postcode', 'state', 'suburb', 'step1', 'step2', 'conversion_tracked', 'states_arr', 'payment_options_arr', 'plans', 'top_picks', 'view_top_picks', 'available_retailers', 'available_discount_type', 'available_contract_length', 'available_payment_options', 'filters'));
		switch ($step) {
			case 1:
				$this->render('compare_step_1');
			break;
			case 2:
				$this->render('compare_step_2');
			break;
			case 3:
				$this->render('compare_step_3');
			break;
		}
	}
	
	public function compare_save($step = 1) {
		if ($this->request->is('put') || $this->request->is('post')) {
			if (!in_array($step, array(1, 2, 3))) {
				$step = 1;
			}
			switch ($step) {
				case 1:
					if (isset($this->request->data['postcode'])) {
						$this->Session->write('User.postcode', $this->request->data['postcode']);
					}
					if (isset($this->request->data['state'])) {
						$this->Session->write('User.state', $this->request->data['state']);
					}
					if (isset($this->request->data['suburb'])) {
						$this->Session->write('User.suburb', $this->request->data['suburb']);
					}
					$data = array(
						'plan_type' => (isset($this->request->data['plan_type'])) ? $this->request->data['plan_type'] : '',
						'customer_type' => (isset($this->request->data['customer_type'])) ? $this->request->data['customer_type'] : '',
						'looking_for' => (isset($this->request->data['looking_for'])) ? $this->request->data['looking_for'] : '',
						'recent_bill' => '',
						'elec_billing_days' => '',
						'elec_meter_type' => '',
						'elec_supplier' =>'',
						'elec_supplier2' => (isset($this->request->data['elec_supplier2'])) ? $this->request->data['elec_supplier2'] : '',
						'gas_billing_days' => '',
						'gas_off_peak' => '',
						'gas_peak' => '',
						'gas_supplier' => '',
						'gas_supplier2' => (isset($this->request->data['gas_supplier2'])) ? $this->request->data['gas_supplier2'] : '',
						'singlerate_peak' => '',
						'timeofuse_controlled_load' => '',
						'timeofuse_offpeak' => '',
						'timeofuse_peak' => '',
						'timeofuse_shoulder' => '',
						'tworate_controlled_load' => '',
						'tworate_peak' => '',
						'controlled_load' => '',
						'usage_level' => '',
						'business_name' => '',
						'first_name' => '',
						'surname' => '',
						'mobile' => '',
						'phone' => '',
						'other_number' => '',
						'email' => '',
						'term1' => (isset($this->request->data['term1'])) ? 1 : 0,
						'term2' => (isset($this->request->data['term2'])) ? 1 : 0,

					);
					$this->Session->write('User.step1', $data);
				break;
				case 2:
					$data = array(
						'conditional_discount' => $this->request->data['conditional_discount'],
						'rate_freeze' => $this->request->data['rate_freeze'],
						'no_contract_plan' => $this->request->data['no_contract_plan'],
						'bill_smoothing' => $this->request->data['bill_smoothing'],
						'online_account_management' => $this->request->data['online_account_management'],
						'energy_monitoring_tools' => $this->request->data['energy_monitoring_tools'],
						'membership_reward_programs' => $this->request->data['membership_reward_programs'],
						'renewable_energy' => $this->request->data['renewable_energy'],
					);
					$this->Session->write('User.step2', $data);
			}
			return new CakeResponse(array(
				'body' => json_encode(array(
					'status' => '1',
					'data' => $this->Session->read('User')
				)), 
				'type' => 'json',
				'status' => '201'
			));
		}
	}
	
	public function call_me_back() {
		if ($this->request->is('put') || $this->request->is('post')) {
			$data = array(
				'name' => (isset($this->request->data['name'])) ? $this->request->data['name'] : '',
				'mobile' => (isset($this->request->data['mobile'])) ? $this->request->data['mobile'] : '',
				'phone' => (isset($this->request->data['phone'])) ? $this->request->data['phone'] : '',
				'email' => (isset($this->request->data['email'])) ? $this->request->data['email'] : '',
			);
			$step1 = $this->Session->read('User.step1');
			$tracking = $this->Session->read('User.tracking');
			$post['submitted'] = array(
			    'postcode' => $this->Session->read('User.postcode'),
			    'StateSupply' => $this->Session->read('User.state'),
			    'SuburbSupply' => $this->Session->read('User.suburb'),
			    'CustomerType' => ($step1['customer_type'] == 'SME') ? 'Business' : 'Residential',
			    'CurrentRetailerElec' => $step1['elec_supplier2'],
			    'CurrentRetailerGas' => $step1['gas_supplier2'],
			    'TradingName' => '',
			    'MobileNumber' => $data['mobile'],
			    'HomePhone' => $data['phone'],
			    'EmailM' => $data['email'],
			    'medium' => ($tracking) ? $tracking['medium'] : '',
			    'source' => ($tracking) ? $tracking['source'] : '',
			    'url' => ($tracking) ? $tracking['url'] : '',
			    'term' => ($tracking) ? $tracking['term'] : '',
			    'content' => ($tracking) ? $tracking['content'] : '',
			    'kwid' => ($tracking) ? $tracking['kwid'] : '',
			    'keyword' => ($tracking) ? $tracking['keyword'] : '',
			    'adid' => ($tracking) ? $tracking['adid'] : '',
			    'campaign' => ($tracking) ? $tracking['campaign'] : '',
			    'publisher' => ($tracking) ? $tracking['publisher'] : '',
			    'utm_campaign' => ($tracking) ? $tracking['utm_campaign'] : '',
			    'mtype' => ($tracking) ? $tracking['mtype'] : '',
			    'group' => ($tracking) ? $tracking['group'] : '',
			    'Howtheyfoundus' => 'Call Back Request - Results page',
			    'leadage' => ($tracking) ? $tracking['leadage'] : '',
			);
			$name_arr = explode(' ', $data['name']);
			if (count($name_arr) == 2) {
			    $post['submitted']['FirstName'] = ucfirst($name_arr[0]);
			    $post['submitted']['Surname'] = ucfirst($name_arr[1]);
			} else {
			    $post['submitted']['name'] = ucfirst($data['name']);
			}
			$postQuery = http_build_query($post, '', '&');
			// Create a new cURL resource
			$ch = curl_init();
			
			curl_setopt($ch, CURLOPT_URL, 'https://secure.leads360.com/Import.aspx?Provider=RSMSolutions&Client=RSMSolutions&CampaignId=2&XmlResponse=True');
			
			// Set the method to POST
			curl_setopt($ch, CURLOPT_POST, 1);
			
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			
			// Pass POST data
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postQuery);
			$response = curl_exec($ch); // Post to 3rd Party Service
			curl_close($ch); // close cURL resource
			
			$leadId = 0;
			if ($response) {
			    $result = simplexml_load_string($response);
			    foreach ($result->ImportResult[0]->attributes() as $key => $value) {
			        if ($key == 'leadId') {
			            $leadId = (int)$value;
			        }
			    } 
			}
			$this->Submission->create();
			$this->Submission->save(array('Submission' => array(
			    'sid' => time(),
			    'leadid' => $leadId,
			    'request' => $postQuery,
			    'response' => $response,
			    'submitted' => date('Y-m-d H:i:s'),
			    'source' => 'Tools /v1',
			)));
			$this->Session->write('User.sid', $leadId); // use lead id instead
			return new CakeResponse(array(
				'body' => json_encode(array(
					'status' => '1',
					'data' => $leadId
				)), 
				'type' => 'json',
				'status' => '201'
			));
		}
	}
	
	public function conversion_tracked() {
		if ($this->request->is('put') || $this->request->is('post')) {
			$this->Session->write('User.conversion_tracked', $this->request->data['conversion_tracked']);
			return new CakeResponse(array(
				'body' => json_encode(array(
					'status' => '1',
					'data' => $this->request->data['conversion_tracked']
				)), 
				'type' => 'json',
				'status' => '201'
			));
		}
	}
	
	public function call_me_gsp() {
		if ($this->request->is('put') || $this->request->is('post')) {
			$data = array(
				'name' => (isset($this->request->data['name'])) ? $this->request->data['name'] : '',
				'mobile' => (isset($this->request->data['mobile'])) ? $this->request->data['mobile'] : '',
				'postcode' => (isset($this->request->data['postcode'])) ? $this->request->data['postcode'] : '',
			);
			$post['submitted'] = array(
			    'postcode' => $data['postcode'],
			    'MobileNumber' => $data['mobile'],
			    'Howtheyfoundus' => 'Call Me - GSP page',
			    'campaign' => 'GSP',
			    'utm_campaign' => 'GSP'
			);
			$name_arr = explode(' ', $data['name']);
			if (count($name_arr) == 2) {
			    $post['submitted']['FirstName'] = ucfirst($name_arr[0]);
			    $post['submitted']['Surname'] = ucfirst($name_arr[1]);
			} else {
			    $post['submitted']['name'] = ucfirst($data['name']);
			}
			$postQuery = http_build_query($post, '', '&');
			// Create a new cURL resource
			$ch = curl_init();
			
			curl_setopt($ch, CURLOPT_URL, 'https://secure.leads360.com/Import.aspx?Provider=RSMSolutions&Client=RSMSolutions&CampaignId=2&XmlResponse=True');
			
			// Set the method to POST
			curl_setopt($ch, CURLOPT_POST, 1);
			
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			
			// Pass POST data
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postQuery);
			$response = curl_exec($ch); // Post to 3rd Party Service
			curl_close($ch); // close cURL resource
			
			$leadId = 0;
			if ($response) {
			    $result = simplexml_load_string($response);
			    foreach ($result->ImportResult[0]->attributes() as $key => $value) {
			        if ($key == 'leadId') {
			            $leadId = (int)$value;
			        }
			    } 
			}
			$this->Submission->create();
			$this->Submission->save(array('Submission' => array(
			    'sid' => time(),
			    'leadid' => $leadId,
			    'request' => $postQuery,
			    'response' => $response,
			    'submitted' => date('Y-m-d H:i:s'),
			    'source' => 'Tools /v1',
			)));
			$this->Session->write('User.sid', $leadId); // use lead id instead
			$this->redirect( '/compare/1?sid=' . $leadId );
		}
		$this->redirect( '/compare/1' );
	}
}
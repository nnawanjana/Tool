<?php

App::uses('AppController', 'Controller');

class V4Controller extends AppController {
	public $uses = array('Plan', 'Location', 'ElectricityRate', 'GasRate', 'Tariff', 'ElectricityPostcodeDistributor', 'GasPostcodeDistributor', 'ElectricityNmiDistributor', 'Consumption', 'SolarRebateScheme', 'Customer');
	public $helpers = array('Html', 'Icon');
	
	public function beforeFilter() {
		
		parent::beforeFilter();
		
		$this->Auth->allow();
		
		$this->layout = 'v4';
		
		if (!in_array($this->request->clientIp(), unserialize(STAFF_IPS))) {
			//$this->redirect( MAIN_SITE );
		}
		
		$this->_view_top_picks = false;
		if (isset($this->request->query) && !empty($this->request->query)) {
			if (isset($this->request->query['refresh'])) {
				$this->Session->delete('User');
				unset($_COOKIE['top_picks']);
			}
			if (isset($this->request->query['postcode'])) {
			    $this->Session->write('User.postcode', $this->request->query['postcode']);
			}
			if (isset($this->request->query['sid'])) {
			    $this->Session->write('User.sid', $this->request->query['sid']);
			}
			if (isset($this->request->query['state'])) {
			    $this->Session->write('User.state', $this->request->query['state']);
			}
			if (isset($this->request->query['suburb'])) {
			    $this->Session->write('User.suburb', $this->request->query['suburb']);
			}
			if (isset($this->request->query['view_top_picks']) && $this->request->query['view_top_picks'] == 1) {
				$this->_view_top_picks = true;
			}
			if (isset($this->request->query['customer']) && $this->request->query['customer']) {
				$customer = $this->Customer->findById($this->request->query['customer']);
				$this->Session->write('User', unserialize($customer['Customer']['data']));
			}
		}
	}
	
	public function index() {
		$this->redirect( '/v4/compare/1' );
	}
	
	public function compare($step = 1) {
		if (!in_array($step, array(1, 2, 3))) {
			$step = 1;
		}
		$states_arr = unserialize(AU_STATES);
        $payment_options_arr = unserialize(AU_PAYMENTS);
        $step1 = array();
        $tracking = array();
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
				if ($this->Session->check('User.step1')) {
					$step1 = $this->Session->read('User.step1');
				}
			break;
			case 2:
				$this->set('title_for_layout', 'Step 2 - Product Options');
				if ($this->Session->check('User.step1')) {
					$step1 = $this->Session->read('User.step1');
				} 
				else {
					$this->redirect( '/v4/compare/1' );
				}
				if ($this->Session->check('User.step2')) {
					$step2 = $this->Session->read('User.step2');
				}
			break;
			case 3:
				$this->set('title_for_layout', 'Step 3 - See Your Results');
				if ($this->Session->check('User.step1')) {
					$step1 = $this->Session->read('User.step1');
				} 
				else {
					$this->redirect( '/v4/compare/1' );
				}
				if ($this->Session->check('User.step2')) {
					$step2 = $this->Session->read('User.step2');
				} 
				else {
					$this->redirect( '/v4/compare/2' );
				}
				$filters = array(
					'retailer' => array(),
					'discount_type' => array(),
					'contract_length' => array(),
					'payment_options' => array(),
					'plan_type' => $step1['plan_type'],
					'customer_type' => $step1['customer_type'],
					'discount_type' => array('Guaranteed'),
					'sort_by' => $step2['sort_by'],
				);
				if ($step2['pay_on_time_discount'] == 'Yes') {
					$filters['discount_type'][] = 'Pay On Time';
				}
				if ($step2['direct_debit_discount'] == 'Yes') {
					$filters['discount_type'][] = 'Direct Debit';
				}
				if ($step2['dual_fuel_discount'] == 'Yes') {
					$filters['discount_type'][] = 'Dual Fuel';
				}
				if ($step2['bonus_discount'] == 'Yes') {
					$filters['discount_type'][] = 'Bonus';
				}
				if ($step2['prepay_discount'] == 'Yes') {
					$filters['discount_type'][] = 'Prepay';
				}
				$conditions = array();
				$special_plan_name = '';
				$conditions['Plan.status'] = 'Active';
				if ($step1['looking_for'] == 'Move Properties') {
					$conditions['Plan.new_connection'] = 'Yes';
				}
				if (isset($_COOKIE['top_picks']) && $_COOKIE['top_picks']) {
					$top_picks = explode(',', $_COOKIE['top_picks']);
				}
				$distributor_elec = $this->ElectricityPostcodeDistributor->findByPostcodeAndSuburb($this->Session->read('User.postcode'), $this->Session->read('User.suburb'));
				$distributor_gas = $this->GasPostcodeDistributor->findByPostcodeAndSuburb($this->Session->read('User.postcode'), $this->Session->read('User.suburb'));
				if ($this->_view_top_picks && !empty($top_picks)) {
					$view_top_picks = 1;
					$conditions['Plan.id'] = $top_picks;
					if ($this->request->is('put') || $this->request->is('post')) {
						$filters['sort_by'] = (isset($this->request->data['sort_by'])) ? $this->request->data['sort_by'] : $step2['sort_by'];
						$filters['discount_type'] = array();
						if (isset($this->request->data['discount_type']) && !empty($this->request->data['discount_type'])) {
							$filters['discount_type'] = $this->request->data['discount_type'];
						}
					}
				}
				else {
					$conditions['Plan.state'] = $states_arr[$this->Session->read('User.state')];
					$conditions['Plan.package'] = $step1['plan_type'];
					$conditions['Plan.res_sme'] = $step1['customer_type'];
					$conditions['Plan.version'] = array('All', '4');
                    $plan_expiry_or = array(
                        'or' => array(
                        	'Plan.plan_expiry' => '0000-00-00',
                        	'Plan.plan_expiry >' => date('Y-m-d'),
                        ),
                    );
                    $conditions[] = $plan_expiry_or;
					if ($this->request->is('put') || $this->request->is('post')) {
						$filters['sort_by'] = (isset($this->request->data['sort_by'])) ? $this->request->data['sort_by'] : $step2['sort_by'];
						if (isset($this->request->data['plan_type'])) {
    						// save to session
    						$step1['plan_type'] = $this->request->data['plan_type'];
    						$this->Session->write('User.step1', $step1);
						}
						$conditions['Plan.package'] = $filters['plan_type'] = $step1['plan_type'];
						$conditions['Plan.res_sme'] = $filters['customer_type'] = (isset($this->request->data['customer_type'])) ? $this->request->data['customer_type'] : $step1['customer_type'];
						$filters['discount_type'] = array();
						if (isset($this->request->data['discount_type']) && !empty($this->request->data['discount_type'])) {
							$filters['discount_type'] = $this->request->data['discount_type'];
						}
						if (isset($this->request->data['contract_length']) && !empty($this->request->data['contract_length'])  && !in_array('all', $this->request->data['contract_length'])) {
							$conditions['Plan.contract_length'] = $this->request->data['contract_length'];
							$filters['contract_length'] = $this->request->data['contract_length'];
						}
						if (isset($this->request->data['retailer']) && !empty($this->request->data['retailer']) && !in_array('all', $this->request->data['retailer'])) {
							$filters['retailer'] = $this->request->data['retailer'];
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
				$distributor_retailer_arr = array();
				if ($filters['plan_type'] == 'Elec') {
					if ($distributor_elec) {
						if ($distributor_elec['ElectricityPostcodeDistributor']['agl_distributor']) {
							$distributor_retailer_arr[] = 'AGL';
							if ($this->Session->read('User.state') == 'VIC' && $step1['customer_type'] == 'RES') {
    							if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(60, 61, 62))) {
                                    $conditions['Plan.version'][] = 'AGL Savers (Citipower, Jemena & Powercor)';
                                }
                            }
                            if ($this->Session->read('User.state') == 'NSW' && $step1['customer_type'] == 'RES') {
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(40, 42, 44, 45))) {
                                    $conditions['Plan.version'][] = 'AGL Savers (Essential Energy)';
                                }
                            }
                            if ($this->Session->read('User.state') == 'VIC' && $step1['customer_type'] == 'SME') {
    							if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(61, 62))) {
                                    $conditions['Plan.version'][] = 'Business Savers Powercor & Citipower';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(60, 63))) {
                                    $conditions['Plan.version'][] = 'Business Savers Jemena & Ausnet';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(64))) {
                                    $conditions['Plan.version'][] = 'Business Savers United';
                                }
                            }
                            if ($this->Session->read('User.state') == 'NSW' && $step1['customer_type'] == 'SME') {
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(40, 42, 44, 45))) {
                                    $conditions['Plan.version'][] = 'Business Savers Essential';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(41))) {
                                    $conditions['Plan.version'][] = 'Business Savers Ausgrid';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(43))) {
                                    $conditions['Plan.version'][] = 'Business Savers Endeavour';
                                }
                            }
						}
						if ($distributor_elec['ElectricityPostcodeDistributor']['powerdirect_distributor']) {
							$distributor_retailer_arr[] = 'Powerdirect';
							if ($this->Session->read('User.state') == 'VIC' && $step1['customer_type'] == 'RES') {
        				        if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 4), array(6102, 6001, 6203))) {
                                    $conditions['Plan.version'][] = 'Residential (Citipower, Jemena & Powercor)';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 4), array(6407, 6305))) {
                                    $conditions['Plan.version'][] = 'Residential (United & SP Ausnet)';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(60, 61, 64))) {
                                    $conditions['Plan.version'][] = 'Citipower, Jemena & Powercor';
                                }
        				    }
						}
						if ($distributor_elec['ElectricityPostcodeDistributor']['origin_energy_distributor']) {
							$distributor_retailer_arr[] = 'Origin Energy';
							if ($distributor_elec['ElectricityPostcodeDistributor']['origin_energy_special_product_name']) {
							    $conditions['Plan.version'][] = '4 (Special)';
						    }
						    if ($distributor_elec['ElectricityPostcodeDistributor']['origin_energy_origin_saver_patch']) {
							    $conditions['Plan.version'][] = 'Origin Saver Patch';
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(40, 42, 44, 45)) && $distributor_elec['ElectricityPostcodeDistributor']['origin_energy_origin_saver_patch']) {
    							    $conditions['Plan.version'][] = 'Origin Saver Essential Patch';
    						    }
    						    if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(62, 64)) && $distributor_elec['ElectricityPostcodeDistributor']['origin_energy_origin_saver_patch']) {
    							    $conditions['Plan.version'][] = 'Origin Saver Essential Patch VIC';
    						    }
						    }
						    if ($distributor_elec['ElectricityPostcodeDistributor']['origin_energy_businesssaver_hv']) {
							    $conditions['Plan.version'][] = 'BusinessSaver HV';
						    }
						    
						    if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(40, 42, 44, 45))) {
                                $conditions['Plan.version'][] = 'Origin Saver (Essential Energy)'; 
                            }
                            if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(41, 43))) {
                                $conditions['Plan.version'][] = 'Origin Saver (Ausgrid & Endeavour)';
                            }
                            if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(60, 61))) {
                                $conditions['Plan.version'][] = 'Origin Saver (Jemena & Citipower)';
                            }
                            if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(62, 64))) {
                                $conditions['Plan.version'][] = 'Origin Saver (Powercor & United)';
                            }
                            
                            if ($this->Session->read('User.state') == 'VIC' && $step1['customer_type'] == 'RES') {
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(63))) {
                                    $conditions['Plan.version'][] = 'Origin Saver (Ausnet)';
                                }
                            }
                            
                            if ($this->Session->read('User.state') == 'NSW' && $step1['customer_type'] == 'SME') {
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(41))) {
                                    $conditions['Plan.version'][] = 'BusinessSaver Ausgrid';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(43))) {
                                    $conditions['Plan.version'][] = 'BusinessSaver Endeavour Energy';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(40, 42, 44, 45))) {
                                    $conditions['Plan.version'][] = 'BusinessSaver Essential Energy';
                                }
                            }
                            if ($this->Session->read('User.state') == 'VIC' && $step1['customer_type'] == 'SME') {
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(63))) {
                                    $conditions['Plan.version'][] = 'BusinessSaver Ausnet';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(60, 61, 62, 64))) {
                                    $conditions['Plan.version'][] = 'BusinessSaver Citipower, Powercor, Jemena & United';
                                }
                            }
						}
						if ($distributor_elec['ElectricityPostcodeDistributor']['lumo_energy_distributor']) {
							$distributor_retailer_arr[] = 'Lumo Energy';
						}
						if ($distributor_elec['ElectricityPostcodeDistributor']['red_energy_distributor']) {
							$distributor_retailer_arr[] = 'Red Energy';
						}
						if ($distributor_elec['ElectricityPostcodeDistributor']['momentum_distributor']) {
							$distributor_retailer_arr[] = 'Momentum';
						}
						if ($distributor_elec['ElectricityPostcodeDistributor']['powershop_distributor']) {
							$distributor_retailer_arr[] = 'Powershop';
						}
						if ($distributor_elec['ElectricityPostcodeDistributor']['dodo_distributor']) {
							$distributor_retailer_arr[] = 'Dodo';
						}
						if ($distributor_elec['ElectricityPostcodeDistributor']['alinta_energy_distributor']) {
							$distributor_retailer_arr[] = 'Alinta Energy';
						}
						if ($distributor_elec['ElectricityPostcodeDistributor']['energy_australia_distributor']) {
							$distributor_retailer_arr[] = 'Energy Australia';
							if ($this->Session->read('User.state') == 'VIC' && $step1['customer_type'] == 'SME') {
                                if ($step1['nmi']) {
                                    switch (substr($step1['nmi'], 0, 2)) {
                                        case '60':
                                            $conditions['Plan.version'][] = 'Everyday Saver Business Jemena';
                                            $conditions['Plan.version'][] = 'Business Saver Business Jemena';
                                            break;
                                        case '61':
                                            $conditions['Plan.version'][] = 'Everyday Saver Business Citipower';
                                            $conditions['Plan.version'][] = 'Business Saver Business Citipower';
                                            break;
                                        case '62':
                                            $conditions['Plan.version'][] = 'Everyday Saver Business Powercor';
                                            $conditions['Plan.version'][] = 'Business Saver Business Powercor';
                                            break;
                                        case '63':
                                            $conditions['Plan.version'][] = 'Everyday Saver Business Ausnet';
                                            $conditions['Plan.version'][] = 'Business Saver Business Ausnet';
                                            break;
                                        case '64':
                                            $conditions['Plan.version'][] = 'Everyday Saver Business United Energy';
                                            $conditions['Plan.version'][] = 'Business Saver Business United';
                                            break;
                                    }
                                }
    				        }
    				        if ($this->Session->read('User.state') == 'NSW' && $step1['customer_type'] == 'SME') {
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(40, 42, 43, 44, 45))) {
                                    $conditions['Plan.version'][] = 'Everyday Saver Business (Essential & Endeavour Energy)';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(41))) {
                                    $conditions['Plan.version'][] = 'Everyday Saver Business (Ausgrid)';
                                }
        				    }
    				        if ($this->Session->read('User.state') == 'VIC' && $step1['customer_type'] == 'RES') {
        				        if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(60, 63))) {
                                    $conditions['Plan.version'][] = 'Flexi Saver (Jemena & AusNet)';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(61, 62, 64))) {
                                    $conditions['Plan.version'][] = 'Flexi Saver (Powercor, Citipower & United)';
                                }
        				    }
        				    if ($this->Session->read('User.state') == 'NSW' && $step1['customer_type'] == 'RES') {
        				        if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(43))) {
                                    $conditions['Plan.version'][] = 'Flexi Saver (Endeavour)';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(40, 42, 44, 45))) {
                                    $conditions['Plan.version'][] = 'Flexi Saver (Essential)';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(41))) {
                                    $conditions['Plan.version'][] = 'Flexi Saver (Ausgrid)';
                                }
        				    }
						}
						if ($distributor_elec['ElectricityPostcodeDistributor']['sumo_power_distributor']) {
							$distributor_retailer_arr[] = 'Sumo Power';
						}
						if ($distributor_elec['ElectricityPostcodeDistributor']['erm_distributor']) {
    						$distributor_retailer_arr[] = 'ERM';
    				    }
    				    if ($distributor_elec['ElectricityPostcodeDistributor']['next_business_energy_distributor']) {
    						$distributor_retailer_arr[] = 'Next Business Energy';
    				    }
					}
				} 
				elseif ($filters['plan_type'] == 'Gas') {
					if ($distributor_gas) {
						if ($distributor_gas['GasPostcodeDistributor']['agl_distributor']) {
							$distributor_retailer_arr[] = 'AGL';
						}
						if ($distributor_gas['GasPostcodeDistributor']['origin_energy_distributor']) {
							$distributor_retailer_arr[] = 'Origin Energy';
						}
						if ($distributor_gas['GasPostcodeDistributor']['lumo_energy_distributor']) {
							$distributor_retailer_arr[] = 'Lumo Energy';
						}
						if ($distributor_gas['GasPostcodeDistributor']['red_energy_distributor']) {
							$distributor_retailer_arr[] = 'Red Energy';
						}
						if ($distributor_gas['GasPostcodeDistributor']['momentum_distributor']) {
							$distributor_retailer_arr[] = 'Momentum';
						}
						if ($distributor_gas['GasPostcodeDistributor']['dodo_distributor']) {
							$distributor_retailer_arr[] = 'Dodo';
						}
						if ($distributor_gas['GasPostcodeDistributor']['alinta_energy_distributor']) {
							$distributor_retailer_arr[] = 'Alinta Energy';
						}
						if ($distributor_gas['GasPostcodeDistributor']['energy_australia_distributor']) {
							$distributor_retailer_arr[] = 'Energy Australia';
						}
					}
				} 
				elseif ($filters['plan_type'] == 'Dual') {
					if ($distributor_elec && $distributor_gas) {
						if ($distributor_elec['ElectricityPostcodeDistributor']['agl_distributor'] && $distributor_gas['GasPostcodeDistributor']['agl_distributor']) {
							$distributor_retailer_arr[] = 'AGL';
							if ($this->Session->read('User.state') == 'VIC' && $step1['customer_type'] == 'RES') {
    							if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(60, 61, 62))) {
                                    $conditions['Plan.version'][] = 'AGL Savers (Citipower, Jemena & Powercor)';
                                }
                            }
                            if ($this->Session->read('User.state') == 'NSW' && $step1['customer_type'] == 'RES') {
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(40, 42, 44, 45))) {
                                    $conditions['Plan.version'][] = 'AGL Savers (Essential Energy)';
                                }
                            }
                            if ($this->Session->read('User.state') == 'VIC' && $step1['customer_type'] == 'SME') {
    							if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(61, 62))) {
                                    $conditions['Plan.version'][] = 'Business Savers Powercor & Citipower';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(60, 63))) {
                                    $conditions['Plan.version'][] = 'Business Savers Jemena & Ausnet';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(64))) {
                                    $conditions['Plan.version'][] = 'Business Savers United';
                                }
                            }
                            if ($this->Session->read('User.state') == 'NSW' && $step1['customer_type'] == 'SME') {
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(40, 42, 44, 45))) {
                                    $conditions['Plan.version'][] = 'Business Savers Essential';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(41))) {
                                    $conditions['Plan.version'][] = 'Business Savers Ausgrid';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(43))) {
                                    $conditions['Plan.version'][] = 'Business Savers Endeavour';
                                }
                            }
						}
						if ($distributor_elec['ElectricityPostcodeDistributor']['origin_energy_distributor'] && $distributor_gas['GasPostcodeDistributor']['origin_energy_distributor']) {
							$distributor_retailer_arr[] = 'Origin Energy';
							if ($distributor_elec['ElectricityPostcodeDistributor']['origin_energy_special_product_name']) {
							    $conditions['Plan.version'][] = '4 (Special)';
						    }
						    if ($distributor_elec['ElectricityPostcodeDistributor']['origin_energy_origin_saver_patch'] || $distributor_gas['GasPostcodeDistributor']['origin_energy_origin_saver_patch']) {
							    $conditions['Plan.version'][] = 'Origin Saver Patch';
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(40, 42, 44, 45)) && $distributor_elec['ElectricityPostcodeDistributor']['origin_energy_origin_saver_patch']) {
    							    $conditions['Plan.version'][] = 'Origin Saver Essential Patch';
    						    }
    						    if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(62, 64)) && $distributor_elec['ElectricityPostcodeDistributor']['origin_energy_origin_saver_patch']) {
    							    $conditions['Plan.version'][] = 'Origin Saver Essential Patch VIC';
    						    }
						    }
						    if ($distributor_elec['ElectricityPostcodeDistributor']['origin_energy_businesssaver_hv']) {
							    $conditions['Plan.version'][] = 'BusinessSaver HV';
						    }
						    
						    if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(40, 42, 44, 45))) {
                                $conditions['Plan.version'][] = 'Origin Saver (Essential Energy)'; 
                            }
                            if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(41, 43))) {
                                $conditions['Plan.version'][] = 'Origin Saver (Ausgrid & Endeavour)';
                            }
                            if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(60, 61))) {
                                $conditions['Plan.version'][] = 'Origin Saver (Jemena & Citipower)';
                            }
                            if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(62, 64))) {
                                $conditions['Plan.version'][] = 'Origin Saver (Powercor & United)';
                            }
                            
                            if ($this->Session->read('User.state') == 'VIC' && $step1['customer_type'] == 'RES') {
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(63))) {
                                    $conditions['Plan.version'][] = 'Origin Saver (Ausnet)';
                                }
                            }
                            
                            if ($this->Session->read('User.state') == 'NSW' && $step1['customer_type'] == 'SME') {
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(41))) {
                                    $conditions['Plan.version'][] = 'BusinessSaver Ausgrid';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(43))) {
                                    $conditions['Plan.version'][] = 'BusinessSaver Endeavour Energy';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(40, 42, 44, 45))) {
                                    $conditions['Plan.version'][] = 'BusinessSaver Essential Energy';
                                }
                            }
                            if ($this->Session->read('User.state') == 'VIC' && $step1['customer_type'] == 'SME') {
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(63))) {
                                    $conditions['Plan.version'][] = 'BusinessSaver Ausnet';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(60, 61, 62, 64))) {
                                    $conditions['Plan.version'][] = 'BusinessSaver Citipower, Powercor, Jemena & United';
                                }
                            }
						}
						if ($distributor_elec['ElectricityPostcodeDistributor']['lumo_energy_distributor'] && $distributor_gas['GasPostcodeDistributor']['lumo_energy_distributor']) {
							$distributor_retailer_arr[] = 'Lumo Energy';
						}
						if ($distributor_elec['ElectricityPostcodeDistributor']['red_energy_distributor'] && $distributor_gas['GasPostcodeDistributor']['red_energy_distributor']) {
							$distributor_retailer_arr[] = 'Red Energy';
						}
						if ($distributor_elec['ElectricityPostcodeDistributor']['momentum_distributor'] && $distributor_gas['GasPostcodeDistributor']['momentum_distributor']) {
							$distributor_retailer_arr[] = 'Momentum';
						}
						if ($distributor_elec['ElectricityPostcodeDistributor']['dodo_distributor'] && $distributor_gas['GasPostcodeDistributor']['dodo_distributor']) {
							$distributor_retailer_arr[] = 'Dodo';
						}
						if ($distributor_elec['ElectricityPostcodeDistributor']['alinta_energy_distributor'] && $distributor_gas['GasPostcodeDistributor']['alinta_energy_distributor']) {
							$distributor_retailer_arr[] = 'Alinta Energy';
						}
						if ($distributor_elec['ElectricityPostcodeDistributor']['energy_australia_distributor'] && $distributor_gas['GasPostcodeDistributor']['energy_australia_distributor']) {
							$distributor_retailer_arr[] = 'Energy Australia';
							if ($this->Session->read('User.state') == 'VIC' && $step1['customer_type'] == 'SME') {
                                if ($step1['nmi']) {
                                    switch (substr($step1['nmi'], 0, 2)) {
                                        case '60':
                                            $conditions['Plan.version'][] = 'Everyday Saver Business Jemena';
                                            $conditions['Plan.version'][] = 'Business Saver Business Jemena';
                                            break;
                                        case '61':
                                            $conditions['Plan.version'][] = 'Everyday Saver Business Citipower';
                                            $conditions['Plan.version'][] = 'Business Saver Business Citipower';
                                            break;
                                        case '62':
                                            $conditions['Plan.version'][] = 'Everyday Saver Business Powercor';
                                            $conditions['Plan.version'][] = 'Business Saver Business Powercor';
                                            break;
                                        case '63':
                                            $conditions['Plan.version'][] = 'Everyday Saver Business Ausnet';
                                            $conditions['Plan.version'][] = 'Business Saver Business Ausnet';
                                            break;
                                        case '64':
                                            $conditions['Plan.version'][] = 'Everyday Saver Business United Energy';
                                            $conditions['Plan.version'][] = 'Business Saver Business United';
                                            break;
                                    }
                                }
    				        }
    				        if ($this->Session->read('User.state') == 'NSW' && $step1['customer_type'] == 'SME') {
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(40, 42, 43, 44, 45))) {
                                    $conditions['Plan.version'][] = 'Everyday Saver Business (Essential & Endeavour Energy)';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(41))) {
                                    $conditions['Plan.version'][] = 'Everyday Saver Business (Ausgrid)';
                                }
        				    }
    				        if ($this->Session->read('User.state') == 'VIC' && $step1['customer_type'] == 'RES') {
        				        if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(60, 63))) {
                                    $conditions['Plan.version'][] = 'Flexi Saver (Jemena & AusNet)';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(61, 62, 64))) {
                                    $conditions['Plan.version'][] = 'Flexi Saver (Powercor, Citipower & United)';
                                }
        				    }
        				    if ($this->Session->read('User.state') == 'NSW' && $step1['customer_type'] == 'RES') {
        				        if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(43))) {
                                    $conditions['Plan.version'][] = 'Flexi Saver (Endeavour)';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(40, 42, 44, 45))) {
                                    $conditions['Plan.version'][] = 'Flexi Saver (Essential)';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(41))) {
                                    $conditions['Plan.version'][] = 'Flexi Saver (Ausgrid)';
                                }
        				    }
						}
						if ($distributor_elec['ElectricityPostcodeDistributor']['pd_agl_distributor'] && $distributor_gas['GasPostcodeDistributor']['pd_agl_distributor']) {
    						$distributor_retailer_arr[] = 'Powerdirect and AGL';
    				    }
					}
				}
				$solar_specific_plan = false;
				$solar_rebate_scheme = '';
				if ($step1['nmi_distributor']) {
					if ($step1['tariff1']) {
					    $tariff1 = explode('|', $step1['tariff1']);
					    if ($tariff1[3] == 'Solar') {
					    	$solar_specific_plan = true;
					    	$tariff_code = $tariff1[0];
					    }
					}
					if ($step1['tariff2']) {
					    $tariff2 = explode('|', $step1['tariff2']);
					    if ($tariff2[3] == 'Solar') {
					    	$solar_specific_plan = true;
					    	$tariff_code = $tariff2[0];
					    }
					}
					if ($step1['tariff3']) {
					    $tariff3 = explode('|', $step1['tariff3']);
					    if ($tariff3[3] == 'Solar') {
					    	$solar_specific_plan = true;
					    	$tariff_code = $tariff3[0];
					    }
					}
					if ($step1['tariff4']) {
					    $tariff4 = explode('|', $step1['tariff4']);
					    if ($tariff4[3] == 'Solar') {
					    	$solar_specific_plan = true;
					    	$tariff_code = $tariff4[0];
					    }
					}
					if ($tariff_code) {
					    $tariff = $this->Tariff->find('first', array(
					    	'conditions' => array(
					    		'Tariff.tariff_code' => $tariff_code,
					    		'Tariff.res_sme' => $step1['customer_type'],
					    		'Tariff.distributor' => explode('/', $step1['nmi_distributor']),
					    	),
					    ));
					    $solar_rebate_scheme = $tariff['Tariff']['solar_rebate_scheme'];
					}
				}
				if ($solar_specific_plan) {
					$conditions['Plan.solar_specific_plan !='] = 'Not Solar';
				}
				else {
					$conditions['Plan.solar_specific_plan !='] = 'Solar Only';
				}
				$retailer_arr = array();
				if ($filters['retailer']) {
					foreach ($filters['retailer'] as $value) {
						if (in_array($value, $distributor_retailer_arr)) {
							$retailer_arr[] = $value;
						}
					}
					$filters['retailer'] = $retailer_arr;
				} 
				else {
					$retailer_arr = $distributor_retailer_arr;
				}
				if ($step1['nmi_distributor'] && $step1['tariff_parent']) {
					$tariff_parent = explode('|', $step1['tariff_parent']);
					$tariff = $this->Tariff->find('first', array(
					    'conditions' => array(
					    	'Tariff.tariff_code' => $tariff_parent[0],
					    	'Tariff.res_sme' => $step1['customer_type'],
					    	'Tariff.distributor' => explode('/', $step1['nmi_distributor']),
					    ),
					));
					if ($tariff['Tariff']['powershop_unsupported_tariff'] == 'Unsupported') {
					    if (($key = array_search('Powershop', $retailer_arr)) !== false) {
					    	unset($retailer_arr[$key]);
					    }
					}
					if ($tariff['Tariff']['red_energy_unsupported_tariff'] == 'Unsupported') {
					    if (($key = array_search('Red Energy', $retailer_arr)) !== false) {
					    	unset($retailer_arr[$key]);
					    }
					}
					if ($tariff['Tariff']['powerdirect_unsupported_tariff'] == 'Unsupported') {
					    if (($key = array_search('Powerdirect', $retailer_arr)) !== false) {
					    	unset($retailer_arr[$key]);
					    }
					}
					if ($tariff['Tariff']['momentum_unsupported_tariff'] == 'Unsupported') {
					    if (($key = array_search('Momentum', $retailer_arr)) !== false) {
					    	unset($retailer_arr[$key]);
					    }
					}
					if ($tariff['Tariff']['sumo_power_unsupported_tariff'] == 'Unsupported') {
					    if (($key = array_search('Sumo Power', $retailer_arr)) !== false) {
					    	unset($retailer_arr[$key]);
					    }
					}
					if ($tariff['Tariff']['erm_unsupported_tariff'] == 'Unsupported') {
					    if (($key = array_search('ERM', $retailer_arr)) !== false) {
					    	unset($retailer_arr[$key]);
					    }
					}
					if ($tariff['Tariff']['pd_agl_unsupported_tariff'] == 'Unsupported') {
					    if (($key = array_search('Powerdirect and AGL', $retailer_arr)) !== false) {
					    	unset($retailer_arr[$key]);
					    }
					}
				}
				$conditions['Plan.retailer'] = $retailer_arr;
				$order = array();
				if ($step2['pay_on_time_discount'] == 'Yes') {
					$order[] = 'Plan.pay_on_time DESC';
				}
				if ($step2['direct_debit_discount'] == 'Yes') {
					$order[] = 'Plan.direct_debit DESC';
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
				$order[] = 'Plan.retailer ASC';

				$available_retailers = $distributor_retailer_arr;
				if ($view_top_picks) {
					$available_conditions =  array(
				    	'Plan.status' => 'Active',
				    	'Plan.id' => $top_picks,
						'Plan.retailer' => $available_retailers,
				    );
				}
				else {
					$available_conditions = array(
				    	'Plan.status' => 'Active',
						'Plan.state' => $conditions['Plan.state'],
						'Plan.package' => $conditions['Plan.package'],
						'Plan.res_sme' => $conditions['Plan.res_sme'],
						'Plan.retailer' => $available_retailers,
						'Plan.version' => $conditions['Plan.version']
				    );
				    if ($solar_specific_plan) {
						$available_conditions['Plan.solar_specific_plan !='] = 'Not Solar';
					}
					else {
						$available_conditions['Plan.solar_specific_plan !='] = 'Solar Only';
					}
				}
				$available_plans = $this->Plan->find('all', array(
				    'conditions' => $available_conditions,
					'order' => $order
				));
				if (!empty($available_plans)) {
				    foreach ($available_plans as $plan) {
				    	if ($plan['Plan']['discount_pay_on_time_description'] && !in_array('Pay On Time', $available_discount_type)) {
				    		$available_discount_type[] = 'Pay On Time';
				    	}
				    	if ($plan['Plan']['discount_guaranteed_description'] && !in_array('Guaranteed', $available_discount_type)) {
				    		$available_discount_type[] = 'Guaranteed';
				    	}
				    	if ($plan['Plan']['discount_direct_debit_description'] && !in_array('Direct Debit', $available_discount_type)) {
				    		$available_discount_type[] = 'Direct Debit';
				    	}
				    	if ($plan['Plan']['discount_dual_fuel_description'] && !in_array('Dual Fuel', $available_discount_type)) {
				    		$available_discount_type[] = 'Dual Fuel';
				    	}
				    	if ($plan['Plan']['retailer'] == 'Sumo Power' && !in_array('Bonus', $available_discount_type)) {
				    		$available_discount_type[] = 'Bonus';
				    	}
				    	if ($plan['Plan']['retailer'] == 'Sumo Power' && !in_array('Prepay', $available_discount_type)) {
				    		$available_discount_type[] = 'Prepay';
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
				$plans_temp = $this->Plan->find('all', array(
					'conditions' => $conditions,
					'order' => $order
				));
				if (!empty($plans_temp)) {
					$default_consumption = $this->Consumption->findByStateAndResSme($this->Session->read('User.state'), $step1['customer_type']);
					if ($step1['elec_recent_bill'] == 'No' && $step1['elec_usage_level']) {
						$step1['elec_meter_type'] = ($step1['elec_meter_type']) ? $step1['elec_meter_type'] : $step1['elec_meter_type2'];
						$step1['elec_billing_days'] = $default_consumption['Consumption']['elec_billing_days'];
						$default_peak = explode('/', $default_consumption['Consumption']['elec_peak']);
						$default_cl1 = explode('/', $default_consumption['Consumption']['elec_cl1']);
						$default_cl2 = explode('/', $default_consumption['Consumption']['elec_cl2']);
						$default_shoulder = explode('/', $default_consumption['Consumption']['elec_shoulder']);
						$default_offpeak = explode('/', $default_consumption['Consumption']['elec_offpeak']);
						$default_cs = explode('/', $default_consumption['Consumption']['elec_cs']);
						$default_cs_billing_start = $default_consumption['Consumption']['elec_cs_start_date'];
						switch ($step1['elec_meter_type']) {
	                        case 'Single Rate':
								switch ($step1['elec_usage_level']) {
								    case 'Low':
								    	$step1['singlerate_peak'] = ($step1['singlerate_peak']) ? $step1['singlerate_peak'] : $default_peak[0];
								    break;
								    case 'Medium':
								    	$step1['singlerate_peak'] = ($step1['singlerate_peak']) ? $step1['singlerate_peak'] : $default_peak[1];
								    break;
								    case 'High':
								    	$step1['singlerate_peak'] = ($step1['singlerate_peak']) ? $step1['singlerate_peak'] : $default_peak[2];
								    break;
								}
	                            break;
	                        case 'Single Rate + CL1':
	                            switch ($step1['elec_usage_level']) {
								    case 'Low':
								    	$step1['singlerate_cl1_peak'] = ($step1['singlerate_cl1_peak']) ? $step1['singlerate_cl1_peak'] : $default_peak[0];
								    	$step1['singlerate_cl1'] = ($step1['singlerate_cl1']) ? $step1['singlerate_cl1'] : $default_cl1[0];
								    break;
								    case 'Medium':
								    	$step1['singlerate_cl1_peak'] = ($step1['singlerate_cl1_peak']) ? $step1['singlerate_cl1_peak'] : $default_peak[1];
								    	$step1['singlerate_cl1'] = ($step1['singlerate_cl1']) ? $step1['singlerate_cl1'] : $default_cl1[1];
								    break;
								    case 'High':
								    	$step1['singlerate_cl1_peak'] = ($step1['singlerate_cl1_peak']) ? $step1['singlerate_cl1_peak'] : $default_peak[2];
								    	$step1['singlerate_cl1'] = ($step1['singlerate_cl1']) ? $step1['singlerate_cl1'] : $default_cl1[2];
								    break;
								}
	                            break;
	                        case 'Single Rate + CL2':
	                            switch ($step1['elec_usage_level']) {
								    case 'Low':
								    	$step1['singlerate_cl2_peak'] = ($step1['singlerate_cl2_peak']) ? $step1['singlerate_cl2_peak'] : $default_peak[0];
								    	$step1['singlerate_cl2'] = ($step1['singlerate_cl2']) ? $step1['singlerate_cl2'] : $default_cl2[0];
								    break;
								    case 'Medium':
								    	$step1['singlerate_cl2_peak'] = ($step1['singlerate_cl2_peak']) ? $step1['singlerate_cl2_peak'] : $default_peak[1];
								    	$step1['singlerate_cl2'] = ($step1['singlerate_cl2']) ? $step1['singlerate_cl2'] : $default_cl2[1];
								    break;
								    case 'High':
								    	$step1['singlerate_cl2_peak'] = ($step1['singlerate_cl2_peak']) ? $step1['singlerate_cl2_peak'] : $default_peak[2];
								    	$step1['singlerate_cl2'] = ($step1['singlerate_cl2']) ? $step1['singlerate_cl2'] : $default_cl2[2];
								    break;
								}
	                            break;
	                        case 'Single Rate + CL1 + CL2':
	                            switch ($step1['elec_usage_level']) {
								    case 'Low':
								    	$step1['singlerate_cl1_cl2_peak'] = ($step1['singlerate_cl1_cl2_peak']) ? $step1['singlerate_cl1_cl2_peak'] : $default_peak[0];
								    	$step1['singlerate_2_cl1'] = ($step1['singlerate_2_cl1']) ? $step1['singlerate_2_cl1'] : $default_cl1[0];
								    	$step1['singlerate_2_cl2'] = ($step1['singlerate_2_cl2']) ? $step1['singlerate_2_cl2'] : $default_cl2[0];
								    break;
								    case 'Medium':
								    	$step1['singlerate_cl1_cl2_peak'] = ($step1['singlerate_cl1_cl2_peak']) ? $step1['singlerate_cl1_cl2_peak'] : $default_peak[1];
								    	$step1['singlerate_2_cl1'] = ($step1['singlerate_2_cl1']) ? $step1['singlerate_2_cl1'] : $default_cl1[1];
								    	$step1['singlerate_2_cl2'] = ($step1['singlerate_2_cl2']) ? $step1['singlerate_2_cl2'] : $default_cl2[1];
								    break;
								    case 'High':
								    	$step1['singlerate_cl1_cl2_peak'] = ($step1['singlerate_cl1_cl2_peak']) ? $step1['singlerate_cl1_cl2_peak'] : $default_peak[2];
								    	$step1['singlerate_2_cl1'] = ($step1['singlerate_2_cl1']) ? $step1['singlerate_2_cl1'] : $default_cl1[2];
								    	$step1['singlerate_2_cl2'] = ($step1['singlerate_2_cl2']) ? $step1['singlerate_2_cl2'] : $default_cl2[2];
								    break;
								}
	                            break;
	                        case 'Single Rate + Climate Saver':
	                            switch ($step1['elec_usage_level']) {
								    case 'Low':
								    	$step1['singlerate_cs_peak'] = ($step1['singlerate_cs_peak']) ? $step1['singlerate_cs_peak'] : $default_peak[0];
								    	$step1['singlerate_cs'] = ($step1['singlerate_cs']) ? $step1['singlerate_cs'] : $default_cs[0];
								    	$step1['singlerate_cs_billing_start'] = ($step1['singlerate_cs_billing_start']) ? $step1['singlerate_cs_billing_start'] : $default_cs_billing_start;
								    break;
								    case 'Medium':
								    	$step1['singlerate_cs_peak'] = ($step1['singlerate_cs_peak']) ? $step1['singlerate_cs_peak'] : $default_peak[1];
								    	$step1['singlerate_cs'] = ($step1['singlerate_cs']) ? $step1['singlerate_cs'] : $default_cs[1];
								    	$step1['singlerate_cs_billing_start'] = ($step1['singlerate_cs_billing_start']) ? $step1['singlerate_cs_billing_start'] : $default_cs_billing_start;
								    break;
								    case 'High':
								    	$step1['singlerate_cs_peak'] = ($step1['singlerate_cs_peak']) ? $step1['singlerate_cs_peak'] : $default_peak[2];
								    	$step1['singlerate_cs'] = ($step1['singlerate_cs']) ? $step1['singlerate_cs'] : $default_cs[2];
								    	$step1['singlerate_cs_billing_start'] = ($step1['singlerate_cs_billing_start']) ? $step1['singlerate_cs_billing_start'] : $default_cs_billing_start;
								    break;
								}
	                            break;
	                        case 'Single Rate + CL1 + Climate Saver':
	                            switch ($step1['elec_usage_level']) {
								    case 'Low':
								    	$step1['singlerate_cl1_cs_peak'] = ($step1['singlerate_cl1_cs_peak']) ? $step1['singlerate_cl1_cs_peak'] : $default_peak[0];
								    	$step1['singlerate_3_cl1'] = ($step1['singlerate_3_cl1']) ? $step1['singlerate_3_cl1'] : $default_cl1[0];
								    	$step1['singlerate_3_cs'] = ($step1['singlerate_3_cs']) ? $step1['singlerate_3_cs'] : $default_cs[0];
								    	$step1['singlerate_cl1_cs_billing_start'] = ($step1['singlerate_cl1_cs_billing_start']) ? $step1['singlerate_cl1_cs_billing_start'] : $default_cs_billing_start;
								    break;
								    case 'Medium':
								    	$step1['singlerate_cl1_cs_peak'] = ($step1['singlerate_cl1_cs_peak']) ? $step1['singlerate_cl1_cs_peak'] : $default_peak[1];
								    	$step1['singlerate_3_cl1'] = ($step1['singlerate_3_cl1']) ? $step1['singlerate_3_cl1'] : $default_cl1[1];
								    	$step1['singlerate_3_cs'] = ($step1['singlerate_3_cs']) ? $step1['singlerate_3_cs'] : $default_cs[1];
								    	$step1['singlerate_cl1_cs_billing_start'] = ($step1['singlerate_cl1_cs_billing_start']) ? $step1['singlerate_cl1_cs_billing_start'] : $default_cs_billing_start;
								    break;
								    case 'High':
								    	$step1['singlerate_cl1_cs_peak'] = ($step1['singlerate_cl1_cs_peak']) ? $step1['singlerate_cl1_cs_peak'] : $default_peak[2];
								    	$step1['singlerate_3_cl1'] = ($step1['singlerate_3_cl1']) ? $step1['singlerate_3_cl1'] : $default_cl1[2];
								    	$step1['singlerate_3_cs'] = ($step1['singlerate_3_cs']) ? $step1['singlerate_3_cs'] : $default_cs[2];
								    	$step1['singlerate_cl1_cs_billing_start'] = ($step1['singlerate_cl1_cs_billing_start']) ? $step1['singlerate_cl1_cs_billing_start'] : $default_cs_billing_start;
								    break;
								}
	                            break;
	                        case 'Time of Use':
	                            switch ($step1['elec_usage_level']) {
								    case 'Low':
								    	$step1['timeofuse_peak'] = ($step1['timeofuse_peak']) ? $step1['timeofuse_peak'] : $default_peak[0];
								    	$step1['timeofuse_shoulder'] = ($step1['timeofuse_shoulder']) ? $step1['timeofuse_shoulder'] : $default_shoulder[0];
								    	$step1['timeofuse_offpeak'] = ($step1['timeofuse_offpeak']) ? $step1['timeofuse_offpeak'] : $default_offpeak[0];
								    break;
								    case 'Medium':
								    	$step1['timeofuse_peak'] = ($step1['timeofuse_peak']) ? $step1['timeofuse_peak'] : $default_peak[1];
								    	$step1['timeofuse_shoulder'] = ($step1['timeofuse_shoulder']) ? $step1['timeofuse_shoulder'] : $default_shoulder[1];
								    	$step1['timeofuse_offpeak'] = ($step1['timeofuse_offpeak']) ? $step1['timeofuse_offpeak'] : $default_offpeak[1];
								    break;
								    case 'High':
								    	$step1['timeofuse_peak'] = ($step1['timeofuse_peak']) ? $step1['timeofuse_peak'] : $default_peak[2];
								    	$step1['timeofuse_shoulder'] = ($step1['timeofuse_shoulder']) ? $step1['timeofuse_shoulder'] : $default_shoulder[2];
								    	$step1['timeofuse_offpeak'] = ($step1['timeofuse_offpeak']) ? $step1['timeofuse_offpeak'] : $default_offpeak[2];
								    break;
								}
	                            break;
	                        case 'Time of Use (PowerSmart)':
	                            switch ($step1['elec_usage_level']) {
								    case 'Low':
								    	$step1['timeofuse_peak'] = ($step1['timeofuse_peak']) ? $step1['timeofuse_peak'] : $default_peak[0];
								    	$step1['timeofuse_shoulder'] = ($step1['timeofuse_shoulder']) ? $step1['timeofuse_shoulder'] : $default_shoulder[0];
								    	$step1['timeofuse_offpeak'] = ($step1['timeofuse_offpeak']) ? $step1['timeofuse_offpeak'] : $default_offpeak[0];
								    break;
								    case 'Medium':
								    	$step1['timeofuse_peak'] = ($step1['timeofuse_peak']) ? $step1['timeofuse_peak'] : $default_peak[1];
								    	$step1['timeofuse_shoulder'] = ($step1['timeofuse_shoulder']) ? $step1['timeofuse_shoulder'] : $default_shoulder[1];
								    	$step1['timeofuse_offpeak'] = ($step1['timeofuse_offpeak']) ? $step1['timeofuse_offpeak'] : $default_offpeak[1];
								    break;
								    case 'High':
								    	$step1['timeofuse_peak'] = ($step1['timeofuse_peak']) ? $step1['timeofuse_peak'] : $default_peak[2];
								    	$step1['timeofuse_shoulder'] = ($step1['timeofuse_shoulder']) ? $step1['timeofuse_shoulder'] : $default_shoulder[2];
								    	$step1['timeofuse_offpeak'] = ($step1['timeofuse_offpeak']) ? $step1['timeofuse_offpeak'] : $default_offpeak[2];
								    break;
								}
	                            break;
	                        case 'Time of Use (LoadSmart)':
	                            switch ($step1['elec_usage_level']) {
								    case 'Low':
								    	$step1['timeofuse_peak'] = ($step1['timeofuse_peak']) ? $step1['timeofuse_peak'] : $default_peak[0];
								    	$step1['timeofuse_shoulder'] = ($step1['timeofuse_shoulder']) ? $step1['timeofuse_shoulder'] : $default_shoulder[0];
								    	$step1['timeofuse_offpeak'] = ($step1['timeofuse_offpeak']) ? $step1['timeofuse_offpeak'] : $default_offpeak[0];
								    break;
								    case 'Medium':
								    	$step1['timeofuse_peak'] = ($step1['timeofuse_peak']) ? $step1['timeofuse_peak'] : $default_peak[1];
								    	$step1['timeofuse_shoulder'] = ($step1['timeofuse_shoulder']) ? $step1['timeofuse_shoulder'] : $default_shoulder[1];
								    	$step1['timeofuse_offpeak'] = ($step1['timeofuse_offpeak']) ? $step1['timeofuse_offpeak'] : $default_offpeak[1];
								    break;
								    case 'High':
								    	$step1['timeofuse_peak'] = ($step1['timeofuse_peak']) ? $step1['timeofuse_peak'] : $default_peak[2];
								    	$step1['timeofuse_shoulder'] = ($step1['timeofuse_shoulder']) ? $step1['timeofuse_shoulder'] : $default_shoulder[2];
								    	$step1['timeofuse_offpeak'] = ($step1['timeofuse_offpeak']) ? $step1['timeofuse_offpeak'] : $default_offpeak[2];
								    break;
								}
	                            break;
	                        case 'Time of Use + Climate Saver':
	                            switch ($step1['elec_usage_level']) {
								    case 'Low':
								    	$step1['timeofuse_cs_peak'] = ($step1['timeofuse_cs_peak']) ? $step1['timeofuse_cs_peak'] : $default_peak[0];
								    	$step1['timeofuse_cs_offpeak'] = ($step1['timeofuse_cs_offpeak']) ? $step1['timeofuse_cs_offpeak'] : $default_offpeak[0];
								    	$step1['timeofuse_cs'] = ($step1['timeofuse_cs']) ? $step1['timeofuse_cs'] : $default_cs[0];
								    	$step1['timeofuse_cs_billing_start'] = ($step1['timeofuse_cs_billing_start']) ? $step1['timeofuse_cs_billing_start'] : $default_cs_billing_start;
								    break;
								    case 'Medium':
								    	$step1['timeofuse_cs_peak'] = ($step1['timeofuse_cs_peak']) ? $step1['timeofuse_cs_peak'] : $default_peak[1];
								    	$step1['timeofuse_cs_offpeak'] = ($step1['timeofuse_cs_offpeak']) ? $step1['timeofuse_cs_offpeak'] : $default_offpeak[1];
								    	$step1['timeofuse_cs'] = ($step1['timeofuse_cs']) ? $step1['timeofuse_cs'] : $default_cs[1];
								    	$step1['timeofuse_cs_billing_start'] = ($step1['timeofuse_cs_billing_start']) ? $step1['timeofuse_cs_billing_start'] : $default_cs_billing_start;
								    break;
								    case 'High':
								    	$step1['timeofuse_cs_peak'] = ($step1['timeofuse_cs_peak']) ? $step1['timeofuse_cs_peak'] : $default_peak[2];
								    	$step1['timeofuse_cs_offpeak'] = ($step1['timeofuse_cs_offpeak']) ? $step1['timeofuse_cs_offpeak'] : $default_offpeak[2];
								    	$step1['timeofuse_cs'] = ($step1['timeofuse_cs']) ? $step1['timeofuse_cs'] : $default_cs[2];
								    	$step1['timeofuse_cs_billing_start'] = ($step1['timeofuse_cs_billing_start']) ? $step1['timeofuse_cs_billing_start'] : $default_cs_billing_start;
								    break;
								}
	                            break;
	                        case 'Time of Use + CL1 + Climate Saver':
	                            switch ($step1['elec_usage_level']) {
								    case 'Low':
								    	$step1['timeofuse_cl1_cs_peak'] = ($step1['timeofuse_cl1_cs_peak']) ? $step1['timeofuse_cl1_cs_peak'] : $default_peak[0];
								    	$step1['timeofuse_cl1_cs_offpeak'] = ($step1['timeofuse_cl1_cs_offpeak']) ? $step1['timeofuse_cl1_cs_offpeak'] : $default_offpeak[0];
								    	$step1['timeofuse_cl1'] = ($step1['timeofuse_cl1']) ? $step1['timeofuse_cl1'] : $default_cl1[0];
								    	$step1['timeofuse_2_cs'] = ($step1['timeofuse_2_cs']) ? $step1['timeofuse_2_cs'] : $default_cs[0];
								    	$step1['timeofuse_cl1_cs_billing_start'] = ($step1['timeofuse_cl1_cs_billing_start']) ? $step1['timeofuse_cl1_cs_billing_start'] : $default_cs_billing_start;
								    break;
								    case 'Medium':
								    	$step1['timeofuse_cl1_cs_peak'] = ($step1['timeofuse_cl1_cs_peak']) ? $step1['timeofuse_cl1_cs_peak'] : $default_peak[1];
								    	$step1['timeofuse_cl1_cs_offpeak'] = ($step1['timeofuse_cl1_cs_offpeak']) ? $step1['timeofuse_cl1_cs_offpeak'] : $default_offpeak[1];
								    	$step1['timeofuse_cl1'] = ($step1['timeofuse_cl1']) ? $step1['timeofuse_cl1'] : $default_cl1[1];
								    	$step1['timeofuse_2_cs'] = ($step1['timeofuse_2_cs']) ? $step1['timeofuse_2_cs'] : $default_cs[1];
								    	$step1['timeofuse_cl1_cs_billing_start'] = ($step1['timeofuse_cl1_cs_billing_start']) ? $step1['timeofuse_cl1_cs_billing_start'] : $default_cs_billing_start;
								    break;
								    case 'High':
								    	$step1['timeofuse_cl1_cs_peak'] = ($step1['timeofuse_cl1_cs_peak']) ? $step1['timeofuse_cl1_cs_peak'] : $default_peak[2];
								    	$step1['timeofuse_cl1_cs_offpeak'] = ($step1['timeofuse_cl1_cs_offpeak']) ? $step1['timeofuse_cl1_cs_offpeak'] : $default_offpeak[2];
								    	$step1['timeofuse_cl1'] = ($step1['timeofuse_cl1']) ? $step1['timeofuse_cl1'] : $default_cl1[2];
								    	$step1['timeofuse_2_cs'] = ($step1['timeofuse_2_cs']) ? $step1['timeofuse_2_cs'] : $default_cs[2];
								    	$step1['timeofuse_cl1_cs_billing_start'] = ($step1['timeofuse_cl1_cs_billing_start']) ? $step1['timeofuse_cl1_cs_billing_start'] : $default_cs_billing_start;
								    break;
								}
	                            break;
	                        case 'Time of Use + CL1':
	                            switch ($step1['elec_usage_level']) {
								    case 'Low':
								    	$step1['timeofuse_cl1_peak'] = ($step1['timeofuse_cl1_peak']) ? $step1['timeofuse_cl1_peak'] : $default_peak[0];
								    	$step1['timeofuse_cl1_offpeak'] = ($step1['timeofuse_cl1_offpeak']) ? $step1['timeofuse_cl1_offpeak'] : $default_offpeak[0];
								    	$step1['timeofuse_2_cl1'] = ($step1['timeofuse_2_cl1']) ? $step1['timeofuse_2_cl1'] : $default_cl1[0];
								    	$step1['timeofuse_cl1_shoulder'] = ($step1['timeofuse_cl1_shoulder']) ? $step1['timeofuse_cl1_shoulder'] : $default_shoulder[0];
								    break;
								    case 'Medium':
								    	$step1['timeofuse_cl1_peak'] = ($step1['timeofuse_cl1_peak']) ? $step1['timeofuse_cl1_peak'] : $default_peak[1];
								    	$step1['timeofuse_cl1_offpeak'] = ($step1['timeofuse_cl1_offpeak']) ? $step1['timeofuse_cl1_offpeak'] : $default_offpeak[1];
								    	$step1['timeofuse_2_cl1'] = ($step1['timeofuse_2_cl1']) ? $step1['timeofuse_2_cl1'] : $default_cl1[1];
								    	$step1['timeofuse_cl1_shoulder'] = ($step1['timeofuse_cl1_shoulder']) ? $step1['timeofuse_cl1_shoulder'] : $default_shoulder[1];
								    break;
								    case 'High':
								    	$step1['timeofuse_cl1_peak'] = ($step1['timeofuse_cl1_peak']) ? $step1['timeofuse_cl1_peak'] : $default_peak[2];
								    	$step1['timeofuse_cl1_offpeak'] = ($step1['timeofuse_cl1_offpeak']) ? $step1['timeofuse_cl1_offpeak'] : $default_offpeak[2];
								    	$step1['timeofuse_2_cl1'] = ($step1['timeofuse_2_cl1']) ? $step1['timeofuse_2_cl1'] : $default_cl1[2];
								    	$step1['timeofuse_cl1_shoulder'] = ($step1['timeofuse_cl1_shoulder']) ? $step1['timeofuse_cl1_shoulder'] : $default_shoulder[2];
								    break;
								}
	                            break;
	                        case 'Time of Use + CL2':
	                            switch ($step1['elec_usage_level']) {
								    case 'Low':
								    	$step1['timeofuse_cl2_peak'] = ($step1['timeofuse_cl2_peak']) ? $step1['timeofuse_cl2_peak'] : $default_peak[0];
								    	$step1['timeofuse_cl2_offpeak'] = ($step1['timeofuse_cl2_offpeak']) ? $step1['timeofuse_cl2_offpeak'] : $default_offpeak[0];
								    	$step1['timeofuse_2_cl2'] = ($step1['timeofuse_2_cl2']) ? $step1['timeofuse_2_cl2'] : $default_cl2[0];
								    	$step1['timeofuse_cl2_shoulder'] = ($step1['timeofuse_cl2_shoulder']) ? $step1['timeofuse_cl2_shoulder'] : $default_shoulder[0];
								    break;
								    case 'Medium':
								    	$step1['timeofuse_cl2_peak'] = ($step1['timeofuse_cl2_peak']) ? $step1['timeofuse_cl2_peak'] : $default_peak[1];
								    	$step1['timeofuse_cl2_offpeak'] = ($step1['timeofuse_cl2_offpeak']) ? $step1['timeofuse_cl2_offpeak'] : $default_offpeak[1];
								    	$step1['timeofuse_2_cl2'] = ($step1['timeofuse_2_cl2']) ? $step1['timeofuse_2_cl2'] : $default_cl2[1];
								    	$step1['timeofuse_cl2_shoulder'] = ($step1['timeofuse_cl2_shoulder']) ? $step1['timeofuse_cl2_shoulder'] : $default_shoulder[1];
								    break;
								    case 'High':
								    	$step1['timeofuse_cl2_peak'] = ($step1['timeofuse_cl2_peak']) ? $step1['timeofuse_cl2_peak'] : $default_peak[2];
								    	$step1['timeofuse_cl2_offpeak'] = ($step1['timeofuse_cl2_offpeak']) ? $step1['timeofuse_cl2_offpeak'] : $default_offpeak[2];
								    	$step1['timeofuse_2_cl2'] = ($step1['timeofuse_2_cl2']) ? $step1['timeofuse_2_cl2'] : $default_cl2[2];
								    	$step1['timeofuse_cl2_shoulder'] = ($step1['timeofuse_cl2_shoulder']) ? $step1['timeofuse_cl2_shoulder'] : $default_shoulder[2];
								    break;
								}
	                            break;
	                        case 'Time of Use (Tariff 12)':
	                            switch ($step1['elec_usage_level']) {
								    case 'Low':
								    	$step1['timeofuse_tariff12_peak'] = ($step1['timeofuse_tariff12_peak']) ? $step1['timeofuse_tariff12_peak'] : $default_peak[0];
								    	$step1['timeofuse_tariff12_shoulder'] = ($step1['timeofuse_tariff12_shoulder']) ? $step1['timeofuse_tariff12_shoulder'] : $default_shoulder[0];
								    	$step1['timeofuse_tariff12_offpeak'] = ($step1['timeofuse_tariff12_offpeak']) ? $step1['timeofuse_tariff12_offpeak'] : $default_offpeak[0];
								    break;
								    case 'Medium':
								    	$step1['timeofuse_tariff12_peak'] = ($step1['timeofuse_tariff12_peak']) ? $step1['timeofuse_tariff12_peak'] : $default_peak[1];
								    	$step1['timeofuse_tariff12_shoulder'] = ($step1['timeofuse_tariff12_shoulder']) ? $step1['timeofuse_tariff12_shoulder'] : $default_shoulder[1];
								    	$step1['timeofuse_tariff12_offpeak'] = ($step1['timeofuse_tariff12_offpeak']) ? $step1['timeofuse_tariff12_offpeak'] : $default_offpeak[1];
								    break;
								    case 'High':
								    	$step1['timeofuse_tariff12_peak'] = ($step1['timeofuse_tariff12_peak']) ? $step1['timeofuse_tariff12_peak'] : $default_peak[2];
								    	$step1['timeofuse_tariff12_shoulder'] = ($step1['timeofuse_tariff12_shoulder']) ? $step1['timeofuse_tariff12_shoulder'] : $default_shoulder[2];
								    	$step1['timeofuse_tariff12_offpeak'] = ($step1['timeofuse_tariff12_offpeak']) ? $step1['timeofuse_tariff12_offpeak'] : $default_offpeak[2];
								    break;
								}
	                            break;
	                        case 'Time of Use (Tariff 13)':
	                            switch ($step1['elec_usage_level']) {
								    case 'Low':
								    	$step1['timeofuse_tariff13_peak'] = ($step1['timeofuse_tariff13_peak']) ? $step1['timeofuse_tariff13_peak'] : $default_peak[0];
								    	$step1['timeofuse_tariff13_shoulder'] = ($step1['timeofuse_tariff13_shoulder']) ? $step1['timeofuse_tariff13_shoulder'] : $default_shoulder[0];
								    	$step1['timeofuse_tariff13_offpeak'] = ($step1['timeofuse_tariff13_offpeak']) ? $step1['timeofuse_tariff13_offpeak'] : $default_offpeak[0];
								    break;
								    case 'Medium':
								    	$step1['timeofuse_tariff13_peak'] = ($step1['timeofuse_tariff12_peak']) ? $step1['timeofuse_tariff12_peak'] : $default_peak[1];
								    	$step1['timeofuse_tariff13_shoulder'] = ($step1['timeofuse_tariff12_shoulder']) ? $step1['timeofuse_tariff12_shoulder'] : $default_shoulder[1];
								    	$step1['timeofuse_tariff13_offpeak'] = ($step1['timeofuse_tariff12_offpeak']) ? $step1['timeofuse_tariff12_offpeak'] : $default_offpeak[1];
								    break;
								    case 'High':
								    	$step1['timeofuse_tariff13_peak'] = ($step1['timeofuse_tariff13_peak']) ? $step1['timeofuse_tariff13_peak'] : $default_peak[2];
								    	$step1['timeofuse_tariff13_shoulder'] = ($step1['timeofuse_tariff13_shoulder']) ? $step1['timeofuse_tariff13_shoulder'] : $default_shoulder[2];
								    	$step1['timeofuse_tariff13_offpeak'] = ($step1['timeofuse_tariff13_offpeak']) ? $step1['timeofuse_tariff13_offpeak'] : $default_offpeak[2];
								    break;
								}
	                            break;
	                        case 'Flexible Pricing':
	                            $peak_sum = $this->calculate($step1['flexible_peak'], $tier_rates);
	                            $off_peak_sum = ($rate['ElectricityRate']['off_peak_rate']) ? $step1['flexible_offpeak'] * $rate['ElectricityRate']['off_peak_rate'] : 0;
	                            $shoulder_sum = ($rate['ElectricityRate']['shoulder_rate']) ? $step1['flexible_shoulder'] * $rate['ElectricityRate']['shoulder_rate'] : 0;
	                            switch ($step1['elec_usage_level']) {
								    case 'Low':
								    	$step1['flexible_peak'] = ($step1['flexible_peak']) ? $step1['flexible_peak'] : $default_peak[0];
								    	$step1['flexible_shoulder'] = ($step1['flexible_shoulder']) ? $step1['flexible_shoulder'] : $default_shoulder[0];
								    	$step1['flexible_offpeak'] = ($step1['flexible_offpeak']) ? $step1['flexible_offpeak'] : $default_offpeak[0];
								    break;
								    case 'Medium':
								    	$step1['flexible_peak'] = ($step1['flexible_peak']) ? $step1['flexible_peak'] : $default_peak[1];
								    	$step1['flexible_shoulder'] = ($step1['flexible_shoulder']) ? $step1['flexible_shoulder'] : $default_shoulder[1];
								    	$step1['flexible_offpeak'] = ($step1['flexible_offpeak']) ? $step1['flexible_offpeak'] : $default_offpeak[1];
								    break;
								    case 'High':
								    	$step1['flexible_peak'] = ($step1['flexible_peak']) ? $step1['flexible_peak'] : $default_peak[2];
								    	$step1['flexible_shoulder'] = ($step1['flexible_shoulder']) ? $step1['flexible_shoulder'] : $default_shoulder[2];
								    	$step1['flexible_offpeak'] = ($step1['flexible_offpeak']) ? $step1['flexible_offpeak'] : $default_offpeak[2];
								    break;
								}
	                            break;
						}
					}
					if ($step1['gas_recent_bill'] == 'No' && $step1['gas_usage_level']) {
						$step1['gas_billing_days'] = $default_consumption['Consumption']['gas_billing_days'];
						$step1['gas_billing_start'] = $default_consumption['Consumption']['gas_billing_start'];
						$default_peak = explode('/', $default_consumption['Consumption']['gas_peak']);
						switch ($step1['gas_usage_level']) {
							case 'Low':
								$step1['gas_peak'] = ($step1['gas_peak']) ? $step1['gas_peak'] : $default_peak[0];
							break;
							case 'Medium':
								$step1['gas_peak'] = ($step1['gas_peak']) ? $step1['gas_peak'] : $default_peak[1];
							break;
							case 'High':
								$step1['gas_peak'] = ($step1['gas_peak']) ? $step1['gas_peak'] : $default_peak[2];
							break;
						}
					}
					$plans = array();
					foreach ($plans_temp as $key => $plan) {
                        if ($step1['elec_usage_level'] == 'Low' && $plan['Plan']['retailer'] == 'Next Business Energy' && $plan['Plan']['product_name'] == "Business Plan - Only Customer's >22000 KWH per annum") {
                            continue;
                        }
						$plan['Plan']['discount_elec'] = 0;
						$plan['Plan']['discount_gas'] = 0;
						$plan['Plan']['total_elec'] = 0;
						$plan['Plan']['total_gas'] = 0;
	                    $plan['Plan']['total_inc_discount_elec'] = 0;
	                    $plan['Plan']['total_inc_discount_gas'] = 0;
	                    $consumption_data = array();
						if ($filters['plan_type'] == 'Elec' || $filters['plan_type'] == 'Dual') {
							$conditions = array(
								'ElectricityRate.state' => $plan['Plan']['state'],
								'ElectricityRate.res_sme' => $plan['Plan']['res_sme'],
								'ElectricityRate.retailer' => $plan['Plan']['retailer'],
								'ElectricityRate.tariff_type' => $step1['elec_meter_type'],
								'ElectricityRate.rate_name' => $plan['Plan']['rate_name'],
								'ElectricityRate.status' => 'Active',
							);
							if ($distributor_elec) {
								switch ($plan['Plan']['retailer']) {
									case 'AGL':
										$distributor_field = 'agl_distributor';
										break;
									case 'Powerdirect':
										$distributor_field = 'powerdirect_distributor';
										break;
									case 'Origin Energy':
										$distributor_field = 'origin_energy_distributor';
										break;
									case 'Lumo Energy':
										$distributor_field = 'lumo_energy_distributor';
										break;
									case 'Red Energy':
										$distributor_field = 'red_energy_distributor';
										break;
									case 'Momentum':
										$distributor_field = 'momentum_distributor';
										break;
									case 'Powershop':
										$distributor_field = 'powershop_distributor';
										break;
									case 'Dodo':
										$distributor_field = 'dodo_distributor';
										break;
                                    case 'Alinta Energy':
										$distributor_field = 'alinta_energy_distributor';
										break;
                                    case 'Energy Australia':
										$distributor_field = 'energy_australia_distributor';
										break;
                                    case 'Sumo Power':
										$distributor_field = 'sumo_power_distributor';
										break;
                                    case 'ERM':
                                        $distributor_field = 'erm_distributor';
                                        break;
                                    case 'Powerdirect and AGL':
                                        $distributor_field = 'pd_agl_distributor';
                                        break;
                                    case 'Next Business Energy':
                                        $distributor_field = 'next_business_energy_distributor';
                                        break;
								}
								$distributors = explode('/', $distributor_elec['ElectricityPostcodeDistributor'][$distributor_field]);
							}
							if ($step1['nmi_distributor']) {
							    if (strpos($step1['nmi_distributor'], '/') !== false) {
							    	switch ($step1['nmi_distributor']) {
							    		case 'Powercor/Powercor 1/Powercor 2':
							    			if ($plan['Plan']['retailer'] != 'Red Energy') {
							    				$distributors = array('Powercor');
							    			}
							    		break;
							    		case 'Essential Energy/Essential Energy - FW/Essential Energy - UR':
							    			if ($plan['Plan']['retailer'] == 'AGL') {
    							    			if (!in_array($distributor_elec['ElectricityPostcodeDistributor'][$distributor_field], array('Essential Energy - FW', 'Essential Energy - UR'))) {
        							    			$distributors = array('Essential Energy - UR');
    							    			}
							    			} else {
    							    			$distributors = array('Essential Energy');
							    			}
							    		break;
							    	}
							    }
							    else {
							    	$distributors = explode('/', $step1['nmi_distributor']);
							    }
							}
							$conditions['ElectricityRate.distributor'] = $distributors;
							if ($step1['nmi_distributor'] && $step1['tariff_parent']) {
							    $tariff_array = explode('|', $step1['tariff_parent']);
							    $tariff = $this->Tariff->find('first', array(
							    	'conditions' => array(
							    		'Tariff.tariff_code' => $tariff_parent[0],
							    		'Tariff.res_sme' => $step1['customer_type'],
							    		'Tariff.distributor' => explode('/', $step1['nmi_distributor']),
							    	),
							    ));
							    if ($tariff['Tariff']['tariff_class']) {
							    	$tariff_classes = array();
							    	$tariff_classes_temp = explode('/', $tariff['Tariff']['tariff_class']);
							    	foreach ($tariff_classes_temp as $tariff_class) {
								    	if ($tariff['Tariff']['pricing_group'] != $step1['elec_meter_type']) {
								    		$tariff_classes[] = str_replace($tariff['Tariff']['pricing_group'], $tariff_class, $step1['elec_meter_type']);
								    	}
								    	else {
											$tariff_classes[] = $tariff_class;
								    	}
							    	}
							    	$conditions['ElectricityRate.tariff_class'] = $tariff_classes;
							    }
							}
							$rates_cnt = $this->ElectricityRate->find('count', array(
								'conditions' => $conditions,
							));
							if ($rates_cnt == 0) {
								unset($conditions['ElectricityRate.tariff_class']);
								$conditions['ElectricityRate.tariff_type'] = $step1['elec_meter_type'];
							}
							$rates = $this->ElectricityRate->find('all', array(
								'conditions' => $conditions,
								'order' => 'ElectricityRate.id ASC'
							));
							if (!empty($rates)) {
								foreach ($rates as $rate) {
									$plan['Plan']['elec_rate'] = $rate['ElectricityRate'];
									$consumption_data['elec_billing_days'] = $step1['elec_billing_days'];
									$consumption_data['elec_consumption'] = 0;
									$summer_days = 0;
									$winter_days = 0;
									if ($this->Session->read('User.state') == 'SA' && $step1['elec_billing_start']) {
										$step1['elec_billing_start'] = str_replace('/', '-', $step1['elec_billing_start']);
										$summer_start_date = strtotime('01-01' . '-' . date('Y'));
										$summer_end_date = strtotime('31-03' . '-' . date('Y'));
										$billing_start_date = strtotime($step1['elec_billing_start']);
										$billing_end_date = strtotime($step1['elec_billing_start']) + $step1['elec_billing_days'] * 3600 * 24;
										if ($billing_start_date >= $summer_start_date && $billing_start_date <= $summer_end_date) {
										    $summer_days = ($summer_end_date - $billing_start_date) / (3600 * 24);
										} 
										elseif ($billing_end_date >= $summer_start_date && $billing_end_date <= $summer_end_date) {
										    $summer_days = ($billing_end_date - $summer_start_date) / (3600 * 24);
										} 
										$winter_days = $step1['elec_billing_days'] - $summer_days;
									}
									$period = 0;
									switch ($rate['ElectricityRate']['rate_tier_period']) {
										case '2':
											$period = 60.83;
											break;
										case 'D':
											$period = 1;
											break;
										case 'M':
											$period = 30.42;
											break;
										case 'Q':
											$period = 91.25;
											break;
										case 'Y':
											$period = 365;
											break;
									}
									if ($period > 0) {
										if ($summer_days > 0 || $winter_days > 0) {

											$rate['ElectricityRate']['summer_peak_tier_1'] = ($rate['ElectricityRate']['peak_tier_1'] / $period) * $summer_days;
											$rate['ElectricityRate']['summer_peak_tier_2'] = ($rate['ElectricityRate']['peak_tier_2'] / $period) * $summer_days;
											$rate['ElectricityRate']['summer_peak_tier_3'] = ($rate['ElectricityRate']['peak_tier_3'] / $period) * $summer_days;
											$rate['ElectricityRate']['summer_peak_tier_4'] = ($rate['ElectricityRate']['peak_tier_4'] / $period) * $summer_days;
											$rate['ElectricityRate']['winter_peak_tier_1'] = ($rate['ElectricityRate']['peak_tier_1'] / $period) * $winter_days;
											$rate['ElectricityRate']['winter_peak_tier_2'] = ($rate['ElectricityRate']['peak_tier_2'] / $period) * $winter_days;
											$rate['ElectricityRate']['winter_peak_tier_3'] = ($rate['ElectricityRate']['peak_tier_3'] / $period) * $winter_days;
											$rate['ElectricityRate']['winter_peak_tier_4'] = ($rate['ElectricityRate']['peak_tier_4'] / $period) * $winter_days;
											$rate['ElectricityRate']['summer_peak_rate_1'] = 0;
											$rate['ElectricityRate']['summer_peak_rate_2'] = 0;
											$rate['ElectricityRate']['summer_peak_rate_3'] = 0;
											$rate['ElectricityRate']['summer_peak_rate_4'] = 0;
											$rate['ElectricityRate']['summer_peak_rate_5'] = 0;
											$rate['ElectricityRate']['winter_peak_rate_1'] = 0;
											$rate['ElectricityRate']['winter_peak_rate_2'] = 0;
											$rate['ElectricityRate']['winter_peak_rate_3'] = 0;
											$rate['ElectricityRate']['winter_peak_rate_4'] = 0;
											$rate['ElectricityRate']['winter_peak_rate_5'] = 0;
											if (strpos($rate['ElectricityRate']['peak_rate_1'], '/') !== false) {
												$peak_rate_1 = explode('/', $rate['ElectricityRate']['peak_rate_1']);
												$rate['ElectricityRate']['summer_peak_rate_1'] = $peak_rate_1[0];
												$rate['ElectricityRate']['winter_peak_rate_1'] = $peak_rate_1[1];
											}
											else {
												$rate['ElectricityRate']['summer_peak_rate_1'] = $rate['ElectricityRate']['winter_peak_rate_1'] = $rate['ElectricityRate']['peak_rate_1'];
											}
											if (strpos($rate['ElectricityRate']['peak_rate_2'], '/') !== false) {
												$peak_rate_2 = explode('/', $rate['ElectricityRate']['peak_rate_2']);
												$rate['ElectricityRate']['summer_peak_rate_2'] = $peak_rate_2[0];
												$rate['ElectricityRate']['winter_peak_rate_2'] = $peak_rate_2[1];
											}
											else {
												$rate['ElectricityRate']['summer_peak_rate_2'] = $rate['ElectricityRate']['winter_peak_rate_2'] = $rate['ElectricityRate']['peak_rate_2'];
											}
											if (strpos($rate['ElectricityRate']['peak_rate_3'], '/') !== false) {
												$peak_rate_3 = explode('/', $rate['ElectricityRate']['peak_rate_3']);
												$rate['ElectricityRate']['summer_peak_rate_3'] = $peak_rate_3[0];
												$rate['ElectricityRate']['winter_peak_rate_3'] = $peak_rate_3[1];
											}
											else {
												$rate['ElectricityRate']['summer_peak_rate_3'] = $rate['ElectricityRate']['winter_peak_rate_3'] = $rate['ElectricityRate']['peak_rate_3'];
											}
											if (strpos($rate['ElectricityRate']['peak_rate_4'], '/') !== false) {
												$peak_rate_4 = explode('/', $rate['ElectricityRate']['peak_rate_4']);
												$rate['ElectricityRate']['summer_peak_rate_4'] = $peak_rate_4[0];
												$rate['ElectricityRate']['winter_peak_rate_4'] = $peak_rate_4[1];
											}
											else {
												$rate['ElectricityRate']['summer_peak_rate_4'] = $rate['ElectricityRate']['winter_peak_rate_4'] = $rate['ElectricityRate']['peak_rate_4'];
											}
											if (strpos($rate['ElectricityRate']['peak_rate_5'], '/') !== false) {
												$peak_rate_5 = explode('/', $rate['ElectricityRate']['peak_rate_5']);
												$rate['ElectricityRate']['summer_peak_rate_5'] = $peak_rate_5[0];
												$rate['ElectricityRate']['winter_peak_rate_5'] = $peak_rate_5[1];
											}
											else {
												$rate['ElectricityRate']['summer_peak_rate_5'] = $rate['ElectricityRate']['winter_peak_rate_5'] = $rate['ElectricityRate']['peak_rate_5'];
											}
										}
										else {
											$rate['ElectricityRate']['peak_tier_1'] = ($rate['ElectricityRate']['peak_tier_1'] / $period) * $step1['elec_billing_days'];
											$rate['ElectricityRate']['peak_tier_2'] = ($rate['ElectricityRate']['peak_tier_2'] / $period) * $step1['elec_billing_days'];
											$rate['ElectricityRate']['peak_tier_3'] = ($rate['ElectricityRate']['peak_tier_3'] / $period) * $step1['elec_billing_days'];
											$rate['ElectricityRate']['peak_tier_4'] = ($rate['ElectricityRate']['peak_tier_4'] / $period) * $step1['elec_billing_days'];
										}
									}
									if ($summer_days > 0 || $winter_days > 0) {
										$summer_tier_rates = array(
	                            	    	array('tier' => $rate['ElectricityRate']['summer_peak_tier_1'], 'rate' => $rate['ElectricityRate']['summer_peak_rate_1']),
											array('tier' => $rate['ElectricityRate']['summer_peak_tier_2'], 'rate' => $rate['ElectricityRate']['summer_peak_rate_2']),
											array('tier' => $rate['ElectricityRate']['summer_peak_tier_3'], 'rate' => $rate['ElectricityRate']['summer_peak_rate_3']),
											array('tier' => $rate['ElectricityRate']['summer_peak_tier_4'], 'rate' => $rate['ElectricityRate']['summer_peak_rate_4']),
											array('tier' => 0, 'rate' => $rate['ElectricityRate']['summer_peak_rate_5'])
										);
										$winter_tier_rates = array(
	                            	    	array('tier' => $rate['ElectricityRate']['winter_peak_tier_1'], 'rate' => $rate['ElectricityRate']['winter_peak_rate_1']),
											array('tier' => $rate['ElectricityRate']['winter_peak_tier_2'], 'rate' => $rate['ElectricityRate']['winter_peak_rate_2']),
											array('tier' => $rate['ElectricityRate']['winter_peak_tier_3'], 'rate' => $rate['ElectricityRate']['winter_peak_rate_3']),
											array('tier' => $rate['ElectricityRate']['winter_peak_tier_4'], 'rate' => $rate['ElectricityRate']['winter_peak_rate_4']),
											array('tier' => 0, 'rate' => $rate['ElectricityRate']['winter_peak_rate_5'])
										);
									}
									else {
										$tier_rates = array(
	                            	    	array('tier' => $rate['ElectricityRate']['peak_tier_1'], 'rate' => $rate['ElectricityRate']['peak_rate_1']),
											array('tier' => $rate['ElectricityRate']['peak_tier_2'], 'rate' => $rate['ElectricityRate']['peak_rate_2']),
											array('tier' => $rate['ElectricityRate']['peak_tier_3'], 'rate' => $rate['ElectricityRate']['peak_rate_3']),
											array('tier' => $rate['ElectricityRate']['peak_tier_4'], 'rate' => $rate['ElectricityRate']['peak_rate_4']),
											array('tier' => 0, 'rate' => $rate['ElectricityRate']['peak_rate_5'])
										);
									}
									
                                    $usage_sum = 0;
	                            	switch ($step1['elec_meter_type']) {
	                            	    case 'Single Rate':
	                            	    	if ($summer_days > 0 || $winter_days > 0) {
	                            	    		$summer_usage = $this->calculate($step1['singlerate_peak'], $summer_tier_rates);
	                            	    		$winter_usage = $this->calculate($step1['elec_winter_peak'], $winter_tier_rates);
	                            	    		$consumption_data['elec_consumption'] = $step1['singlerate_peak'] + $step1['elec_winter_peak'];
	                            	    		$usage_sum = $peak_sum = $summer_usage + $winter_usage;
	                            	    	}
	                            	    	else {
    	                            	    	$consumption_data['elec_consumption'] = $step1['singlerate_peak'];
		                            	    	$usage_sum = $peak_sum = $this->calculate($step1['singlerate_peak'], $tier_rates);
	                            	    	}
	                            	        break;
	                            	    case 'Single Rate + CL1':
	                            	    	if ($summer_days > 0 || $winter_days > 0) {
	                            	    		$summer_usage = $this->calculate($step1['singlerate_cl1_peak'], $summer_tier_rates);
	                            	    		$winter_usage = $this->calculate($step1['elec_winter_peak'], $winter_tier_rates);
	                            	    		$consumption_data['elec_consumption'] = $step1['singlerate_cl1_peak'] + $step1['elec_winter_peak'];
	                            	    		$peak_sum = $summer_usage + $winter_usage;
	                            	    	}
	                            	    	else {
		                            	    	$peak_sum = $this->calculate($step1['singlerate_cl1_peak'], $tier_rates);
		                            	    	$consumption_data['elec_consumption'] = $step1['singlerate_cl1_peak'];
	                            	    	}
	                            	    	$controlled_load_sum = 0;
	                            	    	if ($rate['ElectricityRate']['controlled_load_tier_1'] && $step1['singlerate_cl1'] > $rate['ElectricityRate']['controlled_load_tier_1']) {
	                            	            $sum1 = $rate['ElectricityRate']['controlled_load_1_rate_1'] * $rate['ElectricityRate']['controlled_load_tier_1'];
	                            	            $controlled_load_sum += $rate['ElectricityRate']['controlled_load_1_rate_2'] * ($step1['singlerate_cl1'] - $rate['ElectricityRate']['controlled_load_tier_1']) + $sum1;
	                            	        } 
	                            	        else {
	                            	            $controlled_load_sum += $step1['singlerate_cl1'] * $rate['ElectricityRate']['controlled_load_1_rate_1'];
	                            	        }
	                            	        $consumption_data['elec_consumption'] += $step1['singlerate_cl1'];
	                            	        $usage_sum = $peak_sum + $controlled_load_sum;
	                            	    	break;
	                            	    case 'Single Rate + CL2':
	                            	    	if ($summer_days > 0 || $winter_days > 0) {
	                            	    		$summer_usage = $this->calculate($step1['singlerate_cl2_peak'], $summer_tier_rates);
	                            	    		$winter_usage = $this->calculate($step1['elec_winter_peak'], $winter_tier_rates);
	                            	    		$consumption_data['elec_consumption'] = $step1['singlerate_cl2_peak'] + $step1['elec_winter_peak'];
	                            	    		$peak_sum = $summer_usage + $winter_usage;
	                            	    	}
	                            	    	else {
		                            	    	$peak_sum = $this->calculate($step1['singlerate_cl2_peak'], $tier_rates);
		                            	    	$consumption_data['elec_consumption'] = $step1['singlerate_cl2_peak'];
	                            	    	}
	                            	    	$controlled_load_sum = $step1['singlerate_cl2'] * $rate['ElectricityRate']['controlled_load_2_rate'];
	                            	    	$consumption_data['elec_consumption'] += $step1['singlerate_cl2'];
	                            	    	$usage_sum = $peak_sum + $controlled_load_sum;
	                            	    	break;
	                            	    case 'Single Rate + CL1 + CL2':
	                            	    	if ($summer_days > 0 || $winter_days > 0) {
	                            	    		$summer_usage = $this->calculate($step1['singlerate_cl1_cl2_peak'], $summer_tier_rates);
	                            	    		$winter_usage = $this->calculate($step1['elec_winter_peak'], $winter_tier_rates);
	                            	    		$consumption_data['elec_consumption'] = $step1['singlerate_cl1_cl2_peak'] + $step1['elec_winter_peak'];
	                            	    		$peak_sum = $summer_usage + $winter_usage;
	                            	    	}
	                            	    	else {
		                            	    	$peak_sum = $this->calculate($step1['singlerate_cl1_cl2_peak'], $tier_rates);
		                            	    	$consumption_data['elec_consumption'] = $step1['singlerate_cl1_cl2_peak'];
	                            	    	}
	                            	    	$controlled_load_sum = 0;
	                            	    	if ($rate['ElectricityRate']['controlled_load_tier_1'] && $step1['singlerate_2_cl1'] > $rate['ElectricityRate']['controlled_load_tier_1']) {
	                            	            $sum1 = $rate['ElectricityRate']['controlled_load_1_rate_1'] * $rate['ElectricityRate']['controlled_load_tier_1'];
	                            	            $controlled_load_sum += $rate['ElectricityRate']['controlled_load_1_rate_2'] * ($step1['singlerate_2_cl1'] - $rate['ElectricityRate']['controlled_load_tier_1']) + $sum1;
	                            	        } 
	                            	        else {
	                            	            $controlled_load_sum += $step1['singlerate_2_cl1'] * $rate['ElectricityRate']['controlled_load_1_rate_1'];
	                            	        }
	                            	        $consumption_data['elec_consumption'] += $step1['singlerate_2_cl1'];
	                            	    	$controlled_load_sum += $step1['singlerate_2_cl2'] * $rate['ElectricityRate']['controlled_load_2_rate'];
	                            	    	$consumption_data['elec_consumption'] += $step1['singlerate_2_cl2'];
	                            	    	$usage_sum = $peak_sum + $controlled_load_sum;
	                            	    	break;
	                            	    case 'Single Rate + Climate Saver':
	                            	    	if ($summer_days > 0 || $winter_days > 0) {
	                            	    		$summer_usage = $this->calculate($step1['singlerate_cs_peak'], $summer_tier_rates);
	                            	    		$winter_usage = $this->calculate($step1['elec_winter_peak'], $winter_tier_rates);
	                            	    		$consumption_data['elec_consumption'] = $step1['singlerate_cs_peak'] + $step1['elec_winter_peak'];
	                            	    		$peak_sum = $summer_usage + $winter_usage;
	                            	    	}
	                            	    	else {
		                            	    	$peak_sum = $this->calculate($step1['singlerate_cs_peak'], $tier_rates);
		                            	    	$consumption_data['elec_consumption'] = $step1['singlerate_cs_peak'];
	                            	    	}
	                            	    	$climate_saver_sum = 0;
	                            	    	if ($rate['ElectricityRate']['climate_saver_rate']) {
	                            	    		if (strpos($rate['ElectricityRate']['climate_saver_rate'], '/') !== false) {
	                            	    			$climate_saver_off_start = strtotime('01-04-'.date('Y'));
													$climate_saver_off_end = strtotime('31-10-'.date('Y'));
													$climate_saver_rate_arr = explode('/', $rate['ElectricityRate']['climate_saver_rate']);
													$singlerate_cs_billing_start = strtotime(str_replace('/', '-', $step1['singlerate_cs_billing_start']));
													if ($singlerate_cs_billing_start >= $climate_saver_off_start && $singlerate_cs_billing_start <= $climate_saver_off_end) {
														$climate_saver_sum = $step1['singlerate_cs'] * $climate_saver_rate_arr[1];
													} 
													else {
														$climate_saver_sum = $step1['singlerate_cs'] * $climate_saver_rate_arr[0];
													}
	                            	    		} 
	                            	    		else {
		                            	    		$climate_saver_sum = $step1['singlerate_cs'] * $rate['ElectricityRate']['climate_saver_rate'];
	                            	    		}
	                            	    	}
	                            	    	$consumption_data['elec_consumption'] += $step1['singlerate_cs'];
	                            	        $usage_sum = $peak_sum + $climate_saver_sum;
	                            	    	break;
	                            	    case 'Single Rate + CL1 + Climate Saver':
	                            	    	if ($summer_days > 0 || $winter_days > 0) {
	                            	    		$summer_usage = $this->calculate($step1['singlerate_cl1_cs_peak'], $summer_tier_rates);
	                            	    		$winter_usage = $this->calculate($step1['elec_winter_peak'], $winter_tier_rates);
	                            	    		$consumption_data['elec_consumption'] = $step1['singlerate_cl1_cs_peak'] + $step1['elec_winter_peak'];
	                            	    		$peak_sum = $summer_usage + $winter_usage;
	                            	    	}
	                            	    	else {
		                            	    	$peak_sum = $this->calculate($step1['singlerate_cl1_cs_peak'], $tier_rates);
		                            	    	$consumption_data['elec_consumption'] = $step1['singlerate_cl1_cs_peak'];
	                            	    	}
	                            	    	$controlled_load_sum = 0;
	                            	    	if ($rate['ElectricityRate']['controlled_load_tier_1'] && $step1['singlerate_3_cl1'] > $rate['ElectricityRate']['controlled_load_tier_1']) {
	                            	            $sum1 = $rate['ElectricityRate']['controlled_load_1_rate_1'] * $rate['ElectricityRate']['controlled_load_tier_1'];
	                            	            $controlled_load_sum += $rate['ElectricityRate']['controlled_load_1_rate_2'] * ($step1['singlerate_3_cl1'] - $rate['ElectricityRate']['controlled_load_tier_1']) + $sum1;
	                            	        } 
	                            	        else {
	                            	            $controlled_load_sum += $step1['singlerate_3_cl1'] * $rate['ElectricityRate']['controlled_load_1_rate_1'];
	                            	        }
	                            	    	$controlled_load_sum += $step1['singlerate_3_cl1'] * $rate['ElectricityRate']['controlled_load_2_rate'];
	                            	    	$consumption_data['elec_consumption'] += $step1['singlerate_3_cl1'];
	                            	    	$climate_saver_sum = 0;
	                            	    	if ($rate['ElectricityRate']['climate_saver_rate']) {
	                            	    		if (strpos($rate['ElectricityRate']['climate_saver_rate'], '/') !== false) {
	                            	    			$climate_saver_off_start = strtotime('01-04-'.date('Y'));
													$climate_saver_off_end = strtotime('31-10-'.date('Y'));
													$climate_saver_rate_arr = explode('/', $rate['ElectricityRate']['climate_saver_rate']);
													$singlerate_cl1_cs_billing_start = strtotime(str_replace('/', '-', $step1['singlerate_cl1_cs_billing_start']));
													if ($singlerate_cl1_cs_billing_start >= $climate_saver_off_start && $singlerate_cl1_cs_billing_start <= $climate_saver_off_end) {
														$climate_saver_sum = $step1['singlerate_3_cs'] * $climate_saver_rate_arr[1];
													} 
													else {
														$climate_saver_sum = $step1['singlerate_3_cs'] * $climate_saver_rate_arr[0];
													}
	                            	    		} 
	                            	    		else {
		                            	    		$climate_saver_sum = $step1['singlerate_3_cs'] * $rate['ElectricityRate']['climate_saver_rate'];
	                            	    		}
	                            	    	}
	                            	    	$consumption_data['elec_consumption'] += $step1['singlerate_3_cs'];
	                            	    	$usage_sum = $peak_sum + $controlled_load_sum + $climate_saver_sum;
	                            	    	break;
	                            	    case 'Time of Use':
	                            	    	if ($summer_days > 0 || $winter_days > 0) {
	                            	    		$summer_usage = $this->calculate($step1['timeofuse_peak'], $summer_tier_rates);
	                            	    		$winter_usage = $this->calculate($step1['elec_winter_peak'], $winter_tier_rates);
	                            	    		$consumption_data['elec_consumption'] = $step1['timeofuse_peak'] + $step1['elec_winter_peak'];
	                            	    		$peak_sum = $summer_usage + $winter_usage;
	                            	    	}
	                            	    	else {
	                            	        	$peak_sum = $this->calculate($step1['timeofuse_peak'], $tier_rates);
	                            	        	$consumption_data['elec_consumption'] = $step1['timeofuse_peak'];
	                            	        }
	                            	        $off_peak_sum = ($rate['ElectricityRate']['off_peak_rate'] && $step1['timeofuse_offpeak']) ? $step1['timeofuse_offpeak'] * $rate['ElectricityRate']['off_peak_rate'] : 0;
	                            	        $consumption_data['elec_consumption'] += $step1['timeofuse_offpeak'];
	                            	        $shoulder_sum = ($rate['ElectricityRate']['shoulder_rate'] && $step1['timeofuse_shoulder']) ? $step1['timeofuse_shoulder'] * $rate['ElectricityRate']['shoulder_rate'] : 0;
	                            	        $consumption_data['elec_consumption'] += $step1['timeofuse_shoulder'];
	                            	        $usage_sum = $peak_sum + $off_peak_sum + $shoulder_sum;
	                            	        break;
	                            	    case 'Time of Use (PowerSmart)':
	                            	    	if ($summer_days > 0 || $winter_days > 0) {
	                            	    		$summer_usage = $this->calculate($step1['timeofuse_ps_peak'], $summer_tier_rates);
	                            	    		$winter_usage = $this->calculate($step1['timeofuse_ps_offpeak'], $winter_tier_rates);
	                            	    		$consumption_data['elec_consumption'] = $step1['timeofuse_ps_peak'] + $step1['timeofuse_ps_offpeak'];
	                            	    		$peak_sum = $summer_usage + $winter_usage;
	                            	    	}
	                            	    	else {
	                            	        	$peak_sum = $this->calculate($step1['timeofuse_ps_peak'], $tier_rates);
	                            	        	$consumption_data['elec_consumption'] = $step1['timeofuse_ps_peak'];
	                            	        }
	                            	        $off_peak_sum = ($rate['ElectricityRate']['off_peak_rate'] && $step1['timeofuse_ps_offpeak']) ? $step1['timeofuse_ps_offpeak'] * $rate['ElectricityRate']['off_peak_rate'] : 0;
	                            	        $consumption_data['elec_consumption'] += $step1['timeofuse_ps_offpeak'];
	                            	        $shoulder_sum = ($rate['ElectricityRate']['shoulder_rate'] && $step1['timeofuse_ps_shoulder']) ? $step1['timeofuse_ps_shoulder'] * $rate['ElectricityRate']['shoulder_rate'] : 0;
	                            	        $consumption_data['elec_consumption'] += $step1['timeofuse_ps_shoulder'];
	                            	        $usage_sum = $peak_sum + $off_peak_sum + $shoulder_sum;
	                            	        $this -> log("OKKEY DEBUG PowerSmart::::: ", 'debug');
	                            	        $this -> log("OKKEY DEBUG usage_sum::::: ".$usage_sum, 'debug');
	                            	        $this -> log("OKKEY DEBUG peak_sum::::: ".$peak_sum, 'debug');
	                            	        $this -> log("OKKEY DEBUG off_peak_sum::::: ".$off_peak_sum, 'debug');
	                            	        $this -> log("OKKEY DEBUG shoulder_sum::::: ".$shoulder_sum, 'debug');
	                            	        break;
	                            	    case 'Time of Use (LoadSmart)':
	                            	    	if ($summer_days > 0 || $winter_days > 0) {
	                            	    		$summer_usage = $this->calculate($step1['timeofuse_ls_peak'], $summer_tier_rates);
	                            	    		$winter_usage = $this->calculate($step1['elec_winter_ls_peak'], $winter_tier_rates);
	                            	    		$consumption_data['elec_consumption'] = $step1['timeofuse_ls_peak'] + $step1['timeofuse_ls_offpeak'];
	                            	    		$peak_sum = $summer_usage + $winter_usage;
	                            	    	}
	                            	    	else {
	                            	        	$peak_sum = $this->calculate($step1['timeofuse_ls_peak'], $tier_rates);
	                            	        	$consumption_data['elec_consumption'] = $step1['timeofuse_ls_peak'];
	                            	        }
	                            	        $off_peak_sum = ($rate['ElectricityRate']['off_peak_rate'] && $step1['timeofuse_ls_offpeak']) ? $step1['timeofuse_ls_offpeak'] * $rate['ElectricityRate']['off_peak_rate'] : 0;
	                            	        $consumption_data['elec_consumption'] += $step1['timeofuse_ls_offpeak'];
	                            	        $shoulder_sum = ($rate['ElectricityRate']['shoulder_rate'] && $step1['timeofuse_ls_shoulder']) ? $step1['timeofuse_ls_shoulder'] * $rate['ElectricityRate']['shoulder_rate'] : 0;
	                            	        $consumption_data['elec_consumption'] += $step1['timeofuse_ls_shoulder'];
	                            	        $usage_sum = $peak_sum + $off_peak_sum + $shoulder_sum;
	                            	        $this -> log("OKKEY DEBUG LoadSmart::::: ", 'debug');
	                            	        $this -> log("OKKEY DEBUG usage_sum::::: ".$usage_sum, 'debug');
	                            	        $this -> log("OKKEY DEBUG peak_sum::::: ".$peak_sum, 'debug');
	                            	        $this -> log("OKKEY DEBUG off_peak_sum::::: ".$off_peak_sum, 'debug');
	                            	        $this -> log("OKKEY DEBUG shoulder_sum::::: ".$shoulder_sum, 'debug');
	                            	        break;
	                            	    case 'Time of Use + Climate Saver':
	                            	    	if ($summer_days > 0 || $winter_days > 0) {
	                            	    		$summer_usage = $this->calculate($step1['timeofuse_cs_peak'], $summer_tier_rates);
	                            	    		$winter_usage = $this->calculate($step1['elec_winter_peak'], $winter_tier_rates);
	                            	    		$consumption_data['elec_consumption'] = $step1['timeofuse_cs_peak'] + $step1['elec_winter_peak'];
	                            	    		$peak_sum = $summer_usage + $winter_usage;
	                            	    	}
	                            	    	else {
		                            	    	$peak_sum = $this->calculate($step1['timeofuse_cs_peak'], $tier_rates);
		                            	    	$consumption_data['elec_consumption'] = $step1['timeofuse_cs_peak'];
	                            	    	}
	                            	        $off_peak_sum = ($rate['ElectricityRate']['off_peak_rate'] && $step1['timeofuse_cs_offpeak']) ? $step1['timeofuse_cs_offpeak'] * $rate['ElectricityRate']['off_peak_rate'] : 0;
	                            	        $consumption_data['elec_consumption'] += $step1['timeofuse_cs_offpeak'];
	                            	        $climate_saver_sum = 0;
	                            	    	if ($rate['ElectricityRate']['climate_saver_rate']) {
	                            	    		if (strpos($rate['ElectricityRate']['climate_saver_rate'], '/') !== false) {
	                            	    			$climate_saver_off_start = strtotime('01-04-'.date('Y'));
													$climate_saver_off_end = strtotime('31-10-'.date('Y'));
													$climate_saver_rate_arr = explode('/', $rate['ElectricityRate']['climate_saver_rate']);
													$timeofuse_cs_billing_start = strtotime(str_replace('/', '-', $step1['timeofuse_cs_billing_start']));
													if ($timeofuse_cs_billing_start >= $climate_saver_off_start && $timeofuse_cs_billing_start <= $climate_saver_off_end) {
														$climate_saver_sum = $step1['timeofuse_cs'] * $climate_saver_rate_arr[1];
													} 
													else {
														$climate_saver_sum = $step1['timeofuse_cs'] * $climate_saver_rate_arr[0];
													}
	                            	    		} 
	                            	    		else {
		                            	    		$climate_saver_sum = $step1['timeofuse_cs'] * $rate['ElectricityRate']['climate_saver_rate'];
	                            	    		}
	                            	    	}
	                            	    	$consumption_data['elec_consumption'] += $step1['timeofuse_cs'];
	                            	        $usage_sum = $peak_sum + $off_peak_sum + $climate_saver_sum;
	                            	        break;
	                            	    case 'Time of Use + CL1 + Climate Saver':
	                            	    	if ($summer_days > 0 || $winter_days > 0) {
	                            	    		$summer_usage = $this->calculate($step1['timeofuse_cl1_cs_peak'], $summer_tier_rates);
	                            	    		$winter_usage = $this->calculate($step1['elec_winter_peak'], $winter_tier_rates);
	                            	    		$consumption_data['elec_consumption'] = $step1['timeofuse_cl1_cs_peak'] + $step1['elec_winter_peak'];
	                            	    		$peak_sum = $summer_usage + $winter_usage;
	                            	    	}
	                            	    	else {
		                            	    	$peak_sum = $this->calculate($step1['timeofuse_cl1_cs_peak'], $tier_rates);
		                            	    	$consumption_data['elec_consumption'] = $step1['timeofuse_cl1_cs_peak'];
	                            	    	}
	                            	        $off_peak_sum = ($rate['ElectricityRate']['off_peak_rate'] && $step1['timeofuse_cl1_cs_offpeak']) ? $step1['timeofuse_cl1_cs_offpeak'] * $rate['ElectricityRate']['off_peak_rate'] : 0;
	                            	        $consumption_data['elec_consumption'] += $step1['timeofuse_cl1_cs_offpeak'];
	                            	        $controlled_load_sum = 0;
	                            	        if ($rate['ElectricityRate']['controlled_load_tier_1'] && $step1['timeofuse_cl1'] > $rate['ElectricityRate']['controlled_load_tier_1']) {
	                            	            $sum1 = $rate['ElectricityRate']['controlled_load_1_rate_1'] * $rate['ElectricityRate']['controlled_load_tier_1'];
	                            	            $controlled_load_sum += $rate['ElectricityRate']['controlled_load_1_rate_2'] * ($step1['timeofuse_cl1'] - $rate['ElectricityRate']['controlled_load_tier_1']) + $sum1;
	                            	        } 
	                            	        else {
	                            	            $controlled_load_sum += $step1['timeofuse_cl1'] * $rate['ElectricityRate']['controlled_load_1_rate_1'];
	                            	        }
	                            	        $consumption_data['elec_consumption'] += $step1['timeofuse_cl1'];
	                            	        $climate_saver_sum = 0;
	                            	    	if ($rate['ElectricityRate']['climate_saver_rate']) {
	                            	    		if (strpos($rate['ElectricityRate']['climate_saver_rate'], '/') !== false) {
	                            	    			$climate_saver_off_start = strtotime('01-04-'.date('Y'));
													$climate_saver_off_end = strtotime('31-10-'.date('Y'));
													$climate_saver_rate_arr = explode('/', $rate['ElectricityRate']['climate_saver_rate']);
													$timeofuse_cl1_cs_billing_start = strtotime(str_replace('/', '-', $step1['timeofuse_cl1_cs_billing_start']));
													if ($timeofuse_cl1_cs_billing_start >= $climate_saver_off_start && $timeofuse_cl1_cs_billing_start <= $climate_saver_off_end) {
														$climate_saver_sum = $step1['timeofuse_2_cs'] * $climate_saver_rate_arr[1];
													} 
													else {
														$climate_saver_sum = $step1['timeofuse_2_cs'] * $climate_saver_rate_arr[0];
													}
	                            	    		} 
	                            	    		else {
		                            	    		$climate_saver_sum = $step1['timeofuse_2_cs'] * $rate['ElectricityRate']['climate_saver_rate'];
	                            	    		}
	                            	    	}
	                            	    	$consumption_data['elec_consumption'] += $step1['timeofuse_2_cs'];
	                            	        $usage_sum = $peak_sum + $off_peak_sum + $controlled_load_sum + $climate_saver_sum;
	                            	        break;
	                            	    case 'Time of Use + CL1':
	                            	    	if ($summer_days > 0 || $winter_days > 0) {
	                            	    		$summer_usage = $this->calculate($step1['timeofuse_cl1_peak'], $summer_tier_rates);
	                            	    		$winter_usage = $this->calculate($step1['elec_winter_peak'], $winter_tier_rates);
	                            	    		$consumption_data['elec_consumption'] = $step1['timeofuse_cl1_peak'] + $step1['elec_winter_peak'];
	                            	    		$peak_sum = $summer_usage + $winter_usage;
	                            	    	}
	                            	    	else {
		                            	    	$peak_sum = $this->calculate($step1['timeofuse_cl1_peak'], $tier_rates);
		                            	    	$consumption_data['elec_consumption'] = $step1['timeofuse_cl1_peak'];
	                            	    	}
	                            	        $off_peak_sum = ($rate['ElectricityRate']['off_peak_rate'] && $step1['timeofuse_cl1_offpeak']) ? $step1['timeofuse_cl1_offpeak'] * $rate['ElectricityRate']['off_peak_rate'] : 0;
	                            	        $consumption_data['elec_consumption'] += $step1['timeofuse_cl1_offpeak'];
	                            	        $controlled_load_sum = 0;
	                            	        if ($rate['ElectricityRate']['controlled_load_tier_1'] && $step1['timeofuse_2_cl1'] > $rate['ElectricityRate']['controlled_load_tier_1']) {
	                            	            $sum1 = $rate['ElectricityRate']['controlled_load_1_rate_1'] * $rate['ElectricityRate']['controlled_load_tier_1'];
	                            	            $controlled_load_sum += $rate['ElectricityRate']['controlled_load_1_rate_2'] * ($step1['timeofuse_2_cl1'] - $rate['ElectricityRate']['controlled_load_tier_1']) + $sum1;
	                            	        } 
	                            	        else {
	                            	            $controlled_load_sum += $step1['timeofuse_2_cl1'] * $rate['ElectricityRate']['controlled_load_1_rate_1'];
	                            	        }
	                            	        $consumption_data['elec_consumption'] += $step1['timeofuse_2_cl1'];
	                            	        $shoulder_sum = ($rate['ElectricityRate']['shoulder_rate'] && $step1['timeofuse_cl1_shoulder']) ? $step1['timeofuse_cl1_shoulder'] * $rate['ElectricityRate']['shoulder_rate'] : 0;
	                            	        $consumption_data['elec_consumption'] += $step1['timeofuse_cl1_shoulder'];
	                            	        $usage_sum = $peak_sum + $off_peak_sum + $controlled_load_sum + $shoulder_sum;
	                            	        break;
	                            	    case 'Time of Use + CL2':
	                            	    	if ($summer_days > 0 || $winter_days > 0) {
	                            	    		$summer_usage = $this->calculate($step1['timeofuse_cl2_peak'], $summer_tier_rates);
	                            	    		$winter_usage = $this->calculate($step1['elec_winter_peak'], $winter_tier_rates);
	                            	    		$consumption_data['elec_consumption'] = $step1['timeofuse_cl2_peak'] + $step1['elec_winter_peak'];
	                            	    		$peak_sum = $summer_usage + $winter_usage;
	                            	    	}
	                            	    	else {
		                            	    	$peak_sum = $this->calculate($step1['timeofuse_cl2_peak'], $tier_rates);
		                            	    	$consumption_data['elec_consumption'] = $step1['timeofuse_cl2_peak'];
	                            	    	}
	                            	        $off_peak_sum = ($rate['ElectricityRate']['off_peak_rate'] && $step1['timeofuse_cl2_offpeak']) ? $step1['timeofuse_cl2_offpeak'] * $rate['ElectricityRate']['off_peak_rate'] : 0;
	                            	        $consumption_data['elec_consumption'] += $step1['timeofuse_cl2_offpeak'];
	                            	        $controlled_load_sum = ($rate['ElectricityRate']['controlled_load_2_rate'] && $step1['timeofuse_2_cl2']) ? $step1['timeofuse_2_cl2'] * $rate['ElectricityRate']['controlled_load_2_rate'] : 0;
	                            	        $consumption_data['elec_consumption'] += $step1['timeofuse_2_cl2'];
	                            	        $shoulder_sum = ($rate['ElectricityRate']['shoulder_rate'] && $step1['timeofuse_cl2_shoulder']) ? $step1['timeofuse_cl2_shoulder'] * $rate['ElectricityRate']['shoulder_rate'] : 0;
	                            	        $consumption_data['elec_consumption'] += $step1['timeofuse_cl2_shoulder'];
	                            	        $usage_sum = $peak_sum + $off_peak_sum + $controlled_load_sum + $shoulder_sum;
	                            	        break;
	                            	    case 'Time of Use (Tariff 12)':
	                            	    	if ($summer_days > 0 || $winter_days > 0) {
	                            	    		$summer_usage = $this->calculate($step1['timeofuse_tariff12_peak'], $summer_tier_rates);
	                            	    		$winter_usage = $this->calculate($step1['elec_winter_peak'], $winter_tier_rates);
	                            	    		$consumption_data['elec_consumption'] = $step1['timeofuse_tariff12_peak'] + $step1['elec_winter_peak'];
	                            	    		$peak_sum = $summer_usage + $winter_usage;
	                            	    	}
	                            	    	else {
		                            	    	$peak_sum = $this->calculate($step1['timeofuse_tariff12_peak'], $tier_rates);
		                            	    	$consumption_data['elec_consumption'] = $step1['timeofuse_tariff12_peak'];
	                            	    	}
	                            	        $off_peak_sum = ($rate['ElectricityRate']['off_peak_rate']) ? $step1['timeofuse_tariff12_offpeak'] * $rate['ElectricityRate']['off_peak_rate'] : 0;
	                            	        $consumption_data['elec_consumption'] += $step1['timeofuse_tariff12_offpeak'];
	                            	        $shoulder_sum = ($rate['ElectricityRate']['shoulder_rate'] && $step1['timeofuse_tariff12_shoulder']) ? $step1['timeofuse_tariff12_shoulder'] * $rate['ElectricityRate']['shoulder_rate'] : 0;
	                            	        $consumption_data['elec_consumption'] += $step1['timeofuse_tariff12_shoulder'];
	                            	        $usage_sum = $peak_sum + $off_peak_sum + $shoulder_sum;
	                            	        break;
	                            	    case 'Time of Use (Tariff 13)':
	                            	    	if ($summer_days > 0 || $winter_days > 0) {
	                            	    		$summer_usage = $this->calculate($step1['timeofuse_tariff13_peak'], $summer_tier_rates);
	                            	    		$winter_usage = $this->calculate($step1['elec_winter_peak'], $winter_tier_rates);
	                            	    		$consumption_data['elec_consumption'] = $step1['timeofuse_tariff13_peak'] + $step1['elec_winter_peak'];
	                            	    		$peak_sum = $summer_usage + $winter_usage;
	                            	    	}
	                            	    	else {
		                            	    	$peak_sum = $this->calculate($step1['timeofuse_tariff13_peak'], $tier_rates);
		                            	    	$consumption_data['elec_consumption'] = $step1['timeofuse_tariff13_peak'];
	                            	    	}
	                            	        $off_peak_sum = ($rate['ElectricityRate']['off_peak_rate']) ? $step1['timeofuse_tariff13_offpeak'] * $rate['ElectricityRate']['off_peak_rate'] : 0;
	                            	        $consumption_data['elec_consumption'] += $step1['timeofuse_tariff13_offpeak'];
	                            	        $shoulder_sum = ($rate['ElectricityRate']['shoulder_rate'] && $step1['timeofuse_tariff13_shoulder']) ? $step1['timeofuse_tariff13_shoulder'] * $rate['ElectricityRate']['shoulder_rate'] : 0;
	                            	        $consumption_data['elec_consumption'] += $step1['timeofuse_tariff13_shoulder'];
	                            	        $usage_sum = $peak_sum + $off_peak_sum + $shoulder_sum;
	                            	        break;
	                            	    case 'Flexible Pricing':
	                            	    	if ($summer_days > 0 || $winter_days > 0) {
	                            	    		$summer_usage = $this->calculate($step1['flexible_peak'], $summer_tier_rates);
	                            	    		$winter_usage = $this->calculate($step1['elec_winter_peak'], $winter_tier_rates);
	                            	    		$consumption_data['elec_consumption'] = $step1['flexible_peak'] + $step1['elec_winter_peak'];
	                            	    		$peak_sum = $summer_usage + $winter_usage;
	                            	    	}
	                            	    	else {
		                            	    	$peak_sum = $this->calculate($step1['flexible_peak'], $tier_rates);
		                            	    	$consumption_data['elec_consumption'] = $step1['flexible_peak'];
	                            	    	}
	                            	        $off_peak_sum = ($rate['ElectricityRate']['off_peak_rate']) ? $step1['flexible_offpeak'] * $rate['ElectricityRate']['off_peak_rate'] : 0;
	                            	        $consumption_data['elec_consumption'] += $step1['flexible_offpeak'];
	                            	        $shoulder_sum = ($rate['ElectricityRate']['shoulder_rate']) ? $step1['flexible_shoulder'] * $rate['ElectricityRate']['shoulder_rate'] : 0;
	                            	        $consumption_data['elec_consumption'] += $step1['flexible_shoulder'];
	                            	        $usage_sum = $peak_sum + $off_peak_sum + $shoulder_sum;
	                            	        break;
	                            	}
	                            	$stp_sum_elec = 0;
	                            	$discount_elec = 0;
	                            	if ($rate['ElectricityRate']['stp_period'] == 'Y') {
	                            	    $elec_billing = $step1['elec_billing_days'] / 365;
	                            	} 
	                            	else if ($rate['ElectricityRate']['stp_period'] == 'Q') {
	                            	    $elec_billing = $step1['elec_billing_days'] / 91.25;
	                            	} 
	                            	else if ($rate['ElectricityRate']['stp_period'] == 'M') {
	                            	    $elec_billing = $step1['elec_billing_days'] / 30.42;
	                            	} 
	                            	else {
	                            	    $elec_billing = $step1['elec_billing_days'];
	                            	}
	                            	$stp_sum_elec = $elec_billing * $rate['ElectricityRate']['stp'];
	                            	if ($plan['Plan']['discount_applies']) {
	                            		$temp_total_elec = 0;
		                            	switch ($plan['Plan']['discount_applies']) {
	                            		    case 'Usage':
	                            		    	$temp_total_elec = $usage_sum;
												break;
	                            		    case 'Usage + STP + GST':
	                            		    	$temp_total_elec = ($usage_sum + $stp_sum_elec) * 1.1;
												break;
	                            		}
	                            		if ($plan['Plan']['retailer'] == 'Powershop') {
											$discount_elec += $temp_total_elec * $plan['Plan']['discount_guaranteed_elec'] / 100;
										}
	                            		if (!empty($filters['discount_type'])) {
	                            			if ($plan['Plan']['discount_pay_on_time_elec'] && in_array('Pay On Time', $filters['discount_type'])) {
												if (substr($plan['Plan']['discount_pay_on_time_elec'], 0, 1) == '$') {
													//$discount_elec += $plan['Plan']['discount_pay_on_time_elec'];
												} 
												else {
													$discount_elec += $temp_total_elec * $plan['Plan']['discount_pay_on_time_elec'] / 100;
												}
											}
											if ($plan['Plan']['discount_guaranteed_elec'] && in_array('Guaranteed', $filters['discount_type'])) {
												if (substr($plan['Plan']['discount_guaranteed_elec'], 0, 1) == '$') {
													//$discount_elec += $plan['Plan']['discount_guaranteed_elec'];
												} 
												else {
													if ($plan['Plan']['retailer'] != 'Powershop') {
														$discount_elec += $temp_total_elec * $plan['Plan']['discount_guaranteed_elec'] / 100;
													}
												}
											}
											if ($plan['Plan']['discount_direct_debit_elec'] && in_array('Direct Debit', $filters['discount_type'])) {
											    if (substr($plan['Plan']['discount_direct_debit_elec'], 0, 1) == '$') {
											    	//$discount_elec += $plan['Plan']['discount_direct_debit_elec'];
											    } 
											    else {
											    	$discount_elec += $temp_total_elec * $plan['Plan']['discount_direct_debit_elec'] / 100;
											    }
											}
											if ($plan['Plan']['discount_dual_fuel_elec'] && in_array('Dual Fuel', $filters['discount_type'])) {
											    if (substr($plan['Plan']['discount_dual_fuel_elec'], 0, 1) == '$') {
											    	//$discount_elec += $plan['Plan']['discount_dual_fuel_elec'];
											    } 
											    else {
											    	$discount_elec += $temp_total_elec * $plan['Plan']['discount_dual_fuel_elec'] / 100;
											    }
											}
											if ($plan['Plan']['discount_prepay_elec'] && in_array('Prepay', $filters['discount_type'])) {
											    if (substr($plan['Plan']['discount_prepay_elec'], 0, 1) == '$') {
											    	//$discount_elec += $plan['Plan']['discount_prepay_elec'];
											    } 
											    else {
											    	$discount_elec += $temp_total_elec * $plan['Plan']['discount_prepay_elec'] / 100;
											    }
											}
											if ($plan['Plan']['discount_bonus_sumo'] && in_array('Bonus', $filters['discount_type'])) {
											    if (substr($plan['Plan']['discount_bonus_sumo'], 0, 1) == '$') {
											    	//$discount_elec += $plan['Plan']['discount_bonus_sumo'];
											    } 
											    else {
											    	$discount_elec += $temp_total_elec * $plan['Plan']['discount_bonus_sumo'] / 100;
											    }
											}
										}
										$plan['Plan']['discount_elec'] = $discount_elec;
										switch ($plan['Plan']['discount_applies']) {
										    case 'Usage':
										    	$plan['Plan']['total_elec'] = round(($usage_sum + $stp_sum_elec) * 1.1);
										    	$plan['Plan']['total_inc_discount_elec'] = round(($usage_sum - $discount_elec + $stp_sum_elec) * 1.1);
										    	break;
										    case 'Usage + STP + GST':
										    	$plan['Plan']['total_elec'] = round(($usage_sum + $stp_sum_elec) * 1.1);
										    	$plan['Plan']['total_inc_discount_elec'] = round(($usage_sum + $stp_sum_elec) * 1.1 - $discount_elec);
										    	break;
										}
	                            	}
	                            	else {
		                            	$plan['Plan']['total_elec'] = $plan['Plan']['total_inc_discount_elec'] = round(($usage_sum + $stp_sum_elec) * 1.1);
	                            	}
								}
							}
							$plan['Plan']['solar_rate'] = array();
							if ($solar_rebate_scheme) {
								if (strpos($solar_rebate_scheme, '/') !== false) {
									$solar_rebate_scheme = $step1['solar_rebate_scheme'];
								}
							    $solar_rebate_scheme_rate = $this->SolarRebateScheme->findByStateAndScheme($states_arr[$this->Session->read('User.state')], $solar_rebate_scheme);
							    $plan['Plan']['solar_rate']['government'] = $solar_rebate_scheme_rate['SolarRebateScheme']['government'];
							    switch ($plan['Plan']['retailer']) {
							    	case 'AGL':
							    		$plan['Plan']['solar_rate']['retailer'] = $solar_rebate_scheme_rate['SolarRebateScheme']['agl'];
							    	break;
							    	case 'Lumo Energy':
							    		$plan['Plan']['solar_rate']['retailer'] = $solar_rebate_scheme_rate['SolarRebateScheme']['lumo_energy'];
							    	break;
							    	case 'Momentum':
							    		$plan['Plan']['solar_rate']['retailer'] = $solar_rebate_scheme_rate['SolarRebateScheme']['momentum'];
							    	break;
							    	case 'Origin Energy':
							    		$plan['Plan']['solar_rate']['retailer'] = $solar_rebate_scheme_rate['SolarRebateScheme']['origin_energy'];
							    	break;
							    	case 'Powerdirect':
							    		$plan['Plan']['solar_rate']['retailer'] = $solar_rebate_scheme_rate['SolarRebateScheme']['powerdirect'];
							    	break;
							    	case 'Red Energy':
							    		$plan['Plan']['solar_rate']['retailer'] = $solar_rebate_scheme_rate['SolarRebateScheme']['red_energy'];
							    	break;
							    	case 'Powershop':
							    		$plan['Plan']['solar_rate']['retailer'] = $solar_rebate_scheme_rate['SolarRebateScheme']['powershop'];
							    	break;
							    	case 'Sumo Power':
							    		$plan['Plan']['solar_rate']['retailer'] = $solar_rebate_scheme_rate['SolarRebateScheme']['sumo_power'];
							    	break;
							    	case 'Alinta Energy':
							    		$plan['Plan']['solar_rate']['retailer'] = $solar_rebate_scheme_rate['SolarRebateScheme']['alinta_energy'];
							    	break;
							    	case 'ERM':
							    		$plan['Plan']['solar_rate']['retailer'] = $solar_rebate_scheme_rate['SolarRebateScheme']['erm'];
							    	break;
							    	case 'Powerdirect and AGL':
							    		$plan['Plan']['solar_rate']['retailer'] = $solar_rebate_scheme_rate['SolarRebateScheme']['pd_agl'];
							    	break;
							    	case 'Energy Australia':
							    		$plan['Plan']['solar_rate']['retailer'] = $solar_rebate_scheme_rate['SolarRebateScheme']['energy_australia'];
							    	break;
							    	case 'Next Business Energy':
							    		$plan['Plan']['solar_rate']['retailer'] = $solar_rebate_scheme_rate['SolarRebateScheme']['next_business_energy'];
							    	break;
							    }
							}
						}
						if ($filters['plan_type'] == 'Gas' || $filters['plan_type'] == 'Dual') {
							$conditions = array(
								'GasRate.state' => $plan['Plan']['state'],
								'GasRate.res_sme' => $plan['Plan']['res_sme'],
								'GasRate.retailer' => $plan['Plan']['retailer'],
								'GasRate.rate_name' => $plan['Plan']['rate_name'],
								'GasRate.status' => 'Active',
							);
							if ($distributor_gas) {
								switch ($plan['Plan']['retailer']) {
									case 'AGL':
										$distributor_field = 'agl_distributor';
										break;
									case 'Origin Energy':
										$distributor_field = 'origin_energy_distributor';
										break;
									case 'Lumo Energy':
										$distributor_field = 'lumo_energy_distributor';
										break;
									case 'Red Energy':
										$distributor_field = 'red_energy_distributor';
										break;
									case 'Momentum':
										$distributor_field = 'momentum_distributor';
										break;
									case 'Dodo':
										$distributor_field = 'dodo_distributor';
										break;
                                    case 'Alinta Energy':
										$distributor_field = 'alinta_energy_distributor';
										break;
                                    case 'Energy Australia':
										$distributor_field = 'energy_australia_distributor';
										break;
                                    case 'Powerdirect and AGL':
                                        $distributor_field = 'pd_agl_distributor';
                                        break;
								}
								$conditions['GasRate.distributor'] = explode('/', $distributor_gas['GasPostcodeDistributor'][$distributor_field]);
							}
							$rates = $this->GasRate->find('all', array(
								'conditions' => $conditions,
								'order' => 'GasRate.id ASC'
							));
							if (!empty($rates)) {
								foreach ($rates as $rate) {
									$plan['Plan']['gas_rate'] = $rate['GasRate'];
									$consumption_data['gas_billing_days'] = $step1['gas_billing_days'];
									$consumption_data['gas_consumption'] = $step1['gas_peak'] + $step1['gas_off_peak'];
									if ($rate['GasRate']['peak_start_date'] && $rate['GasRate']['peak_end_date']) {
    									if ($step1['gas_peak'] && $step1['gas_off_peak']) {
        								    $step1['gas_billing_start'] = str_replace('/', '-', $step1['gas_billing_start']);
										    $peak_start_date = strtotime($rate['GasRate']['peak_start_date'] . '-' . date('Y'));
										    $peak_end_date = strtotime($rate['GasRate']['peak_end_date'] . '-' . date('Y'));
										    $billing_start_date = strtotime($step1['gas_billing_start']);
										    $billing_end_date = strtotime($step1['gas_billing_start']) + $step1['gas_billing_days'] * 3600 * 24;
										    if ($billing_start_date >= $peak_start_date && $billing_start_date <= $peak_end_date) {
										        if ($billing_end_date >= $peak_end_date) {
										        	$peak_days = ($peak_end_date - $billing_start_date) / (3600 * 24);
										        } 
										        else {
										        	$peak_days = ($billing_end_date - $billing_start_date) / (3600 * 24);
										        }
										        $off_peak_days = $step1['gas_billing_days'] - $peak_days;
										    } 
										    elseif ($billing_end_date >= $peak_start_date && $billing_end_date <= $peak_end_date) {
										        $peak_days = ($billing_end_date - $peak_start_date) / (3600 * 24);
										        $off_peak_days = $step1['gas_billing_days'] - $peak_days;
										    } 
										    else {
										        $peak_days = 0;
										        $off_peak_days = $step1['gas_billing_days'];
										    }
    									}
    									else if (!$step1['gas_peak']) {
        									$peak_days = 0;
										    $off_peak_days = $step1['gas_billing_days'];
    									}
    									else if (!$step1['gas_off_peak']) {
        									$off_peak_days = 0;
										    $peak_days = $step1['gas_billing_days'];
    									}
    									if ($peak_days == 0) {
                                            $step1['gas_off_peak'] = $step1['gas_peak'] + $step1['gas_off_peak'];
                                            $step1['gas_peak'] = 0;
                                            
    									}
    									if ($off_peak_days == 0) {
                                            $step1['gas_peak'] = $step1['gas_peak'] + $step1['gas_off_peak'];
                                            $step1['gas_off_peak'] = 0;
    									}
    									if ($off_peak_days > 0 && !$rate['GasRate']['off_peak_rate_1']) {
        									$peak_days = $peak_days + $off_peak_days;
        									$off_peak_days = 0;
        									$step1['gas_peak'] = $step1['gas_peak'] + $step1['gas_off_peak'];
        									$step1['gas_off_peak'] = 0;
    									}
										$period = 0;
										switch ($rate['GasRate']['rate_tier_period']) {
										    case '2':
										    	$period = 60.83;
										    	break;
										    case 'D':
										    	$period = 1;
										    	break;
										    case 'M':
										    	$period = 30.42;
										    	break;
										    case 'Q':
										    	$period = 91.25;
										    	break;
										    case 'Y':
										    	$period = 365;
										    	break;
										}
										if ($period > 0) {
										    $rate['GasRate']['peak_tier_1'] = ($rate['GasRate']['peak_tier_1'] / $period) * $peak_days;
										    $rate['GasRate']['peak_tier_2'] = ($rate['GasRate']['peak_tier_2'] / $period) * $peak_days;
										    $rate['GasRate']['peak_tier_3'] = ($rate['GasRate']['peak_tier_3'] / $period) * $peak_days;
										    $rate['GasRate']['peak_tier_4'] = ($rate['GasRate']['peak_tier_4'] / $period) * $peak_days;
										    $rate['GasRate']['peak_tier_5'] = ($rate['GasRate']['peak_tier_5'] / $period) * $peak_days;
										    $rate['GasRate']['off_peak_tier_1'] = ($rate['GasRate']['off_peak_tier_1'] / $period) * $off_peak_days;
										    $rate['GasRate']['off_peak_tier_2'] = ($rate['GasRate']['off_peak_tier_2'] / $period) * $off_peak_days;
										    $rate['GasRate']['off_peak_tier_3'] = ($rate['GasRate']['off_peak_tier_3'] / $period) * $off_peak_days;
										    $rate['GasRate']['off_peak_tier_4'] = ($rate['GasRate']['off_peak_tier_4'] / $period) * $off_peak_days;
										}
										$peak_sum = 0;
										if ($peak_days > 0) {
    										$peak_tier_rates = array(
										        array('tier' => $rate['GasRate']['peak_tier_1'], 'rate' => $rate['GasRate']['peak_rate_1'] / 100),
                                                array('tier' => $rate['GasRate']['peak_tier_2'], 'rate' => $rate['GasRate']['peak_rate_2'] / 100),
                                                array('tier' => $rate['GasRate']['peak_tier_3'], 'rate' => $rate['GasRate']['peak_rate_3'] / 100),
                                                array('tier' => $rate['GasRate']['peak_tier_4'], 'rate' => $rate['GasRate']['peak_rate_4'] / 100),
                                                array('tier' => $rate['GasRate']['peak_tier_5'], 'rate' => $rate['GasRate']['peak_rate_5'] / 100),
                                                array('tier' => 0, 'rate' => $rate['GasRate']['peak_rate_6'] / 100),
                                            );
                                            $peak_sum = $this->calculate($step1['gas_peak'], $peak_tier_rates, true);
										}
										$off_peak_sum = 0;
										if ($off_peak_days > 0) {
										    $off_peak_tier_rates = array(
										    	array('tier' => $rate['GasRate']['off_peak_tier_1'], 'rate' => $rate['GasRate']['off_peak_rate_1'] / 100),
										    	array('tier' => $rate['GasRate']['off_peak_tier_2'], 'rate' => $rate['GasRate']['off_peak_rate_2'] / 100),
										    	array('tier' => $rate['GasRate']['off_peak_tier_3'], 'rate' => $rate['GasRate']['off_peak_rate_3'] / 100),
										    	array('tier' => $rate['GasRate']['off_peak_tier_4'], 'rate' => $rate['GasRate']['off_peak_rate_4'] / 100),
										    	array('tier' => 0, 'rate' => $rate['GasRate']['off_peak_rate_5'] / 100),
											);
										    $off_peak_sum = $this->calculate($step1['gas_off_peak'], $off_peak_tier_rates);
										}
									} 
									else {
										$off_peak_sum = 0;
										$period = 0;
										switch ($rate['GasRate']['rate_tier_period']) {
										    case '2':
										    	$period = 60.83;
										    	break;
										    case 'D':
										    	$period = 1;
										    	break;
										    case 'M':
										    	$period = 30.42;
										    	break;
										    case 'Q':
										    	$period = 91.25;
										    	break;
										    case 'Y':
										    	$period = 365;
										    	break;
										}
										if ($period > 0) {
										    $rate['GasRate']['peak_tier_1'] = ($rate['GasRate']['peak_tier_1'] / $period) * $step1['gas_billing_days'];
										    $rate['GasRate']['peak_tier_2'] = ($rate['GasRate']['peak_tier_2'] / $period) * $step1['gas_billing_days'];
										    $rate['GasRate']['peak_tier_3'] = ($rate['GasRate']['peak_tier_3'] / $period) * $step1['gas_billing_days'];
										    $rate['GasRate']['peak_tier_4'] = ($rate['GasRate']['peak_tier_4'] / $period) * $step1['gas_billing_days'];
										    $rate['GasRate']['peak_tier_5'] = ($rate['GasRate']['peak_tier_5'] / $period) * $step1['gas_billing_days'];
										}
										$peak_tier_rates = array(
										    array('tier' => $rate['GasRate']['peak_tier_1'], 'rate' => $rate['GasRate']['peak_rate_1'] / 100),
										    array('tier' => $rate['GasRate']['peak_tier_2'], 'rate' => $rate['GasRate']['peak_rate_2'] / 100),
										    array('tier' => $rate['GasRate']['peak_tier_3'], 'rate' => $rate['GasRate']['peak_rate_3'] / 100),
										    array('tier' => $rate['GasRate']['peak_tier_4'], 'rate' => $rate['GasRate']['peak_rate_4'] / 100),
										    array('tier' => $rate['GasRate']['peak_tier_5'], 'rate' => $rate['GasRate']['peak_rate_5'] / 100),
										    array('tier' => 0, 'rate' => $rate['GasRate']['peak_rate_6'] / 100),
										);
										$peak_sum = $this->calculate(($step1['gas_peak'] + $step1['gas_off_peak']), $peak_tier_rates, true);
									}
	                            	$usage_sum = $peak_sum + $off_peak_sum;
	                            	$stp_sum_gas = 0;
	                            	$discount_gas = 0;
	                            	if ($rate['GasRate']['stp_period'] == 'Y') {
	                            	    $gas_billing = $step1['gas_billing_days'] / 365;
	                            	} 
	                            	else if ($rate['GasRate']['stp_period'] == 'Q') {
	                            	    $gas_billing = $step1['gas_billing_days'] / 91.25;
	                            	} 
	                            	else if ($rate['GasRate']['stp_period'] == 'M') {
	                            	    $gas_billing = $step1['gas_billing_days'] / 30.42;
	                            	} 
	                            	else {
	                            	    $gas_billing = $step1['gas_billing_days'];
	                            	}
	                            	$stp_sum_gas = $gas_billing * $rate['GasRate']['stp'];
	                            	if ($plan['Plan']['discount_applies']) {
		                            	$temp_total_gas = 0;
	                            		switch ($plan['Plan']['discount_applies']) {
	                            		    case 'Usage':
	                            		    	$temp_total_gas = $usage_sum;
	                            		    break;
	                            		    case 'Usage + STP + GST':
	                            		    	$temp_total_gas = ($usage_sum + $stp_sum_gas) * 1.1;
	                            		    break;
	                            		}
	                            		if (!empty($filters['discount_type'])) {
	                            			if ($plan['Plan']['discount_pay_on_time_gas'] && in_array('Pay On Time', $filters['discount_type'])) {
												if (substr($plan['Plan']['discount_pay_on_time_gas'], 0, 1) == '$') {
													//$discount_gas += $plan['Plan']['discount_pay_on_time_gas'];
												} 
												else {
													$discount_gas += $temp_total_gas * $plan['Plan']['discount_pay_on_time_gas'] / 100;
												}
											}
											if ($plan['Plan']['discount_guaranteed_gas'] && in_array('Guaranteed', $filters['discount_type'])) {
												if (substr($plan['Plan']['discount_guaranteed_gas'], 0, 1) == '$') {
													//$discount_gas += $plan['Plan']['discount_guaranteed_gas'];
												} 
												else {
													$discount_gas += $temp_total_gas * $plan['Plan']['discount_guaranteed_gas'] / 100;
												}
											}
											if ($plan['Plan']['discount_direct_debit_gas'] && in_array('Direct Debit', $filters['discount_type'])) {
											    if (substr($plan['Plan']['discount_direct_debit_gas'], 0, 1) == '$') {
											    	//$discount_gas += $plan['Plan']['discount_direct_debit_gas'];
											    } 
											    else {
											    	$discount_gas += $temp_total_gas * $plan['Plan']['discount_direct_debit_gas'] / 100;
											    }
											}
											if ($plan['Plan']['discount_dual_fuel_gas'] && in_array('Dual Fuel', $filters['discount_type'])) {
											    if (substr($plan['Plan']['discount_direct_debit_gas'], 0, 1) == '$') {
											    	//$discount_gas += $plan['Plan']['discount_direct_debit_gas'];
											    } 
											    else {
											    	$discount_gas += $temp_total_gas * $plan['Plan']['discount_dual_fuel_gas'] / 100;
											    }
											}
										}
										$plan['Plan']['discount_gas'] = $discount_gas;
										switch ($plan['Plan']['discount_applies']) {
	                            		    case 'Usage':
	                            		    	$plan['Plan']['total_gas'] = round(($usage_sum + $stp_sum_gas) * 1.1);
	                            		    	$plan['Plan']['total_inc_discount_gas'] = round(($usage_sum - $discount_gas + $stp_sum_gas) * 1.1);
	                            		    	break;
	                            		    case 'Usage + STP + GST':
	                            		    	$plan['Plan']['total_gas'] = round(($usage_sum + $stp_sum_gas) * 1.1);
	                            		    	$plan['Plan']['total_inc_discount_gas'] = round(($usage_sum + $stp_sum_gas) * 1.1 - $discount_gas);
	                            		    break;
	                            		}
	                            	}
	                            	else {
		                            	$plan['Plan']['total_gas'] = $plan['Plan']['total_inc_discount_gas'] = round(($usage_sum + $stp_sum_gas) * 1.1);
	                            	}
	                            }
	                        }
						}
						$this->Session->write('User.consumption_data', $consumption_data);
						switch ($filters['sort_by']) {
							case 'lowest_price':
								unset($plans[$key]);
								if ($filters['plan_type'] == 'Elec') {
									$plans[$plan['Plan']['total_inc_discount_elec'] * 10000 + $plan['Plan']['id']] = $plan;
								} 
								else if ($filters['plan_type'] == 'Gas') {
									$plans[$plan['Plan']['total_inc_discount_gas'] * 10000 + $plan['Plan']['id']] = $plan;
								} 
								else if ($filters['plan_type'] == 'Dual') {
									$plans[($plan['Plan']['total_inc_discount_elec'] + $plan['Plan']['total_inc_discount_gas']) * 10000 + $plan['Plan']['id']] = $plan;
								}
							break;
							case 'elec_peak':
								$plans[$plan['Plan']['elec_rate']['peak_rate_1'] * 10000 + $plan['Plan']['id']] = $plan;
							break;
							case 'gas_peak':
								unset($plans[$key]);
								$plans[$plan['Plan']['gas_rate']['peak_rate_1'] * 10000 + $plan['Plan']['id']] = $plan;
							break;
							case 'elec_cl':
								unset($plans[$key]);
								$plans[$plan['Plan']['elec_rate']['controlled_load_1_rate_1'] * 10000 + $plan['Plan']['id']] = $plan;
							break;
							case 'elec_offpeak':
								unset($plans[$key]);
								$plans[$plan['Plan']['elec_rate']['off_peak_rate'] * 10000 + $plan['Plan']['id']] = $plan;
							break;
							case 'gas_offpeak':
								unset($plans[$key]);
								$plans[$plan['Plan']['gas_rate']['off_peak_rate_1'] * 10000 + $plan['Plan']['id']] = $plan;
							break;
							case 'elec_stp':
								unset($plans[$key]);
								$plans[$plan['Plan']['elec_rate']['stp'] * 10000 + $plan['Plan']['id']] = $plan;
							break;
							case 'gas_stp':
								unset($plans[$key]);
								$plans[$plan['Plan']['gas_rate']['stp'] * 10000 + $plan['Plan']['id']] = $plan;
							break;
							case 'my_preferences':
							default:
								$plans[$key] = $plan;
							break;
						}
					}
					ksort($plans);
				}
			break;
		}
		$sid = $this->Session->read('User.sid');
		$postcode = $this->Session->read('User.postcode');
		$state = $this->Session->read('User.state');
		$suburb = $this->Session->read('User.suburb');
		$tracking = $this->Session->read('User.tracking');
		$conversion_tracked = ($this->Session->read('User.conversion_tracked')) ? 1 : 0;
		$this->set(compact('step', 'sid', 'postcode', 'state', 'suburb', 'step1', 'tracking', 'step2', 'conversion_tracked', 'states_arr', 'payment_options_arr', 'plans', 'top_picks', 'view_top_picks', 'available_retailers', 'available_discount_type', 'available_contract_length', 'available_payment_options', 'filters'));
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
			$sid = $this->Session->read('User.sid');
			$tracking = $this->Session->read('User.tracking');
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
					if (!$tracking) {
						$tracking = array(
							'medium' => (isset($this->request->data['medium'])) ? $this->request->data['medium'] : '',
							'source' => (isset($this->request->data['source'])) ? $this->request->data['source'] : '',
							'url' => (isset($this->request->data['url'])) ? $this->request->data['url'] : '',
							'term' => (isset($this->request->data['term'])) ? $this->request->data['term'] : '',
							'content' => (isset($this->request->data['content'])) ? $this->request->data['content'] : '',
							'kwid' => (isset($this->request->data['kwid'])) ? $this->request->data['kwid'] : '',
							'keyword' => (isset($this->request->data['keyword'])) ? $this->request->data['keyword'] : '',
							'adid' => (isset($this->request->data['adid'])) ? $this->request->data['adid'] : '',
							'campaign' => (isset($this->request->data['campaign'])) ? $this->request->data['campaign'] : '',
							'publisher' => (isset($this->request->data['publisher'])) ? $this->request->data['publisher'] : '',
							'utm_campaign' => (isset($this->request->data['utm_campaign'])) ? $this->request->data['utm_campaign'] : '',
							'mtype' => (isset($this->request->data['mtype'])) ? $this->request->data['mtype'] : '',
							'group' => (isset($this->request->data['group'])) ? $this->request->data['group'] : '',
							'how_they_found_us' => (isset($this->request->data['how_they_found_us'])) ? $this->request->data['how_they_found_us'] : '',
							'leadage' => (isset($this->request->data['leadage'])) ? $this->request->data['leadage'] : '',
						);
						$this->Session->write('User.tracking', $tracking);
					}
					$nmi = (isset($this->request->data['nmi'])) ? $this->request->data['nmi'] : '';
					$nmi_distributor = '';
					if ($nmi) {
						$nmi_mapping = $this->ElectricityNmiDistributor->findByNmi(strtoupper(substr($nmi, 0, 2)));
						if ($nmi_mapping) {
							$nmi_distributor = $nmi_mapping['ElectricityNmiDistributor']['distributor'];
						}
					}
					$data = array(
						'plan_type' => (isset($this->request->data['plan_type'])) ? $this->request->data['plan_type'] : '',
						'customer_type' => (isset($this->request->data['customer_type'])) ? $this->request->data['customer_type'] : '',
						'looking_for' => (isset($this->request->data['looking_for'])) ? $this->request->data['looking_for'] : 'Compare Plans',
						'move_in_date' => (isset($this->request->data['move_in_date'])) ? $this->request->data['move_in_date'] : '',
						'move_in_date_not_sure' => (isset($this->request->data['move_in_date_not_sure'])) ? $this->request->data['move_in_date_not_sure'] : '',
						'elec_recent_bill' => (isset($this->request->data['elec_recent_bill'])) ? $this->request->data['elec_recent_bill'] : '',
						'gas_recent_bill' => (isset($this->request->data['gas_recent_bill'])) ? $this->request->data['gas_recent_bill'] : '',
						'elec_billing_days' => (isset($this->request->data['elec_billing_days'])) ? $this->request->data['elec_billing_days'] : '',
						'elec_billing_start' => (isset($this->request->data['elec_billing_start'])) ? $this->request->data['elec_billing_start'] : '',
						'elec_winter_peak' => (isset($this->request->data['elec_winter_peak'])) ? $this->request->data['elec_winter_peak'] : '',
						'elec_spend' => (isset($this->request->data['elec_spend'])) ? $this->request->data['elec_spend'] : '',
						'elec_meter_type' => (isset($this->request->data['elec_meter_type'])) ? $this->request->data['elec_meter_type'] : '',
						'elec_meter_type2' => (isset($this->request->data['elec_meter_type2'])) ? $this->request->data['elec_meter_type2'] : '',
						'elec_supplier' => (isset($this->request->data['elec_supplier'])) ? $this->request->data['elec_supplier'] : '',
						'elec_supplier2' => (isset($this->request->data['elec_supplier2'])) ? $this->request->data['elec_supplier2'] : '',
						'nmi' => $nmi,
						'nmi_distributor' => $nmi_distributor,
						'tariff_parent' => (isset($this->request->data['tariff_parent'])) ? $this->request->data['tariff_parent'] : '',
						'tariff1' => (isset($this->request->data['tariff1'])) ? $this->request->data['tariff1'] : '',
						'tariff2' => (isset($this->request->data['tariff2'])) ? $this->request->data['tariff2'] : '',
						'tariff3' => (isset($this->request->data['tariff3'])) ? $this->request->data['tariff3'] : '',
						'tariff4' => (isset($this->request->data['tariff4'])) ? $this->request->data['tariff4'] : '',
						'solar_generated' => (isset($this->request->data['solar_generated'])) ? $this->request->data['solar_generated'] : '',
						'gas_billing_days' => (isset($this->request->data['gas_billing_days'])) ? $this->request->data['gas_billing_days'] : '',
						'gas_billing_start' => (isset($this->request->data['gas_billing_start'])) ? $this->request->data['gas_billing_start'] : '',
						'gas_spend' => (isset($this->request->data['gas_spend'])) ? $this->request->data['gas_spend'] : '',
						'gas_off_peak' => (isset($this->request->data['gas_off_peak'])) ? $this->request->data['gas_off_peak'] : '',
						'gas_peak' => (isset($this->request->data['gas_peak'])) ? $this->request->data['gas_peak'] : '',
						'gas_supplier' => (isset($this->request->data['gas_supplier'])) ? $this->request->data['gas_supplier'] : '',
						'gas_supplier2' => (isset($this->request->data['gas_supplier2'])) ? $this->request->data['gas_supplier2'] : '',
						'singlerate_peak' => (isset($this->request->data['singlerate_peak'])) ? $this->request->data['singlerate_peak'] : '',
						'singlerate_cl1_peak' => (isset($this->request->data['singlerate_cl1_peak'])) ? $this->request->data['singlerate_cl1_peak'] : '',
						'singlerate_cl2_peak' => (isset($this->request->data['singlerate_cl2_peak'])) ? $this->request->data['singlerate_cl2_peak'] : '',
						'singlerate_cl1_cl2_peak' => (isset($this->request->data['singlerate_cl1_cl2_peak'])) ? $this->request->data['singlerate_cl1_cl2_peak'] : '',
						'singlerate_cl1' => (isset($this->request->data['singlerate_cl1'])) ? $this->request->data['singlerate_cl1'] : '',
						'singlerate_cl2' => (isset($this->request->data['singlerate_cl2'])) ? $this->request->data['singlerate_cl2'] : '',
						'singlerate_2_cl1' => (isset($this->request->data['singlerate_2_cl1'])) ? $this->request->data['singlerate_2_cl1'] : '',
						'singlerate_2_cl2' => (isset($this->request->data['singlerate_2_cl2'])) ? $this->request->data['singlerate_2_cl2'] : '',
						'singlerate_cs_peak' => (isset($this->request->data['singlerate_cs_peak'])) ? $this->request->data['singlerate_cs_peak'] : '',
						'singlerate_cs' => (isset($this->request->data['singlerate_cs'])) ? $this->request->data['singlerate_cs'] : '',
						'singlerate_cs_billing_start' => (isset($this->request->data['singlerate_cs_billing_start'])) ? $this->request->data['singlerate_cs_billing_start'] : '',
						'singlerate_cl1_cs_peak' => (isset($this->request->data['singlerate_cl1_cs_peak'])) ? $this->request->data['singlerate_cl1_cs_peak'] : '',
						'singlerate_3_cs' => (isset($this->request->data['singlerate_3_cs'])) ? $this->request->data['singlerate_3_cs'] : '',
						'singlerate_3_cl1' => (isset($this->request->data['singlerate_3_cl1'])) ? $this->request->data['singlerate_3_cl1'] : '',
						'singlerate_cl1_cs_billing_start' => (isset($this->request->data['singlerate_cl1_cs_billing_start'])) ? $this->request->data['singlerate_cl1_cs_billing_start'] : '',
						'timeofuse_peak' => (isset($this->request->data['timeofuse_peak'])) ? $this->request->data['timeofuse_peak'] : '',
						'timeofuse_offpeak' => (isset($this->request->data['timeofuse_offpeak'])) ? $this->request->data['timeofuse_offpeak'] : '',
						'timeofuse_shoulder' => (isset($this->request->data['timeofuse_shoulder'])) ? $this->request->data['timeofuse_shoulder'] : '',
						'timeofuse_ps_peak' => (isset($this->request->data['timeofuse_ps_peak'])) ? $this->request->data['timeofuse_ps_peak'] : '',
						'timeofuse_ps_offpeak' => (isset($this->request->data['timeofuse_ps_offpeak'])) ? $this->request->data['timeofuse_ps_offpeak'] : '',
						'timeofuse_ps_shoulder' => (isset($this->request->data['timeofuse_ps_shoulder'])) ? $this->request->data['timeofuse_ps_shoulder'] : '',
						'timeofuse_ls_peak' => (isset($this->request->data['timeofuse_ls_peak'])) ? $this->request->data['timeofuse_ls_peak'] : '',
						'timeofuse_ls_offpeak' => (isset($this->request->data['timeofuse_ls_offpeak'])) ? $this->request->data['timeofuse_ls_offpeak'] : '',
						'timeofuse_ls_shoulder' => (isset($this->request->data['timeofuse_ls_shoulder'])) ? $this->request->data['timeofuse_ls_shoulder'] : '',
						'timeofuse_cs_peak' => (isset($this->request->data['timeofuse_cs_peak'])) ? $this->request->data['timeofuse_cs_peak'] : '',
						'timeofuse_cs_offpeak' => (isset($this->request->data['timeofuse_cs_offpeak'])) ? $this->request->data['timeofuse_cs_offpeak'] : '',
						'timeofuse_cs' => (isset($this->request->data['timeofuse_cs'])) ? $this->request->data['timeofuse_cs'] : '',
						'timeofuse_cs_billing_start' => (isset($this->request->data['timeofuse_cs_billing_start'])) ? $this->request->data['timeofuse_cs_billing_start'] : '',
						'timeofuse_cl1_cs_peak' => (isset($this->request->data['timeofuse_cl1_cs_peak'])) ? $this->request->data['timeofuse_cl1_cs_peak'] : '',
						'timeofuse_cl1_cs_offpeak' => (isset($this->request->data['timeofuse_cl1_cs_offpeak'])) ? $this->request->data['timeofuse_cl1_cs_offpeak'] : '',
						'timeofuse_cl1' => (isset($this->request->data['timeofuse_cl1'])) ? $this->request->data['timeofuse_cl1'] : '',
						'timeofuse_2_cs' => (isset($this->request->data['timeofuse_2_cs'])) ? $this->request->data['timeofuse_2_cs'] : '',
						'timeofuse_cl1_cs_billing_start' => (isset($this->request->data['timeofuse_cl1_cs_billing_start'])) ? $this->request->data['timeofuse_cl1_cs_billing_start'] : '',
						'timeofuse_cl1_peak' => (isset($this->request->data['timeofuse_cl1_peak'])) ? $this->request->data['timeofuse_cl1_peak'] : '',
						'timeofuse_cl1_offpeak' => (isset($this->request->data['timeofuse_cl1_offpeak'])) ? $this->request->data['timeofuse_cl1_offpeak'] : '',
						'timeofuse_2_cl1' => (isset($this->request->data['timeofuse_2_cl1'])) ? $this->request->data['timeofuse_2_cl1'] : '',
						'timeofuse_cl1_shoulder' => (isset($this->request->data['timeofuse_cl1_shoulder'])) ? $this->request->data['timeofuse_cl1_shoulder'] : '',
						'timeofuse_cl2_peak' => (isset($this->request->data['timeofuse_cl2_peak'])) ? $this->request->data['timeofuse_cl2_peak'] : '',
						'timeofuse_cl2_offpeak' => (isset($this->request->data['timeofuse_cl2_offpeak'])) ? $this->request->data['timeofuse_cl2_offpeak'] : '',
						'timeofuse_2_cl2' => (isset($this->request->data['timeofuse_2_cl2'])) ? $this->request->data['timeofuse_2_cl2'] : '',
						'timeofuse_cl2_shoulder' => (isset($this->request->data['timeofuse_cl2_shoulder'])) ? $this->request->data['timeofuse_cl2_shoulder'] : '',
						'timeofuse_tariff12_peak' => (isset($this->request->data['timeofuse_tariff12_peak'])) ? $this->request->data['timeofuse_tariff12_peak'] : '',
						'timeofuse_tariff12_offpeak' => (isset($this->request->data['timeofuse_tariff12_offpeak'])) ? $this->request->data['timeofuse_tariff12_offpeak'] : '',
						'timeofuse_tariff12_shoulder' => (isset($this->request->data['timeofuse_tariff12_shoulder'])) ? $this->request->data['timeofuse_tariff12_shoulder'] : '',
						'timeofuse_tariff13_peak' => (isset($this->request->data['timeofuse_tariff13_peak'])) ? $this->request->data['timeofuse_tariff13_peak'] : '',
						'timeofuse_tariff13_offpeak' => (isset($this->request->data['timeofuse_tariff13_offpeak'])) ? $this->request->data['timeofuse_tariff13_offpeak'] : '',
						'timeofuse_tariff13_shoulder' => (isset($this->request->data['timeofuse_tariff13_shoulder'])) ? $this->request->data['timeofuse_tariff13_shoulder'] : '',
						'flexible_peak' => (isset($this->request->data['flexible_peak'])) ? $this->request->data['flexible_peak'] : '',
						'flexible_offpeak' => (isset($this->request->data['flexible_offpeak'])) ? $this->request->data['flexible_offpeak'] : '',
						'flexible_shoulder' => (isset($this->request->data['flexible_shoulder'])) ? $this->request->data['flexible_shoulder'] : '',
						'elec_usage_level' => (isset($this->request->data['elec_usage_level'])) ? $this->request->data['elec_usage_level'] : '',
						'gas_usage_level' => (isset($this->request->data['gas_usage_level'])) ? $this->request->data['gas_usage_level'] : '',
						'business_name' => (isset($this->request->data['business_name'])) ? $this->request->data['business_name'] : '',
						'first_name' => (isset($this->request->data['first_name'])) ? $this->request->data['first_name'] : '',
						'surname' => (isset($this->request->data['surname'])) ? $this->request->data['surname'] : '',
						'mobile' => (isset($this->request->data['mobile'])) ? $this->request->data['mobile'] : '',
						'phone' => (isset($this->request->data['phone'])) ? $this->request->data['phone'] : '',
						'other_number' => (isset($this->request->data['other_number'])) ? $this->request->data['other_number'] : '',
						'email' => (isset($this->request->data['email'])) ? $this->request->data['email'] : '',
						'term1' => (isset($this->request->data['term1'])) ? 1 : 0,
						'solar_rebate_scheme' => (isset($this->request->data['solar_rebate_scheme'])) ? $this->request->data['solar_rebate_scheme'] : '',
					);
					$this->Session->write('User.step1', $data);
				break;
				case 2:
					$data = array(
						'pay_on_time_discount' => $this->request->data['pay_on_time_discount'],
						'direct_debit_discount' => $this->request->data['direct_debit_discount'],
						'dual_fuel_discount' => $this->request->data['dual_fuel_discount'],
						'bonus_discount' => $this->request->data['bonus_discount'],
						'prepay_discount' => $this->request->data['prepay_discount'],
						'rate_freeze' => $this->request->data['rate_freeze'],
						'no_contract_plan' => $this->request->data['no_contract_plan'],
						'bill_smoothing' => $this->request->data['bill_smoothing'],
						'online_account_management' => $this->request->data['online_account_management'],
						'energy_monitoring_tools' => $this->request->data['energy_monitoring_tools'],
						'membership_reward_programs' => $this->request->data['membership_reward_programs'],
						'renewable_energy' => $this->request->data['renewable_energy'],
						'sort_by' => $this->request->data['sort_by'],
					);
					$this->Session->write('User.step2', $data);
				break;
				case 3:
					$step1 = $this->Session->read('User.step1');
					$nmi = (isset($this->request->data['nmi'])) ? $this->request->data['nmi'] : $step1['nmi'];
					$nmi_distributor = '';
					if ($nmi) {
						$nmi_mapping = $this->ElectricityNmiDistributor->findByNmi(strtoupper(substr($nmi, 0, 2)));
						if ($nmi_mapping) {
							$nmi_distributor = $nmi_mapping['ElectricityNmiDistributor']['distributor'];
						}
					}
					$data = array(
						'plan_type' => $step1['plan_type'],
						'customer_type' => $step1['customer_type'],
						'looking_for' => $step1['looking_for'],
						'elec_recent_bill' => (isset($this->request->data['elec_recent_bill'])) ? $this->request->data['elec_recent_bill'] : $step1['elec_recent_bill'],
						'gas_recent_bill' => (isset($this->request->data['gas_recent_bill'])) ? $this->request->data['gas_recent_bill'] : $step1['gas_recent_bill'],
						'elec_billing_days' => (isset($this->request->data['elec_billing_days'])) ? $this->request->data['elec_billing_days'] : $step1['elec_billing_days'],
						'elec_billing_start' => (isset($this->request->data['elec_billing_start'])) ? $this->request->data['elec_billing_start'] : $step1['elec_billing_start'],
						'elec_winter_peak' => (isset($this->request->data['elec_winter_peak'])) ? $this->request->data['elec_winter_peak'] : $step1['elec_winter_peak'],
						'elec_spend' => (isset($this->request->data['elec_spend'])) ? $this->request->data['elec_spend'] : $step1['elec_spend'],
						'elec_meter_type' => (isset($this->request->data['elec_meter_type'])) ? $this->request->data['elec_meter_type'] : $step1['elec_meter_type'],
						'elec_meter_type2' => (isset($this->request->data['elec_meter_type2'])) ? $this->request->data['elec_meter_type2'] : $step1['elec_meter_type2'],
						'elec_supplier' => (isset($this->request->data['elec_supplier'])) ? $this->request->data['elec_supplier'] : $step1['elec_supplier'],
						'elec_supplier2' => (isset($this->request->data['elec_supplier2'])) ? $this->request->data['elec_supplier2'] : $step1['elec_supplier2'],
						'nmi' => $nmi,
						'nmi_distributor' => $nmi_distributor,
						'tariff_parent' => (isset($this->request->data['tariff_parent'])) ? $this->request->data['tariff_parent'] : $step1['tariff_parent'],
						'tariff1' => (isset($this->request->data['tariff1'])) ? $this->request->data['tariff1'] : $step1['tariff1'],
						'tariff2' => (isset($this->request->data['tariff2'])) ? $this->request->data['tariff2'] : $step1['tariff2'],
						'tariff3' => (isset($this->request->data['tariff3'])) ? $this->request->data['tariff3'] : $step1['tariff3'],
						'tariff4' => (isset($this->request->data['tariff4'])) ? $this->request->data['tariff4'] : $step1['tariff4'],
						'solar_generated' => (isset($this->request->data['solar_generated'])) ? $this->request->data['solar_generated'] : $step1['solar_generated'],
						'gas_billing_days' => (isset($this->request->data['gas_billing_days'])) ? $this->request->data['gas_billing_days'] : $step1['gas_billing_days'],
						'gas_billing_start' => (isset($this->request->data['gas_billing_start'])) ? $this->request->data['gas_billing_start'] : $step1['gas_billing_start'],
						'gas_spend' => (isset($this->request->data['gas_spend'])) ? $this->request->data['gas_spend'] : $step1['gas_spend'],
						'gas_off_peak' => (isset($this->request->data['gas_off_peak'])) ? $this->request->data['gas_off_peak'] : $step1['gas_off_peak'],
						'gas_peak' => (isset($this->request->data['gas_peak'])) ? $this->request->data['gas_peak'] : $step1['gas_peak'],
						'gas_supplier' => (isset($this->request->data['gas_supplier'])) ? $this->request->data['gas_supplier'] : $step1['gas_supplier'],
						'gas_supplier2' => (isset($this->request->data['gas_supplier2'])) ? $this->request->data['gas_supplier2'] : $step1['gas_supplier2'],
						'singlerate_peak' => (isset($this->request->data['singlerate_peak'])) ? $this->request->data['singlerate_peak'] : $step1['singlerate_peak'],
						'singlerate_cl1_peak' => (isset($this->request->data['singlerate_cl1_peak'])) ? $this->request->data['singlerate_cl1_peak'] : $step1['singlerate_cl1_peak'],
						'singlerate_cl2_peak' => (isset($this->request->data['singlerate_cl2_peak'])) ? $this->request->data['singlerate_cl2_peak'] : $step1['singlerate_cl2_peak'],
						'singlerate_cl1_cl2_peak' => (isset($this->request->data['singlerate_cl1_cl2_peak'])) ? $this->request->data['singlerate_cl1_cl2_peak'] : $step1['singlerate_cl1_cl2_peak'],
						'singlerate_cl1' => (isset($this->request->data['singlerate_cl1'])) ? $this->request->data['singlerate_cl1'] : $step1['singlerate_cl1'],
						'singlerate_cl2' => (isset($this->request->data['singlerate_cl2'])) ? $this->request->data['singlerate_cl2'] : $step1['singlerate_cl2'],
						'singlerate_2_cl1' => (isset($this->request->data['singlerate_2_cl1'])) ? $this->request->data['singlerate_2_cl1'] : $step1['singlerate_2_cl1'],
						'singlerate_2_cl2' => (isset($this->request->data['singlerate_2_cl2'])) ? $this->request->data['singlerate_2_cl2'] : $step1['singlerate_2_cl2'],
						'singlerate_cs_peak' => (isset($this->request->data['singlerate_cs_peak'])) ? $this->request->data['singlerate_cs_peak'] : $step1['singlerate_cs_peak'],
						'singlerate_cs' => (isset($this->request->data['singlerate_cs'])) ? $this->request->data['singlerate_cs'] : $step1['singlerate_cs'],
						'singlerate_cs_billing_start' => (isset($this->request->data['singlerate_cs_billing_start'])) ? $this->request->data['singlerate_cs_billing_start'] : $step1['singlerate_cs_billing_start'],
						'singlerate_cl1_cs_peak' => (isset($this->request->data['singlerate_cl1_cs_peak'])) ? $this->request->data['singlerate_cl1_cs_peak'] : $step1['singlerate_cl1_cs_peak'],
						'singlerate_3_cs' => (isset($this->request->data['singlerate_3_cs'])) ? $this->request->data['singlerate_3_cs'] : $step1['singlerate_3_cs'],
						'singlerate_3_cl1' => (isset($this->request->data['singlerate_3_cl1'])) ? $this->request->data['singlerate_3_cl1'] : $step1['singlerate_3_cl1'],
						'singlerate_cl1_cs_billing_start' => (isset($this->request->data['singlerate_cl1_cs_billing_start'])) ? $this->request->data['singlerate_cl1_cs_billing_start'] : $step1['singlerate_cl1_cs_billing_start'],
						'timeofuse_peak' => (isset($this->request->data['timeofuse_peak'])) ? $this->request->data['timeofuse_peak'] : $step1['timeofuse_peak'],
						'timeofuse_offpeak' => (isset($this->request->data['timeofuse_offpeak'])) ? $this->request->data['timeofuse_offpeak'] : $step1['timeofuse_offpeak'],
						'timeofuse_shoulder' => (isset($this->request->data['timeofuse_shoulder'])) ? $this->request->data['timeofuse_shoulder'] : $step1['timeofuse_shoulder'],
						'timeofuse_cs_peak' => (isset($this->request->data['timeofuse_cs_peak'])) ? $this->request->data['timeofuse_cs_peak'] : $step1['timeofuse_cs_peak'],
						'timeofuse_cs_offpeak' => (isset($this->request->data['timeofuse_cs_offpeak'])) ? $this->request->data['timeofuse_cs_offpeak'] : $step1['timeofuse_cs_offpeak'],
						'timeofuse_cs' => (isset($this->request->data['timeofuse_cs'])) ? $this->request->data['timeofuse_cs'] : $step1['timeofuse_cs'],
						'timeofuse_cs_billing_start' => (isset($this->request->data['timeofuse_cs_billing_start'])) ? $this->request->data['timeofuse_cs_billing_start'] : $step1['timeofuse_cs_billing_start'],
						'timeofuse_cl1_cs_peak' => (isset($this->request->data['timeofuse_cl1_cs_peak'])) ? $this->request->data['timeofuse_cl1_cs_peak'] : $step1['timeofuse_cl1_cs_peak'],
						'timeofuse_cl1_cs_offpeak' => (isset($this->request->data['timeofuse_cl1_cs_offpeak'])) ? $this->request->data['timeofuse_cl1_cs_offpeak'] : $step1['timeofuse_cl1_cs_offpeak'],
						'timeofuse_cl1' => (isset($this->request->data['timeofuse_cl1'])) ? $this->request->data['timeofuse_cl1'] : $step1['timeofuse_cl1'],
						'timeofuse_2_cs' => (isset($this->request->data['timeofuse_2_cs'])) ? $this->request->data['timeofuse_2_cs'] : $step1['timeofuse_2_cs'],
						'timeofuse_cl1_cs_billing_start' => (isset($this->request->data['timeofuse_cl1_cs_billing_start'])) ? $this->request->data['timeofuse_cl1_cs_billing_start'] : $step1['timeofuse_cl1_cs_billing_start'],
						'timeofuse_cl1_peak' => (isset($this->request->data['timeofuse_cl1_peak'])) ? $this->request->data['timeofuse_cl1_peak'] : $step1['timeofuse_cl1_peak'],
						'timeofuse_cl1_offpeak' => (isset($this->request->data['timeofuse_cl1_offpeak'])) ? $this->request->data['timeofuse_cl1_offpeak'] : $step1['timeofuse_cl1_offpeak'],
						'timeofuse_2_cl1' => (isset($this->request->data['timeofuse_2_cl1'])) ? $this->request->data['timeofuse_2_cl1'] : $step1['timeofuse_2_cl1'],
						'timeofuse_cl1_shoulder' => (isset($this->request->data['timeofuse_cl1_shoulder'])) ? $this->request->data['timeofuse_cl1_shoulder'] : $step1['timeofuse_cl1_shoulder'],
						'timeofuse_cl2_peak' => (isset($this->request->data['timeofuse_cl2_peak'])) ? $this->request->data['timeofuse_cl2_peak'] : $step1['timeofuse_cl2_peak'],
						'timeofuse_cl2_offpeak' => (isset($this->request->data['timeofuse_cl2_offpeak'])) ? $this->request->data['timeofuse_cl2_offpeak'] : $step1['timeofuse_cl2_offpeak'],
						'timeofuse_2_cl2' => (isset($this->request->data['timeofuse_2_cl2'])) ? $this->request->data['timeofuse_2_cl2'] : $step1['timeofuse_2_cl2'],
						'timeofuse_cl2_shoulder' => (isset($this->request->data['timeofuse_cl2_shoulder'])) ? $this->request->data['timeofuse_cl2_shoulder'] : '',
						'timeofuse_tariff12_peak' => (isset($this->request->data['timeofuse_tariff12_peak'])) ? $this->request->data['timeofuse_tariff12_peak'] : $step1['timeofuse_tariff12_peak'],
						'timeofuse_tariff12_offpeak' => (isset($this->request->data['timeofuse_tariff12_offpeak'])) ? $this->request->data['timeofuse_tariff12_offpeak'] : $step1['timeofuse_tariff12_offpeak'],
						'timeofuse_tariff12_shoulder' => (isset($this->request->data['timeofuse_tariff12_shoulder'])) ? $this->request->data['timeofuse_tariff12_shoulder'] : $step1['timeofuse_tariff12_shoulder'],
						'timeofuse_tariff13_peak' => (isset($this->request->data['timeofuse_tariff13_peak'])) ? $this->request->data['timeofuse_tariff13_peak'] : $step1['timeofuse_tariff13_peak'],
						'timeofuse_tariff13_offpeak' => (isset($this->request->data['timeofuse_tariff13_offpeak'])) ? $this->request->data['timeofuse_tariff13_offpeak'] : $step1['timeofuse_tariff13_offpeak'],
						'timeofuse_tariff13_shoulder' => (isset($this->request->data['timeofuse_tariff13_shoulder'])) ? $this->request->data['timeofuse_tariff13_shoulder'] : $step1['timeofuse_tariff13_shoulder'],
						'flexible_peak' => (isset($this->request->data['flexible_peak'])) ? $this->request->data['flexible_peak'] : $step1['flexible_peak'],
						'flexible_offpeak' => (isset($this->request->data['flexible_offpeak'])) ? $this->request->data['flexible_offpeak'] : $step1['flexible_offpeak'],
						'flexible_shoulder' => (isset($this->request->data['flexible_shoulder'])) ? $this->request->data['flexible_shoulder'] : $step1['flexible_shoulder'],
						'elec_usage_level' => (isset($this->request->data['elec_usage_level'])) ? $this->request->data['elec_usage_level'] : $step1['elec_usage_level'],
						'gas_usage_level' => (isset($this->request->data['gas_usage_level'])) ? $this->request->data['gas_usage_level'] : $step1['gas_usage_level'],
						'business_name' => (isset($this->request->data['business_name'])) ? $this->request->data['business_name'] : $step1['business_name'],
						'first_name' => (isset($this->request->data['first_name'])) ? $this->request->data['first_name'] : $step1['first_name'],
						'surname' => (isset($this->request->data['surname'])) ? $this->request->data['surname'] : $step1['surname'],
						'mobile' => (isset($this->request->data['mobile'])) ? $this->request->data['mobile'] : $step1['mobile'],
						'phone' => (isset($this->request->data['phone'])) ? $this->request->data['phone'] : $step1['phone'],
						'other_number' => (isset($this->request->data['other_number'])) ? $this->request->data['other_number'] : $step1['other_number'],
						'email' => (isset($this->request->data['email'])) ? $this->request->data['email'] : $step1['email'],
						'term1' => (isset($this->request->data['term1'])) ? 1 : $step1['term1'],
						'solar_rebate_scheme' => (isset($this->request->data['solar_rebate_scheme'])) ? 1 : $step1['solar_rebate_scheme'],
					);
					$this->Session->write('User.step1', $data);
				break;
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
	
	private function calculate($usage, $tier_rates, $has_tier_5 = false) {
    	$rate = array();
    	$tier = array();
    	$i = 0;
    	$return = false;
    	foreach ($tier_rates as $tier_rate) {
    	    $i++;
    	    $tier[$i] = $tier_rate['tier'];
    	    $rate[$i] = $tier_rate['rate'];
    	}
    	$sum[1] = 0;
    	if ($rate[1]) {
    	    if ($tier[1]) {
    	        if ($usage >= $tier[1]) {
    	            $sum[1] = $rate[1] * $tier[1];
    	        } 
    	        else {
	    	        $sum[1] = $usage * $rate[1];
	    	        $return = true;
    	        }
    	    } 
    	    else {
    	        $sum[1] = $usage * $rate[1];
    	        $return = true;
    	    }
    	}
    	$sum[2] = 0;
    	if (!$return && $rate[2]) {
    	    if ($tier[2]) {
    	        if ($usage >= ($tier[1] + $tier[2])) {
    	            $sum[2] = $rate[2] * $tier[2];
    	        } 
    	        else if ($usage > $tier[1] && $usage < ($tier[1] + $tier[2])) {
    	            $sum[2] = $rate[2] * ($usage - $tier[1]);
    	            $return = true;
    	        }
    	    } 
    	    else {
    	    	if ($usage > $tier[1] ) {
	    	    	$sum[2] = $rate[2] * ($usage - $tier[1]);
    	    	}
    	    	$return = true;
    	    }
    	}
    	$sum[3] = 0;
    	if (!$return && $rate[3]) {
    	    if ($tier[3]) {
    	        if ($usage >= ($tier[1] + $tier[2] + $tier[3])) {
    	            $sum[3] = $rate[3] * $tier[3];
    	        } 
    	        else if ($usage > ($tier[1] + $tier[2]) && $usage < ($tier[1] + $tier[2] + $tier[3])) {
    	            $sum[3] = $rate[3] * ($usage - $tier[1] - $tier[2]);
    	            $return = true;
    	        }
    	    } 
    	    else {
    	    	if ($usage > ($tier[1] + $tier[2])) {
	    	    	$sum[3] = $rate[3] * ($usage - $tier[1] - $tier[2]);
    	    	}
    	    	$return = true;
    	    }
    	}
    	$sum[4] = 0;
    	if (!$return && $rate[4]) {
    	    if ($tier[4]) {
    	        if ($usage >= ($tier[1] + $tier[2] + $tier[3] + $tier[4])) {
    	            $sum[4] = $rate[4] * $tier[4];
    	        } 
    	        else if ($usage > ($tier[1] + $tier[2] + $tier[3]) && $usage < ($tier[1] + $tier[2] + $tier[3] + $tier[4])) {
    	            $sum[4] = $rate[4] * ($usage - $tier[1] - $tier[2] - $tier[3]);
    	            $return = true;
    	        }
    	    } 
    	    else {
    	    	if ($usage > ($tier[1] + $tier[2] + $tier[3])) {
	    	    	$sum[4] = $rate[4] * ($usage - $tier[1] - $tier[2] - $tier[3]);
    	    	}
    	    	$return = true;
    	    }
    	}
    	if ($has_tier_5 === false) { // last rate
	    	$sum[5] = 0;
			if (!$return && $rate[5]) {
    	    	if ($usage > ($tier[1] + $tier[2] + $tier[3] + $tier[4])) {
    	        	$sum[5] = $rate[5] * ($usage - $tier[1] - $tier[2] - $tier[3] - $tier[4]);
				}
			}
			$sum[6] = 0;
    	} 
    	else {
	    	$sum[5] = 0;
			if (!$return && $rate[5]) {
				if ($tier[5]) {
					if ($usage >= ($tier[1] + $tier[2] + $tier[3] + $tier[4] + $tier[5])) {
    	            	$sum[5] = $rate[5] * $tier[5];
					} 
					else if ($usage > ($tier[1] + $tier[2] + $tier[3] + $tier[4]) && $usage < ($tier[1] + $tier[2] + $tier[3] + $tier[4] + $tier[5])) {
    	            	$sum[5] = $rate[5] * ($usage - $tier[1] - $tier[2] - $tier[3] - $tier[4]);
					}
				} 
				else {
    				if ($usage > ($tier[1] + $tier[2] + $tier[3] + $tier[4])) {
        				$sum[5] = $rate[5] * ($usage - $tier[1] - $tier[2] - $tier[3] - $tier[4]); // last rate
    				}
    				$return = true;
				}
			}
			$sum[6] = 0;
			if (!$return && $rate[6]) { // last rate
    	    	if ($usage > ($tier[1] + $tier[2] + $tier[3] + $tier[4] + $tier[5])) {
    	        	$sum[6] = $rate[6] * ($usage - $tier[1] - $tier[2] - $tier[3] - $tier[4] - $tier[5]);
				}
			}
    	}
    	return ($sum[1] + $sum[2] + $sum[3] + $sum[4] + $sum[5] + $sum[6]);
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
	
	public function get_rates($details = false) {
		$step1 = $this->Session->read('User.step1');
		$state = $this->Session->read('User.state');
		$states_arr = unserialize(AU_STATES);
		if ($this->request->is('put') || $this->request->is('post')) {
			$plan_id = $this->request->data['plan_id'];
			$elec_rate_id = $this->request->data['elec_rate_id'];
			$gas_rate_id = $this->request->data['gas_rate_id'];
			$rate_type = $this->request->data['rate_type'];
			$plan = $this->Plan->findById($plan_id);
			if ($rate_type == 'Elec' || $rate_type == 'Dual') {
				$rate = $this->ElectricityRate->findById($elec_rate_id);
				$plan['Plan']['elec_rate'] = $rate['ElectricityRate'];
			}
			if ($rate_type == 'Gas' || $rate_type == 'Dual') {
				$rate = $this->GasRate->findById($gas_rate_id);
				$plan['Plan']['gas_rate'] = $rate['GasRate'];
			}
			$solar_rebate_scheme = '';
			if ($step1['nmi_distributor']) {
				if ($step1['tariff1']) {
				    $tariff1 = explode('|', $step1['tariff1']);
				    if ($tariff1[3] == 'Solar') {
				    	$tariff_code = $tariff1[0];
				    }
				}
				if ($step1['tariff2']) {
				    $tariff2 = explode('|', $step1['tariff2']);
				    if ($tariff2[3] == 'Solar') {
				    	$tariff_code = $tariff2[0];
				    }
				}
				if ($step1['tariff3']) {
				    $tariff3 = explode('|', $step1['tariff3']);
				    if ($tariff3[3] == 'Solar') {
				    	$tariff_code = $tariff3[0];
				    }
				}
				if ($step1['tariff4']) {
				    $tariff4 = explode('|', $step1['tariff4']);
				    if ($tariff4[3] == 'Solar') {
				    	$tariff_code = $tariff4[0];
				    }
				}
				if ($tariff_code) {
					$tariff = $this->Tariff->find('first', array(
						'conditions' => array(
							'Tariff.tariff_code' => $tariff_code,
							'Tariff.res_sme' => $step1['customer_type'],
							'Tariff.distributor' => explode('/', $step1['nmi_distributor']),
						),
					));
					$solar_rebate_scheme = $tariff['Tariff']['solar_rebate_scheme'];
				}
			}
			$plan['Plan']['solar_rate'] = array();
			if ($solar_rebate_scheme) {
				if (strpos($solar_rebate_scheme, '/') !== false) {
					$solar_rebate_scheme = $step1['solar_rebate_scheme'];
				}
			    $solar_rebate_scheme_rate = $this->SolarRebateScheme->findByStateAndScheme($states_arr[$state], $solar_rebate_scheme);
			    $plan['Plan']['solar_rate']['government'] = $solar_rebate_scheme_rate['SolarRebateScheme']['government'];
			    switch ($plan['Plan']['retailer']) {
			    	case 'AGL':
			    		$plan['Plan']['solar_rate']['retailer'] = $solar_rebate_scheme_rate['SolarRebateScheme']['agl'];
			    	break;
			    	case 'Lumo Energy':
			    		$plan['Plan']['solar_rate']['retailer'] = $solar_rebate_scheme_rate['SolarRebateScheme']['lumo_energy'];
			    	break;
			    	case 'Momentum':
			    		$plan['Plan']['solar_rate']['retailer'] = $solar_rebate_scheme_rate['SolarRebateScheme']['momentum'];
			    	break;
			    	case 'Origin Energy':
			    		$plan['Plan']['solar_rate']['retailer'] = $solar_rebate_scheme_rate['SolarRebateScheme']['origin_energy'];
			    	break;
			    	case 'Powerdirect':
			    		$plan['Plan']['solar_rate']['retailer'] = $solar_rebate_scheme_rate['SolarRebateScheme']['powerdirect'];
			    	break;
			    	case 'Red Energy':
			    		$plan['Plan']['solar_rate']['retailer'] = $solar_rebate_scheme_rate['SolarRebateScheme']['red_energy'];
			    	break;
			    	case 'Powershop':
			    		$plan['Plan']['solar_rate']['retailer'] = $solar_rebate_scheme_rate['SolarRebateScheme']['powershop'];
			    	break;
			    	case 'Sumo Power':
                        $plan['Plan']['solar_rate']['retailer'] = $solar_rebate_scheme_rate['SolarRebateScheme']['sumo_power'];
                    break;
                    case 'Alinta Energy':
			    		$plan['Plan']['solar_rate']['retailer'] = $solar_rebate_scheme_rate['SolarRebateScheme']['alinta_energy'];
			    	break;
			    	case 'ERM':
			    		$plan['Plan']['solar_rate']['retailer'] = $solar_rebate_scheme_rate['SolarRebateScheme']['erm'];
			    	break;
			    	case 'Powerdirect and AGL':
			    		$plan['Plan']['solar_rate']['retailer'] = $solar_rebate_scheme_rate['SolarRebateScheme']['pd_agl'];
			    	break;
			    	case 'Energy Australia':
                        $plan['Plan']['solar_rate']['retailer'] = $solar_rebate_scheme_rate['SolarRebateScheme']['energy_australia'];
                    break;
			    	case 'Next Business Energy':
			    		$plan['Plan']['solar_rate']['retailer'] = $solar_rebate_scheme_rate['SolarRebateScheme']['next_business_energy'];
			    	break;
			    }
			}
			$discount_pay_on_time = (isset($this->request->data['discount_pay_on_time']) && $this->request->data['discount_pay_on_time']) ? true : false;
			$discount_guaranteed = (isset($this->request->data['discount_guaranteed']) && $this->request->data['discount_guaranteed']) ? true : false;
			$discount_direct_debit = (isset($this->request->data['discount_direct_debit']) && $this->request->data['discount_direct_debit']) ? true : false;
			$discount_dual_fuel = (isset($this->request->data['discount_dual_fuel']) && $this->request->data['discount_dual_fuel']) ? true : false;
			$discount_bonus_sumo = (isset($this->request->data['discount_bonus_sumo']) && $this->request->data['discount_bonus_sumo']) ? true : false;
			$discount_prepay = (isset($this->request->data['discount_prepay']) && $this->request->data['discount_prepay']) ? true : false;
			$include_gst = (isset($this->request->data['include_gst']) && $this->request->data['include_gst']) ? true : false;
			$this->autoRender = false;
			$view = new View($this, false);
			$view->layout = 'ajax';
			$view->set(compact('step1', 'state', 'plan', 'discount_pay_on_time', 'discount_guaranteed', 'discount_direct_debit', 'discount_dual_fuel', 'discount_bonus_sumo', 'discount_prepay', 'include_gst', 'rate_type'));
			if ($details) {
				$view_output = $view->render('/Elements/rate_details_v4');
			}
			else {
				$view_output = $view->render('/Elements/rates_v4');
			}
			return new CakeResponse(array(
				'body' => json_encode(array(
					'html' => $view_output
				)), 
				'type' => 'json',
				'status' => '201'
			));
		}
	}
	
	public function signup() {
		if ($this->request->is('put') || $this->request->is('post')) {
			$plan_id = $this->request->data['plan_id'];
			$elec_rate_id = $this->request->data['elec_rate_id'];
			$gas_rate_id = $this->request->data['gas_rate_id'];
			$plan = $this->Plan->findById($plan_id);
			$elec_rate = $this->ElectricityRate->findById($elec_rate_id);
			$gas_rate = $this->GasRate->findById($gas_rate_id);
			$this->Customer->create();
			$this->Customer->save(array('Customer' => array(
			    'plan_id' => $plan_id,
			    'elec_rate_id' => $elec_rate_id,
			    'gas_rate_id' => $gas_rate_id,
			    'postcode' => $this->Session->read('User.postcode'),
			    'state' => $this->Session->read('User.state'),
			    'suburb' => $this->Session->read('User.suburb'),
			    'data' => serialize($this->Session->read('User')),
			    'plan_data' => serialize($plan),
			    'elec_rate_data' => ($elec_rate_id) ? serialize($elec_rate) : '',
			    'gas_rate_data' => ($gas_rate_id) ? serialize($gas_rate) : '',
			)));
			$customer_id = $this->Customer->getInsertID();
			return new CakeResponse(array(
			    'body' => json_encode(array(
			    	'status' => '1',
			    	'id' => $customer_id
			    )), 
			    'type' => 'json',
			    'status' => '201'
			));
		}
	}
	
	public function export() {
    	$this->set('title_for_layout', 'Export');
    	
    	$plans = array();
    	
    	$states_arr = unserialize(AU_STATES);
    	
    	if ($this->request->is('post')) {
        	$state = $this->request->data['Export']['state'];
        	$postcode = $this->request->data['Export']['postcode'];
        	$suburb = ucwords(strtolower($this->request->data['Export']['suburb']));
        	$plan_type = $this->request->data['Export']['plan_type'];
        	$customer_type = $this->request->data['Export']['customer_type'];
        	$nmi = $this->request->data['Export']['nmi'];
        	$tariff_code = $this->request->data['Export']['tariff_code'];
        	//$distributor = $this->request->data['Export']['distributor'];
        	$consumption_level = $this->request->data['Export']['consumption_level'];
        	
        	$conditions = array();
            $conditions['Plan.state'] = $states_arr[$state];
    		$conditions['Plan.package'] = $plan_type;
    		$conditions['Plan.res_sme'] = $customer_type;
    		$conditions['Plan.version'] = array('All', '4');
    		$conditions['Plan.status'] = 'Active';
            $plan_expiry_or = array(
                'or' => array(
                	'Plan.plan_expiry' => '0000-00-00',
                	'Plan.plan_expiry >' => date('Y-m-d'),
                ),
            );
            $conditions[] = $plan_expiry_or;
            
            $default_consumption = $this->Consumption->findByStateAndResSme($state, $customer_type);
            if (in_array($plan_type, array('Elec', 'Dual'))) {
                $distributor_elec = $this->ElectricityPostcodeDistributor->findByPostcodeAndSuburb($postcode, $suburb); 
                
                $nmi_mapping = $this->ElectricityNmiDistributor->findByNmi(strtoupper(substr($nmi, 0, 2)));
				if ($nmi_mapping) {
				    $nmi_distributor = $nmi_mapping['ElectricityNmiDistributor']['distributor'];
				}
                
                $default_peak = explode('/', $default_consumption['Consumption']['elec_peak']);
                $elec_billing_days = $default_consumption['Consumption']['elec_billing_days'];
                //Single Rate
                switch ($consumption_level) {
    			    case 'Low':
    			    	$elec_peak = $default_peak[0];
    			    break;
    			    case 'Medium':
    			    	$elec_peak = $default_peak[1];
    			    break;
    			    case 'High':
    			    	$elec_peak = $default_peak[2];
    			    break;
    			}

                $tariff = $this->Tariff->find('first', array(
    		    	'conditions' => array(
    		    		'Tariff.tariff_code' => $tariff_code,
    		    		'Tariff.res_sme' => $customer_type,
    		    		'Tariff.distributor' => explode('/', $nmi_distributor),
    		    	),
    		    ));
    		    $solar_rebate_scheme = $tariff['Tariff']['solar_rebate_scheme'];
    		    
    	        if ($solar_specific_plan) {
    				$conditions['Plan.solar_specific_plan !='] = 'Not Solar';
    			} else {
    				$conditions['Plan.solar_specific_plan !='] = 'Solar Only';
    			}
            }
            if (in_array($plan_type, array('Gas', 'Dual'))) {
                $distributor_gas = $this->GasPostcodeDistributor->findByPostcodeAndSuburb($postcode, $suburb);
                
                $gas_billing_days = $default_consumption['Consumption']['gas_billing_days'];
				$gas_billing_start = $default_consumption['Consumption']['gas_billing_start'];
				$default_peak = explode('/', $default_consumption['Consumption']['gas_peak']);
				switch ($consumption_level) {
    			    case 'Low':
    			    	$gas_peak = $default_peak[0];
    			    break;
    			    case 'Medium':
    			    	$gas_peak = $default_peak[1];
    			    break;
    			    case 'High':
    			    	$gas_peak = $default_peak[2];
    			    break;
    			}
            }
            if ($plan_type == 'Elec') {
                if ($distributor_elec) {
                    if ($distributor_elec['ElectricityPostcodeDistributor']['agl_distributor']) {
						//
						if ($state == 'VIC' && $customer_type == 'RES') {
							if ($nmi && in_array(substr($nmi, 0, 2), array(60, 61, 62))) {
                                $conditions['Plan.version'][] = 'AGL Savers (Citipower, Jemena & Powercor)';
                            }
                        }
                        if ($state == 'NSW' && $customer_type == 'RES') {
                            if ($nmi && in_array(substr($nmi, 0, 2), array(40, 42, 44, 45))) {
                                $conditions['Plan.version'][] = 'AGL Savers (Essential Energy)';
                            }
                        }
                        if ($state == 'VIC' && $customer_type == 'SME') {
							if ($nmi && in_array(substr($nmi, 0, 2), array(61, 62))) {
                                $conditions['Plan.version'][] = 'Business Savers Powercor & Citipower';
                            }
                            if ($nmi && in_array(substr($nmi, 0, 2), array(60, 63))) {
                                $conditions['Plan.version'][] = 'Business Savers Jemena & Ausnet';
                            }
                            if ($nmi && in_array(substr($nmi, 0, 2), array(64))) {
                                $conditions['Plan.version'][] = 'Business Savers United';
                            }
                        }
                        if ($state == 'NSW' && $customer_type == 'SME') {
                            if ($nmi && in_array(substr($nmi, 0, 2), array(40, 42, 44, 45))) {
                                $conditions['Plan.version'][] = 'Business Savers Essential';
                            }
                            if ($nmi && in_array(substr($nmi, 0, 2), array(41))) {
                                $conditions['Plan.version'][] = 'Business Savers Ausgrid';
                            }
                            if ($nmi && in_array(substr($nmi, 0, 2), array(43))) {
                                $conditions['Plan.version'][] = 'Business Savers Endeavour';
                            }
                        }
					}
					if ($distributor_elec['ElectricityPostcodeDistributor']['powerdirect_distributor']) {
						//
						if ($state == 'VIC' && $customer_type == 'RES') {
    				        if ($nmi && in_array(substr($nmi, 0, 4), array(6102, 6001, 6203))) {
                                $conditions['Plan.version'][] = 'Residential (Citipower, Jemena & Powercor)';
                            }
                            if ($nmi && in_array(substr($nmi, 0, 4), array(6407, 6305))) {
                                $conditions['Plan.version'][] = 'Residential (United & SP Ausnet)';
                            }
                            if ($nmi && in_array(substr($nmi, 0, 2), array(60, 61, 64))) {
                                $conditions['Plan.version'][] = 'Citipower, Jemena & Powercor';
                            }
    				    }
					}
					if ($distributor_elec['ElectricityPostcodeDistributor']['origin_energy_distributor']) {
						//
						if ($distributor_elec['ElectricityPostcodeDistributor']['origin_energy_special_product_name']) {
						    $conditions['Plan.version'][] = '4 (Special)';
					    }
					    if ($distributor_elec['ElectricityPostcodeDistributor']['origin_energy_origin_saver_patch']) {
						    $conditions['Plan.version'][] = 'Origin Saver Patch';
                            if ($nmi && in_array(substr($nmi, 0, 2), array(40, 42, 44, 45)) && $distributor_elec['ElectricityPostcodeDistributor']['origin_energy_origin_saver_patch']) {
							    $conditions['Plan.version'][] = 'Origin Saver Essential Patch';
						    }
						    if ($nmi && in_array(substr($nmi, 0, 2), array(62, 64)) && $distributor_elec['ElectricityPostcodeDistributor']['origin_energy_origin_saver_patch']) {
							    $conditions['Plan.version'][] = 'Origin Saver Essential Patch VIC';
						    }
					    }
					    if ($distributor_elec['ElectricityPostcodeDistributor']['origin_energy_businesssaver_hv']) {
						    $conditions['Plan.version'][] = 'BusinessSaver HV';
					    }
					    
					    if ($nmi && in_array(substr($nmi, 0, 2), array(40, 42, 44, 45))) {
                            $conditions['Plan.version'][] = 'Origin Saver (Essential Energy)'; 
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(41, 43))) {
                            $conditions['Plan.version'][] = 'Origin Saver (Ausgrid & Endeavour)';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(60, 61))) {
                            $conditions['Plan.version'][] = 'Origin Saver (Jemena & Citipower)';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(62, 64))) {
                            $conditions['Plan.version'][] = 'Origin Saver (Powercor & United)';
                        }
                        
                        if ($state == 'VIC' && $customer_type == 'RES') {
                            if ($nmi && in_array(substr($nmi, 0, 2), array(63))) {
                                $conditions['Plan.version'][] = 'Origin Saver (Ausnet)';
                            }
                        }
                        
                        if ($state == 'NSW' && $customer_type == 'SME') {
                            if ($nmi && in_array(substr($nmi, 0, 2), array(41))) {
                                $conditions['Plan.version'][] = 'BusinessSaver Ausgrid';
                            }
                            if ($nmi && in_array(substr($nmi, 0, 2), array(43))) {
                                $conditions['Plan.version'][] = 'BusinessSaver Endeavour Energy';
                            }
                            if ($nmi && in_array(substr($nmi, 0, 2), array(40, 42, 44, 45))) {
                                $conditions['Plan.version'][] = 'BusinessSaver Essential Energy';
                            }
                        }
                        if ($state == 'VIC' && $customer_type == 'SME') {
                            if ($nmi && in_array(substr($nmi, 0, 2), array(63))) {
                                $conditions['Plan.version'][] = 'BusinessSaver Ausnet';
                            }
                            if ($nmi && in_array(substr($nmi, 0, 2), array(60, 61, 62, 64))) {
                                $conditions['Plan.version'][] = 'BusinessSaver Citipower, Powercor, Jemena & United';
                            }
                        }
					}
					if ($distributor_elec['ElectricityPostcodeDistributor']['lumo_energy_distributor']) {
						//
					}
					if ($distributor_elec['ElectricityPostcodeDistributor']['red_energy_distributor']) {
						//
					}
					if ($distributor_elec['ElectricityPostcodeDistributor']['momentum_distributor']) {
						//
					}
					if ($distributor_elec['ElectricityPostcodeDistributor']['powershop_distributor']) {
						//
					}
					if ($distributor_elec['ElectricityPostcodeDistributor']['dodo_distributor']) {
						//
					}
					if ($distributor_elec['ElectricityPostcodeDistributor']['alinta_energy_distributor']) {
						//
					}
					if ($distributor_elec['ElectricityPostcodeDistributor']['energy_australia_distributor']) {
						//
						if ($state == 'VIC' && $customer_type == 'SME') {
                            if ($nmi) {
                                switch (substr($nmi, 0, 2)) {
                                    case '60':
                                        $conditions['Plan.version'][] = 'Everyday Saver Business Jemena';
                                        $conditions['Plan.version'][] = 'Business Saver Business Jemena';
                                        break;
                                    case '61':
                                        $conditions['Plan.version'][] = 'Everyday Saver Business Citipower';
                                        $conditions['Plan.version'][] = 'Business Saver Business Citipower';
                                        break;
                                    case '62':
                                        $conditions['Plan.version'][] = 'Everyday Saver Business Powercor';
                                        $conditions['Plan.version'][] = 'Business Saver Business Powercor';
                                        break;
                                    case '63':
                                        $conditions['Plan.version'][] = 'Everyday Saver Business Ausnet';
                                        $conditions['Plan.version'][] = 'Business Saver Business Ausnet';
                                        break;
                                    case '64':
                                        $conditions['Plan.version'][] = 'Everyday Saver Business United Energy';
                                        $conditions['Plan.version'][] = 'Business Saver Business United';
                                        break;
                                }
                            }
				        }
				        if ($state == 'NSW' && $customer_type == 'SME') {
                            if ($nmi && in_array(substr($nmi, 0, 2), array(40, 42, 43, 44, 45))) {
                                $conditions['Plan.version'][] = 'Everyday Saver Business (Essential & Endeavour Energy)';
                            }
                            if ($nmi && in_array(substr($nmi, 0, 2), array(41))) {
                                $conditions['Plan.version'][] = 'Everyday Saver Business (Ausgrid)';
                            }
    				    }
				        if ($state == 'VIC' && $customer_type == 'RES') {
    				        if ($nmi && in_array(substr($nmi, 0, 2), array(60, 63))) {
                                $conditions['Plan.version'][] = 'Flexi Saver (Jemena & AusNet)';
                            }
                            if ($nmi && in_array(substr($nmi, 0, 2), array(61, 62, 64))) {
                                $conditions['Plan.version'][] = 'Flexi Saver (Powercor, Citipower & United)';
                            }
    				    }
    				    if ($state == 'NSW' && $customer_type == 'RES') {
    				        if ($nmi && in_array(substr($nmi, 0, 2), array(43))) {
                                $conditions['Plan.version'][] = 'Flexi Saver (Endeavour)';
                            }
                            if ($nmi && in_array(substr($nmi, 0, 2), array(40, 42, 44, 45))) {
                                $conditions['Plan.version'][] = 'Flexi Saver (Essential)';
                            }
                            if ($nmi && in_array(substr($nmi, 0, 2), array(41))) {
                                $conditions['Plan.version'][] = 'Flexi Saver (Ausgrid)';
                            }
    				    }
					}
					if ($distributor_elec['ElectricityPostcodeDistributor']['sumo_power_distributor']) {
						//
					}
					if ($distributor_elec['ElectricityPostcodeDistributor']['erm_distributor']) {
						//
				    }
				    if ($distributor_elec['ElectricityPostcodeDistributor']['next_business_energy_distributor']) {
						//
				    }
				}
			} elseif ($plan_type == 'Gas') {
				if ($distributor_gas) {
					if ($distributor_gas['GasPostcodeDistributor']['agl_distributor']) {
						//
					}
					if ($distributor_gas['GasPostcodeDistributor']['origin_energy_distributor']) {
						//
					}
					if ($distributor_gas['GasPostcodeDistributor']['lumo_energy_distributor']) {
						//
					}
					if ($distributor_gas['GasPostcodeDistributor']['red_energy_distributor']) {
						//
					}
					if ($distributor_gas['GasPostcodeDistributor']['momentum_distributor']) {
						//
					}
					if ($distributor_gas['GasPostcodeDistributor']['dodo_distributor']) {
						//
					}
					if ($distributor_gas['GasPostcodeDistributor']['alinta_energy_distributor']) {
						//
					}
					if ($distributor_gas['GasPostcodeDistributor']['energy_australia_distributor']) {
						//
					}
				}
			} elseif ($plan_type == 'Dual') {
			    if ($distributor_elec && $distributor_gas) {
					if ($distributor_elec['ElectricityPostcodeDistributor']['agl_distributor'] && $distributor_gas['GasPostcodeDistributor']['agl_distributor']) {
						//
						if ($state == 'VIC' && $customer_type == 'RES') {
							if ($nmi && in_array(substr($nmi, 0, 2), array(60, 61, 62))) {
                                $conditions['Plan.version'][] = 'AGL Savers (Citipower, Jemena & Powercor)';
                            }
                        }
                        if ($state == 'NSW' && $customer_type == 'RES') {
                            if ($nmi && in_array(substr($nmi, 0, 2), array(40, 42, 44, 45))) {
                                $conditions['Plan.version'][] = 'AGL Savers (Essential Energy)';
                            }
                        }
                        if ($state == 'VIC' && $customer_type == 'SME') {
							if ($nmi && in_array(substr($nmi, 0, 2), array(61, 62))) {
                                $conditions['Plan.version'][] = 'Business Savers Powercor & Citipower';
                            }
                            if ($nmi && in_array(substr($nmi, 0, 2), array(60, 63))) {
                                $conditions['Plan.version'][] = 'Business Savers Jemena & Ausnet';
                            }
                            if ($nmi && in_array(substr($nmi, 0, 2), array(64))) {
                                $conditions['Plan.version'][] = 'Business Savers United';
                            }
                        }
                        if ($state == 'NSW' && $customer_type == 'SME') {
                            if ($nmi && in_array(substr($nmi, 0, 2), array(40, 42, 44, 45))) {
                                $conditions['Plan.version'][] = 'Business Savers Essential';
                            }
                            if ($nmi && in_array(substr($nmi, 0, 2), array(41))) {
                                $conditions['Plan.version'][] = 'Business Savers Ausgrid';
                            }
                            if ($nmi && in_array(substr($nmi, 0, 2), array(43))) {
                                $conditions['Plan.version'][] = 'Business Savers Endeavour';
                            }
                        }
					}
					if ($distributor_elec['ElectricityPostcodeDistributor']['origin_energy_distributor'] && $distributor_gas['GasPostcodeDistributor']['origin_energy_distributor']) {
						//
						if ($distributor_elec['ElectricityPostcodeDistributor']['origin_energy_special_product_name']) {
						    $conditions['Plan.version'][] = '4 (Special)';
					    }
					    if ($distributor_elec['ElectricityPostcodeDistributor']['origin_energy_origin_saver_patch'] || $distributor_gas['GasPostcodeDistributor']['origin_energy_origin_saver_patch']) {
						    $conditions['Plan.version'][] = 'Origin Saver Patch';
                            if ($nmi && in_array(substr($nmi, 0, 2), array(40, 42, 44, 45)) && $distributor_elec['ElectricityPostcodeDistributor']['origin_energy_origin_saver_patch']) {
							    $conditions['Plan.version'][] = 'Origin Saver Essential Patch';
						    }
						    if ($nmi && in_array(substr($nmi, 0, 2), array(62, 64)) && $distributor_elec['ElectricityPostcodeDistributor']['origin_energy_origin_saver_patch']) {
							    $conditions['Plan.version'][] = 'Origin Saver Essential Patch VIC';
						    }
					    }
					    if ($distributor_elec['ElectricityPostcodeDistributor']['origin_energy_businesssaver_hv']) {
						    $conditions['Plan.version'][] = 'BusinessSaver HV';
					    }
					    
					    if ($nmi && in_array(substr($nmi, 0, 2), array(40, 42, 44, 45))) {
                            $conditions['Plan.version'][] = 'Origin Saver (Essential Energy)'; 
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(41, 43))) {
                            $conditions['Plan.version'][] = 'Origin Saver (Ausgrid & Endeavour)';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(60, 61))) {
                            $conditions['Plan.version'][] = 'Origin Saver (Jemena & Citipower)';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(62, 64))) {
                            $conditions['Plan.version'][] = 'Origin Saver (Powercor & United)';
                        }
                        
                        if ($state == 'VIC' && $customer_type == 'RES') {
                            if ($nmi && in_array(substr($nmi, 0, 2), array(63))) {
                                $conditions['Plan.version'][] = 'Origin Saver (Ausnet)';
                            }
                        }
                        
                        if ($state == 'NSW' && $customer_type == 'SME') {
                            if ($nmi && in_array(substr($nmi, 0, 2), array(41))) {
                                $conditions['Plan.version'][] = 'BusinessSaver Ausgrid';
                            }
                            if ($nmi && in_array(substr($nmi, 0, 2), array(43))) {
                                $conditions['Plan.version'][] = 'BusinessSaver Endeavour Energy';
                            }
                            if ($nmi && in_array(substr($nmi, 0, 2), array(40, 42, 44, 45))) {
                                $conditions['Plan.version'][] = 'BusinessSaver Essential Energy';
                            }
                        }
                        if ($state == 'VIC' && $customer_type == 'SME') {
                            if ($nmi && in_array(substr($nmi, 0, 2), array(63))) {
                                $conditions['Plan.version'][] = 'BusinessSaver Ausnet';
                            }
                            if ($nmi && in_array(substr($nmi, 0, 2), array(60, 61, 62, 64))) {
                                $conditions['Plan.version'][] = 'BusinessSaver Citipower, Powercor, Jemena & United';
                            }
                        }
					}
					if ($distributor_elec['ElectricityPostcodeDistributor']['lumo_energy_distributor'] && $distributor_gas['GasPostcodeDistributor']['lumo_energy_distributor']) {
						//
					}
					if ($distributor_elec['ElectricityPostcodeDistributor']['red_energy_distributor'] && $distributor_gas['GasPostcodeDistributor']['red_energy_distributor']) {
						//
					}
					if ($distributor_elec['ElectricityPostcodeDistributor']['momentum_distributor'] && $distributor_gas['GasPostcodeDistributor']['momentum_distributor']) {
						//
					}
					if ($distributor_elec['ElectricityPostcodeDistributor']['dodo_distributor'] && $distributor_gas['GasPostcodeDistributor']['dodo_distributor']) {
						//
					}
					if ($distributor_elec['ElectricityPostcodeDistributor']['alinta_energy_distributor'] && $distributor_gas['GasPostcodeDistributor']['alinta_energy_distributor']) {
						//
					}
					if ($distributor_elec['ElectricityPostcodeDistributor']['energy_australia_distributor'] && $distributor_gas['GasPostcodeDistributor']['energy_australia_distributor']) {
						//
						if ($state == 'VIC' && $customer_type == 'SME') {
                            if ($nmi) {
                                switch (substr($nmi, 0, 2)) {
                                    case '60':
                                        $conditions['Plan.version'][] = 'Everyday Saver Business Jemena';
                                        $conditions['Plan.version'][] = 'Business Saver Business Jemena';
                                        break;
                                    case '61':
                                        $conditions['Plan.version'][] = 'Everyday Saver Business Citipower';
                                        $conditions['Plan.version'][] = 'Business Saver Business Citipower';
                                        break;
                                    case '62':
                                        $conditions['Plan.version'][] = 'Everyday Saver Business Powercor';
                                        $conditions['Plan.version'][] = 'Business Saver Business Powercor';
                                        break;
                                    case '63':
                                        $conditions['Plan.version'][] = 'Everyday Saver Business Ausnet';
                                        $conditions['Plan.version'][] = 'Business Saver Business Ausnet';
                                        break;
                                    case '64':
                                        $conditions['Plan.version'][] = 'Everyday Saver Business United Energy';
                                        $conditions['Plan.version'][] = 'Business Saver Business United';
                                        break;
                                }
                            }
				        }
				        if ($state == 'NSW' && $customer_type == 'SME') {
                            if ($nmi && in_array(substr($nmi, 0, 2), array(40, 42, 43, 44, 45))) {
                                $conditions['Plan.version'][] = 'Everyday Saver Business (Essential & Endeavour Energy)';
                            }
                            if ($nmi && in_array(substr($nmi, 0, 2), array(41))) {
                                $conditions['Plan.version'][] = 'Everyday Saver Business (Ausgrid)';
                            }
    				    }
				        if ($state == 'VIC' && $customer_type == 'RES') {
    				        if ($nmi && in_array(substr($nmi, 0, 2), array(60, 63))) {
                                $conditions['Plan.version'][] = 'Flexi Saver (Jemena & AusNet)';
                            }
                            if ($nmi && in_array(substr($nmi, 0, 2), array(61, 62, 64))) {
                                $conditions['Plan.version'][] = 'Flexi Saver (Powercor, Citipower & United)';
                            }
    				    }
    				    if ($state == 'NSW' && $customer_type == 'RES') {
    				        if ($nmi && in_array(substr($nmi, 0, 2), array(43))) {
                                $conditions['Plan.version'][] = 'Flexi Saver (Endeavour)';
                            }
                            if ($nmi && in_array(substr($nmi, 0, 2), array(40, 42, 44, 45))) {
                                $conditions['Plan.version'][] = 'Flexi Saver (Essential)';
                            }
                            if ($nmi && in_array(substr($nmi, 0, 2), array(41))) {
                                $conditions['Plan.version'][] = 'Flexi Saver (Ausgrid)';
                            }
    				    }
					}
					if ($distributor_elec['ElectricityPostcodeDistributor']['pd_agl_distributor'] && $distributor_gas['GasPostcodeDistributor']['pd_agl_distributor']) {
						//
				    }
				}
			}
            
            $order[] = 'Plan.retailer ASC';
            
            $plans_temp = $this->Plan->find('all', array(
				'conditions' => $conditions,
				'order' => $order
            ));
            
			foreach ($plans_temp as $key => $plan) {
		        $plan['Plan']['discount_elec'] = 0;
				$plan['Plan']['discount_gas'] = 0;
				$plan['Plan']['total_elec'] = 0;
				$plan['Plan']['total_gas'] = 0;
                $plan['Plan']['total_inc_discount_elec'] = 0;
                $plan['Plan']['total_inc_discount_gas'] = 0;
                if (in_array($plan_type, array('Elec', 'Dual'))) {
                    $conditions = array(
						'ElectricityRate.state' => $plan['Plan']['state'],
						'ElectricityRate.res_sme' => $plan['Plan']['res_sme'],
						'ElectricityRate.retailer' => $plan['Plan']['retailer'],
						'ElectricityRate.tariff_type' => 'Single Rate',
						'ElectricityRate.rate_name' => $plan['Plan']['rate_name'],
						'ElectricityRate.status' => 'Active',
					);
			        if ($distributor_elec) {
						switch ($plan['Plan']['retailer']) {
							case 'AGL':
								$distributor_field = 'agl_distributor';
								break;
							case 'Powerdirect':
								$distributor_field = 'powerdirect_distributor';
								break;
							case 'Origin Energy':
								$distributor_field = 'origin_energy_distributor';
								break;
							case 'Lumo Energy':
								$distributor_field = 'lumo_energy_distributor';
								break;
							case 'Red Energy':
								$distributor_field = 'red_energy_distributor';
								break;
							case 'Momentum':
								$distributor_field = 'momentum_distributor';
								break;
							case 'Powershop':
								$distributor_field = 'powershop_distributor';
								break;
							case 'Dodo':
								$distributor_field = 'dodo_distributor';
								break;
                            case 'Alinta Energy':
								$distributor_field = 'alinta_energy_distributor';
								break;
                            case 'Energy Australia':
								$distributor_field = 'energy_australia_distributor';
								break;
                            case 'Sumo Power':
								$distributor_field = 'sumo_power_distributor';
								break;
                            case 'ERM':
                                $distributor_field = 'erm_distributor';
                                break;
                            case 'Powerdirect and AGL':
                                $distributor_field = 'pd_agl_distributor';
                                break;
                            case 'Next Business Energy':
                                $distributor_field = 'next_business_energy_distributor';
                                break;
						}
						$distributors = explode('/', $distributor_elec['ElectricityPostcodeDistributor'][$distributor_field]);
					}
			        if ($nmi_distributor) {
					    if (strpos($nmi_distributor, '/') !== false) {
					    	switch ($nmi_distributor) {
					    		case 'Powercor/Powercor 1/Powercor 2':
					    			if ($plan['Plan']['retailer'] != 'Red Energy') {
					    				$distributors = array('Powercor');
					    			}
					    		break;
					    		case 'Essential Energy/Essential Energy - FW/Essential Energy - UR':
					    			if ($plan['Plan']['retailer'] == 'AGL') {
						    			if (!in_array($distributor_elec['ElectricityPostcodeDistributor'][$distributor_field], array('Essential Energy - FW', 'Essential Energy - UR'))) {
							    			$distributors = array('Essential Energy - UR');
						    			}
					    			} else {
						    			$distributors = array('Essential Energy');
					    			}
					    		break;
					    	}
					    } else {
					    	$distributors = explode('/', $nmi_distributor);
					    }
					}
					$conditions['ElectricityRate.distributor'] = $distributors;
			        if ($nmi_distributor && $tariff_code) {
					    $tariff = $this->Tariff->find('first', array(
					    	'conditions' => array(
					    		'Tariff.tariff_code' => $tariff_code,
					    		'Tariff.res_sme' => $customer_type,
					    		'Tariff.distributor' => explode('/', $nmi_distributor),
					    	),
					    ));
					    if ($tariff['Tariff']['tariff_class']) {
					    	$tariff_classes = array();
					    	$tariff_classes_temp = explode('/', $tariff['Tariff']['tariff_class']);
					    	foreach ($tariff_classes_temp as $tariff_class) {
						    	if ($tariff['Tariff']['pricing_group'] != 'Single Rate') {
						    		$tariff_classes[] = str_replace($tariff['Tariff']['pricing_group'], $tariff_class, 'Single Rate');
						    	} else {
									$tariff_classes[] = $tariff_class;
						    	}
					    	}
					    	$conditions['ElectricityRate.tariff_class'] = $tariff_classes;
					    }
					}
					$rates_cnt = $this->ElectricityRate->find('count', array(
						'conditions' => $conditions,
					));
					if ($rates_cnt == 0) {
						unset($conditions['ElectricityRate.tariff_class']);
						$conditions['ElectricityRate.tariff_type'] = 'Single Rate';
					}
			        $rates = $this->ElectricityRate->find('all', array(
						'conditions' => $conditions,
						'order' => 'ElectricityRate.id ASC'
					));
					if (!empty($rates)) {
    					foreach ($rates as $rate) {
							$plan['Plan']['elec_rate'] = $rate['ElectricityRate'];
							$period = 0;
							switch ($rate['ElectricityRate']['rate_tier_period']) {
								case '2':
									$period = 60.83;
									break;
								case 'D':
									$period = 1;
									break;
								case 'M':
									$period = 30.42;
									break;
								case 'Q':
									$period = 91.25;
									break;
								case 'Y':
									$period = 365;
									break;
							}
							if ($period > 0) {
							    $rate['ElectricityRate']['peak_tier_1'] = ($rate['ElectricityRate']['peak_tier_1'] / $period) * $elec_billing_days;
								$rate['ElectricityRate']['peak_tier_2'] = ($rate['ElectricityRate']['peak_tier_2'] / $period) * $elec_billing_days;
								$rate['ElectricityRate']['peak_tier_3'] = ($rate['ElectricityRate']['peak_tier_3'] / $period) * $elec_billing_days;
								$rate['ElectricityRate']['peak_tier_4'] = ($rate['ElectricityRate']['peak_tier_4'] / $period) * $elec_billing_days;
							}
						    $tier_rates = array(
                    	    	array('tier' => $rate['ElectricityRate']['peak_tier_1'], 'rate' => $rate['ElectricityRate']['peak_rate_1']),
								array('tier' => $rate['ElectricityRate']['peak_tier_2'], 'rate' => $rate['ElectricityRate']['peak_rate_2']),
								array('tier' => $rate['ElectricityRate']['peak_tier_3'], 'rate' => $rate['ElectricityRate']['peak_rate_3']),
								array('tier' => $rate['ElectricityRate']['peak_tier_4'], 'rate' => $rate['ElectricityRate']['peak_rate_4']),
								array('tier' => 0, 'rate' => $rate['ElectricityRate']['peak_rate_5'])
							);
                            $usage_sum = $peak_sum = $this->calculate($elec_peak, $tier_rates);
                            $stp_sum_elec = 0;
                        	$discount_elec = 0;
                        	if ($rate['ElectricityRate']['stp_period'] == 'Y') {
                        	    $elec_billing = $elec_billing_days / 365;
                        	} 
                        	else if ($rate['ElectricityRate']['stp_period'] == 'Q') {
                        	    $elec_billing = $elec_billing_days / 91.25;
                        	} 
                        	else if ($rate['ElectricityRate']['stp_period'] == 'M') {
                        	    $elec_billing = $elec_billing_days / 30.42;
                        	} 
                        	else {
                        	    $elec_billing = $elec_billing_days;
                        	}
                        	$stp_sum_elec = $elec_billing * $rate['ElectricityRate']['stp'];
                        	if ($plan['Plan']['discount_applies']) {
                        		$temp_total_elec = 0;
                            	switch ($plan['Plan']['discount_applies']) {
                        		    case 'Usage':
                        		    	$temp_total_elec = $usage_sum;
										break;
                        		    case 'Usage + STP + GST':
                        		    	$temp_total_elec = ($usage_sum + $stp_sum_elec) * 1.1;
										break;
                        		}
                        		$discount_guaranteed_elec += $temp_total_elec * $plan['Plan']['discount_guaranteed_elec'] / 100;
    						    $discount_elec += $temp_total_elec * $plan['Plan']['discount_pay_on_time_elec'] / 100;
    						    $discount_elec += $temp_total_elec * $plan['Plan']['discount_guaranteed_elec'] / 100;
    							$discount_elec += $temp_total_elec * $plan['Plan']['discount_direct_debit_elec'] / 100;
    							$discount_elec += $temp_total_elec * $plan['Plan']['discount_dual_fuel_elec'] / 100;
    							$discount_elec += $temp_total_elec * $plan['Plan']['discount_prepay_elec'] / 100;
    							$discount_elec += $temp_total_elec * $plan['Plan']['discount_bonus_sumo'] / 100;
    							$plan['Plan']['discount_elec'] = $discount_elec;
    							switch ($plan['Plan']['discount_applies']) {
    							    case 'Usage':
    							    	$plan['Plan']['total_elec'] = round(($usage_sum + $stp_sum_elec) * 1.1);
    							    	$plan['Plan']['total_inc_discount_guaranteed_elec'] = round(($usage_sum - $discount_guaranteed_elec + $stp_sum_elec) * 1.1);
    							    	$plan['Plan']['total_inc_discount_elec'] = round(($usage_sum - $discount_elec + $stp_sum_elec) * 1.1);
    							    	break;
    							    case 'Usage + STP + GST':
    							    	$plan['Plan']['total_elec'] = round(($usage_sum + $stp_sum_elec) * 1.1);
    							    	$plan['Plan']['total_inc_discount_guaranteed_elec'] = round(($usage_sum + $stp_sum_elec) * 1.1 - $discount_guaranteed_elec);
    							    	$plan['Plan']['total_inc_discount_elec'] = round(($usage_sum + $stp_sum_elec) * 1.1 - $discount_elec);
    							    	break;
    							}
                        	} else {
                            	$plan['Plan']['total_elec'] = $plan['Plan']['total_inc_discount_elec'] = round(($usage_sum + $stp_sum_elec) * 1.1);
	                        }
                        }
    				}
                }
                if (in_array($plan_type, array('Gas', 'Dual'))) {
                    $conditions = array(
						'GasRate.state' => $plan['Plan']['state'],
						'GasRate.res_sme' => $plan['Plan']['res_sme'],
						'GasRate.retailer' => $plan['Plan']['retailer'],
						'GasRate.rate_name' => $plan['Plan']['rate_name'],
						'GasRate.status' => 'Active',
					);
			        if ($distributor_gas) {
						switch ($plan['Plan']['retailer']) {
							case 'AGL':
								$distributor_field = 'agl_distributor';
								break;
							case 'Origin Energy':
								$distributor_field = 'origin_energy_distributor';
								break;
							case 'Lumo Energy':
								$distributor_field = 'lumo_energy_distributor';
								break;
							case 'Red Energy':
								$distributor_field = 'red_energy_distributor';
								break;
							case 'Momentum':
								$distributor_field = 'momentum_distributor';
								break;
							case 'Dodo':
								$distributor_field = 'dodo_distributor';
								break;
                            case 'Alinta Energy':
								$distributor_field = 'alinta_energy_distributor';
								break;
                            case 'Energy Australia':
								$distributor_field = 'energy_australia_distributor';
								break;
                            case 'Powerdirect and AGL':
                                $distributor_field = 'pd_agl_distributor';
                                break;
						}
						$conditions['GasRate.distributor'] = explode('/', $distributor_gas['GasPostcodeDistributor'][$distributor_field]);
					}
			        $rates = $this->GasRate->find('all', array(
						'conditions' => $conditions,
						'order' => 'GasRate.id ASC'
					));
					if (!empty($rates)) {
						foreach ($rates as $rate) {
							$plan['Plan']['gas_rate'] = $rate['GasRate'];
							$period = 0;
							switch ($rate['GasRate']['rate_tier_period']) {
							    case '2':
							    	$period = 60.83;
							    	break;
							    case 'D':
							    	$period = 1;
							    	break;
							    case 'M':
							    	$period = 30.42;
							    	break;
							    case 'Q':
							    	$period = 91.25;
							    	break;
							    case 'Y':
							    	$period = 365;
							    	break;
							}
							if ($period > 0) {
							    $rate['GasRate']['peak_tier_1'] = ($rate['GasRate']['peak_tier_1'] / $period) * $gas_billing_days;
							    $rate['GasRate']['peak_tier_2'] = ($rate['GasRate']['peak_tier_2'] / $period) * $gas_billing_days;
							    $rate['GasRate']['peak_tier_3'] = ($rate['GasRate']['peak_tier_3'] / $period) * $gas_billing_days;
							    $rate['GasRate']['peak_tier_4'] = ($rate['GasRate']['peak_tier_4'] / $period) * $gas_billing_days;
							    $rate['GasRate']['peak_tier_5'] = ($rate['GasRate']['peak_tier_5'] / $period) * $gas_billing_days;
							}
							$peak_tier_rates = array(
							    array('tier' => $rate['GasRate']['peak_tier_1'], 'rate' => $rate['GasRate']['peak_rate_1'] / 100),
							    array('tier' => $rate['GasRate']['peak_tier_2'], 'rate' => $rate['GasRate']['peak_rate_2'] / 100),
							    array('tier' => $rate['GasRate']['peak_tier_3'], 'rate' => $rate['GasRate']['peak_rate_3'] / 100),
							    array('tier' => $rate['GasRate']['peak_tier_4'], 'rate' => $rate['GasRate']['peak_rate_4'] / 100),
							    array('tier' => $rate['GasRate']['peak_tier_5'], 'rate' => $rate['GasRate']['peak_rate_5'] / 100),
							    array('tier' => 0, 'rate' => $rate['GasRate']['peak_rate_6'] / 100),
							);
							$peak_sum = $this->calculate($gas_peak, $peak_tier_rates, true);
							$off_peak_sum = 0;
							
							$usage_sum = $peak_sum + $off_peak_sum;
                        	$stp_sum_gas = 0;
                        	$discount_gas = 0;
                        	if ($rate['GasRate']['stp_period'] == 'Y') {
                        	    $gas_billing = $gas_billing_days / 365;
                        	} 
                        	else if ($rate['GasRate']['stp_period'] == 'Q') {
                        	    $gas_billing = $gas_billing_days / 91.25;
                        	} 
                        	else if ($rate['GasRate']['stp_period'] == 'M') {
                        	    $gas_billing = $gas_billing_days / 30.42;
                        	} 
                        	else {
                        	    $gas_billing = $gas_billing_days;
                        	}
                        	$stp_sum_gas = $gas_billing * $rate['GasRate']['stp'];
                        	if ($plan['Plan']['discount_applies']) {
                            	$temp_total_gas = 0;
                        		switch ($plan['Plan']['discount_applies']) {
                        		    case 'Usage':
                        		    	$temp_total_gas = $usage_sum;
                        		    break;
                        		    case 'Usage + STP + GST':
                        		    	$temp_total_gas = ($usage_sum + $stp_sum_gas) * 1.1;
                        		    break;
                        		}
                                $discount_guaranteed_gas += $temp_total_gas * $plan['Plan']['discount_guaranteed_gas'] / 100;
                                $discount_gas += $temp_total_gas * $plan['Plan']['discount_pay_on_time_gas'] / 100;
    							$discount_gas += $temp_total_gas * $plan['Plan']['discount_guaranteed_gas'] / 100;
    							$discount_gas += $temp_total_gas * $plan['Plan']['discount_direct_debit_gas'] / 100;
    							$discount_gas += $temp_total_gas * $plan['Plan']['discount_dual_fuel_gas'] / 100;
    							$plan['Plan']['discount_gas'] = $discount_gas;
    							switch ($plan['Plan']['discount_applies']) {
                        		    case 'Usage':
                        		    	$plan['Plan']['total_gas'] = round(($usage_sum + $stp_sum_gas) * 1.1);
                        		    	$plan['Plan']['total_inc_discount_guaranteed_gas'] = round(($usage_sum - $discount_guaranteed_gas + $stp_sum_gas) * 1.1);
                        		    	$plan['Plan']['total_inc_discount_gas'] = round(($usage_sum - $discount_gas + $stp_sum_gas) * 1.1);
                        		    	break;
                        		    case 'Usage + STP + GST':
                        		    	$plan['Plan']['total_gas'] = round(($usage_sum + $stp_sum_gas) * 1.1);
                        		    	$plan['Plan']['total_inc_discount_guaranteed_gas'] = round(($usage_sum + $stp_sum_gas) * 1.1 - $discount_guaranteed_gas);
                        		    	$plan['Plan']['total_inc_discount_gas'] = round(($usage_sum + $stp_sum_gas) * 1.1 - $discount_gas);
                        		    break;
                        		}
                        	} else {
                            	$plan['Plan']['total_gas'] = $plan['Plan']['total_inc_discount_gas'] = round(($usage_sum + $stp_sum_gas) * 1.1);
                        	}
						}
                    }
                }
                unset($plans[$key]);
				if ($plan_type == 'Elec') {
					$plans[$plan['Plan']['total_inc_discount_elec'] * 10000 + $plan['Plan']['id']] = $plan;
				}  else if ($plan_type == 'Gas') {
					$plans[$plan['Plan']['total_inc_discount_gas'] * 10000 + $plan['Plan']['id']] = $plan;
				}  else if ($plan_type == 'Dual') {
					$plans[($plan['Plan']['total_inc_discount_elec'] + $plan['Plan']['total_inc_discount_gas']) * 10000 + $plan['Plan']['id']] = $plan;
				}
			}
			ksort($plans);
			
			$lines = array();
            $lines[] = 'State,Customer Type,Consumption,Fuel Type,Retailer,Product Name,Electricity Distributor,Gas Distributor,Estimated Electricity Cost,Estimated Gas Cost,Rankings (Inc Discounts)';
            $i = 0;
            foreach ($plans as $plan) {
                $i++;
                $line = array(
                    $state,
                    $customer_type,
                    $consumption_level,
                    $plan_type,
                    $plan['Plan']['retailer'],
                    '"'.$plan['Plan']['product_name'].'"',
                    (in_array($plan_type, array('Elec', 'Dual'))) ? $plan['Plan']['elec_rate']['distributor'] : '',
                    (in_array($plan_type, array('Gas', 'Dual'))) ? $plan['Plan']['gas_rate']['distributor'] : '',
                    (in_array($plan_type, array('Elec', 'Dual'))) ? '$'.$plan['Plan']['total_inc_discount_elec'] : '',
                    (in_array($plan_type, array('Gas', 'Dual'))) ? '$'.$plan['Plan']['total_inc_discount_gas'] : '',
                    $i,
                );
                $lines[] = implode(',', $line);
            }
            $content = implode("\n", $lines);
            // disable caching
            $last_modified = gmdate("D, d M Y H:i:s");
            $time = date('YmdHis');
            $filename = "plans_{$time}.csv";
            
            header("Expires: Tue, 01 Jan 2001 00:00:01 GMT");
            header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
            header("Last-Modified: {$last_modified} GMT");
        
            // force download  
            header("Content-Type: application/force-download");
            header("Content-Type: application/octet-stream");
            header("Content-Type: application/download");
            header('Content-Type: text/x-csv');
            header("Content-Disposition: attachment;filename={$filename}");
            header("Content-Transfer-Encoding: binary");
            header("Connection: close");
            echo $content;
            exit;
        }
        
        $states = array(
    	    'VIC' => 'Victoria', 
    	    'NSW' => 'New South Wales',
    	    'QLD' => 'Queenslan',
    	    'SA' => 'South Australia'
        );
    	$plan_types = array(
    	    'Elec' => 'Electricity',
    	    'Gas' => 'Gas',
    	    'Dual' => 'Electricity & Gas'
        );
    	$customer_types = array(
    	    'RES' => 'Residential',
    	    'SME' => 'Business'
        );
    	$consumption_levels = array(
    	    'Low' => 'Low',
    	    'Medium' => 'Medium',
    	    'High' => 'High'
        );
    	$this->set(compact('plan_types', 'customer_types', 'consumption_levels', 'states'));
	}
	
	public function export2() {
    	$this->redirect( '/v4/export3' );

    	$this->set('title_for_layout', 'Export');
    	
    	if ($this->request->is('post')) {
        	if (!empty($this->request->data['Export']['csv']['tmp_name']) && is_uploaded_file($this->request->data['Export']['csv']['tmp_name'])) {
                $temp = explode('.', $this->request->data['Export']['csv']['name']);
                $filename = md5(uniqid()) . '.' . end($temp);
                move_uploaded_file($this->data['Export']['csv']['tmp_name'], WWW_ROOT . DS . 'csv' . DS . $filename);
                $csv = array_map('str_getcsv', file(WWW_ROOT . DS . 'csv' . DS . $filename));
                $lines = array();
                $lines[] = 'State,Elec Distributor,Postcode,Suburb,NMI,RES Tariff, SME Tariff, Plan Type,Customer Type,Consumption Level,Retailer,Product Name,Electricity Distributor,Gas Distributor,Estimated Electricity Cost,Estimated Gas Cost,Rankings (Inc Discounts)';
                $i = 0;
                foreach ($csv as $value) {
                    $i++;
                    if ($i == 1) {
                        continue;
                    }
                    $plan_types = array('Elec', 'Gas', 'Dual');
                    $consumption_levels = array('Low', 'Medium', 'High');
                    foreach ($plan_types as $plan_type) {
                        foreach ($consumption_levels as $consumption_level) {
                            $customer_type = ($value[5]) ? 'RES' : 'SME';
                            $tariff_code = ($value[5]) ? $value[5] : $value[6];
                            $data = array(
                                'state' => $value[0],
                                'postcode' => $value[2],
                                'suburb' => $value[3],
                                'nmi' => $value[4],
                                'tariff_code' => $tariff_code,
                                'plan_type' => $plan_type,
                                'customer_type' => $customer_type,
                                'consumption_level' => $consumption_level,
                            );
                            $plans = $this->get_plans($data);
                            $j = 0;
                            foreach ($plans as $plan) {
                                $j++;
                                $line = array(
                                    $value[0],
                                    $value[1],
                                    $value[2],
                                    $value[3],
                                    $value[4],
                                    $value[5],
                                    $value[6],
                                    $plan_type,
                                    $customer_type,
                                    $consumption_level,
                                    $plan['Plan']['retailer'],
                                    '"'.$plan['Plan']['product_name'].'"',
                                    (in_array($plan_type, array('Elec', 'Dual'))) ? $plan['Plan']['elec_rate']['distributor'] : '',
                                    (in_array($plan_type, array('Gas', 'Dual'))) ? $plan['Plan']['gas_rate']['distributor'] : '',
                                    (in_array($plan_type, array('Elec', 'Dual'))) ? '$'.$plan['Plan']['total_inc_discount_elec'] : '',
                                    (in_array($plan_type, array('Gas', 'Dual'))) ? '$'.$plan['Plan']['total_inc_discount_gas'] : '',
                                    $j,
                                );
                                $lines[] = implode(',', $line);
                            }
                        }
                    }
                }
                $content = implode("\n", $lines);
                // disable caching
                $last_modified = gmdate("D, d M Y H:i:s");
                $time = date('YmdHis');
                $filename = "plans_{$time}.csv";
                
                header("Expires: Tue, 01 Jan 2001 00:00:01 GMT");
                header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
                header("Last-Modified: {$last_modified} GMT");
            
                // force download  
                header("Content-Type: application/force-download");
                header("Content-Type: application/octet-stream");
                header("Content-Type: application/download");
                header('Content-Type: text/x-csv');
                header("Content-Disposition: attachment;filename={$filename}");
                header("Content-Transfer-Encoding: binary");
                header("Connection: close");
                echo $content;
                exit;
            }
        }
	}
	
	public function export3() {
        $spreadsheet_url = 'https://docs.google.com/spreadsheets/d/12bk3Q4fd0mXq_DzHPz5mhozUdS50az0b0lknILlKgQo/pub?single=true&gid=1456169634&output=csv';
        $csv = array();
        if (($handle = fopen($spreadsheet_url, "r")) !== false) {
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                $csv[] = $data;
            }
            fclose($handle);
        }
        
        $lines = array();
        $lines[] = 'State,Elec Distributor,Postcode,Suburb,NMI,RES Tariff, SME Tariff, Plan Type,Customer Type,Consumption Level,Retailer,Product Name,Product Description,Electricity Distributor,Gas Distributor,BD:Estimated Electricity Cost,BD:Estimated Gas Cost,BD:Total Cost,BD:Rankings (Inc Discounts),AD:Estimated Electricity Cost,AD:Estimated Gas Cost,AD:Total Cost,AD:Rankings (Inc Discounts), Ranking 1,Ranking 2,Ranking 3';

        $i = 0;
        foreach ($csv as $value) {
            $i++;
            if ($i == 1) {
                continue;
            }
            $plan_types = array('Elec', 'Gas', 'Dual');
            $consumption_levels = array('Low', 'Medium', 'High');
            foreach ($plan_types as $plan_type) {
                foreach ($consumption_levels as $consumption_level) {
                    $customer_type = ($value[5]) ? 'RES' : 'SME';
                    $tariff_code = ($value[5]) ? $value[5] : $value[6];
                    $data = array(
                        'state' => $value[0],
                        'postcode' => $value[2],
                        'suburb' => $value[3],
                        'nmi' => $value[4],
                        'tariff_code' => $tariff_code,
                        'plan_type' => $plan_type,
                        'customer_type' => $customer_type,
                        'consumption_level' => $consumption_level,
                    );
                    $plans = $this->get_plans($data);
                    $plans = array_values($plans);
                    $cost1 = $cost2 = $cost3 = '';
                    if (in_array($plan_type, array('Elec', 'Gas'))) {
                        if (isset($plans[0])) {
                            $cost1 = ($plan_type == 'Elec') ? $plans[0]['Plan']['total_inc_discount_elec'] : $plans[0]['Plan']['total_inc_discount_gas'];
                        }
                        if (isset($plans[1])) {
                            $cost2 = ($plan_type == 'Elec') ? $plans[1]['Plan']['total_inc_discount_elec'] : $plans[1]['Plan']['total_inc_discount_gas'];
                        }
                        if (isset($plans[2])) {
                            $cost3 = ($plan_type == 'Elec') ? $plans[2]['Plan']['total_inc_discount_elec'] : $plans[2]['Plan']['total_inc_discount_gas'];
                        }
                    }
                    elseif (in_array($plan_type, array('Dual'))) {
                        if (isset($plans[0])) {
                            $cost1 = $plans[0]['Plan']['total_inc_discount_elec'] + $plans[0]['Plan']['total_inc_discount_gas'];
                        }
                        if (isset($plans[1])) {
                            $cost2 = $plans[1]['Plan']['total_inc_discount_elec'] + $plans[1]['Plan']['total_inc_discount_gas'];
                        }
                        if (isset($plans[2])) {
                            $cost3 = $plans[2]['Plan']['total_inc_discount_elec'] + $plans[2]['Plan']['total_inc_discount_gas'];
                        }
                    }
                    $j = 0;
                    foreach ($plans as $plan) {
                        $j++;
                        $ranking1 = $ranking2 = $ranking3 = '';
                        if (in_array($plan_type, array('Elec', 'Gas'))) {
                            $cost = ($plan_type == 'Elec') ? $plan['Plan']['total_inc_discount_elec'] : $plan['Plan']['total_inc_discount_gas'];
                            $ranking1 = round((($cost - $cost1) / $cost) * 100, 2)."%";
                            $ranking2 = ($cost2) ? round((($cost - $cost2) / $cost) * 100, 2)."%" : '';
                            $ranking3 = ($cost3) ? round((($cost - $cost3) / $cost) * 100, 2)."%" : '';
                        }
                        elseif (in_array($plan_type, array('Dual'))) {
                            $cost = $plan['Plan']['total_inc_discount_elec'] + $plan['Plan']['total_inc_discount_gas'];
                            $ranking1 = round((($cost - $cost1) / $cost) * 100, 2)."%";
                            $ranking2 = ($cost2) ? round((($cost - $cost2) / $cost) * 100, 2)."%" : '';
                            $ranking3 = ($cost3) ? round((($cost - $cost3) / $cost) * 100, 2)."%" : '';
                        }
                        $line = array(
                            $value[0],
                            $value[1],
                            $value[2],
                            $value[3],
                            $value[4],
                            $value[5],
                            $value[6],
                            $plan_type,
                            $customer_type,
                            $consumption_level,
                            $plan['Plan']['retailer'],
                            '"'.$plan['Plan']['product_name'].'"',
                            '"'.$plan['Plan']['product_summary'].'"',
                            (in_array($plan_type, array('Elec', 'Dual'))) ? $plan['Plan']['elec_rate']['distributor'] : '',
                            (in_array($plan_type, array('Gas', 'Dual'))) ? $plan['Plan']['gas_rate']['distributor'] : '',
                            (in_array($plan_type, array('Elec', 'Dual'))) ? '$'.$plan['Plan']['total_inc_discount_guaranteed_elec'] : '',
                            (in_array($plan_type, array('Gas', 'Dual'))) ? '$'.$plan['Plan']['total_inc_discount_guaranteed_gas'] : '',
                            '$'.($plan['Plan']['total_inc_discount_guaranteed_elec'] + $plan['Plan']['total_inc_discount_guaranteed_gas']),
                            $j,
                            (in_array($plan_type, array('Elec', 'Dual'))) ? '$'.$plan['Plan']['total_inc_discount_elec'] : '',
                            (in_array($plan_type, array('Gas', 'Dual'))) ? '$'.$plan['Plan']['total_inc_discount_gas'] : '',
                            '$'.($plan['Plan']['total_inc_discount_elec'] + $plan['Plan']['total_inc_discount_gas']),
                            $j,
                            $ranking1,
                            $ranking2,
                            $ranking3,
                        );
                        $lines[] = implode(',', $line);
                    }
                }
            }
        }
        $content = implode("\n", $lines);
        // disable caching
        $last_modified = gmdate("D, d M Y H:i:s");
        $time = date('YmdHis');
        $filename = "plans_{$time}.csv";
        
        header("Expires: Tue, 01 Jan 2001 00:00:01 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$last_modified} GMT");
    
        // force download  
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Type: text/x-csv');
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary");
        header("Connection: close");
        echo $content;
        exit;
	}
	
	private function get_plans($data) {
	    $state = $data['state'];
    	$postcode = $data['postcode'];
    	$suburb = ucwords(strtolower($data['suburb']));
    	$plan_type = $data['plan_type'];
    	$customer_type = $data['customer_type'];
    	$nmi = $data['nmi'];
    	$tariff_code = $data['tariff_code'];
    	//$distributor = $data['distributor'];
    	$consumption_level = $data['consumption_level'];
        	
    	$plans = array();
    	
    	$states_arr = unserialize(AU_STATES);
    	
	    $conditions = array();
        $conditions['Plan.state'] = $states_arr[$state];
		$conditions['Plan.package'] = $plan_type;
		$conditions['Plan.res_sme'] = $customer_type;
		$conditions['Plan.version'] = array('All', '4', '5');
		$conditions['Plan.status'] = 'Active';
        $plan_expiry_or = array(
            'or' => array(
            	'Plan.plan_expiry' => '0000-00-00',
            	'Plan.plan_expiry >' => date('Y-m-d'),
            ),
        );
        $conditions[] = $plan_expiry_or;
        
        $default_consumption = $this->Consumption->findByStateAndResSme($state, $customer_type);
        if (in_array($plan_type, array('Elec', 'Dual'))) {
            $distributor_elec = $this->ElectricityPostcodeDistributor->findByPostcodeAndSuburb($postcode, $suburb); 
            
            $nmi_mapping = $this->ElectricityNmiDistributor->findByNmi(strtoupper(substr($nmi, 0, 2)));
			if ($nmi_mapping) {
			    $nmi_distributor = $nmi_mapping['ElectricityNmiDistributor']['distributor'];
			}
            
            $default_peak = explode('/', $default_consumption['Consumption']['elec_peak']);
            $elec_billing_days = $default_consumption['Consumption']['elec_billing_days'];
            //Single Rate
            switch ($consumption_level) {
			    case 'Low':
			    	$elec_peak = $default_peak[0];
			    break;
			    case 'Medium':
			    	$elec_peak = $default_peak[1];
			    break;
			    case 'High':
			    	$elec_peak = $default_peak[2];
			    break;
			}

            $tariff = $this->Tariff->find('first', array(
		    	'conditions' => array(
		    		'Tariff.tariff_code' => $tariff_code,
		    		'Tariff.res_sme' => $customer_type,
		    		'Tariff.distributor' => explode('/', $nmi_distributor),
		    	),
		    ));
		    $solar_rebate_scheme = $tariff['Tariff']['solar_rebate_scheme'];
		    
	        if ($solar_specific_plan) {
				$conditions['Plan.solar_specific_plan !='] = 'Not Solar';
			} else {
				$conditions['Plan.solar_specific_plan !='] = 'Solar Only';
			}
        }
        if (in_array($plan_type, array('Gas', 'Dual'))) {
            $distributor_gas = $this->GasPostcodeDistributor->findByPostcodeAndSuburb($postcode, $suburb);
            
            $gas_billing_days = $default_consumption['Consumption']['gas_billing_days'];
			$gas_billing_start = $default_consumption['Consumption']['gas_billing_start'];
			$default_peak = explode('/', $default_consumption['Consumption']['gas_peak']);
			switch ($consumption_level) {
			    case 'Low':
			    	$gas_peak = $default_peak[0];
			    break;
			    case 'Medium':
			    	$gas_peak = $default_peak[1];
			    break;
			    case 'High':
			    	$gas_peak = $default_peak[2];
			    break;
			}
        }
        $distributor_retailer_arr = array();
        if ($plan_type == 'Elec') {
            if ($distributor_elec) {
                if ($distributor_elec['ElectricityPostcodeDistributor']['agl_distributor']) {
					$distributor_retailer_arr[] = 'AGL';
					if ($state == 'VIC' && $customer_type == 'RES') {
						if ($nmi && in_array(substr($nmi, 0, 2), array(60, 61, 62))) {
                            $conditions['Plan.version'][] = 'AGL Savers (Citipower, Jemena & Powercor)';
                        }
                    }
                    if ($state == 'NSW' && $customer_type == 'RES') {
                        if ($nmi && in_array(substr($nmi, 0, 2), array(40, 42, 44, 45))) {
                            $conditions['Plan.version'][] = 'AGL Savers (Essential Energy)';
                        }
                    }
                    if ($state == 'VIC' && $customer_type == 'SME') {
						if ($nmi && in_array(substr($nmi, 0, 2), array(61, 62))) {
                            $conditions['Plan.version'][] = 'Business Savers Powercor & Citipower';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(60, 63))) {
                            $conditions['Plan.version'][] = 'Business Savers Jemena & Ausnet';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(64))) {
                            $conditions['Plan.version'][] = 'Business Savers United';
                        }
                    }
                    if ($state == 'NSW' && $customer_type == 'SME') {
                        if ($nmi && in_array(substr($nmi, 0, 2), array(40, 42, 44, 45))) {
                            $conditions['Plan.version'][] = 'Business Savers Essential';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(41))) {
                            $conditions['Plan.version'][] = 'Business Savers Ausgrid';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(43))) {
                            $conditions['Plan.version'][] = 'Business Savers Endeavour';
                        }
                    }
				}
				if ($distributor_elec['ElectricityPostcodeDistributor']['powerdirect_distributor']) {
					$distributor_retailer_arr[] = 'Powerdirect';
					if ($state == 'VIC' && $customer_type == 'RES') {
				        if ($nmi && in_array(substr($nmi, 0, 4), array(6102, 6001, 6203))) {
                            $conditions['Plan.version'][] = 'Residential (Citipower, Jemena & Powercor)';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 4), array(6407, 6305))) {
                            $conditions['Plan.version'][] = 'Residential (United & SP Ausnet)';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(60, 61, 64))) {
                            $conditions['Plan.version'][] = 'Citipower, Jemena & Powercor';
                        }
				    }
				}
				if ($distributor_elec['ElectricityPostcodeDistributor']['origin_energy_distributor']) {
					$distributor_retailer_arr[] = 'Origin Energy';
					if ($distributor_elec['ElectricityPostcodeDistributor']['origin_energy_special_product_name']) {
					    $conditions['Plan.version'][] = '4 (Special)';
				    }
				    if ($distributor_elec['ElectricityPostcodeDistributor']['origin_energy_origin_saver_patch']) {
					    $conditions['Plan.version'][] = 'Origin Saver Patch';
                        if ($nmi && in_array(substr($nmi, 0, 2), array(40, 42, 44, 45)) && $distributor_elec['ElectricityPostcodeDistributor']['origin_energy_origin_saver_patch']) {
						    $conditions['Plan.version'][] = 'Origin Saver Essential Patch';
					    }
					    if ($nmi && in_array(substr($nmi, 0, 2), array(62, 64)) && $distributor_elec['ElectricityPostcodeDistributor']['origin_energy_origin_saver_patch']) {
						    $conditions['Plan.version'][] = 'Origin Saver Essential Patch VIC';
					    }
				    }
				    if ($distributor_elec['ElectricityPostcodeDistributor']['origin_energy_businesssaver_hv']) {
					    $conditions['Plan.version'][] = 'BusinessSaver HV';
				    }
				    
				    if ($nmi && in_array(substr($nmi, 0, 2), array(40, 42, 44, 45))) {
                        $conditions['Plan.version'][] = 'Origin Saver (Essential Energy)'; 
                    }
                    if ($nmi && in_array(substr($nmi, 0, 2), array(41, 43))) {
                        $conditions['Plan.version'][] = 'Origin Saver (Ausgrid & Endeavour)';
                    }
                    if ($nmi && in_array(substr($nmi, 0, 2), array(60, 61))) {
                        $conditions['Plan.version'][] = 'Origin Saver (Jemena & Citipower)';
                    }
                    if ($nmi && in_array(substr($nmi, 0, 2), array(62, 64))) {
                        $conditions['Plan.version'][] = 'Origin Saver (Powercor & United)';
                    }
                    
                    if ($state == 'VIC' && $customer_type == 'RES') {
                        if ($nmi && in_array(substr($nmi, 0, 2), array(63))) {
                            $conditions['Plan.version'][] = 'Origin Saver (Ausnet)';
                        }
                    }
                    
                    if ($state == 'NSW' && $customer_type == 'SME') {
                        if ($nmi && in_array(substr($nmi, 0, 2), array(41))) {
                            $conditions['Plan.version'][] = 'BusinessSaver Ausgrid';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(43))) {
                            $conditions['Plan.version'][] = 'BusinessSaver Endeavour Energy';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(40, 42, 44, 45))) {
                            $conditions['Plan.version'][] = 'BusinessSaver Essential Energy';
                        }
                    }
                    if ($state == 'VIC' && $customer_type == 'SME') {
                        if ($nmi && in_array(substr($nmi, 0, 2), array(63))) {
                            $conditions['Plan.version'][] = 'BusinessSaver Ausnet';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(60, 61, 62, 64))) {
                            $conditions['Plan.version'][] = 'BusinessSaver Citipower, Powercor, Jemena & United';
                        }
                    }
				}
				if ($distributor_elec['ElectricityPostcodeDistributor']['lumo_energy_distributor']) {
					$distributor_retailer_arr[] = 'Lumo Energy';
				}
				if ($distributor_elec['ElectricityPostcodeDistributor']['red_energy_distributor']) {
					$distributor_retailer_arr[] = 'Red Energy';
				}
				if ($distributor_elec['ElectricityPostcodeDistributor']['momentum_distributor']) {
					$distributor_retailer_arr[] = 'Momentum';
				}
				if ($distributor_elec['ElectricityPostcodeDistributor']['powershop_distributor']) {
					$distributor_retailer_arr[] = 'Powershop';
				}
				if ($distributor_elec['ElectricityPostcodeDistributor']['dodo_distributor']) {
					$distributor_retailer_arr[] = 'Dodo';
				}
				if ($distributor_elec['ElectricityPostcodeDistributor']['alinta_energy_distributor']) {
					$distributor_retailer_arr[] = 'Alinta Energy';
				}
				if ($distributor_elec['ElectricityPostcodeDistributor']['energy_australia_distributor']) {
					$distributor_retailer_arr[] = 'Energy Australia';
					if ($state == 'VIC' && $customer_type == 'SME') {
                        if ($nmi) {
                            switch (substr($nmi, 0, 2)) {
                                case '60':
                                    $conditions['Plan.version'][] = 'Everyday Saver Business Jemena';
                                    $conditions['Plan.version'][] = 'Business Saver Business Jemena';
                                    break;
                                case '61':
                                    $conditions['Plan.version'][] = 'Everyday Saver Business Citipower';
                                    $conditions['Plan.version'][] = 'Business Saver Business Citipower';
                                    break;
                                case '62':
                                    $conditions['Plan.version'][] = 'Everyday Saver Business Powercor';
                                    $conditions['Plan.version'][] = 'Business Saver Business Powercor';
                                    break;
                                case '63':
                                    $conditions['Plan.version'][] = 'Everyday Saver Business Ausnet';
                                    $conditions['Plan.version'][] = 'Business Saver Business Ausnet';
                                    break;
                                case '64':
                                    $conditions['Plan.version'][] = 'Everyday Saver Business United Energy';
                                    $conditions['Plan.version'][] = 'Business Saver Business United';
                                    break;
                            }
                        }
			        }
			        if ($state == 'NSW' && $customer_type == 'SME') {
                        if ($nmi && in_array(substr($nmi, 0, 2), array(40, 42, 43, 44, 45))) {
                            $conditions['Plan.version'][] = 'Everyday Saver Business (Essential & Endeavour Energy)';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(41))) {
                            $conditions['Plan.version'][] = 'Everyday Saver Business (Ausgrid)';
                        }
				    }
			        if ($state == 'VIC' && $customer_type == 'RES') {
				        if ($nmi && in_array(substr($nmi, 0, 2), array(60, 63))) {
                            $conditions['Plan.version'][] = 'Flexi Saver (Jemena & AusNet)';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(61, 62, 64))) {
                            $conditions['Plan.version'][] = 'Flexi Saver (Powercor, Citipower & United)';
                        }
				    }
				    if ($state == 'NSW' && $customer_type == 'RES') {
				        if ($nmi && in_array(substr($nmi, 0, 2), array(43))) {
                            $conditions['Plan.version'][] = 'Flexi Saver (Endeavour)';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(40, 42, 44, 45))) {
                            $conditions['Plan.version'][] = 'Flexi Saver (Essential)';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(41))) {
                            $conditions['Plan.version'][] = 'Flexi Saver (Ausgrid)';
                        }
				    }
				}
				if ($distributor_elec['ElectricityPostcodeDistributor']['sumo_power_distributor']) {
					$distributor_retailer_arr[] = 'Sumo Power';
				}
				if ($distributor_elec['ElectricityPostcodeDistributor']['erm_distributor']) {
					$distributor_retailer_arr[] = 'ERM';
			    }
			    if ($distributor_elec['ElectricityPostcodeDistributor']['next_business_energy_distributor']) {
					$distributor_retailer_arr[] = 'Next Business Energy';
			    }
			}
		} elseif ($plan_type == 'Gas') {
			if ($distributor_gas) {
				if ($distributor_gas['GasPostcodeDistributor']['agl_distributor']) {
					$distributor_retailer_arr[] = 'AGL';
				}
				if ($distributor_gas['GasPostcodeDistributor']['origin_energy_distributor']) {
					$distributor_retailer_arr[] = 'Origin Energy';
				}
				if ($distributor_gas['GasPostcodeDistributor']['lumo_energy_distributor']) {
					$distributor_retailer_arr[] = 'Lumo Energy';
				}
				if ($distributor_gas['GasPostcodeDistributor']['red_energy_distributor']) {
					$distributor_retailer_arr[] = 'Red Energy';
				}
				if ($distributor_gas['GasPostcodeDistributor']['momentum_distributor']) {
					$distributor_retailer_arr[] = 'Momentum';
				}
				if ($distributor_gas['GasPostcodeDistributor']['dodo_distributor']) {
					$distributor_retailer_arr[] = 'Dodo';
				}
				if ($distributor_gas['GasPostcodeDistributor']['alinta_energy_distributor']) {
					$distributor_retailer_arr[] = 'Alinta Energy';
				}
				if ($distributor_gas['GasPostcodeDistributor']['energy_australia_distributor']) {
					$distributor_retailer_arr[] = 'Energy Australia';
				}
			}
		} elseif ($plan_type == 'Dual') {
		    if ($distributor_elec && $distributor_gas) {
				if ($distributor_elec['ElectricityPostcodeDistributor']['agl_distributor'] && $distributor_gas['GasPostcodeDistributor']['agl_distributor']) {
					$distributor_retailer_arr[] = 'AGL';
					if ($state == 'VIC' && $customer_type == 'RES') {
						if ($nmi && in_array(substr($nmi, 0, 2), array(60, 61, 62))) {
                            $conditions['Plan.version'][] = 'AGL Savers (Citipower, Jemena & Powercor)';
                        }
                    }
                    if ($state == 'NSW' && $customer_type == 'RES') {
                        if ($nmi && in_array(substr($nmi, 0, 2), array(40, 42, 44, 45))) {
                            $conditions['Plan.version'][] = 'AGL Savers (Essential Energy)';
                        }
                    }
                    if ($state == 'VIC' && $customer_type == 'SME') {
						if ($nmi && in_array(substr($nmi, 0, 2), array(61, 62))) {
                            $conditions['Plan.version'][] = 'Business Savers Powercor & Citipower';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(60, 63))) {
                            $conditions['Plan.version'][] = 'Business Savers Jemena & Ausnet';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(64))) {
                            $conditions['Plan.version'][] = 'Business Savers United';
                        }
                    }
                    if ($state == 'NSW' && $customer_type == 'SME') {
                        if ($nmi && in_array(substr($nmi, 0, 2), array(40, 42, 44, 45))) {
                            $conditions['Plan.version'][] = 'Business Savers Essential';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(41))) {
                            $conditions['Plan.version'][] = 'Business Savers Ausgrid';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(43))) {
                            $conditions['Plan.version'][] = 'Business Savers Endeavour';
                        }
                    }
				}
				if ($distributor_elec['ElectricityPostcodeDistributor']['origin_energy_distributor'] && $distributor_gas['GasPostcodeDistributor']['origin_energy_distributor']) {
					$distributor_retailer_arr[] = 'Origin Energy';
					if ($distributor_elec['ElectricityPostcodeDistributor']['origin_energy_special_product_name']) {
					    $conditions['Plan.version'][] = '4 (Special)';
				    }
				    if ($distributor_elec['ElectricityPostcodeDistributor']['origin_energy_origin_saver_patch'] || $distributor_gas['GasPostcodeDistributor']['origin_energy_origin_saver_patch']) {
					    $conditions['Plan.version'][] = 'Origin Saver Patch';
                        if ($nmi && in_array(substr($nmi, 0, 2), array(40, 42, 44, 45)) && $distributor_elec['ElectricityPostcodeDistributor']['origin_energy_origin_saver_patch']) {
						    $conditions['Plan.version'][] = 'Origin Saver Essential Patch';
					    }
					    if ($nmi && in_array(substr($nmi, 0, 2), array(62, 64)) && $distributor_elec['ElectricityPostcodeDistributor']['origin_energy_origin_saver_patch']) {
						    $conditions['Plan.version'][] = 'Origin Saver Essential Patch VIC';
					    }
				    }
				    if ($distributor_elec['ElectricityPostcodeDistributor']['origin_energy_businesssaver_hv']) {
					    $conditions['Plan.version'][] = 'BusinessSaver HV';
				    }
				    
				    if ($nmi && in_array(substr($nmi, 0, 2), array(40, 42, 44, 45))) {
                        $conditions['Plan.version'][] = 'Origin Saver (Essential Energy)'; 
                    }
                    if ($nmi && in_array(substr($nmi, 0, 2), array(41, 43))) {
                        $conditions['Plan.version'][] = 'Origin Saver (Ausgrid & Endeavour)';
                    }
                    if ($nmi && in_array(substr($nmi, 0, 2), array(60, 61))) {
                        $conditions['Plan.version'][] = 'Origin Saver (Jemena & Citipower)';
                    }
                    if ($nmi && in_array(substr($nmi, 0, 2), array(62, 64))) {
                        $conditions['Plan.version'][] = 'Origin Saver (Powercor & United)';
                    }
                    
                    if ($state == 'VIC' && $customer_type == 'RES') {
                        if ($nmi && in_array(substr($nmi, 0, 2), array(63))) {
                            $conditions['Plan.version'][] = 'Origin Saver (Ausnet)';
                        }
                    }
                    
                    if ($state == 'NSW' && $customer_type == 'SME') {
                        if ($nmi && in_array(substr($nmi, 0, 2), array(41))) {
                            $conditions['Plan.version'][] = 'BusinessSaver Ausgrid';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(43))) {
                            $conditions['Plan.version'][] = 'BusinessSaver Endeavour Energy';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(40, 42, 44, 45))) {
                            $conditions['Plan.version'][] = 'BusinessSaver Essential Energy';
                        }
                    }
                    if ($state == 'VIC' && $customer_type == 'SME') {
                        if ($nmi && in_array(substr($nmi, 0, 2), array(63))) {
                            $conditions['Plan.version'][] = 'BusinessSaver Ausnet';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(60, 61, 62, 64))) {
                            $conditions['Plan.version'][] = 'BusinessSaver Citipower, Powercor, Jemena & United';
                        }
                    }
				}
				if ($distributor_elec['ElectricityPostcodeDistributor']['lumo_energy_distributor'] && $distributor_gas['GasPostcodeDistributor']['lumo_energy_distributor']) {
					$distributor_retailer_arr[] = 'Lumo Energy';
				}
				if ($distributor_elec['ElectricityPostcodeDistributor']['red_energy_distributor'] && $distributor_gas['GasPostcodeDistributor']['red_energy_distributor']) {
					$distributor_retailer_arr[] = 'Red Energy';
				}
				if ($distributor_elec['ElectricityPostcodeDistributor']['momentum_distributor'] && $distributor_gas['GasPostcodeDistributor']['momentum_distributor']) {
					$distributor_retailer_arr[] = 'Momentum';
				}
				if ($distributor_elec['ElectricityPostcodeDistributor']['dodo_distributor'] && $distributor_gas['GasPostcodeDistributor']['dodo_distributor']) {
					$distributor_retailer_arr[] = 'Dodo';
				}
				if ($distributor_elec['ElectricityPostcodeDistributor']['alinta_energy_distributor'] && $distributor_gas['GasPostcodeDistributor']['alinta_energy_distributor']) {
					$distributor_retailer_arr[] = 'Alinta Energy';
				}
				if ($distributor_elec['ElectricityPostcodeDistributor']['energy_australia_distributor'] && $distributor_gas['GasPostcodeDistributor']['energy_australia_distributor']) {
					$distributor_retailer_arr[] = 'Energy Australia';
					if ($state == 'VIC' && $customer_type == 'SME') {
                        if ($nmi) {
                            switch (substr($nmi, 0, 2)) {
                                case '60':
                                    $conditions['Plan.version'][] = 'Everyday Saver Business Jemena';
                                    $conditions['Plan.version'][] = 'Business Saver Business Jemena';
                                    break;
                                case '61':
                                    $conditions['Plan.version'][] = 'Everyday Saver Business Citipower';
                                    $conditions['Plan.version'][] = 'Business Saver Business Citipower';
                                    break;
                                case '62':
                                    $conditions['Plan.version'][] = 'Everyday Saver Business Powercor';
                                    $conditions['Plan.version'][] = 'Business Saver Business Powercor';
                                    break;
                                case '63':
                                    $conditions['Plan.version'][] = 'Everyday Saver Business Ausnet';
                                    $conditions['Plan.version'][] = 'Business Saver Business Ausnet';
                                    break;
                                case '64':
                                    $conditions['Plan.version'][] = 'Everyday Saver Business United Energy';
                                    $conditions['Plan.version'][] = 'Business Saver Business United';
                                    break;
                            }
                        }
			        }
			        if ($state == 'NSW' && $customer_type == 'SME') {
                        if ($nmi && in_array(substr($nmi, 0, 2), array(40, 42, 43, 44, 45))) {
                            $conditions['Plan.version'][] = 'Everyday Saver Business (Essential & Endeavour Energy)';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(41))) {
                            $conditions['Plan.version'][] = 'Everyday Saver Business (Ausgrid)';
                        }
				    }
			        if ($state == 'VIC' && $customer_type == 'RES') {
				        if ($nmi && in_array(substr($nmi, 0, 2), array(60, 63))) {
                            $conditions['Plan.version'][] = 'Flexi Saver (Jemena & AusNet)';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(61, 62, 64))) {
                            $conditions['Plan.version'][] = 'Flexi Saver (Powercor, Citipower & United)';
                        }
				    }
				    if ($state == 'NSW' && $customer_type == 'RES') {
				        if ($nmi && in_array(substr($nmi, 0, 2), array(43))) {
                            $conditions['Plan.version'][] = 'Flexi Saver (Endeavour)';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(40, 42, 44, 45))) {
                            $conditions['Plan.version'][] = 'Flexi Saver (Essential)';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(41))) {
                            $conditions['Plan.version'][] = 'Flexi Saver (Ausgrid)';
                        }
				    }
				}
				if ($distributor_elec['ElectricityPostcodeDistributor']['pd_agl_distributor'] && $distributor_gas['GasPostcodeDistributor']['pd_agl_distributor']) {
					$distributor_retailer_arr[] = 'Powerdirect and AGL';
			    }
			}
		}
		
        if (in_array($plan_type, array('Elec', 'Dual'))) {
			$tariff = $this->Tariff->find('first', array(
			    'conditions' => array(
			    	'Tariff.tariff_code' => $tariff_code,
			    	'Tariff.res_sme' => $customer_type,
			    	'Tariff.distributor' => explode('/', $nmi_distributor),
			    ),
			));
			if ($tariff['Tariff']['powershop_unsupported_tariff'] == 'Unsupported') {
			    if (($key = array_search('Powershop', $retailer_arr)) !== false) {
			    	unset($distributor_retailer_arr[$key]);
			    }
			}
			if ($tariff['Tariff']['red_energy_unsupported_tariff'] == 'Unsupported') {
			    if (($key = array_search('Red Energy', $retailer_arr)) !== false) {
			    	unset($distributor_retailer_arr[$key]);
			    }
			}
			if ($tariff['Tariff']['powerdirect_unsupported_tariff'] == 'Unsupported') {
			    if (($key = array_search('Powerdirect', $retailer_arr)) !== false) {
			    	unset($distributor_retailer_arr[$key]);
			    }
			}
			if ($tariff['Tariff']['momentum_unsupported_tariff'] == 'Unsupported') {
			    if (($key = array_search('Momentum', $retailer_arr)) !== false) {
			    	unset($distributor_retailer_arr[$key]);
			    }
			}
			if ($tariff['Tariff']['sumo_power_unsupported_tariff'] == 'Unsupported') {
			    if (($key = array_search('Sumo Power', $retailer_arr)) !== false) {
			    	unset($distributor_retailer_arr[$key]);
			    }
			}
			if ($tariff['Tariff']['erm_unsupported_tariff'] == 'Unsupported') {
			    if (($key = array_search('ERM', $retailer_arr)) !== false) {
			    	unset($distributor_retailer_arr[$key]);
			    }
			}
			if ($tariff['Tariff']['pd_agl_unsupported_tariff'] == 'Unsupported') {
			    if (($key = array_search('Powerdirect and AGL', $retailer_arr)) !== false) {
			    	unset($distributor_retailer_arr[$key]);
			    }
			}
		}
		$conditions['Plan.retailer'] = $distributor_retailer_arr;
        
        $order[] = 'Plan.retailer ASC';
        
        $plans_temp = $this->Plan->find('all', array(
			'conditions' => $conditions,
			'order' => $order
        ));
        
		foreach ($plans_temp as $key => $plan) {
	        $plan['Plan']['discount_elec'] = 0;
			$plan['Plan']['discount_gas'] = 0;
			$plan['Plan']['total_elec'] = 0;
			$plan['Plan']['total_gas'] = 0;
            $plan['Plan']['total_inc_discount_elec'] = 0;
            $plan['Plan']['total_inc_discount_gas'] = 0;
            if (in_array($plan_type, array('Elec', 'Dual'))) {
                $conditions = array(
					'ElectricityRate.state' => $plan['Plan']['state'],
					'ElectricityRate.res_sme' => $plan['Plan']['res_sme'],
					'ElectricityRate.retailer' => $plan['Plan']['retailer'],
					'ElectricityRate.tariff_type' => 'Single Rate',
					'ElectricityRate.rate_name' => $plan['Plan']['rate_name'],
					'ElectricityRate.status' => 'Active',
				);
		        if ($distributor_elec) {
					switch ($plan['Plan']['retailer']) {
						case 'AGL':
							$distributor_field = 'agl_distributor';
							break;
						case 'Powerdirect':
							$distributor_field = 'powerdirect_distributor';
							break;
						case 'Origin Energy':
							$distributor_field = 'origin_energy_distributor';
							break;
						case 'Lumo Energy':
							$distributor_field = 'lumo_energy_distributor';
							break;
						case 'Red Energy':
							$distributor_field = 'red_energy_distributor';
							break;
						case 'Momentum':
							$distributor_field = 'momentum_distributor';
							break;
						case 'Powershop':
							$distributor_field = 'powershop_distributor';
							break;
						case 'Dodo':
							$distributor_field = 'dodo_distributor';
							break;
                        case 'Alinta Energy':
							$distributor_field = 'alinta_energy_distributor';
							break;
                        case 'Energy Australia':
							$distributor_field = 'energy_australia_distributor';
							break;
                        case 'Sumo Power':
							$distributor_field = 'sumo_power_distributor';
							break;
                        case 'ERM':
                            $distributor_field = 'erm_distributor';
                            break;
                        case 'Powerdirect and AGL':
                            $distributor_field = 'pd_agl_distributor';
                            break;
                        case 'Next Business Energy':
                            $distributor_field = 'next_business_energy_distributor';
                            break;
					}
					$distributors = explode('/', $distributor_elec['ElectricityPostcodeDistributor'][$distributor_field]);
				}
		        if ($nmi_distributor) {
				    if (strpos($nmi_distributor, '/') !== false) {
				    	switch ($nmi_distributor) {
				    		case 'Powercor/Powercor 1/Powercor 2':
				    			if ($plan['Plan']['retailer'] != 'Red Energy') {
				    				$distributors = array('Powercor');
				    			}
				    		break;
				    		case 'Essential Energy/Essential Energy - FW/Essential Energy - UR':
				    			if ($plan['Plan']['retailer'] == 'AGL') {
					    			if (!in_array($distributor_elec['ElectricityPostcodeDistributor'][$distributor_field], array('Essential Energy - FW', 'Essential Energy - UR'))) {
						    			$distributors = array('Essential Energy - UR');
					    			}
				    			} else {
					    			$distributors = array('Essential Energy');
				    			}
				    		break;
				    	}
				    } else {
				    	$distributors = explode('/', $nmi_distributor);
				    }
				}
				$conditions['ElectricityRate.distributor'] = $distributors;
		        if ($nmi_distributor && $tariff_code) {
				    $tariff = $this->Tariff->find('first', array(
				    	'conditions' => array(
				    		'Tariff.tariff_code' => $tariff_code,
				    		'Tariff.res_sme' => $customer_type,
				    		'Tariff.distributor' => explode('/', $nmi_distributor),
				    	),
				    ));
				    if ($tariff['Tariff']['tariff_class']) {
				    	$tariff_classes = array();
				    	$tariff_classes_temp = explode('/', $tariff['Tariff']['tariff_class']);
				    	foreach ($tariff_classes_temp as $tariff_class) {
					    	if ($tariff['Tariff']['pricing_group'] != 'Single Rate') {
					    		$tariff_classes[] = str_replace($tariff['Tariff']['pricing_group'], $tariff_class, 'Single Rate');
					    	} else {
								$tariff_classes[] = $tariff_class;
					    	}
				    	}
				    	$conditions['ElectricityRate.tariff_class'] = $tariff_classes;
				    }
				}
				$rates_cnt = $this->ElectricityRate->find('count', array(
					'conditions' => $conditions,
				));
				if ($rates_cnt == 0) {
					unset($conditions['ElectricityRate.tariff_class']);
					$conditions['ElectricityRate.tariff_type'] = 'Single Rate';
				}
		        $rates = $this->ElectricityRate->find('all', array(
					'conditions' => $conditions,
					'order' => 'ElectricityRate.id ASC'
				));
				if (!empty($rates)) {
					foreach ($rates as $rate) {
						$plan['Plan']['elec_rate'] = $rate['ElectricityRate'];
						$period = 0;
						switch ($rate['ElectricityRate']['rate_tier_period']) {
							case '2':
								$period = 60.83;
								break;
							case 'D':
								$period = 1;
								break;
							case 'M':
								$period = 30.42;
								break;
							case 'Q':
								$period = 91.25;
								break;
							case 'Y':
								$period = 365;
								break;
						}
						if ($period > 0) {
						    $rate['ElectricityRate']['peak_tier_1'] = ($rate['ElectricityRate']['peak_tier_1'] / $period) * $elec_billing_days;
							$rate['ElectricityRate']['peak_tier_2'] = ($rate['ElectricityRate']['peak_tier_2'] / $period) * $elec_billing_days;
							$rate['ElectricityRate']['peak_tier_3'] = ($rate['ElectricityRate']['peak_tier_3'] / $period) * $elec_billing_days;
							$rate['ElectricityRate']['peak_tier_4'] = ($rate['ElectricityRate']['peak_tier_4'] / $period) * $elec_billing_days;
						}
					    $tier_rates = array(
                	    	array('tier' => $rate['ElectricityRate']['peak_tier_1'], 'rate' => $rate['ElectricityRate']['peak_rate_1']),
							array('tier' => $rate['ElectricityRate']['peak_tier_2'], 'rate' => $rate['ElectricityRate']['peak_rate_2']),
							array('tier' => $rate['ElectricityRate']['peak_tier_3'], 'rate' => $rate['ElectricityRate']['peak_rate_3']),
							array('tier' => $rate['ElectricityRate']['peak_tier_4'], 'rate' => $rate['ElectricityRate']['peak_rate_4']),
							array('tier' => 0, 'rate' => $rate['ElectricityRate']['peak_rate_5'])
						);
                        $usage_sum = $peak_sum = $this->calculate($elec_peak, $tier_rates);
                        $stp_sum_elec = 0;
                        $discount_guaranteed_elec = 0;
                    	$discount_elec = 0;
                    	if ($rate['ElectricityRate']['stp_period'] == 'Y') {
                    	    $elec_billing = $elec_billing_days / 365;
                    	} 
                    	else if ($rate['ElectricityRate']['stp_period'] == 'Q') {
                    	    $elec_billing = $elec_billing_days / 91.25;
                    	} 
                    	else if ($rate['ElectricityRate']['stp_period'] == 'M') {
                    	    $elec_billing = $elec_billing_days / 30.42;
                    	} 
                    	else {
                    	    $elec_billing = $elec_billing_days;
                    	}
                    	$stp_sum_elec = $elec_billing * $rate['ElectricityRate']['stp'];
                    	if ($plan['Plan']['discount_applies']) {
                    		$temp_total_elec = 0;
                        	switch ($plan['Plan']['discount_applies']) {
                    		    case 'Usage':
                    		    	$temp_total_elec = $usage_sum;
									break;
                    		    case 'Usage + STP + GST':
                    		    	$temp_total_elec = ($usage_sum + $stp_sum_elec) * 1.1;
									break;
                    		}
						    $discount_guaranteed_elec += $temp_total_elec * $plan['Plan']['discount_guaranteed_elec'] / 100;
						    $discount_elec += $temp_total_elec * $plan['Plan']['discount_pay_on_time_elec'] / 100;
						    $discount_elec += $temp_total_elec * $plan['Plan']['discount_guaranteed_elec'] / 100;
							$discount_elec += $temp_total_elec * $plan['Plan']['discount_direct_debit_elec'] / 100;
							$discount_elec += $temp_total_elec * $plan['Plan']['discount_dual_fuel_elec'] / 100;
							$discount_elec += $temp_total_elec * $plan['Plan']['discount_prepay_elec'] / 100;
							$discount_elec += $temp_total_elec * $plan['Plan']['discount_bonus_sumo'] / 100;
							$plan['Plan']['discount_elec'] = $discount_elec;
							switch ($plan['Plan']['discount_applies']) {
							    case 'Usage':
							    	$plan['Plan']['total_elec'] = round(($usage_sum + $stp_sum_elec) * 1.1);
							    	$plan['Plan']['total_inc_discount_guaranteed_elec'] = round(($usage_sum - $discount_guaranteed_elec + $stp_sum_elec) * 1.1);
							    	$plan['Plan']['total_inc_discount_elec'] = round(($usage_sum - $discount_elec + $stp_sum_elec) * 1.1);
							    	break;
							    case 'Usage + STP + GST':
							    	$plan['Plan']['total_elec'] = round(($usage_sum + $stp_sum_elec) * 1.1);
							    	$plan['Plan']['total_inc_discount_guaranteed_elec'] = round(($usage_sum + $stp_sum_elec) * 1.1 - $discount_guaranteed_elec);
							    	$plan['Plan']['total_inc_discount_elec'] = round(($usage_sum + $stp_sum_elec) * 1.1 - $discount_elec);
							    	break;
							}
                    	} else {
                        	$plan['Plan']['total_elec'] = $plan['Plan']['total_inc_discount_elec'] = round(($usage_sum + $stp_sum_elec) * 1.1);
                        }
                    }
				}
            }
            if (in_array($plan_type, array('Gas', 'Dual'))) {
                $conditions = array(
					'GasRate.state' => $plan['Plan']['state'],
					'GasRate.res_sme' => $plan['Plan']['res_sme'],
					'GasRate.retailer' => $plan['Plan']['retailer'],
					'GasRate.rate_name' => $plan['Plan']['rate_name'],
					'GasRate.status' => 'Active',
				);
		        if ($distributor_gas) {
					switch ($plan['Plan']['retailer']) {
						case 'AGL':
							$distributor_field = 'agl_distributor';
							break;
						case 'Origin Energy':
							$distributor_field = 'origin_energy_distributor';
							break;
						case 'Lumo Energy':
							$distributor_field = 'lumo_energy_distributor';
							break;
						case 'Red Energy':
							$distributor_field = 'red_energy_distributor';
							break;
						case 'Momentum':
							$distributor_field = 'momentum_distributor';
							break;
						case 'Dodo':
							$distributor_field = 'dodo_distributor';
							break;
                        case 'Alinta Energy':
							$distributor_field = 'alinta_energy_distributor';
							break;
                        case 'Energy Australia':
							$distributor_field = 'energy_australia_distributor';
							break;
                        case 'Powerdirect and AGL':
                            $distributor_field = 'pd_agl_distributor';
                            break;
					}
					$conditions['GasRate.distributor'] = explode('/', $distributor_gas['GasPostcodeDistributor'][$distributor_field]);
				}
		        $rates = $this->GasRate->find('all', array(
					'conditions' => $conditions,
					'order' => 'GasRate.id ASC'
				));
				if (!empty($rates)) {
					foreach ($rates as $rate) {
						$plan['Plan']['gas_rate'] = $rate['GasRate'];
						$period = 0;
						switch ($rate['GasRate']['rate_tier_period']) {
						    case '2':
						    	$period = 60.83;
						    	break;
						    case 'D':
						    	$period = 1;
						    	break;
						    case 'M':
						    	$period = 30.42;
						    	break;
						    case 'Q':
						    	$period = 91.25;
						    	break;
						    case 'Y':
						    	$period = 365;
						    	break;
						}
						if ($period > 0) {
						    $rate['GasRate']['peak_tier_1'] = ($rate['GasRate']['peak_tier_1'] / $period) * $gas_billing_days;
						    $rate['GasRate']['peak_tier_2'] = ($rate['GasRate']['peak_tier_2'] / $period) * $gas_billing_days;
						    $rate['GasRate']['peak_tier_3'] = ($rate['GasRate']['peak_tier_3'] / $period) * $gas_billing_days;
						    $rate['GasRate']['peak_tier_4'] = ($rate['GasRate']['peak_tier_4'] / $period) * $gas_billing_days;
						    $rate['GasRate']['peak_tier_5'] = ($rate['GasRate']['peak_tier_5'] / $period) * $gas_billing_days;
						}
						$peak_tier_rates = array(
						    array('tier' => $rate['GasRate']['peak_tier_1'], 'rate' => $rate['GasRate']['peak_rate_1'] / 100),
						    array('tier' => $rate['GasRate']['peak_tier_2'], 'rate' => $rate['GasRate']['peak_rate_2'] / 100),
						    array('tier' => $rate['GasRate']['peak_tier_3'], 'rate' => $rate['GasRate']['peak_rate_3'] / 100),
						    array('tier' => $rate['GasRate']['peak_tier_4'], 'rate' => $rate['GasRate']['peak_rate_4'] / 100),
						    array('tier' => $rate['GasRate']['peak_tier_5'], 'rate' => $rate['GasRate']['peak_rate_5'] / 100),
						    array('tier' => 0, 'rate' => $rate['GasRate']['peak_rate_6'] / 100),
						);
						$peak_sum = $this->calculate($gas_peak, $peak_tier_rates, true);
						$off_peak_sum = 0;
						
						$usage_sum = $peak_sum + $off_peak_sum;
                    	$stp_sum_gas = 0;
                    	$discount_guaranteed_gas = 0;
                    	$discount_gas = 0;
                    	if ($rate['GasRate']['stp_period'] == 'Y') {
                    	    $gas_billing = $gas_billing_days / 365;
                    	} 
                    	else if ($rate['GasRate']['stp_period'] == 'Q') {
                    	    $gas_billing = $gas_billing_days / 91.25;
                    	} 
                    	else if ($rate['GasRate']['stp_period'] == 'M') {
                    	    $gas_billing = $gas_billing_days / 30.42;
                    	} 
                    	else {
                    	    $gas_billing = $gas_billing_days;
                    	}
                    	$stp_sum_gas = $gas_billing * $rate['GasRate']['stp'];
                    	if ($plan['Plan']['discount_applies']) {
                        	$temp_total_gas = 0;
                    		switch ($plan['Plan']['discount_applies']) {
                    		    case 'Usage':
                    		    	$temp_total_gas = $usage_sum;
                    		    break;
                    		    case 'Usage + STP + GST':
                    		    	$temp_total_gas = ($usage_sum + $stp_sum_gas) * 1.1;
                    		    break;
                    		}
                    		$discount_guaranteed_gas += $temp_total_gas * $plan['Plan']['discount_guaranteed_gas'] / 100;
                            $discount_gas += $temp_total_gas * $plan['Plan']['discount_pay_on_time_gas'] / 100;
							$discount_gas += $temp_total_gas * $plan['Plan']['discount_guaranteed_gas'] / 100;
							$discount_gas += $temp_total_gas * $plan['Plan']['discount_direct_debit_gas'] / 100;
							$discount_gas += $temp_total_gas * $plan['Plan']['discount_dual_fuel_gas'] / 100;
							$plan['Plan']['discount_gas'] = $discount_gas;
							switch ($plan['Plan']['discount_applies']) {
                    		    case 'Usage':
                    		    	$plan['Plan']['total_gas'] = round(($usage_sum + $stp_sum_gas) * 1.1);
                    		    	$plan['Plan']['total_inc_discount_guaranteed_gas'] = round(($usage_sum - $discount_guaranteed_gas + $stp_sum_gas) * 1.1);
                    		    	$plan['Plan']['total_inc_discount_gas'] = round(($usage_sum - $discount_gas + $stp_sum_gas) * 1.1);
                    		    	break;
                    		    case 'Usage + STP + GST':
                    		    	$plan['Plan']['total_gas'] = round(($usage_sum + $stp_sum_gas) * 1.1);
                    		    	$plan['Plan']['total_inc_discount_guaranteed_gas'] = round(($usage_sum + $stp_sum_gas) * 1.1 - $discount_guaranteed_gas);
                    		    	$plan['Plan']['total_inc_discount_gas'] = round(($usage_sum + $stp_sum_gas) * 1.1 - $discount_gas);
                    		    break;
                    		}
                    	} else {
                        	$plan['Plan']['total_gas'] = $plan['Plan']['total_inc_discount_gas'] = round(($usage_sum + $stp_sum_gas) * 1.1);
                    	}
					}
                }
            }
            unset($plans[$key]);
			if ($plan_type == 'Elec') {
				$plans[$plan['Plan']['total_inc_discount_elec'] * 10000 + $plan['Plan']['id']] = $plan;
			}  else if ($plan_type == 'Gas') {
				$plans[$plan['Plan']['total_inc_discount_gas'] * 10000 + $plan['Plan']['id']] = $plan;
			}  else if ($plan_type == 'Dual') {
				$plans[($plan['Plan']['total_inc_discount_elec'] + $plan['Plan']['total_inc_discount_gas']) * 10000 + $plan['Plan']['id']] = $plan;
			}
		}
		ksort($plans);
		
		return $plans;
	}
}
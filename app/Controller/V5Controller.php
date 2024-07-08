<?php

App::uses('AppController', 'Controller');

class V5Controller extends AppController
{
    public $uses = array('Plan', 'Location', 'ElectricityRate', 'GasRate', 'Tariff', 'ElectricityPostcodeDistributor', 'GasPostcodeDistributor', 'ElectricityNmiDistributor', 'Consumption', 'SolarRebateScheme', 'Customer', 'Sale', 'Submission', 'LeadAgent', 'BroadbandLog', 'OffshoreLeadExceptions', 'DmoVdo', 'Option', 'Lead');
    public $helpers = array('Html', 'Icon');

    public function beforeFilter()
    {

        parent::beforeFilter();

        //$this->Auth->allow();

        $this->layout = 'v5';

        if (!in_array($this->request->clientIp(), unserialize(STAFF_IPS))) {
            $this->redirect(MAIN_SITE);
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
                $customer = $this->Customer->findByCustomerKey($this->request->query['customer']);
                $this->Session->write('User', unserialize($customer['Customer']['data']));
                $this->Session->write('User.customer', $customer['Customer']['id']);
            }
        }

        // leads360 URLs
        $leads360_url_1 = $this->Option->find('first', array(
            'conditions' => array(
                'Option.option_name' => 'leads360_url_1',
            ),
        ));
        $this->leads360_url_1 = $leads360_url_1['Option']['option_value'];

        $leads360_url_2 = $this->Option->find('first', array(
            'conditions' => array(
                'Option.option_name' => 'leads360_url_2',
            ),
        ));
        $this->leads360_url_2 = $leads360_url_2['Option']['option_value'];
    }

    public function index()
    {
        $this->set('title_for_layout', 'Customer Details');

        // reset User for new customer details
        $this->Session->delete('User');
        unset($_COOKIE['top_picks']);

        $step = 'customer_details';

        $this->set(compact('step'));
    }

    public function compare($step = 1)
    {
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
                if (!$this->Session->check('User.outbound') && !$this->Session->check('User.inbound')) {
                    $this->redirect('/v5/');
                }
                break;
            case 2:
                $this->set('title_for_layout', 'Step 2 - Product Options');
                if ($this->Session->check('User.step1')) {
                    $step1 = $this->Session->read('User.step1');
                } else {
                    $this->redirect('/v5/compare/1');
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
                    $this->redirect('/v5/compare/1');
                }
                if ($this->Session->check('User.step2')) {
                    $step2 = $this->Session->read('User.step2');
                } else {
                    $this->redirect('/v5/compare/2');
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
                    'discount_pay_on_time_all' => 0,
                    'discount_guaranteed_all' => 0,
                    'discount_direct_debit_all' => 0,
                    'discount_dual_fuel_all' => 0,
                    'discount_bonus_sumo_all' => 0,
                    'discount_prepay_all' => 0,
                    'include_gst_all' => 1,
                );
                /*
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
                */
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
                } else {
                    $conditions['Plan.state'] = $states_arr[$this->Session->read('User.state')];
                    $conditions['Plan.package'] = $step1['plan_type'];
                    $conditions['Plan.res_sme'] = (isset($step1['is_soho']) && $step1['is_soho'] == 1) ? 'SOHO' : $step1['customer_type'];
                    $conditions['Plan.version'] = array('All', '4', '5');
                    $plan_start_or = array(
                        'or' => array(
                            'Plan.plan_start' => '0000-00-00',
                            'Plan.plan_start <=' => date('Y-m-d'),
                        ),
                    );
                    $conditions[] = $plan_start_or;
                    $plan_expiry_or = array(
                        'or' => array(
                            'Plan.plan_expiry' => '0000-00-00',
                            'Plan.plan_expiry >=' => date('Y-m-d'),
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
                        if (isset($step1['is_soho']) && $step1['is_soho'] == 1) {
                            $conditions['Plan.res_sme'] = 'SOHO';
                        }
                        $filters['discount_type'] = array();
                        if (isset($this->request->data['discount_type']) && !empty($this->request->data['discount_type'])) {
                            $filters['discount_type'] = $this->request->data['discount_type'];
                        }
                        if (isset($this->request->data['contract_length']) && !empty($this->request->data['contract_length']) && !in_array('all', $this->request->data['contract_length'])) {
                            $conditions['Plan.contract_length'] = $this->request->data['contract_length'];
                            $filters['contract_length'] = $this->request->data['contract_length'];
                        }
                        if (isset($this->request->data['retailer']) && !empty($this->request->data['retailer']) && !in_array('all', $this->request->data['retailer'])) {
                            $filters['retailer'] = $this->request->data['retailer'];
                        }
                        if (isset($this->request->data['payment_options']) && !empty($this->request->data['payment_options']) && !in_array('all', $this->request->data['payment_options'])) {
                            $payment_options_or = array();
                            foreach ($this->request->data['payment_options'] as $value) {
                                $payment_options_or['or']["Plan.{$value}"] = 'Yes';
                            }
                            $conditions[] = $payment_options_or;
                            $filters['payment_options'] = $this->request->data['payment_options'];
                        }

                        $filters['discount_pay_on_time_all'] = (isset($this->request->data['discount_pay_on_time_all']) && $this->request->data['discount_pay_on_time_all'] == 1) ? 1 : 0;
                        $filters['discount_guaranteed_all'] = (isset($this->request->data['discount_guaranteed_all']) && $this->request->data['discount_guaranteed_all'] == 1) ? 1 : 0;
                        $filters['discount_direct_debit_all'] = (isset($this->request->data['discount_direct_debit_all']) && $this->request->data['discount_direct_debit_all'] == 1) ? 1 : 0;
                        $filters['discount_dual_fuel_all'] = (isset($this->request->data['discount_dual_fuel_all']) && $this->request->data['discount_dual_fuel_all'] == 1) ? 1 : 0;
                        $filters['discount_bonus_sumo_all'] = (isset($this->request->data['discount_bonus_sumo_all']) && $this->request->data['discount_bonus_sumo_all'] == 1) ? 1 : 0;
                        $filters['discount_prepay_all'] = (isset($this->request->data['discount_prepay_all']) && $this->request->data['discount_prepay_all'] == 1) ? 1 : 0;
                        $filters['include_gst_all'] = (isset($this->request->data['include_gst_all']) && $this->request->data['include_gst_all'] == 1) ? 1 : 0;
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
                            if ($this->Session->read('User.state') == 'NSW' && $step1['customer_type'] == 'RES') {
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(41))) {
                                    $conditions['Plan.version'][] = 'Powerdirect Discount Saver (Ausgrid)';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(40, 42, 44, 45))) {
                                    $conditions['Plan.version'][] = 'Powerdirect Discount Saver (Essential Energy)';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(43))) {
                                    $conditions['Plan.version'][] = 'Powerdirect Discount Saver (Endeavour Energy)';
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

                            if ($this->Session->read('User.state') == 'NSW' && $step1['customer_type'] == 'RES') {
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(41))) {
                                    $conditions['Plan.version'][] = 'Origin Saver Ausgrid';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(40, 42, 44, 45))) {
                                    $conditions['Plan.version'][] = 'Origin Saver Essential Energy';
                                }
                            }
                            if ($this->Session->read('User.state') == 'VIC' && $step1['customer_type'] == 'RES') {
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(62))) {
                                    $conditions['Plan.version'][] = 'Origin Bill Saver Powercor';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(61))) {
                                    $conditions['Plan.version'][] = 'Origin Bill Saver Citipower';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(64))) {
                                    $conditions['Plan.version'][] = 'Origin Bill Saver United Energy';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(60))) {
                                    $conditions['Plan.version'][] = 'Origin Bill Saver Jemena';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(63))) {
                                    $conditions['Plan.version'][] = 'Origin Bill Saver Ausnet';
                                }
                            }

                            if ($this->Session->read('User.state') == 'VIC' && $step1['customer_type'] == 'SME') {
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(61))) {
                                    $conditions['Plan.version'][] = 'BusinessSaver (Citipower)';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(62))) {
                                    $conditions['Plan.version'][] = 'BusinessSaver (Powercor)';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(60))) {
                                    $conditions['Plan.version'][] = 'BusinessSaver (Jemena)';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(64))) {
                                    $conditions['Plan.version'][] = 'BusinessSaver (United Energy)';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(63))) {
                                    $conditions['Plan.version'][] = 'BusinessSaver (Ausnet)';
                                }
                            }
                            if ($this->Session->read('User.state') == 'NSW' && $step1['customer_type'] == 'SME') {
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(41))) {
                                    $conditions['Plan.version'][] = 'BusinessSaver (Ausgrid)';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(43))) {
                                    $conditions['Plan.version'][] = 'BusinessSaver (Endeavour Energy)';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(40, 42, 44, 45))) {
                                    $conditions['Plan.version'][] = 'BusinessSaver (Essential Energy)';
                                }

                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(40, 42, 43, 44, 45))) {
                                    $conditions['Plan.version'][] = 'BusinessSaver (Essential & Endeavour)';
                                }
                            }

                            if ($this->Session->read('User.state') == 'NSW' && $step1['customer_type'] == 'RES') {
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(40, 41, 42, 44, 45))) {
                                    $conditions['Plan.version'][] = 'Origin Saver (Ausgrid+Essential)';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(43))) {
                                    $conditions['Plan.version'][] = 'Origin Saver (Endeavour)';
                                }

                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(41))) {
                                    $conditions['Plan.version'][] = 'Origin Max Saver (Ausgrid)';
                                    $conditions['Plan.version'][] = 'Origin Flexi (Ausgrid)';
                                }

                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(43))) {
                                    $conditions['Plan.version'][] = 'Origin Max Saver (Endeavour Energy)';
                                    $conditions['Plan.version'][] = 'Origin Flexi (Endeavour Energy)';
                                }

                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(40, 42, 44, 45))) {
                                    $conditions['Plan.version'][] = 'Origin Max Saver (Essential Energy)';
                                    $conditions['Plan.version'][] = 'Origin Flexi (Essential Energy)';
                                }
                            }

                            if ($this->Session->read('User.state') == 'VIC' && $step1['customer_type'] == 'RES') {
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(63))) {
                                    $conditions['Plan.version'][] = 'Origin Max Saver (Ausnet)';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(61))) {
                                    $conditions['Plan.version'][] = 'Origin Max Saver (Citipower)';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(60))) {
                                    $conditions['Plan.version'][] = 'Origin Max Saver (Jemena)';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(62))) {
                                    $conditions['Plan.version'][] = 'Origin Max Saver (Powercor)';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(64))) {
                                    $conditions['Plan.version'][] = 'Origin Max Saver (United Energy)';
                                }
                            }

                        }
                        if ($distributor_elec['ElectricityPostcodeDistributor']['lumo_energy_distributor']) {
                            $distributor_retailer_arr[] = 'Lumo Energy';
                        }
                        if ($distributor_elec['ElectricityPostcodeDistributor']['momentum_distributor']) {
                            $distributor_retailer_arr[] = 'Momentum';
                        }
                        if ($distributor_elec['ElectricityPostcodeDistributor']['powershop_distributor']) {
                            $distributor_retailer_arr[] = 'Powershop';
                        }
                        if ($distributor_elec['ElectricityPostcodeDistributor']['alinta_energy_distributor']) {
                            $distributor_retailer_arr[] = 'Alinta Energy';

                            if ($this->Session->read('User.state') == 'NSW' && $step1['customer_type'] == 'SME') {
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(40, 41, 42, 44, 45))) {
                                    $conditions['Plan.version'][] = 'Corporate Saver (Ausgrid+Essential)';
                                }
                            }
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
                            }
                            if ($this->Session->read('User.state') == 'VIC' && $step1['customer_type'] == 'RES') {
                            }
                            if ($this->Session->read('User.state') == 'NSW' && $step1['customer_type'] == 'RES') {
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(40, 42, 43, 44, 45))) {
                                    $conditions['Plan.version'][] = 'Flexi Saver Essential Endeavour';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(41))) {
                                    $conditions['Plan.version'][] = 'Flexi Saver Ausgrid';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(40, 42, 44, 45))) {
                                    $conditions['Plan.version'][] = 'Flexi Saver Essential';
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
                        if ($distributor_elec['ElectricityPostcodeDistributor']['actewagl_distributor']) {
                            $distributor_retailer_arr[] = 'ActewAGL';
                        }
                        if ($distributor_elec['ElectricityPostcodeDistributor']['elysian_energy_distributor']) {
                            $distributor_retailer_arr[] = 'Elysian Energy';
                        }
                        if ($distributor_elec['ElectricityPostcodeDistributor']['testing_retailer_distributor']) {
                            $distributor_retailer_arr[] = 'Testing Retailer';
                        }
                        if ($distributor_elec['ElectricityPostcodeDistributor']['ovo_energy_distributor']) {
                            $distributor_retailer_arr[] = 'OVO Energy';
                        }
                    }
                } elseif ($filters['plan_type'] == 'Gas') {
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
                        if ($distributor_gas['GasPostcodeDistributor']['momentum_distributor']) {
                            $distributor_retailer_arr[] = 'Momentum';
                        }
                        if ($distributor_gas['GasPostcodeDistributor']['powershop_distributor']) {
                            $distributor_retailer_arr[] = 'Powershop';
                        }
                        if ($distributor_gas['GasPostcodeDistributor']['alinta_energy_distributor']) {
                            $distributor_retailer_arr[] = 'Alinta Energy';
                        }
                        if ($distributor_gas['GasPostcodeDistributor']['energy_australia_distributor']) {
                            $distributor_retailer_arr[] = 'Energy Australia';
                        }
                        if ($distributor_gas['GasPostcodeDistributor']['sumo_power_distributor']) {
                            $distributor_retailer_arr[] = 'Sumo Power';
                        }
                        if ($distributor_gas['GasPostcodeDistributor']['actewagl_distributor']) {
                            $distributor_retailer_arr[] = 'ActewAGL';
                        }
                        if ($distributor_gas['GasPostcodeDistributor']['elysian_energy_distributor']) {
                            $distributor_retailer_arr[] = 'Elysian Energy';
                        }
                        if ($distributor_gas['GasPostcodeDistributor']['testing_retailer_distributor']) {
                            $distributor_retailer_arr[] = 'Testing Retailer';
                        }
                    }
                } elseif ($filters['plan_type'] == 'Dual') {
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

                            if ($this->Session->read('User.state') == 'NSW' && $step1['customer_type'] == 'RES') {
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(41))) {
                                    $conditions['Plan.version'][] = 'Origin Saver Ausgrid';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(40, 42, 44, 45))) {
                                    $conditions['Plan.version'][] = 'Origin Saver Essential Energy';
                                }
                            }

                            if ($this->Session->read('User.state') == 'VIC' && $step1['customer_type'] == 'RES') {
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(62))) {
                                    $conditions['Plan.version'][] = 'Origin Bill Saver (Powercor)';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(61))) {
                                    $conditions['Plan.version'][] = 'Origin Bill Saver (Citipower)';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(64))) {
                                    $conditions['Plan.version'][] = 'Origin Bill Saver (United Energy)';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(60))) {
                                    $conditions['Plan.version'][] = 'Origin Bill Saver (Jemena)';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(63))) {
                                    $conditions['Plan.version'][] = 'Origin Bill Saver (Ausnet)';
                                }
                            }

                            if ($this->Session->read('User.state') == 'VIC' && $step1['customer_type'] == 'SME') {
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(61))) {
                                    $conditions['Plan.version'][] = 'BusinessSaver (Citipower)';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(62))) {
                                    $conditions['Plan.version'][] = 'BusinessSaver (Powercor)';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(60))) {
                                    $conditions['Plan.version'][] = 'BusinessSaver (Jemena)';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(64))) {
                                    $conditions['Plan.version'][] = 'BusinessSaver (United Energy)';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(63))) {
                                    $conditions['Plan.version'][] = 'BusinessSaver (Ausnet)';
                                }
                            }
                            if ($this->Session->read('User.state') == 'NSW' && $step1['customer_type'] == 'SME') {
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(41))) {
                                    $conditions['Plan.version'][] = 'BusinessSaver (Ausgrid)';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(43))) {
                                    $conditions['Plan.version'][] = 'BusinessSaver (Endeavour Energy)';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(40, 42, 44, 45))) {
                                    $conditions['Plan.version'][] = 'BusinessSaver (Essential Energy)';
                                }

                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(40, 42, 43, 44, 45))) {
                                    $conditions['Plan.version'][] = 'BusinessSaver (Essential & Endeavour)';
                                }
                            }

                            if ($this->Session->read('User.state') == 'NSW' && $step1['customer_type'] == 'RES') {
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(40, 41, 42, 44, 45))) {
                                    $conditions['Plan.version'][] = 'Origin Saver (Ausgrid+Essential)';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(43))) {
                                    $conditions['Plan.version'][] = 'Origin Saver (Endeavour)';
                                }

                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(41))) {
                                    $conditions['Plan.version'][] = 'Origin Max Saver (Ausgrid)';
                                    $conditions['Plan.version'][] = 'Origin Flexi (Ausgrid)';
                                }

                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(43))) {
                                    $conditions['Plan.version'][] = 'Origin Max Saver (Endeavour Energy)';
                                    $conditions['Plan.version'][] = 'Origin Flexi (Endeavour Energy)';
                                }

                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(40, 42, 44, 45))) {
                                    $conditions['Plan.version'][] = 'Origin Max Saver (Essential Energy)';
                                    $conditions['Plan.version'][] = 'Origin Flexi (Essential Energy)';
                                }
                            }

                            if ($this->Session->read('User.state') == 'VIC' && $step1['customer_type'] == 'RES') {
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(63))) {
                                    $conditions['Plan.version'][] = 'Origin Max Saver (Ausnet)';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(61))) {
                                    $conditions['Plan.version'][] = 'Origin Max Saver (Citipower)';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(60))) {
                                    $conditions['Plan.version'][] = 'Origin Max Saver (Jemena)';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(62))) {
                                    $conditions['Plan.version'][] = 'Origin Max Saver (Powercor)';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(64))) {
                                    $conditions['Plan.version'][] = 'Origin Max Saver (United Energy)';
                                }
                            }
                        }
                        if ($distributor_elec['ElectricityPostcodeDistributor']['lumo_energy_distributor'] && $distributor_gas['GasPostcodeDistributor']['lumo_energy_distributor']) {
                            $distributor_retailer_arr[] = 'Lumo Energy';
                        }
                        if ($distributor_elec['ElectricityPostcodeDistributor']['momentum_distributor'] && $distributor_gas['GasPostcodeDistributor']['momentum_distributor']) {
                            $distributor_retailer_arr[] = 'Momentum';
                        }
                        if ($distributor_elec['ElectricityPostcodeDistributor']['powershop_distributor'] && $distributor_gas['GasPostcodeDistributor']['powershop_distributor']) {
                            $distributor_retailer_arr[] = 'Powershop';
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
                            }
                            if ($this->Session->read('User.state') == 'VIC' && $step1['customer_type'] == 'RES') {
                                if ($step1['nmi'] && (in_array(substr($step1['nmi'], 0, 4), array(6102, 6203, 6407)) || in_array(substr($step1['nmi'], 0, 3), array(600)))) {
                                    $conditions['Plan.version'][] = 'Flexi Saver Citipower Powercor Jemena United';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 4), array(6305))) {
                                    $conditions['Plan.version'][] = 'Flexi Saver Ausnet';
                                }
                            }
                            if ($this->Session->read('User.state') == 'NSW' && $step1['customer_type'] == 'RES') {
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(40, 42, 43, 44, 45))) {
                                    $conditions['Plan.version'][] = 'Flexi Saver Essential Endeavour';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(41))) {
                                    $conditions['Plan.version'][] = 'Flexi Saver Ausgrid';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 2), array(40, 42, 44, 45))) {
                                    $conditions['Plan.version'][] = 'Flexi Saver Essential';
                                }
                            }
                        }
                        if ($distributor_elec['ElectricityPostcodeDistributor']['sumo_power_distributor'] && $distributor_gas['GasPostcodeDistributor']['sumo_power_distributor']) {
                            $distributor_retailer_arr[] = 'Sumo Power';
                        }
                        if ($distributor_elec['ElectricityPostcodeDistributor']['pd_agl_distributor'] && $distributor_gas['GasPostcodeDistributor']['pd_agl_distributor']) {
                            $distributor_retailer_arr[] = 'Powerdirect and AGL';
                            if ($this->Session->read('User.state') == 'VIC' && $step1['customer_type'] == 'RES') {
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 4), array(6102, 6001, 6203))) {
                                    $conditions['Plan.version'][] = 'Residential (Citipower, Jemena & Powercor) + AGL Savers';
                                }
                                if ($step1['nmi'] && in_array(substr($step1['nmi'], 0, 4), array(6407, 6305))) {
                                    $conditions['Plan.version'][] = 'Residential (United & SP Ausnet) + AGL Savers';
                                }
                            }
                        }
                        if ($distributor_elec['ElectricityPostcodeDistributor']['actewagl_distributor'] && $distributor_gas['GasPostcodeDistributor']['actewagl_distributor']) {
                            $distributor_retailer_arr[] = 'ActewAGL';
                        }
                        if ($distributor_elec['ElectricityPostcodeDistributor']['elysian_energy_distributor'] && $distributor_gas['GasPostcodeDistributor']['elysian_energy_distributor']) {
                            $distributor_retailer_arr[] = 'Elysian Energy';
                        }
                        if ($distributor_elec['ElectricityPostcodeDistributor']['testing_retailer_distributor'] && $distributor_gas['GasPostcodeDistributor']['testing_retailer_distributor']) {
                            $distributor_retailer_arr[] = 'Testing Retailer';
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
                } else {
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
                } else {
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

                    if ($tariff['Tariff']['agl_unsupported_tariff'] == 'Unsupported') {
                        if (($key = array_search('AGL', $retailer_arr)) !== false) {
                            unset($retailer_arr[$key]);
                        }
                    }
                    if ($tariff['Tariff']['origin_energy_unsupported_tariff'] == 'Unsupported') {
                        if (($key = array_search('Origin Energy', $retailer_arr)) !== false) {
                            unset($retailer_arr[$key]);
                        }
                    }

                    if ($tariff['Tariff']['powershop_unsupported_tariff'] == 'Unsupported') {
                        if (($key = array_search('Powershop', $retailer_arr)) !== false) {
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
                    if ($tariff['Tariff']['alinta_energy_unsupported_tariff'] == 'Unsupported') {
                        if (($key = array_search('Alinta Energy', $retailer_arr)) !== false) {
                            unset($retailer_arr[$key]);
                        }
                    }
                    if ($tariff['Tariff']['energy_australia_unsupported_tariff'] == 'Unsupported') {
                        if (($key = array_search('Energy Australia', $retailer_arr)) !== false) {
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
                    if ($tariff['Tariff']['lumo_energy_unsupported_tariff'] == 'Unsupported') {
                        if (($key = array_search('Lumo Energy', $retailer_arr)) !== false) {
                            unset($retailer_arr[$key]);
                        }
                    }
                    if ($tariff['Tariff']['next_business_energy_unsupported_tariff'] == 'Unsupported') {
                        if (($key = array_search('Next Business Energy', $retailer_arr)) !== false) {
                            unset($retailer_arr[$key]);
                        }
                    }
                    if ($tariff['Tariff']['actewagl_unsupported_tariff'] == 'Unsupported') {
                        if (($key = array_search('ActewAGL', $retailer_arr)) !== false) {
                            unset($retailer_arr[$key]);
                        }
                    }
                    if ($tariff['Tariff']['elysian_energy_unsupported_tariff'] == 'Unsupported') {
                        if (($key = array_search('Elysian Energy', $retailer_arr)) !== false) {
                            unset($retailer_arr[$key]);
                        }
                    }
                    if ($tariff['Tariff']['testing_retailer_unsupported_tariff'] == 'Unsupported') {
                        if (($key = array_search('Testing Retailer', $retailer_arr)) !== false) {
                            unset($retailer_arr[$key]);
                        }
                    }
                    if ($tariff['Tariff']['ovo_energy_unsupported_tariff'] == 'Unsupported') {
                        if (($key = array_search('OVO Energy', $retailer_arr)) !== false) {
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
                    $available_conditions = array(
                        'Plan.status' => 'Active',
                        'Plan.id' => $top_picks,
                        'Plan.retailer' => $available_retailers,
                    );
                } else {
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
                    } else {
                        $available_conditions['Plan.solar_specific_plan !='] = 'Solar Only';
                    }
                }
                $available_plans = $this->Plan->find('all', array(
                    'conditions' => $available_conditions,
                    'order' => $order
                ));

                $this->log("OKKEY DEBUG available_plans::::: " . json_encode($available_plans), 'debug');
                $this->log("OKKEY DEBUG conditions::::: " . json_encode($conditions), 'debug');
                if (!empty($available_plans)) {
                    foreach ($available_plans as $plan) {
                        if (($plan['Plan']['discount_pay_on_time_elec'] || $plan['Plan']['discount_pay_on_time_gas']) && !in_array('Pay On Time', $available_discount_type)) {
                            $available_discount_type[] = 'Pay On Time';
                        }
                        if (($plan['Plan']['discount_guaranteed_elec'] || $plan['Plan']['discount_guaranteed_gas']) && !in_array('Guaranteed', $available_discount_type)) {
                            $available_discount_type[] = 'Guaranteed';
                        }
                        if (($plan['Plan']['discount_direct_debit_elec'] || $plan['Plan']['discount_direct_debit_gas']) && !in_array('Direct Debit', $available_discount_type)) {
                            $available_discount_type[] = 'Direct Debit';
                        }
                        if (($plan['Plan']['discount_dual_fuel_elec'] || $plan['Plan']['discount_dual_fuel_gas']) && !in_array('Dual Fuel', $available_discount_type)) {
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
                        case 'Transitional Time of Use':
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
                        case 'Transitional Time of Use + CL1':
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
                        case 'Transitional Time of Use + CL2':
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

                $plans_temp = $this->Plan->find('all', array(
                    'conditions' => $conditions,
                    'order' => $order
                ));
                $this->log("OKKEY DEBUG plans_temp::::: " . json_encode($plans_temp), 'debug');
                if (!empty($plans_temp)) {
                    $plans = array();
                    foreach ($plans_temp as $key => $plan) {
                        if ($step1['elec_usage_level'] == 'Low' && $plan['Plan']['retailer'] == 'Next Business Energy' && $plan['Plan']['product_name'] == "Business Plan - Only Customer's >22000 KWH per annum") {
                            continue;
                        }

                        if ($step1['customer_type'] != 'SME') {
                            if ($step1['campaign_name'] == 'SeekLeads') {
                                if ($step1['lead_origin'] == 'Offshore') {
                                    switch ($plan['Plan']['retailer']) {
                                        case 'AGL':
                                            $exception_field = 'agl';
                                            break;
                                        case 'Powerdirect':
                                            $exception_field = 'powerdirect';
                                            break;
                                        case 'Origin Energy':
                                            $exception_field = 'origin_energy';
                                            break;
                                        case 'Alinta Energy':
                                            $exception_field = 'alinta_energy';
                                            break;
                                        case 'Sumo Power':
                                            $exception_field = 'sumo_power';
                                            break;
                                        case 'Lumo Energy':
                                            $exception_field = 'lumo_energy';
                                            break;
                                        case 'Momentum':
                                            $exception_field = 'momentum';
                                            break;
                                        case 'Next Business Energy':
                                            $exception_field = 'next_business_energy';
                                            break;
                                        case 'Powershop':
                                            $exception_field = 'powershop';
                                            break;
                                        case 'ActewAGL':
                                            $exception_field = 'actewagl';
                                            break;
                                        case 'Elysian Energy':
                                            $exception_field = 'elysian_energy';
                                            break;
                                        case 'Testing Retailer':
                                            $exception_field = 'testing_retailer';
                                            break;
                                        case 'OVO Energy':
                                            $exception_field = 'ovo_energy';
                                            break;
                                    }
                                    if ($exception_field) {
                                        if (isset($step1['centre_name']) && $step1['centre_name']) {
                                            $offshore_lead_exceptions = $this->OffshoreLeadExceptions->find('first', array(
                                                'conditions' => array(
                                                    "OffshoreLeadExceptions.{$exception_field}" => 'No',
                                                    "OffshoreLeadExceptions.centre_name" => $step1['centre_name'],
                                                )
                                            ));
                                            if ($offshore_lead_exceptions) {
                                                continue;
                                            }
                                            $offshore_lead_exceptions_exist = $this->OffshoreLeadExceptions->find('first', array(
                                                'conditions' => array(
                                                    "OffshoreLeadExceptions.centre_name" => $step1['centre_name'],
                                                )
                                            ));
                                            if (!$offshore_lead_exceptions_exist) {
                                                continue;
                                            }
                                        } else {
                                            continue;
                                        }
                                    }
                                }
                            } else {
                                if ($step1['lead_origin'] == 'Offshore') {
                                    if ($plan['Plan']['offshore_lead_generation'] == 'No') {
                                        //echo $plan['Plan']['retailer'];
                                        continue;
                                    } elseif ($plan['Plan']['offshore_lead_generation'] == 'Exception') {
                                        switch ($plan['Plan']['retailer']) {
                                            case 'AGL':
                                                $exception_field = 'agl';
                                                break;
                                            case 'Powerdirect':
                                                $exception_field = 'powerdirect';
                                                break;
                                            case 'Origin Energy':
                                                $exception_field = 'origin_energy';
                                                break;
                                            case 'Alinta Energy':
                                                $exception_field = 'alinta_energy';
                                                break;
                                            case 'Sumo Power':
                                                $exception_field = 'sumo_power';
                                                break;
                                            case 'Lumo Energy':
                                                $exception_field = 'lumo_energy';
                                                break;
                                            case 'Momentum':
                                                $exception_field = 'momentum';
                                                break;
                                            case 'Next Business Energy':
                                                $exception_field = 'next_business_energy';
                                                break;
                                            case 'Powershop':
                                                $exception_field = 'powershop';
                                                break;
                                            case 'ActewAGL':
                                                $exception_field = 'actewagl';
                                                break;
                                            case 'Elysian Energy':
                                                $exception_field = 'elysian_energy';
                                                break;
                                            case 'Testing Retailer':
                                                $exception_field = 'testing_retailer';
                                                break;
                                            case 'OVO Energy':
                                                $exception_field = 'ovo_energy';
                                                break;
                                        }
                                        if ($exception_field) {
                                            $offshore_lead_exceptions = $this->OffshoreLeadExceptions->find('first', array(
                                                'conditions' => array(
                                                    "OffshoreLeadExceptions.{$exception_field}" => 'No',
                                                    "OffshoreLeadExceptions.centre_name" => $step1['centre_name'],
                                                )
                                            ));
                                            if ($offshore_lead_exceptions) {
                                                continue;
                                            }
                                            $offshore_lead_exceptions_exist = $this->OffshoreLeadExceptions->find('first', array(
                                                'conditions' => array(
                                                    "OffshoreLeadExceptions.centre_name" => $step1['centre_name'],
                                                )
                                            ));
                                            if (!$offshore_lead_exceptions_exist) {
                                                continue;
                                            }
                                        }
                                    }
                                }
                            }
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

                            $elec_rate_start_or = array(
                                'or' => array(
                                    'ElectricityRate.rate_start' => '0000-00-00',
                                    'ElectricityRate.rate_start <=' => date('Y-m-d'),
                                ),
                            );
                            $conditions[] = $elec_rate_start_or;

                            $elec_rate_expire_or = array(
                                'or' => array(
                                    'ElectricityRate.rate_expire' => '0000-00-00',
                                    'ElectricityRate.rate_expire >=' => date('Y-m-d'),
                                ),
                            );
                            $conditions[] = $elec_rate_expire_or;

                            $electricity_dmo_vdo_conditions = array(
                                'DmoVdo.state' => $plan['Plan']['state'],
                                'DmoVdo.res_sme' => $plan['Plan']['res_sme'],
                                'DmoVdo.retailer' => $plan['Plan']['retailer'],
                                'DmoVdo.package' => $plan['Plan']['package'],
                                'DmoVdo.plan' => $plan['Plan']['product_name'],
                                'DmoVdo.tariff_type' => $step1['elec_meter_type'],
                                'DmoVdo.version' => array('All', '4', '5'),
                            );

                            $electricity_dmo_vdo_start_or = array(
                                'or' => array(
                                    'DmoVdo.start_date' => '0000-00-00',
                                    'DmoVdo.start_date <=' => date('Y-m-d'),
                                ),
                            );
                            $electricity_dmo_vdo_conditions[] = $electricity_dmo_vdo_start_or;

                            $electricity_dmo_vdo_expire_or = array(
                                'or' => array(
                                    'DmoVdo.expiry_date' => '0000-00-00',
                                    'DmoVdo.expiry_date >=' => date('Y-m-d'),
                                ),
                            );
                            $electricity_dmo_vdo_conditions[] = $electricity_dmo_vdo_expire_or;

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
                                    case 'Momentum':
                                        $distributor_field = 'momentum_distributor';
                                        break;
                                    case 'Powershop':
                                        $distributor_field = 'powershop_distributor';
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
                                    case 'ActewAGL':
                                        $distributor_field = 'actewagl_distributor';
                                        break;
                                    case 'Elysian Energy':
                                        $distributor_field = 'elysian_energy_distributor';
                                        break;
                                    case 'Testing Retailer':
                                        $distributor_field = 'testing_retailer_distributor';
                                        break;
                                    case 'OVO Energy':
                                        $distributor_field = 'ovo_energy_distributor';
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
                                    }
                                } else {
                                    $distributors = explode('/', $step1['nmi_distributor']);
                                }
                            }
                            $conditions['ElectricityRate.distributor'] = $distributors;
                            $electricity_dmo_vdo_conditions['DmoVdo.distributor'] = $distributors;
                            if ($step1['nmi_distributor'] && $step1['tariff_parent']) {
                                $tariff_parent = explode('|', $step1['tariff_parent']);
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
                                        } else {
                                            $tariff_classes[] = $tariff_class;
                                        }
                                    }
                                    $conditions['ElectricityRate.tariff_class'] = $tariff_classes;
                                    $electricity_dmo_vdo_conditions['DmoVdo.tariff_class'] = $tariff_classes;
                                }

                                if ($plan['Plan']['retailer'] == 'Origin Energy') {
                                    if ($plan['Plan']['solar_boost_fit']) {
                                        $net_gross_tariff = $tariff['Tariff']['net_gross_tariff'];
                                        $solar_rebate_scheme = $tariff['Tariff']['solar_rebate_scheme'];
                                        if (!$net_gross_tariff || !$solar_rebate_scheme) {
                                            if (isset($step1['tariff2']) && $step1['tariff2']) {
                                                $child_tariff_array = explode('|', $step1['tariff2']);
                                                $child_tariff = $this->Tariff->find('first', array(
                                                    'conditions' => array(
                                                        'Tariff.tariff_code' => $child_tariff_array[0],
                                                        'Tariff.res_sme' => $step1['customer_type'],
                                                        'Tariff.distributor' => explode('/', $step1['nmi_distributor']),
                                                    ),
                                                ));
                                                if (!$net_gross_tariff) {
                                                    $net_gross_tariff = $child_tariff['Tariff']['net_gross_tariff'];
                                                }
                                                if (!$solar_rebate_scheme) {
                                                    $solar_rebate_scheme = $child_tariff['Tariff']['solar_rebate_scheme'];
                                                }
                                            }

                                            if (isset($step1['tariff3']) && $step1['tariff3']) {
                                                $child_tariff_array = explode('|', $step1['tariff3']);
                                                $child_tariff = $this->Tariff->find('first', array(
                                                    'conditions' => array(
                                                        'Tariff.tariff_code' => $child_tariff_array[0],
                                                        'Tariff.res_sme' => $step1['customer_type'],
                                                        'Tariff.distributor' => explode('/', $step1['nmi_distributor']),
                                                    ),
                                                ));
                                                if (!$net_gross_tariff) {
                                                    $net_gross_tariff = $child_tariff['Tariff']['net_gross_tariff'];
                                                }
                                                if (!$solar_rebate_scheme) {
                                                    $solar_rebate_scheme = $child_tariff['Tariff']['solar_rebate_scheme'];
                                                }
                                            }

                                            if (isset($step1['tariff4']) && $step1['tariff4']) {
                                                $child_tariff_array = explode('|', $step1['tariff4']);
                                                $child_tariff = $this->Tariff->find('first', array(
                                                    'conditions' => array(
                                                        'Tariff.tariff_code' => $child_tariff_array[0],
                                                        'Tariff.res_sme' => $step1['customer_type'],
                                                        'Tariff.distributor' => explode('/', $step1['nmi_distributor']),
                                                    ),
                                                ));
                                                if (!$net_gross_tariff) {
                                                    $net_gross_tariff = $child_tariff['Tariff']['net_gross_tariff'];
                                                }
                                                if (!$solar_rebate_scheme) {
                                                    $solar_rebate_scheme = $child_tariff['Tariff']['solar_rebate_scheme'];
                                                }
                                            }
                                        }
                                        if ((!isset($step1['inverter_capacity']) || $step1['inverter_capacity'] != 'Yes') || substr($solar_rebate_scheme, 0, 4) == 'PFiT' || $net_gross_tariff != 'Net') {
                                            continue;
                                        }
                                    }
                                }

                                if ($plan['Plan']['retailer'] == 'AGL') {
                                    if ($plan['Plan']['solar_boost_fit']) {
                                        $solar_rebate_scheme = $tariff['Tariff']['solar_rebate_scheme'];
                                        if (!$solar_rebate_scheme) {
                                            if (isset($step1['tariff2']) && $step1['tariff2']) {
                                                $child_tariff_array = explode('|', $step1['tariff2']);
                                                $child_tariff = $this->Tariff->find('first', array(
                                                    'conditions' => array(
                                                        'Tariff.tariff_code' => $child_tariff_array[0],
                                                        'Tariff.res_sme' => $step1['customer_type'],
                                                        'Tariff.distributor' => explode('/', $step1['nmi_distributor']),
                                                    ),
                                                ));
                                                if (!$solar_rebate_scheme) {
                                                    $solar_rebate_scheme = $child_tariff['Tariff']['solar_rebate_scheme'];
                                                }
                                            }

                                            if (isset($step1['tariff3']) && $step1['tariff3']) {
                                                $child_tariff_array = explode('|', $step1['tariff3']);
                                                $child_tariff = $this->Tariff->find('first', array(
                                                    'conditions' => array(
                                                        'Tariff.tariff_code' => $child_tariff_array[0],
                                                        'Tariff.res_sme' => $step1['customer_type'],
                                                        'Tariff.distributor' => explode('/', $step1['nmi_distributor']),
                                                    ),
                                                ));
                                                if (!$solar_rebate_scheme) {
                                                    $solar_rebate_scheme = $child_tariff['Tariff']['solar_rebate_scheme'];
                                                }
                                            }

                                            if (isset($step1['tariff4']) && $step1['tariff4']) {
                                                $child_tariff_array = explode('|', $step1['tariff4']);
                                                $child_tariff = $this->Tariff->find('first', array(
                                                    'conditions' => array(
                                                        'Tariff.tariff_code' => $child_tariff_array[0],
                                                        'Tariff.res_sme' => $step1['customer_type'],
                                                        'Tariff.distributor' => explode('/', $step1['nmi_distributor']),
                                                    ),
                                                ));
                                                if (!$solar_rebate_scheme) {
                                                    $solar_rebate_scheme = $child_tariff['Tariff']['solar_rebate_scheme'];
                                                }
                                            }
                                        }
                                        if (!isset($step1['inverter_capacity']) || $step1['inverter_capacity'] != 'Yes') {
                                            continue;
                                        }
                                    }
                                }

                                if ($plan['Plan']['retailer'] == 'Momentum') {
                                    if ($plan['Plan']['solar_boost_fit']) {
                                        $solar_rebate_scheme = $tariff['Tariff']['solar_rebate_scheme'];
                                        if (!$solar_rebate_scheme) {
                                            if (isset($step1['tariff2']) && $step1['tariff2']) {
                                                $child_tariff_array = explode('|', $step1['tariff2']);
                                                $child_tariff = $this->Tariff->find('first', array(
                                                    'conditions' => array(
                                                        'Tariff.tariff_code' => $child_tariff_array[0],
                                                        'Tariff.res_sme' => $step1['customer_type'],
                                                        'Tariff.distributor' => explode('/', $step1['nmi_distributor']),
                                                    ),
                                                ));
                                                if (!$solar_rebate_scheme) {
                                                    $solar_rebate_scheme = $child_tariff['Tariff']['solar_rebate_scheme'];
                                                }
                                            }

                                            if (isset($step1['tariff3']) && $step1['tariff3']) {
                                                $child_tariff_array = explode('|', $step1['tariff3']);
                                                $child_tariff = $this->Tariff->find('first', array(
                                                    'conditions' => array(
                                                        'Tariff.tariff_code' => $child_tariff_array[0],
                                                        'Tariff.res_sme' => $step1['customer_type'],
                                                        'Tariff.distributor' => explode('/', $step1['nmi_distributor']),
                                                    ),
                                                ));
                                                if (!$solar_rebate_scheme) {
                                                    $solar_rebate_scheme = $child_tariff['Tariff']['solar_rebate_scheme'];
                                                }
                                            }

                                            if (isset($step1['tariff4']) && $step1['tariff4']) {
                                                $child_tariff_array = explode('|', $step1['tariff4']);
                                                $child_tariff = $this->Tariff->find('first', array(
                                                    'conditions' => array(
                                                        'Tariff.tariff_code' => $child_tariff_array[0],
                                                        'Tariff.res_sme' => $step1['customer_type'],
                                                        'Tariff.distributor' => explode('/', $step1['nmi_distributor']),
                                                    ),
                                                ));
                                                if (!$solar_rebate_scheme) {
                                                    $solar_rebate_scheme = $child_tariff['Tariff']['solar_rebate_scheme'];
                                                }
                                            }
                                        }
                                        if (!isset($step1['inverter_capacity']) || $step1['inverter_capacity'] != 'Yes' || $solar_rebate_scheme != 'RFiT') {
                                            continue;
                                        }
                                    }
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
                            $electricity_dmo_vdo = $this->DmoVdo->find('first', array(
                                'conditions' => $electricity_dmo_vdo_conditions,
                                'order' => 'DmoVdo.id ASC'
                            ));
                            if (!empty($electricity_dmo_vdo)) {
                                $plan['Plan']['elec_dmo_vdo'] = $electricity_dmo_vdo['DmoVdo'];
                            }
                            $this->log("OKKEY DEBUG Rate conditions::::: " . json_encode($conditions), 'debug');
                            $this->log("OKKEY DEBUG Rate::::: " . json_encode($rates), 'debug');
                            if (!empty($rates)) {
                                foreach ($rates as $rate) {
                                    $plan['Plan']['elec_rate'] = $rate['ElectricityRate'];
                                    $gst = 1;
                                    if (isset($rate['ElectricityRate']['gst_rates']) && $rate['ElectricityRate']['gst_rates'] == 'Yes') {
                                        $gst = 1.1;
                                    }
                                    $consumption_data['elec_billing_days'] = $step1['elec_billing_days'];
                                    $consumption_data['elec_consumption'] = 0;
                                    $summer_days = 0;
                                    $winter_days = 0;
                                    if ($this->Session->read('User.state') == 'SA') {
                                        //if ($this->Session->read('User.state') == 'SA' && $step1['elec_billing_start']) {
                                        /*
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
                                        */

                                        // hardcode billing days
                                        $summer_days = $step1['elec_billing_days'];
                                        $winter_days = 0;
                                        $step1['elec_winter_peak'] = 0;
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
                                                $rate['ElectricityRate']['summer_peak_rate_1'] = $peak_rate_1[0] / $gst;
                                                $rate['ElectricityRate']['winter_peak_rate_1'] = $peak_rate_1[1] / $gst;
                                            } else {
                                                $rate['ElectricityRate']['summer_peak_rate_1'] = $rate['ElectricityRate']['winter_peak_rate_1'] = $rate['ElectricityRate']['peak_rate_1'] / $gst;
                                            }
                                            if (strpos($rate['ElectricityRate']['peak_rate_2'], '/') !== false) {
                                                $peak_rate_2 = explode('/', $rate['ElectricityRate']['peak_rate_2']);
                                                $rate['ElectricityRate']['summer_peak_rate_2'] = $peak_rate_2[0] / $gst;
                                                $rate['ElectricityRate']['winter_peak_rate_2'] = $peak_rate_2[1] / $gst;
                                            } else {
                                                $rate['ElectricityRate']['summer_peak_rate_2'] = $rate['ElectricityRate']['winter_peak_rate_2'] = $rate['ElectricityRate']['peak_rate_2'] / $gst;
                                            }
                                            if (strpos($rate['ElectricityRate']['peak_rate_3'], '/') !== false) {
                                                $peak_rate_3 = explode('/', $rate['ElectricityRate']['peak_rate_3']);
                                                $rate['ElectricityRate']['summer_peak_rate_3'] = $peak_rate_3[0] / $gst;
                                                $rate['ElectricityRate']['winter_peak_rate_3'] = $peak_rate_3[1] / $gst;
                                            } else {
                                                $rate['ElectricityRate']['summer_peak_rate_3'] = $rate['ElectricityRate']['winter_peak_rate_3'] = $rate['ElectricityRate']['peak_rate_3'] / $gst;
                                            }
                                            if (strpos($rate['ElectricityRate']['peak_rate_4'], '/') !== false) {
                                                $peak_rate_4 = explode('/', $rate['ElectricityRate']['peak_rate_4']);
                                                $rate['ElectricityRate']['summer_peak_rate_4'] = $peak_rate_4[0] / $gst;
                                                $rate['ElectricityRate']['winter_peak_rate_4'] = $peak_rate_4[1] / $gst;
                                            } else {
                                                $rate['ElectricityRate']['summer_peak_rate_4'] = $rate['ElectricityRate']['winter_peak_rate_4'] = $rate['ElectricityRate']['peak_rate_4'] / $gst;
                                            }
                                            if (strpos($rate['ElectricityRate']['peak_rate_5'], '/') !== false) {
                                                $peak_rate_5 = explode('/', $rate['ElectricityRate']['peak_rate_5']);
                                                $rate['ElectricityRate']['summer_peak_rate_5'] = $peak_rate_5[0] / $gst;
                                                $rate['ElectricityRate']['winter_peak_rate_5'] = $peak_rate_5[1] / $gst;
                                            } else {
                                                $rate['ElectricityRate']['summer_peak_rate_5'] = $rate['ElectricityRate']['winter_peak_rate_5'] = $rate['ElectricityRate']['peak_rate_5'] / $gst;
                                            }
                                        } else {
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
                                    } else {
                                        $tier_rates = array(
                                            array('tier' => $rate['ElectricityRate']['peak_tier_1'], 'rate' => $rate['ElectricityRate']['peak_rate_1'] / $gst),
                                            array('tier' => $rate['ElectricityRate']['peak_tier_2'], 'rate' => $rate['ElectricityRate']['peak_rate_2'] / $gst),
                                            array('tier' => $rate['ElectricityRate']['peak_tier_3'], 'rate' => $rate['ElectricityRate']['peak_rate_3'] / $gst),
                                            array('tier' => $rate['ElectricityRate']['peak_tier_4'], 'rate' => $rate['ElectricityRate']['peak_rate_4'] / $gst),
                                            array('tier' => 0, 'rate' => $rate['ElectricityRate']['peak_rate_5'] / $gst)
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
                                            } else {
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
                                            } else {
                                                $peak_sum = $this->calculate($step1['singlerate_cl1_peak'], $tier_rates);
                                                $consumption_data['elec_consumption'] = $step1['singlerate_cl1_peak'];
                                            }
                                            $controlled_load_sum = 0;
                                            if ($rate['ElectricityRate']['controlled_load_tier_1'] && $step1['singlerate_cl1'] > $rate['ElectricityRate']['controlled_load_tier_1']) {
                                                $sum1 = $rate['ElectricityRate']['controlled_load_1_rate_1'] / $gst * $rate['ElectricityRate']['controlled_load_tier_1'];
                                                $controlled_load_sum += $rate['ElectricityRate']['controlled_load_1_rate_2'] / $gst * ($step1['singlerate_cl1'] - $rate['ElectricityRate']['controlled_load_tier_1']) + $sum1;
                                            } else {
                                                $controlled_load_sum += $step1['singlerate_cl1'] * $rate['ElectricityRate']['controlled_load_1_rate_1'] / $gst;
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
                                            } else {
                                                $peak_sum = $this->calculate($step1['singlerate_cl2_peak'], $tier_rates);
                                                $consumption_data['elec_consumption'] = $step1['singlerate_cl2_peak'];
                                            }
                                            $controlled_load_sum = $step1['singlerate_cl2'] * $rate['ElectricityRate']['controlled_load_2_rate'] / $gst;
                                            $consumption_data['elec_consumption'] += $step1['singlerate_cl2'];
                                            $usage_sum = $peak_sum + $controlled_load_sum;
                                            break;
                                        case 'Single Rate + CL1 + CL2':
                                            if ($summer_days > 0 || $winter_days > 0) {
                                                $summer_usage = $this->calculate($step1['singlerate_cl1_cl2_peak'], $summer_tier_rates);
                                                $winter_usage = $this->calculate($step1['elec_winter_peak'], $winter_tier_rates);
                                                $consumption_data['elec_consumption'] = $step1['singlerate_cl1_cl2_peak'] + $step1['elec_winter_peak'];
                                                $peak_sum = $summer_usage + $winter_usage;
                                            } else {
                                                $peak_sum = $this->calculate($step1['singlerate_cl1_cl2_peak'], $tier_rates);
                                                $consumption_data['elec_consumption'] = $step1['singlerate_cl1_cl2_peak'];
                                            }
                                            $controlled_load_sum = 0;
                                            if ($rate['ElectricityRate']['controlled_load_tier_1'] && $step1['singlerate_2_cl1'] > $rate['ElectricityRate']['controlled_load_tier_1']) {
                                                $sum1 = $rate['ElectricityRate']['controlled_load_1_rate_1'] / $gst * $rate['ElectricityRate']['controlled_load_tier_1'];
                                                $controlled_load_sum += $rate['ElectricityRate']['controlled_load_1_rate_2'] / $gst * ($step1['singlerate_2_cl1'] - $rate['ElectricityRate']['controlled_load_tier_1']) + $sum1;
                                            } else {
                                                $controlled_load_sum += $step1['singlerate_2_cl1'] * $rate['ElectricityRate']['controlled_load_1_rate_1'] / $gst;
                                            }
                                            $consumption_data['elec_consumption'] += $step1['singlerate_2_cl1'];
                                            $controlled_load_sum += $step1['singlerate_2_cl2'] * $rate['ElectricityRate']['controlled_load_2_rate'] / $gst;
                                            $consumption_data['elec_consumption'] += $step1['singlerate_2_cl2'];
                                            $usage_sum = $peak_sum + $controlled_load_sum;
                                            break;
                                        case 'Single Rate + Climate Saver':
                                            if ($summer_days > 0 || $winter_days > 0) {
                                                $summer_usage = $this->calculate($step1['singlerate_cs_peak'], $summer_tier_rates);
                                                $winter_usage = $this->calculate($step1['elec_winter_peak'], $winter_tier_rates);
                                                $consumption_data['elec_consumption'] = $step1['singlerate_cs_peak'] + $step1['elec_winter_peak'];
                                                $peak_sum = $summer_usage + $winter_usage;
                                            } else {
                                                $peak_sum = $this->calculate($step1['singlerate_cs_peak'], $tier_rates);
                                                $consumption_data['elec_consumption'] = $step1['singlerate_cs_peak'];
                                            }
                                            $climate_saver_sum = 0;
                                            if ($rate['ElectricityRate']['climate_saver_rate']) {
                                                if (strpos($rate['ElectricityRate']['climate_saver_rate'], '/') !== false) {
                                                    $climate_saver_off_start = strtotime('01-04-' . date('Y'));
                                                    $climate_saver_off_end = strtotime('31-10-' . date('Y'));
                                                    $climate_saver_rate_arr = explode('/', $rate['ElectricityRate']['climate_saver_rate']);
                                                    $singlerate_cs_billing_start = strtotime(str_replace('/', '-', $step1['singlerate_cs_billing_start']));
                                                    if ($singlerate_cs_billing_start >= $climate_saver_off_start && $singlerate_cs_billing_start <= $climate_saver_off_end) {
                                                        $climate_saver_sum = $step1['singlerate_cs'] * $climate_saver_rate_arr[1] / $gst;
                                                    } else {
                                                        $climate_saver_sum = $step1['singlerate_cs'] * $climate_saver_rate_arr[0] / $gst;
                                                    }
                                                } else {
                                                    $climate_saver_sum = $step1['singlerate_cs'] * $rate['ElectricityRate']['climate_saver_rate'] / $gst;
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
                                            } else {
                                                $peak_sum = $this->calculate($step1['singlerate_cl1_cs_peak'], $tier_rates);
                                                $consumption_data['elec_consumption'] = $step1['singlerate_cl1_cs_peak'];
                                            }
                                            $controlled_load_sum = 0;
                                            if ($rate['ElectricityRate']['controlled_load_tier_1'] && $step1['singlerate_3_cl1'] > $rate['ElectricityRate']['controlled_load_tier_1']) {
                                                $sum1 = $rate['ElectricityRate']['controlled_load_1_rate_1'] / $gst * $rate['ElectricityRate']['controlled_load_tier_1'];
                                                $controlled_load_sum += $rate['ElectricityRate']['controlled_load_1_rate_2'] / $gst * ($step1['singlerate_3_cl1'] - $rate['ElectricityRate']['controlled_load_tier_1']) + $sum1;
                                            } else {
                                                $controlled_load_sum += $step1['singlerate_3_cl1'] * $rate['ElectricityRate']['controlled_load_1_rate_1'] / $gst;
                                            }
                                            $controlled_load_sum += $step1['singlerate_3_cl1'] * $rate['ElectricityRate']['controlled_load_2_rate'] / $gst;
                                            $consumption_data['elec_consumption'] += $step1['singlerate_3_cl1'];
                                            $climate_saver_sum = 0;
                                            if ($rate['ElectricityRate']['climate_saver_rate']) {
                                                if (strpos($rate['ElectricityRate']['climate_saver_rate'], '/') !== false) {
                                                    $climate_saver_off_start = strtotime('01-04-' . date('Y'));
                                                    $climate_saver_off_end = strtotime('31-10-' . date('Y'));
                                                    $climate_saver_rate_arr = explode('/', $rate['ElectricityRate']['climate_saver_rate']);
                                                    $singlerate_cl1_cs_billing_start = strtotime(str_replace('/', '-', $step1['singlerate_cl1_cs_billing_start']));
                                                    if ($singlerate_cl1_cs_billing_start >= $climate_saver_off_start && $singlerate_cl1_cs_billing_start <= $climate_saver_off_end) {
                                                        $climate_saver_sum = $step1['singlerate_3_cs'] * $climate_saver_rate_arr[1] / $gst;
                                                    } else {
                                                        $climate_saver_sum = $step1['singlerate_3_cs'] * $climate_saver_rate_arr[0] / $gst;
                                                    }
                                                } else {
                                                    $climate_saver_sum = $step1['singlerate_3_cs'] * $rate['ElectricityRate']['climate_saver_rate'] / $gst;
                                                }
                                            }
                                            $consumption_data['elec_consumption'] += $step1['singlerate_3_cs'];
                                            $usage_sum = $peak_sum + $controlled_load_sum + $climate_saver_sum;
                                            break;
                                        case 'Time of Use':
                                        case 'Transitional Time of Use':
                                            if ($summer_days > 0 || $winter_days > 0) {
                                                $summer_usage = $this->calculate($step1['timeofuse_peak'], $summer_tier_rates);
                                                $winter_usage = $this->calculate($step1['elec_winter_peak'], $winter_tier_rates);
                                                $consumption_data['elec_consumption'] = $step1['timeofuse_peak'] + $step1['elec_winter_peak'];
                                                $peak_sum = $summer_usage + $winter_usage;
                                            } else {
                                                $peak_sum = $this->calculate($step1['timeofuse_peak'], $tier_rates);
                                                $consumption_data['elec_consumption'] = $step1['timeofuse_peak'];
                                            }
                                            $off_peak_sum = ($rate['ElectricityRate']['off_peak_rate'] && $step1['timeofuse_offpeak']) ? $step1['timeofuse_offpeak'] * $rate['ElectricityRate']['off_peak_rate'] / $gst : 0;
                                            $consumption_data['elec_consumption'] += $step1['timeofuse_offpeak'];
                                            $shoulder_sum = ($rate['ElectricityRate']['shoulder_rate'] && $step1['timeofuse_shoulder']) ? $step1['timeofuse_shoulder'] * $rate['ElectricityRate']['shoulder_rate'] / $gst : 0;
                                            $consumption_data['elec_consumption'] += $step1['timeofuse_shoulder'];
                                            $usage_sum = $peak_sum + $off_peak_sum + $shoulder_sum;
                                            break;
                                        case 'Time of Use (PowerSmart)':
                                            if ($summer_days > 0 || $winter_days > 0) {
                                                $summer_usage = $this->calculate($step1['timeofuse_ps_peak'], $summer_tier_rates);
                                                $winter_usage = $this->calculate($step1['timeofuse_ps_offpeak'], $winter_tier_rates);
                                                $consumption_data['elec_consumption'] = $step1['timeofuse_ps_peak'] + $step1['timeofuse_ps_offpeak'];
                                                $peak_sum = $summer_usage + $winter_usage;
                                            } else {
                                                $peak_sum = $this->calculate($step1['timeofuse_ps_peak'], $tier_rates);
                                                $consumption_data['elec_consumption'] = $step1['timeofuse_ps_peak'];
                                            }
                                            $off_peak_sum = ($rate['ElectricityRate']['off_peak_rate'] && $step1['timeofuse_ps_offpeak']) ? $step1['timeofuse_ps_offpeak'] * $rate['ElectricityRate']['off_peak_rate'] / $gst : 0;
                                            $consumption_data['elec_consumption'] += $step1['timeofuse_ps_offpeak'];
                                            $shoulder_sum = ($rate['ElectricityRate']['shoulder_rate'] && $step1['timeofuse_ps_shoulder']) ? $step1['timeofuse_ps_shoulder'] * $rate['ElectricityRate']['shoulder_rate'] / $gst : 0;
                                            $consumption_data['elec_consumption'] += $step1['timeofuse_ps_shoulder'];
                                            $usage_sum = $peak_sum + $off_peak_sum + $shoulder_sum;
                                            $this->log("OKKEY DEBUG PowerSmart::::: ", 'debug');
                                            $this->log("OKKEY DEBUG usage_sum::::: " . $usage_sum, 'debug');
                                            $this->log("OKKEY DEBUG peak_sum::::: " . $peak_sum, 'debug');
                                            $this->log("OKKEY DEBUG off_peak_sum::::: " . $off_peak_sum, 'debug');
                                            $this->log("OKKEY DEBUG shoulder_sum::::: " . $shoulder_sum, 'debug');
                                            break;
                                        case 'Time of Use (LoadSmart)':
                                            if ($summer_days > 0 || $winter_days > 0) {
                                                $summer_usage = $this->calculate($step1['timeofuse_ls_peak'], $summer_tier_rates);
                                                $winter_usage = $this->calculate($step1['elec_winter_ls_peak'], $winter_tier_rates);
                                                $consumption_data['elec_consumption'] = $step1['timeofuse_ls_peak'] + $step1['timeofuse_ls_offpeak'];
                                                $peak_sum = $summer_usage + $winter_usage;
                                            } else {
                                                $peak_sum = $this->calculate($step1['timeofuse_ls_peak'], $tier_rates);
                                                $consumption_data['elec_consumption'] = $step1['timeofuse_ls_peak'];
                                            }
                                            $off_peak_sum = ($rate['ElectricityRate']['off_peak_rate'] && $step1['timeofuse_ls_offpeak']) ? $step1['timeofuse_ls_offpeak'] * $rate['ElectricityRate']['off_peak_rate'] / $gst : 0;
                                            $consumption_data['elec_consumption'] += $step1['timeofuse_ls_offpeak'];
                                            $shoulder_sum = ($rate['ElectricityRate']['shoulder_rate'] && $step1['timeofuse_ls_shoulder']) ? $step1['timeofuse_ls_shoulder'] * $rate['ElectricityRate']['shoulder_rate'] / $gst : 0;
                                            $consumption_data['elec_consumption'] += $step1['timeofuse_ls_shoulder'];
                                            $usage_sum = $peak_sum + $off_peak_sum + $shoulder_sum;
                                            $this->log("OKKEY DEBUG LoadSmart::::: ", 'debug');
                                            $this->log("OKKEY DEBUG usage_sum::::: " . $usage_sum, 'debug');
                                            $this->log("OKKEY DEBUG peak_sum::::: " . $peak_sum, 'debug');
                                            $this->log("OKKEY DEBUG off_peak_sum::::: " . $off_peak_sum, 'debug');
                                            $this->log("OKKEY DEBUG shoulder_sum::::: " . $shoulder_sum, 'debug');
                                            break;
                                        case 'Time of Use + Climate Saver':
                                            if ($summer_days > 0 || $winter_days > 0) {
                                                $summer_usage = $this->calculate($step1['timeofuse_cs_peak'], $summer_tier_rates);
                                                $winter_usage = $this->calculate($step1['elec_winter_peak'], $winter_tier_rates);
                                                $consumption_data['elec_consumption'] = $step1['timeofuse_cs_peak'] + $step1['elec_winter_peak'];
                                                $peak_sum = $summer_usage + $winter_usage;
                                            } else {
                                                $peak_sum = $this->calculate($step1['timeofuse_cs_peak'], $tier_rates);
                                                $consumption_data['elec_consumption'] = $step1['timeofuse_cs_peak'];
                                            }
                                            $off_peak_sum = ($rate['ElectricityRate']['off_peak_rate'] && $step1['timeofuse_cs_offpeak']) ? $step1['timeofuse_cs_offpeak'] * $rate['ElectricityRate']['off_peak_rate'] / $gst : 0;
                                            $consumption_data['elec_consumption'] += $step1['timeofuse_cs_offpeak'];
                                            $climate_saver_sum = 0;
                                            if ($rate['ElectricityRate']['climate_saver_rate']) {
                                                if (strpos($rate['ElectricityRate']['climate_saver_rate'], '/') !== false) {
                                                    $climate_saver_off_start = strtotime('01-04-' . date('Y'));
                                                    $climate_saver_off_end = strtotime('31-10-' . date('Y'));
                                                    $climate_saver_rate_arr = explode('/', $rate['ElectricityRate']['climate_saver_rate']);
                                                    $timeofuse_cs_billing_start = strtotime(str_replace('/', '-', $step1['timeofuse_cs_billing_start']));
                                                    if ($timeofuse_cs_billing_start >= $climate_saver_off_start && $timeofuse_cs_billing_start <= $climate_saver_off_end) {
                                                        $climate_saver_sum = $step1['timeofuse_cs'] * $climate_saver_rate_arr[1] / $gst;
                                                    } else {
                                                        $climate_saver_sum = $step1['timeofuse_cs'] * $climate_saver_rate_arr[0] / $gst;
                                                    }
                                                } else {
                                                    $climate_saver_sum = $step1['timeofuse_cs'] * $rate['ElectricityRate']['climate_saver_rate'] / $gst;
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
                                            } else {
                                                $peak_sum = $this->calculate($step1['timeofuse_cl1_cs_peak'], $tier_rates);
                                                $consumption_data['elec_consumption'] = $step1['timeofuse_cl1_cs_peak'];
                                            }
                                            $off_peak_sum = ($rate['ElectricityRate']['off_peak_rate'] && $step1['timeofuse_cl1_cs_offpeak']) ? $step1['timeofuse_cl1_cs_offpeak'] * $rate['ElectricityRate']['off_peak_rate'] / $gst : 0;
                                            $consumption_data['elec_consumption'] += $step1['timeofuse_cl1_cs_offpeak'];
                                            $controlled_load_sum = 0;
                                            if ($rate['ElectricityRate']['controlled_load_tier_1'] && $step1['timeofuse_cl1'] > $rate['ElectricityRate']['controlled_load_tier_1']) {
                                                $sum1 = $rate['ElectricityRate']['controlled_load_1_rate_1'] / $gst * $rate['ElectricityRate']['controlled_load_tier_1'];
                                                $controlled_load_sum += $rate['ElectricityRate']['controlled_load_1_rate_2'] / $gst * ($step1['timeofuse_cl1'] - $rate['ElectricityRate']['controlled_load_tier_1']) + $sum1;
                                            } else {
                                                $controlled_load_sum += $step1['timeofuse_cl1'] * $rate['ElectricityRate']['controlled_load_1_rate_1'] / $gst;
                                            }
                                            $consumption_data['elec_consumption'] += $step1['timeofuse_cl1'];
                                            $climate_saver_sum = 0;
                                            if ($rate['ElectricityRate']['climate_saver_rate']) {
                                                if (strpos($rate['ElectricityRate']['climate_saver_rate'], '/') !== false) {
                                                    $climate_saver_off_start = strtotime('01-04-' . date('Y'));
                                                    $climate_saver_off_end = strtotime('31-10-' . date('Y'));
                                                    $climate_saver_rate_arr = explode('/', $rate['ElectricityRate']['climate_saver_rate']);
                                                    $timeofuse_cl1_cs_billing_start = strtotime(str_replace('/', '-', $step1['timeofuse_cl1_cs_billing_start']));
                                                    if ($timeofuse_cl1_cs_billing_start >= $climate_saver_off_start && $timeofuse_cl1_cs_billing_start <= $climate_saver_off_end) {
                                                        $climate_saver_sum = $step1['timeofuse_2_cs'] * $climate_saver_rate_arr[1] / $gst;
                                                    } else {
                                                        $climate_saver_sum = $step1['timeofuse_2_cs'] * $climate_saver_rate_arr[0] / $gst;
                                                    }
                                                } else {
                                                    $climate_saver_sum = $step1['timeofuse_2_cs'] * $rate['ElectricityRate']['climate_saver_rate'] / $gst;
                                                }
                                            }
                                            $consumption_data['elec_consumption'] += $step1['timeofuse_2_cs'];
                                            $usage_sum = $peak_sum + $off_peak_sum + $controlled_load_sum + $climate_saver_sum;
                                            break;
                                        case 'Time of Use + CL1':
                                        case 'Transitional Time of Use + CL1':
                                            if ($summer_days > 0 || $winter_days > 0) {
                                                $summer_usage = $this->calculate($step1['timeofuse_cl1_peak'], $summer_tier_rates);
                                                $winter_usage = $this->calculate($step1['elec_winter_peak'], $winter_tier_rates);
                                                $consumption_data['elec_consumption'] = $step1['timeofuse_cl1_peak'] + $step1['elec_winter_peak'];
                                                $peak_sum = $summer_usage + $winter_usage;
                                            } else {
                                                $peak_sum = $this->calculate($step1['timeofuse_cl1_peak'], $tier_rates);
                                                $consumption_data['elec_consumption'] = $step1['timeofuse_cl1_peak'];
                                            }
                                            $off_peak_sum = ($rate['ElectricityRate']['off_peak_rate'] && $step1['timeofuse_cl1_offpeak']) ? $step1['timeofuse_cl1_offpeak'] * $rate['ElectricityRate']['off_peak_rate'] / $gst : 0;
                                            $consumption_data['elec_consumption'] += $step1['timeofuse_cl1_offpeak'];
                                            $controlled_load_sum = 0;
                                            if ($rate['ElectricityRate']['controlled_load_tier_1'] && $step1['timeofuse_2_cl1'] > $rate['ElectricityRate']['controlled_load_tier_1']) {
                                                $sum1 = $rate['ElectricityRate']['controlled_load_1_rate_1'] / $gst * $rate['ElectricityRate']['controlled_load_tier_1'];
                                                $controlled_load_sum += $rate['ElectricityRate']['controlled_load_1_rate_2'] / $gst * ($step1['timeofuse_2_cl1'] - $rate['ElectricityRate']['controlled_load_tier_1']) + $sum1;
                                            } else {
                                                $controlled_load_sum += $step1['timeofuse_2_cl1'] * $rate['ElectricityRate']['controlled_load_1_rate_1'] / $gst;
                                            }
                                            $consumption_data['elec_consumption'] += $step1['timeofuse_2_cl1'];
                                            $shoulder_sum = ($rate['ElectricityRate']['shoulder_rate'] && $step1['timeofuse_cl1_shoulder']) ? $step1['timeofuse_cl1_shoulder'] * $rate['ElectricityRate']['shoulder_rate'] / $gst : 0;
                                            $consumption_data['elec_consumption'] += $step1['timeofuse_cl1_shoulder'];
                                            $usage_sum = $peak_sum + $off_peak_sum + $controlled_load_sum + $shoulder_sum;
                                            break;
                                        case 'Time of Use + CL2':
                                        case 'Transitional Time of Use + CL2':
                                            if ($summer_days > 0 || $winter_days > 0) {
                                                $summer_usage = $this->calculate($step1['timeofuse_cl2_peak'], $summer_tier_rates);
                                                $winter_usage = $this->calculate($step1['elec_winter_peak'], $winter_tier_rates);
                                                $consumption_data['elec_consumption'] = $step1['timeofuse_cl2_peak'] + $step1['elec_winter_peak'];
                                                $peak_sum = $summer_usage + $winter_usage;
                                            } else {
                                                $peak_sum = $this->calculate($step1['timeofuse_cl2_peak'], $tier_rates);
                                                $consumption_data['elec_consumption'] = $step1['timeofuse_cl2_peak'];
                                            }
                                            $off_peak_sum = ($rate['ElectricityRate']['off_peak_rate'] && $step1['timeofuse_cl2_offpeak']) ? $step1['timeofuse_cl2_offpeak'] * $rate['ElectricityRate']['off_peak_rate'] / $gst : 0;
                                            $consumption_data['elec_consumption'] += $step1['timeofuse_cl2_offpeak'];
                                            $controlled_load_sum = ($rate['ElectricityRate']['controlled_load_2_rate'] && $step1['timeofuse_2_cl2']) ? $step1['timeofuse_2_cl2'] * $rate['ElectricityRate']['controlled_load_2_rate'] / $gst : 0;
                                            $consumption_data['elec_consumption'] += $step1['timeofuse_2_cl2'];
                                            $shoulder_sum = ($rate['ElectricityRate']['shoulder_rate'] && $step1['timeofuse_cl2_shoulder']) ? $step1['timeofuse_cl2_shoulder'] * $rate['ElectricityRate']['shoulder_rate'] / $gst : 0;
                                            $consumption_data['elec_consumption'] += $step1['timeofuse_cl2_shoulder'];
                                            $usage_sum = $peak_sum + $off_peak_sum + $controlled_load_sum + $shoulder_sum;
                                            break;
                                        case 'Time of Use (Tariff 12)':
                                            if ($summer_days > 0 || $winter_days > 0) {
                                                $summer_usage = $this->calculate($step1['timeofuse_tariff12_peak'], $summer_tier_rates);
                                                $winter_usage = $this->calculate($step1['elec_winter_peak'], $winter_tier_rates);
                                                $consumption_data['elec_consumption'] = $step1['timeofuse_tariff12_peak'] + $step1['elec_winter_peak'];
                                                $peak_sum = $summer_usage + $winter_usage;
                                            } else {
                                                $peak_sum = $this->calculate($step1['timeofuse_tariff12_peak'], $tier_rates);
                                                $consumption_data['elec_consumption'] = $step1['timeofuse_tariff12_peak'];
                                            }
                                            $off_peak_sum = ($rate['ElectricityRate']['off_peak_rate']) ? $step1['timeofuse_tariff12_offpeak'] * $rate['ElectricityRate']['off_peak_rate'] / $gst : 0;
                                            $consumption_data['elec_consumption'] += $step1['timeofuse_tariff12_offpeak'];
                                            $shoulder_sum = ($rate['ElectricityRate']['shoulder_rate'] && $step1['timeofuse_tariff12_shoulder']) ? $step1['timeofuse_tariff12_shoulder'] * $rate['ElectricityRate']['shoulder_rate'] / $gst : 0;
                                            $consumption_data['elec_consumption'] += $step1['timeofuse_tariff12_shoulder'];
                                            $usage_sum = $peak_sum + $off_peak_sum + $shoulder_sum;
                                            break;
                                        case 'Time of Use (Tariff 13)':
                                            if ($summer_days > 0 || $winter_days > 0) {
                                                $summer_usage = $this->calculate($step1['timeofuse_tariff13_peak'], $summer_tier_rates);
                                                $winter_usage = $this->calculate($step1['elec_winter_peak'], $winter_tier_rates);
                                                $consumption_data['elec_consumption'] = $step1['timeofuse_tariff13_peak'] + $step1['elec_winter_peak'];
                                                $peak_sum = $summer_usage + $winter_usage;
                                            } else {
                                                $peak_sum = $this->calculate($step1['timeofuse_tariff13_peak'], $tier_rates);
                                                $consumption_data['elec_consumption'] = $step1['timeofuse_tariff13_peak'];
                                            }
                                            $off_peak_sum = ($rate['ElectricityRate']['off_peak_rate']) ? $step1['timeofuse_tariff13_offpeak'] * $rate['ElectricityRate']['off_peak_rate'] / $gst : 0;
                                            $consumption_data['elec_consumption'] += $step1['timeofuse_tariff13_offpeak'];
                                            $shoulder_sum = ($rate['ElectricityRate']['shoulder_rate'] && $step1['timeofuse_tariff13_shoulder']) ? $step1['timeofuse_tariff13_shoulder'] * $rate['ElectricityRate']['shoulder_rate'] / $gst : 0;
                                            $consumption_data['elec_consumption'] += $step1['timeofuse_tariff13_shoulder'];
                                            $usage_sum = $peak_sum + $off_peak_sum + $shoulder_sum;
                                            break;
                                        case 'Flexible Pricing':
                                            if ($summer_days > 0 || $winter_days > 0) {
                                                $summer_usage = $this->calculate($step1['flexible_peak'], $summer_tier_rates);
                                                $winter_usage = $this->calculate($step1['elec_winter_peak'], $winter_tier_rates);
                                                $consumption_data['elec_consumption'] = $step1['flexible_peak'] + $step1['elec_winter_peak'];
                                                $peak_sum = $summer_usage + $winter_usage;
                                            } else {
                                                $peak_sum = $this->calculate($step1['flexible_peak'], $tier_rates);
                                                $consumption_data['elec_consumption'] = $step1['flexible_peak'];
                                            }
                                            $off_peak_sum = ($rate['ElectricityRate']['off_peak_rate']) ? $step1['flexible_offpeak'] * $rate['ElectricityRate']['off_peak_rate'] / $gst : 0;
                                            $consumption_data['elec_consumption'] += $step1['flexible_offpeak'];
                                            $shoulder_sum = ($rate['ElectricityRate']['shoulder_rate']) ? $step1['flexible_shoulder'] * $rate['ElectricityRate']['shoulder_rate'] / $gst : 0;
                                            $consumption_data['elec_consumption'] += $step1['flexible_shoulder'];
                                            $usage_sum = $peak_sum + $off_peak_sum + $shoulder_sum;
                                            break;
                                    }
                                    $stp_sum_elec = 0;
                                    $discount_elec = 0;
                                    if ($rate['ElectricityRate']['stp_period'] == 'Y') {
                                        if ($summer_days > 0 || $winter_days > 0) {
                                            $elec_billing = ($summer_days + $winter_days) / 365;
                                        } else {
                                            $elec_billing = $step1['elec_billing_days'] / 365;
                                        }
                                    } else if ($rate['ElectricityRate']['stp_period'] == 'Q') {
                                        if ($summer_days > 0 || $winter_days > 0) {
                                            $elec_billing = ($summer_days + $winter_days) / 91.25;
                                        } else {
                                            $elec_billing = $step1['elec_billing_days'] / 91.25;
                                        }
                                    } else if ($rate['ElectricityRate']['stp_period'] == 'M') {
                                        if ($summer_days > 0 || $winter_days > 0) {
                                            $elec_billing = ($summer_days + $winter_days) / 30.42;
                                        } else {
                                            $elec_billing = $step1['elec_billing_days'] / 30.42;
                                        }
                                    } else {
                                        if ($summer_days > 0 || $winter_days > 0) {
                                            $elec_billing = $summer_days + $winter_days;
                                        } else {
                                            $elec_billing = $step1['elec_billing_days'];
                                        }
                                    }
                                    $this->log("OKKEY DEBUG step1-'elec_billing_days'::::" . $step1['elec_billing_days'], 'debug');
                                    $stp_sum_elec = $elec_billing * ($rate['ElectricityRate']['stp'] / $gst);
                                    $this->log("OKKEY DEBUG stp_sum_elec ::::" . $stp_sum_elec, 'debug');
                                    $this->log("OKKEY DEBUG plan discount apply ::::" . json_encode($plan['Plan']), 'debug');
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

                                        $this->log("OKKEY DEBUG filters ::::" . json_encode($filters), 'debug');
                                        if (!empty($filters['discount_type'])) {
                                            if ($plan['Plan']['discount_pay_on_time_elec'] && in_array('Pay On Time', $filters['discount_type'])) {
                                                if (substr($plan['Plan']['discount_pay_on_time_elec'], 0, 1) == '$') {
                                                    //$discount_elec += $plan['Plan']['discount_pay_on_time_elec'];
                                                } else {
                                                    $discount_elec += $temp_total_elec * $plan['Plan']['discount_pay_on_time_elec'] / 100;
                                                }
                                            }
                                            if ($plan['Plan']['discount_guaranteed_elec'] && in_array('Guaranteed', $filters['discount_type'])) {
                                                if (substr($plan['Plan']['discount_guaranteed_elec'], 0, 1) == '$') {
                                                    //$discount_elec += $plan['Plan']['discount_guaranteed_elec'];
                                                } else {
                                                    if ($plan['Plan']['retailer'] != 'Powershop') {
                                                        $discount_elec += $temp_total_elec * $plan['Plan']['discount_guaranteed_elec'] / 100;
                                                    }
                                                }
                                            }
                                            if ($plan['Plan']['discount_direct_debit_elec'] && in_array('Direct Debit', $filters['discount_type'])) {
                                                if (substr($plan['Plan']['discount_direct_debit_elec'], 0, 1) == '$') {
                                                    //$discount_elec += $plan['Plan']['discount_direct_debit_elec'];
                                                } else {
                                                    $discount_elec += $temp_total_elec * $plan['Plan']['discount_direct_debit_elec'] / 100;
                                                }
                                            }
                                            if ($plan['Plan']['discount_dual_fuel_elec'] && in_array('Dual Fuel', $filters['discount_type'])) {
                                                if (substr($plan['Plan']['discount_dual_fuel_elec'], 0, 1) == '$') {
                                                    //$discount_elec += $plan['Plan']['discount_direct_debit_elec'];
                                                } else {
                                                    $discount_elec += $temp_total_elec * $plan['Plan']['discount_dual_fuel_elec'] / 100;
                                                }
                                            }
                                            if ($plan['Plan']['discount_prepay_elec'] && in_array('Prepay', $filters['discount_type'])) {
                                                if (substr($plan['Plan']['discount_prepay_elec'], 0, 1) == '$') {
                                                    //$discount_elec += $plan['Plan']['discount_prepay_elec'];
                                                } else {
                                                    $discount_elec += $temp_total_elec * $plan['Plan']['discount_prepay_elec'] / 100;
                                                }
                                            }
                                            if ($plan['Plan']['discount_bonus_sumo'] && in_array('Bonus', $filters['discount_type'])) {
                                                if (substr($plan['Plan']['discount_bonus_sumo'], 0, 1) == '$') {
                                                    //$discount_elec += $plan['Plan']['discount_bonus_sumo'];
                                                } else {
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
                                    } else {
                                        $this->log("OKKEY DEBUG plan discount apply into else::::", 'debug');
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
                                    case 'ActewAGL':
                                        $plan['Plan']['solar_rate']['retailer'] = $solar_rebate_scheme_rate['SolarRebateScheme']['actewagl'];
                                        break;
                                    case 'Elysian Energy':
                                        $plan['Plan']['solar_rate']['retailer'] = $solar_rebate_scheme_rate['SolarRebateScheme']['elysian_energy'];
                                        break;
                                    case 'Testing Retailer':
                                        $plan['Plan']['solar_rate']['retailer'] = $solar_rebate_scheme_rate['SolarRebateScheme']['testing_retailer'];
                                        break;
                                    case 'OVO Energy':
                                        $plan['Plan']['solar_rate']['retailer'] = $solar_rebate_scheme_rate['SolarRebateScheme']['ovo_energy'];
                                        break;
                                }

                                if ($plan['Plan']['solar_boost_fit']) {
                                    $plan['Plan']['solar_rate']['retailer_original'] = $plan['Plan']['solar_rate']['retailer'];
                                    $plan['Plan']['solar_rate']['retailer'] = $plan['Plan']['solar_boost_fit'];
                                }

                                if ($step1['solar_generated']) {
                                    $govt_solar_rate = ($plan['Plan']['solar_rate']['government'] == '1 for 1') ? round($tier_rates[0]['rate'] * 100, 3) : $plan['Plan']['solar_rate']['government'];
                                    if ($step1['looking_for'] == 'Move Properties' && in_array($state, array('NSW', 'QLD'))) {
                                        $govt_solar_rate = 0;
                                    }
                                    $retailer_solar_rate = ($plan['Plan']['solar_rate']['retailer'] == '1 for 1') ? round($tier_rates[0]['rate'] * 100, 3) : $plan['Plan']['solar_rate']['retailer'];


                                    $solar_credit = $step1['solar_generated'] * ($govt_solar_rate + $retailer_solar_rate) / 100;

                                    if ($plan['Plan']['solar_boost_fit'] && $plan['Plan']['solar_boost_cap']) {
                                        if ($step1['elec_billing_days']) {
                                            $solar_tier = $plan['Plan']['solar_boost_cap'] * $step1['elec_billing_days'];
                                            if ($step1['solar_generated'] > $solar_tier) {
                                                $solar_generated_remaining = $step1['solar_generated'] - $solar_tier;
                                                $solar_credit = $solar_tier * $plan['Plan']['solar_boost_fit'] / 100;
                                                $solar_credit += $solar_generated_remaining * $plan['Plan']['solar_rate']['retailer_original'] / 100;
                                            }
                                        }
                                    }

                                    $plan['Plan']['solar_credit'] = $solar_credit;

                                    $plan['Plan']['total_elec'] = round($plan['Plan']['total_elec'] - $solar_credit);
                                    $plan['Plan']['total_inc_discount_elec'] = round($plan['Plan']['total_inc_discount_elec'] - $solar_credit);
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

                            $gas_rate_start_or = array(
                                'or' => array(
                                    'GasRate.rate_start' => '0000-00-00',
                                    'GasRate.rate_start <=' => date('Y-m-d'),
                                ),
                            );
                            $conditions[] = $gas_rate_start_or;

                            $gas_rate_expire_or = array(
                                'or' => array(
                                    'GasRate.rate_expire' => '0000-00-00',
                                    'GasRate.rate_expire >=' => date('Y-m-d'),
                                ),
                            );
                            $conditions[] = $gas_rate_expire_or;

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
                                    case 'Momentum':
                                        $distributor_field = 'momentum_distributor';
                                        break;
                                    case 'Powershop':
                                        $distributor_field = 'powershop_distributor';
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
                                    case 'Powerdirect and AGL':
                                        $distributor_field = 'pd_agl_distributor';
                                        break;
                                    case 'ActewAGL':
                                        $distributor_field = 'actewagl_distributor';
                                        break;
                                    case 'Elysian Energy':
                                        $distributor_field = 'elysian_energy_distributor';
                                        break;
                                    case 'Testing Retailer':
                                        $distributor_field = 'testing_retailer_distributor';
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
                                    $gst = 1;
                                    if (isset($rate['GasRate']['gst_rates']) && $rate['GasRate']['gst_rates'] == 'Yes') {
                                        $gst = 1.1;
                                    }
                                    $consumption_data['gas_billing_days'] = $step1['gas_billing_days'];
                                    $consumption_data['gas_consumption'] = $step1['gas_peak'] + $step1['gas_off_peak'];
                                    $gas_peak = $step1['gas_peak'];
                                    $gas_off_peak = $step1['gas_off_peak'];
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
                                                } else {
                                                    $peak_days = ($billing_end_date - $billing_start_date) / (3600 * 24);
                                                }
                                                $off_peak_days = $step1['gas_billing_days'] - $peak_days;
                                            } elseif ($billing_end_date >= $peak_start_date && $billing_end_date <= $peak_end_date) {
                                                $peak_days = ($billing_end_date - $peak_start_date) / (3600 * 24);
                                                $off_peak_days = $step1['gas_billing_days'] - $peak_days;
                                            } else {
                                                $peak_days = 0;
                                                $off_peak_days = $step1['gas_billing_days'];
                                            }
                                        } else if (!$step1['gas_peak']) {
                                            $peak_days = 0;
                                            $off_peak_days = $step1['gas_billing_days'];
                                        } else if (!$step1['gas_off_peak']) {
                                            $off_peak_days = 0;
                                            $peak_days = $step1['gas_billing_days'];
                                        }

                                        if ($peak_days == 0) {
                                            $gas_off_peak = $step1['gas_peak'] + $step1['gas_off_peak'];
                                            $gas_peak = 0;
                                        }
                                        if ($off_peak_days == 0) {
                                            $gas_peak = $step1['gas_peak'] + $step1['gas_off_peak'];
                                            $gas_off_peak = 0;
                                        }
                                        if ($off_peak_days > 0 && !$rate['GasRate']['off_peak_rate_1']) {
                                            $peak_days = $peak_days + $off_peak_days;
                                            $off_peak_days = 0;
                                            $gas_peak = $step1['gas_peak'] + $step1['gas_off_peak'];
                                            $gas_off_peak = 0;
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
                                                array('tier' => $rate['GasRate']['peak_tier_1'], 'rate' => $rate['GasRate']['peak_rate_1'] / 100 / $gst),
                                                array('tier' => $rate['GasRate']['peak_tier_2'], 'rate' => $rate['GasRate']['peak_rate_2'] / 100 / $gst),
                                                array('tier' => $rate['GasRate']['peak_tier_3'], 'rate' => $rate['GasRate']['peak_rate_3'] / 100 / $gst),
                                                array('tier' => $rate['GasRate']['peak_tier_4'], 'rate' => $rate['GasRate']['peak_rate_4'] / 100 / $gst),
                                                array('tier' => $rate['GasRate']['peak_tier_5'], 'rate' => $rate['GasRate']['peak_rate_5'] / 100 / $gst),
                                                array('tier' => 0, 'rate' => $rate['GasRate']['peak_rate_6'] / 100 / $gst),
                                            );
                                            $peak_sum = $this->calculate($gas_peak, $peak_tier_rates, true);
                                        }
                                        $off_peak_sum = 0;
                                        if ($off_peak_days > 0) {
                                            $off_peak_tier_rates = array(
                                                array('tier' => $rate['GasRate']['off_peak_tier_1'], 'rate' => $rate['GasRate']['off_peak_rate_1'] / 100 / $gst),
                                                array('tier' => $rate['GasRate']['off_peak_tier_2'], 'rate' => $rate['GasRate']['off_peak_rate_2'] / 100 / $gst),
                                                array('tier' => $rate['GasRate']['off_peak_tier_3'], 'rate' => $rate['GasRate']['off_peak_rate_3'] / 100 / $gst),
                                                array('tier' => $rate['GasRate']['off_peak_tier_4'], 'rate' => $rate['GasRate']['off_peak_rate_4'] / 100 / $gst),
                                                array('tier' => 0, 'rate' => $rate['GasRate']['off_peak_rate_5'] / 100 / $gst),
                                            );
                                            $off_peak_sum = $this->calculate($gas_off_peak, $off_peak_tier_rates);
                                        }
                                    } else {
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
                                            array('tier' => $rate['GasRate']['peak_tier_1'], 'rate' => $rate['GasRate']['peak_rate_1'] / 100 / $gst),
                                            array('tier' => $rate['GasRate']['peak_tier_2'], 'rate' => $rate['GasRate']['peak_rate_2'] / 100 / $gst),
                                            array('tier' => $rate['GasRate']['peak_tier_3'], 'rate' => $rate['GasRate']['peak_rate_3'] / 100 / $gst),
                                            array('tier' => $rate['GasRate']['peak_tier_4'], 'rate' => $rate['GasRate']['peak_rate_4'] / 100 / $gst),
                                            array('tier' => $rate['GasRate']['peak_tier_5'], 'rate' => $rate['GasRate']['peak_rate_5'] / 100 / $gst),
                                            array('tier' => 0, 'rate' => $rate['GasRate']['peak_rate_6'] / 100 / $gst),
                                        );
                                        $peak_sum = $this->calculate(($gas_peak + $gas_off_peak), $peak_tier_rates, true);
                                    }
                                    $usage_sum = $peak_sum + $off_peak_sum;
                                    $stp_sum_gas = 0;
                                    $discount_gas = 0;
                                    if ($rate['GasRate']['stp_period'] == 'Y') {
                                        $gas_billing = $step1['gas_billing_days'] / 365;
                                    } else if ($rate['GasRate']['stp_period'] == 'Q') {
                                        $gas_billing = $step1['gas_billing_days'] / 91.25;
                                    } else if ($rate['GasRate']['stp_period'] == 'M') {
                                        $gas_billing = $step1['gas_billing_days'] / 30.42;
                                    } else {
                                        $gas_billing = $step1['gas_billing_days'];
                                    }
                                    $stp_sum_gas = $gas_billing * ($rate['GasRate']['stp'] / $gst);
                                    if ($plan['Plan']['discount_applies'] || $plan['Plan']['discount_applies_gas']) {
                                        $temp_total_gas = 0;
                                        $discount_applies_gas = $plan['Plan']['discount_applies'];
                                        if ($plan['Plan']['discount_applies_gas']) {
                                            $discount_applies_gas = $plan['Plan']['discount_applies_gas'];
                                        }
                                        switch ($discount_applies_gas) {
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
                                                } else {
                                                    $discount_gas += $temp_total_gas * $plan['Plan']['discount_pay_on_time_gas'] / 100;
                                                }
                                            }
                                            if ($plan['Plan']['discount_guaranteed_gas'] && in_array('Guaranteed', $filters['discount_type'])) {
                                                if (substr($plan['Plan']['discount_guaranteed_gas'], 0, 1) == '$') {
                                                    //$discount_gas += $plan['Plan']['discount_guaranteed_gas'];
                                                } else {
                                                    $discount_gas += $temp_total_gas * $plan['Plan']['discount_guaranteed_gas'] / 100;
                                                }
                                            }
                                            if ($plan['Plan']['discount_direct_debit_gas'] && in_array('Direct Debit', $filters['discount_type'])) {
                                                if (substr($plan['Plan']['discount_direct_debit_gas'], 0, 1) == '$') {
                                                    //$discount_gas += $plan['Plan']['discount_direct_debit_gas'];
                                                } else {
                                                    $discount_gas += $temp_total_gas * $plan['Plan']['discount_direct_debit_gas'] / 100;
                                                }
                                            }
                                            if ($plan['Plan']['discount_dual_fuel_gas'] && in_array('Dual Fuel', $filters['discount_type'])) {
                                                if (substr($plan['Plan']['discount_direct_debit_gas'], 0, 1) == '$') {
                                                    //$discount_gas += $plan['Plan']['discount_direct_debit_gas'];
                                                } else {
                                                    $discount_gas += $temp_total_gas * $plan['Plan']['discount_dual_fuel_gas'] / 100;
                                                }
                                            }
                                        }
                                        $plan['Plan']['discount_gas'] = $discount_gas;
                                        $discount_applies_gas = $plan['Plan']['discount_applies'];
                                        if ($plan['Plan']['discount_applies_gas']) {
                                            $discount_applies_gas = $plan['Plan']['discount_applies_gas'];
                                        }
                                        switch ($discount_applies_gas) {
                                            case 'Usage':
                                                $plan['Plan']['total_gas'] = round(($usage_sum + $stp_sum_gas) * 1.1);
                                                $plan['Plan']['total_inc_discount_gas'] = round(($usage_sum - $discount_gas + $stp_sum_gas) * 1.1);
                                                break;
                                            case 'Usage + STP + GST':
                                                $plan['Plan']['total_gas'] = round(($usage_sum + $stp_sum_gas) * 1.1);
                                                $plan['Plan']['total_inc_discount_gas'] = round(($usage_sum + $stp_sum_gas) * 1.1 - $discount_gas);
                                                break;
                                        }
                                    } else {
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
                                } else if ($filters['plan_type'] == 'Gas') {
                                    $plans[$plan['Plan']['total_inc_discount_gas'] * 10000 + $plan['Plan']['id']] = $plan;
                                } else if ($filters['plan_type'] == 'Dual') {
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

                    // ranking
                    $i = 0;
                    foreach ($plans as $k => $p) {
                        $i++;
                        $p['Plan']['ranking'] = $i;
                        $plans[$k] = $p;
                    }
                }
                break;
        }

        $sid = $this->Session->read('User.sid');
        $postcode = $this->Session->read('User.postcode');
        $state = $this->Session->read('User.state');
        $suburb = $this->Session->read('User.suburb');
        $conversion_tracked = ($this->Session->read('User.conversion_tracked')) ? 1 : 0;
        $outbound = 0;
        if ($this->Session->check('User.outbound')) {
            $outbound = $this->Session->read('User.outbound');
        }
        $inbound = 0;
        if ($this->Session->check('User.inbound')) {
            $inbound = $this->Session->read('User.inbound');
        }
        $this->set(compact('step', 'sid', 'postcode', 'state', 'suburb', 'step1', 'tracking', 'step2', 'conversion_tracked', 'states_arr', 'payment_options_arr', 'plans', 'top_picks', 'view_top_picks', 'available_retailers', 'available_discount_type', 'available_contract_length', 'available_payment_options', 'filters', 'outbound', 'inbound'));
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

    public function compare_save($step = 1)
    {
        if ($this->request->is('put') || $this->request->is('post')) {
            if (!in_array($step, array(1, 2, 3))) {
                $step = 1;
            }
            $sid = $this->Session->read('User.sid');
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
                    if (isset($this->request->data['campaign_id'])) {
                        $this->Session->write('User.campaign_id', $this->request->data['campaign_id']);
                    }
                    if (isset($this->request->data['campaign_name'])) {
                        $this->Session->write('User.campaign_name', $this->request->data['campaign_name']);
                    }
                    if (isset($this->request->data['first_campaign'])) {
                        $this->Session->write('User.first_campaign', $this->request->data['first_campaign']);
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
                        'campaign_id' => (isset($this->request->data['campaign_id'])) ? $this->request->data['campaign_id'] : '',
                        'campaign_name' => (isset($this->request->data['campaign_name'])) ? $this->request->data['campaign_name'] : '',
                        'first_campaign' => (isset($this->request->data['first_campaign'])) ? $this->request->data['first_campaign'] : '',
                        'campaign_source' => (isset($this->request->data['campaign_source'])) ? $this->request->data['campaign_source'] : '',
                        'centre_name' => (isset($this->request->data['centre_name'])) ? $this->request->data['centre_name'] : '',
                        'lead_origin' => (isset($this->request->data['lead_origin'])) ? $this->request->data['lead_origin'] : '',
                        'plan_type' => (isset($this->request->data['plan_type'])) ? $this->request->data['plan_type'] : '',
                        'is_broadband' => (isset($this->request->data['is_broadband']) && $this->request->data['is_broadband']) ? 1 : 0,
                        'customer_type' => (isset($this->request->data['customer_type'])) ? $this->request->data['customer_type'] : '',
                        'is_soho' => (isset($this->request->data['is_soho']) && $this->request->data['is_soho']) ? 1 : 0,
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
                        'elec_current_discount_choice' => (isset($this->request->data['elec_current_discount_choice'])) ? $this->request->data['elec_current_discount_choice'] : '',
                        'elec_current_discount' => (isset($this->request->data['elec_current_discount'])) ? $this->request->data['elec_current_discount'] : '',
                        'elec_current_discount_type' => (isset($this->request->data['elec_current_discount_type'])) ? $this->request->data['elec_current_discount_type'] : '',
                        'elec_current_discount_applies' => (isset($this->request->data['elec_current_discount_applies'])) ? $this->request->data['elec_current_discount_applies'] : '',
                        'gas_current_discount_choice' => (isset($this->request->data['gas_current_discount_choice'])) ? $this->request->data['gas_current_discount_choice'] : '',
                        'gas_current_discount' => (isset($this->request->data['gas_current_discount'])) ? $this->request->data['gas_current_discount'] : '',
                        'gas_current_discount_type' => (isset($this->request->data['gas_current_discount_type'])) ? $this->request->data['gas_current_discount_type'] : '',
                        'gas_current_discount_applies' => (isset($this->request->data['gas_current_discount_applies'])) ? $this->request->data['gas_current_discount_applies'] : '',
                        'nmi' => $nmi,
                        'nmi_distributor' => $nmi_distributor,
                        'tariff_parent' => (isset($this->request->data['tariff_parent'])) ? $this->request->data['tariff_parent'] : '',
                        'tariff1' => (isset($this->request->data['tariff1'])) ? $this->request->data['tariff1'] : '',
                        'tariff2' => (isset($this->request->data['tariff2'])) ? $this->request->data['tariff2'] : '',
                        'tariff3' => (isset($this->request->data['tariff3'])) ? $this->request->data['tariff3'] : '',
                        'tariff4' => (isset($this->request->data['tariff4'])) ? $this->request->data['tariff4'] : '',
                        'solar_generated' => (isset($this->request->data['solar_generated'])) ? $this->request->data['solar_generated'] : '',
                        'inverter_capacity' => (isset($this->request->data['inverter_capacity'])) ? $this->request->data['inverter_capacity'] : '',
                        'tenant_owner' => (isset($this->request->data['tenant_owner'])) ? $this->request->data['tenant_owner'] : '',
                        'battery_storage_solution' => (isset($this->request->data['battery_storage_solution'])) ? $this->request->data['battery_storage_solution'] : '',
                        'battery_storage_solar_solution' => (isset($this->request->data['battery_storage_solar_solution'])) ? $this->request->data['battery_storage_solar_solution'] : '',
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
                        'agent_id' => (isset($this->request->data['agent_id'])) ? $this->request->data['agent_id'] : '',
                        'referring_agent' => (isset($this->request->data['referring_agent'])) ? $this->request->data['referring_agent'] : '',
                    );
                    $this->Session->write('User.step1', $data);
                    // Post to velocify
                    $submission = array();
                    $submission['submitted']['FuelType'] = $data['plan_type'];
                    $submission['BusOrResidential'] = ($data['customer_type'] == 'SME' || $data['is_soho'] == 1) ? 'Business' : 'Residential';
                    $submission['submitted']['SaleType'] = ($data['customer_type'] == 'SME') ? 'BUS' : 'RES';
                    $submission['submitted']['MoveinOrTransfer'] = ($data['looking_for'] == 'Move Properties') ? 'Move In' : 'Transfer';
                    $submission['submitted']['FirstName'] = $this->request->data['first_name'];
                    $submission['submitted']['surname'] = $this->request->data['surname'];

                    $submission['submitted']['MobileNumber'] = (isset($this->request->data['mobile']) && $this->request->data['mobile']) ? $this->request->data['mobile'] : 0;
                    $submission['submitted']['primaryPhone'] = (isset($this->request->data['mobile']) && $this->request->data['mobile']) ? $this->request->data['mobile'] : '';
                    $submission['submitted']['HomePhone'] = (isset($this->request->data['home_phone']) && $this->request->data['home_phone']) ? $this->request->data['home_phone'] : 0;
                    $submission['submitted']['WorkNumber'] = (isset($this->request->data['work_number']) && $this->request->data['work_number']) ? $this->request->data['work_number'] : 0;

                    $ban_phone_numbers = unserialize(BAN_PHONE_NUMBERS);
                    if (in_array($submission['submitted']['MobileNumber'], $ban_phone_numbers)) {
                        $submission['submitted']['status'] = '*TestStatus';
                    }
                    if (in_array($submission['submitted']['HomePhone'], $ban_phone_numbers)) {
                        $submission['submitted']['status'] = '*TestStatus';
                    }
                    if (in_array($submission['submitted']['WorkNumber'], $ban_phone_numbers)) {
                        $submission['submitted']['status'] = '*TestStatus';
                    }

                    $submission['submitted']['EmailM'] = $this->request->data['email'];
                    $submission['submitted']['SuburbSupply'] = $this->request->data['suburb'];
                    $submission['submitted']['PostcodeSupply'] = $this->request->data['postcode'];
                    $submission['submitted']['StateSupply'] = $this->request->data['state'];

                    if ($data['customer_type'] == 'SME' && $this->request->data['company_industry']) {
                        $submission['submitted']['CompanyIndustry'] = $this->request->data['company_industry'];
                    }

                    $outbound = $this->Session->read('User.outbound');
                    if ($outbound) {
                        $contact_code = $this->Session->read('User.contact_code');
                        $submission['submitted']['ContactCode'] = $contact_code;
                        $submission['submitted']['call_medium'] = 'Outbound';
                    }
                    $inbound = $this->Session->read('User.inbound');
                    if ($inbound) {
                        $submission['submitted']['call_medium'] = 'Inbound';
                    }

                    if ($nmi) {
                        $submission['submitted']['NMICode'] = $nmi;
                    }

                    if (in_array($data['plan_type'], array('Elec', 'Dual'))) {
                        if ($data['elec_supplier']) {
                            $submission['submitted']['CurrentRetailerElec'] = $data['elec_supplier'];
                        } else {
                            $submission['submitted']['CurrentRetailerElec'] = $data['elec_supplier2'];
                        }

                        if (isset($data['elec_current_discount_choice']) && $data['elec_current_discount_choice'] == 'Yes' && $data['elec_current_discount']) {
                            $submission['submitted']['current_electricity_discount'] = $data['elec_current_discount'];
                            $submission['submitted']['discount_condition_electricity'] = $data['elec_current_discount_type'];
                            $submission['submitted']['discount_portion_electricity'] = $data['elec_current_discount_applies'];
                        }

                    }
                    if (in_array($data['plan_type'], array('Gas', 'Dual'))) {
                        if ($data['gas_supplier']) {
                            $submission['submitted']['CurrentRetailerGas'] = $data['gas_supplier'];
                        } else {
                            $submission['submitted']['CurrentRetailerGas'] = $data['gas_supplier2'];
                        }

                        if (isset($data['gas_current_discount_choice']) && $data['gas_current_discount_choice'] == 'Yes' && $data['gas_current_discount']) {
                            $submission['submitted']['current_gas_discount'] = $data['gas_current_discount'];
                            $submission['submitted']['discount_condition_gas'] = $data['gas_current_discount_type'];
                            $submission['submitted']['discount_portion_gas'] = $data['gas_current_discount_applies'];
                        }
                    }

                    $tariffs = array();
                    $solar_specific_plan = false;
                    if ($data['tariff1']) {
                        $tariff1 = explode('|', $data['tariff1']);
                        $tariffs[] = $tariff1[0];
                        if ($tariff1[3] == 'Solar') {
                            $solar_specific_plan = true;
                        }
                    }
                    if ($data['tariff2']) {
                        $tariff2 = explode('|', $data['tariff2']);
                        $tariffs[] = $tariff2[0];
                        if ($tariff2[3] == 'Solar') {
                            $solar_specific_plan = true;
                        }
                    }
                    if ($data['tariff3']) {
                        $tariff3 = explode('|', $data['tariff3']);
                        $tariffs[] = $tariff3[0];
                        if ($tariff3[3] == 'Solar') {
                            $solar_specific_plan = true;
                        }
                    }
                    if ($data['tariff4']) {
                        $tariff4 = explode('|', $data['tariff4']);
                        $tariffs[] = $tariff4[0];
                        if ($tariff4[3] == 'Solar') {
                            $solar_specific_plan = true;
                        }
                    }
                    if (!empty($tariffs)) {
                        $submission['submitted']['MSATSTariffCode'] = implode('/', $tariffs);
                    }
                    if ($solar_specific_plan) {
                        $submission['submitted']['SolarPanels'] = 'Yes';
                        if ($data['battery_storage_solution']) {
                            $submission['submitted']['BatteryStorageEOI'] = $data['battery_storage_solution'];
                        }

                    } elseif (!empty($tariffs)) {
                        if ($data['battery_storage_solar_solution']) {
                            $submission['submitted']['BatteryStorageSolarEOI'] = $data['battery_storage_solar_solution'];
                        }
                    }

                    $submission['submitted']['TenantOwner'] = $data['tenant_owner'];

                    if ($data['is_broadband']) {
                        $count = $this->BroadbandLog->find('count', array(
                            'conditions' => array(
                                'BroadbandLog.phone' => $data['mobile'],
                                'BroadbandLog.submitted >' => date('Y-m-d H:i:s', strtotime('-8 hours')),
                            )
                        ));

                        if ($count <= 0) {
                            $broadband_data = array(
                                'firstName' => $this->request->data['first_name'],
                                'surname' => $this->request->data['surname'],
                                'phone' => $this->request->data['mobile'],
                                'Email' => $this->request->data['email'],
                                'Postcode' => $this->request->data['postcode'],
                                'ReferringAgent' => $this->request->data['referring_agent'],
                                'medium' => 'free',
                            );
                            $broadband_request = json_encode($broadband_data);
                            $broadband_response = $this->broadband($broadband_request);
                            if ($broadband_response) {
                                if ($broadband_response['Response'] == 'Success') {
                                    $cb_lead_id = $broadband_response['Reference Number'];
                                    $submission['submitted']['cb_lead_id'] = $cb_lead_id;

                                    $this->BroadbandLog->create();
                                    $this->BroadbandLog->save(array('BroadbandLog' => array(
                                        'leadid' => $this->request->data['sid'],
                                        'phone' => $this->request->data['mobile'],
                                        'request' => $broadband_request,
                                        'response' => serialize($broadband_response),
                                        'submitted' => date('Y-m-d H:i:s'),
                                    )));
                                }
                            }
                        }
                    }

                    $submission['submitted']['ConnectionDate'] = $submission['submitted']['MoveinDate'] = '0';
                    $submission['submitted']['bpid_elec'] = '';
                    if ($data['looking_for'] == 'Move Properties' && $data['move_in_date']) {
                        $submission['submitted']['bpid_elec'] = $data['move_in_date'];

                        $data['move_in_date'] = str_replace('/', '-', $data['move_in_date']);
                        $submission['submitted']['ConnectionDate'] = $submission['submitted']['MoveinDate'] = date('m/d/Y', strtotime($data['move_in_date']));
                    }

                    if ($this->request->data['sid']) {
                        $sid = $this->request->data['sid'];
                        $campaign_id = $this->request->data['campaign_id'];
                        $campaign_name = $this->request->data['campaign_name'];
                        $first_campaign = $this->request->data['first_campaign'];
                        if ($campaign_id) {
                            switch ($campaign_id) {
                                case '100':
                                    if (!$first_campaign) {
                                        $first_campaign = $submission['submitted']['first_campaign_name'] = 'Electrician Inbound Leads';
                                    }
                                    break;
                                case '95':
                                    if (!$first_campaign) {
                                        $first_campaign = $submission['submitted']['first_campaign_name'] = 'True Value Solar';
                                    }
                                    break;
                                case '1':
                                    if (!$first_campaign) {
                                        $first_campaign = $submission['submitted']['first_campaign_name'] = 'Phone';
                                    }
                                    break;
                            }
                        } else {
                            if (!$first_campaign) {
                                $first_campaign = $submission['submitted']['first_campaign_name'] = $campaign_name;
                            }
                        }

                        $agent_id = '';
                        if (isset($this->request->data['agent_id']) && $this->request->data['agent_id']) {
                            $agent_id = $this->request->data['agent_id'];
                            // Chelsea / Sean / Jolene
                            if (in_array($agent_id, array('125', '191', '196'))) {
                                $submission['submitted']['status'] = '*TestStatus';
                            }
                        }

                        $this->update_lead($campaign_id, $sid, $submission);

                        $agent_name = '';
                        if (isset($this->request->data['agent_name']) && $this->request->data['agent_name']) {
                            $agent_name = $this->request->data['agent_name'];
                        }

                        if ($agent_id && $agent_name) {
                            $agent = $this->assign_to_agent($sid, $agent_id, $agent_name);
                        }
                    } else {
                        $campaign_id = (isset($this->request->data['campaign_id']) && $this->request->data['campaign_id']) ? $this->request->data['campaign_id'] : 1;
                        $campaign_name = (isset($this->request->data['campaign_name']) && $this->request->data['campaign_name']) ? $this->request->data['campaign_name'] : 'Phone';
                        $first_campaign = (isset($this->request->data['first_campaign']) && $this->request->data['first_campaign']) ? $this->request->data['first_campaign'] : 'Phone';
                        $submission['submitted']['first_campaign_name'] = $first_campaign;

                        if (isset($this->request->data['centre_name']) && $this->request->data['centre_name']) {
                            $submission['submitted']['streaming_frequency'] = $this->request->data['centre_name'];
                        }
                        if (isset($this->request->data['campaign_source']) && $this->request->data['campaign_source']) {
                            $submission['submitted']['source'] = $this->request->data['campaign_source'];
                        }

                        $agent_id = '';
                        if (isset($this->request->data['agent_id']) && $this->request->data['agent_id']) {
                            $agent_id = $this->request->data['agent_id'];
                            // Chelsea / Sean / Jolene
                            if (in_array($agent_id, array('125', '191', '196'))) {
                                $submission['submitted']['status'] = '*TestStatus';
                            }
                        }
                        $sid = $this->create_lead($campaign_id, $submission);

                        if ($agent_id) {
                            $agent = $this->assign_to_agent($sid, $agent_id);
                        }
                    }

                    $this->Session->write('User.sid', $sid);
                    $this->Session->write('User.campaign_id', $campaign_id);
                    $this->Session->write('User.campaign_name', $campaign_name);
                    $this->Session->write('User.first_campaign', $first_campaign);
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
                        'campaign_id' => $step1['campaign_id'],
                        'campaign_name' => $step1['campaign_name'],
                        'first_campaign' => $step1['first_campaign'],
                        'campaign_source' => $step1['campaign_source'],
                        'centre_name' => $step1['centre_name'],
                        'lead_origin' => $step1['lead_origin'],
                        'plan_type' => $step1['plan_type'],
                        'is_broadband' => $step1['is_broadband'],
                        'customer_type' => $step1['customer_type'],
                        'is_soho' => $step1['is_soho'],
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
                        'elec_current_discount_choice' => (isset($this->request->data['elec_current_discount_choice'])) ? $this->request->data['elec_current_discount_choice'] : $step1['elec_current_discount_choice'],
                        'elec_current_discount' => (isset($this->request->data['elec_current_discount'])) ? $this->request->data['elec_current_discount'] : $step1['elec_current_discount'],
                        'elec_current_discount_type' => (isset($this->request->data['elec_current_discount_type'])) ? $this->request->data['elec_current_discount_type'] : $step1['elec_current_discount_type'],
                        'elec_current_discount_applies' => (isset($this->request->data['elec_current_discount_applies'])) ? $this->request->data['elec_current_discount_applies'] : $step1['elec_current_discount_applies'],
                        'gas_current_discount_choice' => (isset($this->request->data['gas_current_discount_choice'])) ? $this->request->data['gas_current_discount_choice'] : $step1['gas_current_discount_choice'],
                        'gas_current_discount' => (isset($this->request->data['gas_current_discount'])) ? $this->request->data['gas_current_discount'] : $step1['gas_current_discount'],
                        'gas_current_discount_type' => (isset($this->request->data['gas_current_discount_type'])) ? $this->request->data['gas_current_discount_type'] : $step1['gas_current_discount_type'],
                        'gas_current_discount_applies' => (isset($this->request->data['gas_current_discount_applies'])) ? $this->request->data['gas_current_discount_applies'] : $step1['gas_current_discount_applies'],
                        'nmi' => $nmi,
                        'nmi_distributor' => $nmi_distributor,
                        'tariff_parent' => (isset($this->request->data['tariff_parent'])) ? $this->request->data['tariff_parent'] : $step1['tariff_parent'],
                        'tariff1' => (isset($this->request->data['tariff1'])) ? $this->request->data['tariff1'] : $step1['tariff1'],
                        'tariff2' => (isset($this->request->data['tariff2'])) ? $this->request->data['tariff2'] : $step1['tariff2'],
                        'tariff3' => (isset($this->request->data['tariff3'])) ? $this->request->data['tariff3'] : $step1['tariff3'],
                        'tariff4' => (isset($this->request->data['tariff4'])) ? $this->request->data['tariff4'] : $step1['tariff4'],
                        'solar_generated' => (isset($this->request->data['solar_generated'])) ? $this->request->data['solar_generated'] : $step1['solar_generated'],
                        'inverter_capacity' => (isset($this->request->data['inverter_capacity'])) ? $this->request->data['inverter_capacity'] : $step1['inverter_capacity'],
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
                        'timeofuse_ps_peak' => (isset($this->request->data['timeofuse_ps_peak'])) ? $this->request->data['timeofuse_ps_peak'] : $step1['timeofuse_ps_peak'],
                        'timeofuse_ps_offpeak' => (isset($this->request->data['timeofuse_ps_offpeak'])) ? $this->request->data['timeofuse_ps_offpeak'] : $step1['timeofuse_ps_offpeak'],
                        'timeofuse_ps_shoulder' => (isset($this->request->data['timeofuse_ps_shoulder'])) ? $this->request->data['timeofuse_ps_shoulder'] : $step1['timeofuse_ps_shoulder'],
                        'timeofuse_ls_peak' => (isset($this->request->data['timeofuse_ls_peak'])) ? $this->request->data['timeofuse_ls_peak'] : $step1['timeofuse_ls_peak'],
                        'timeofuse_ls_offpeak' => (isset($this->request->data['timeofuse_ls_offpeak'])) ? $this->request->data['timeofuse_ls_offpeak'] : $step1['timeofuse_ls_offpeak'],
                        'timeofuse_ls_shoulder' => (isset($this->request->data['timeofuse_ls_shoulder'])) ? $this->request->data['timeofuse_ls_shoulder'] : $step1['timeofuse_ls_shoulder'],
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
                        'referring_agent' => $step1['referring_agent'],
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

    public function customer_details_save()
    {
        if ($this->request->is('put') || $this->request->is('post')) {
            $submission = array();
            $submission['submitted']['FirstName'] = $this->request->data['first_name'];
            $submission['submitted']['surname'] = $this->request->data['surname'];

            $submission['submitted']['MobileNumber'] = (isset($this->request->data['mobile']) && $this->request->data['mobile']) ? $this->request->data['mobile'] : 0;
            $submission['submitted']['primaryPhone'] = (isset($this->request->data['mobile']) && $this->request->data['mobile']) ? $this->request->data['mobile'] : '';
            $submission['submitted']['HomePhone'] = (isset($this->request->data['home_phone']) && $this->request->data['home_phone']) ? $this->request->data['home_phone'] : 0;
            $submission['submitted']['WorkNumber'] = (isset($this->request->data['work_number']) && $this->request->data['work_number']) ? $this->request->data['work_number'] : 0;

            $submission['submitted']['ContactCode'] = $this->request->data['contact_code'];

            $outbound = $this->Session->read('User.outbound');
            if ($outbound) {
                $contact_code = $this->Session->read('User.contact_code');
                $submission['submitted']['ContactCode'] = $contact_code;
                $submission['submitted']['call_medium'] = 'Outbound';
            }
            $inbound = $this->Session->read('User.inbound');
            if ($inbound) {
                $submission['submitted']['call_medium'] = 'Inbound';
            }

            $campaign_id = 1;
            $first_campaign = $campaign_name = 'Phone';
            if (isset($this->request->data['campaign_id2']) && $this->request->data['campaign_id2']) {
                $campaign_id2 = $this->request->data['campaign_id2'];
                switch ($campaign_id2) {
                    case '100':
                        $campaign_id = 100;
                        $first_campaign = $campaign_name = 'Electrician Inbound Leads';
                        break;
                    case '95':
                        $campaign_id = 95;
                        $first_campaign = $campaign_name = 'True Value Solar';
                        break;
                    case '1':
                        $campaign_id = 1;
                        $first_campaign = $campaign_name = 'Phone';
                        break;
                }

                if ($campaign_id2 == 100 && isset($this->request->data['electrician_name']) && $this->request->data['electrician_name']) {
                    $submission['submitted']['referralnametest5'] = $this->request->data['electrician_name'];
                }
            }
            $submission['submitted']['first_campaign_name'] = $first_campaign;

            $ban_phone_numbers = unserialize(BAN_PHONE_NUMBERS);
            if (in_array($submission['submitted']['MobileNumber'], $ban_phone_numbers)) {
                $submission['submitted']['status'] = '*TestStatus';
            }
            if (in_array($submission['submitted']['HomePhone'], $ban_phone_numbers)) {
                $submission['submitted']['status'] = '*TestStatus';
            }
            if (in_array($submission['submitted']['WorkNumber'], $ban_phone_numbers)) {
                $submission['submitted']['status'] = '*TestStatus';
            }

            $agent_id = '';
            if (isset($this->request->data['agent_id']) && $this->request->data['agent_id']) {
                $agent_id = $this->request->data['agent_id'];
                // Chelsea / Sean / Jolene
                if (in_array($agent_id, array('125', '191', '196'))) {
                    $submission['submitted']['status'] = '*TestStatus';
                }
            }

            $sid = 0;
            switch ($this->request->data['action']) {
                case 'no-sale-ok':
                    $lead_action = $this->request->data['lead_action'];

                    if ($lead_action) {
                        $submission['submitted']['status'] = '(Sales Status) Select No Sale Reason';
                    }

                    $sid = $this->create_lead($campaign_id, $submission);

                    if ($lead_action) {
                        $add_action_response = $this->add_lead_action($sid, $lead_action);
                    }
                    break;
                case 'create-lead':
                    $sid = $this->create_lead($campaign_id, $submission);
                    break;
            }

            if ($agent_id) {
                $agent = $this->assign_to_agent($sid, $agent_id);
            }

            $this->Session->write('User.sid', $sid);
            $this->Session->write('User.campaign_id', $campaign_id);
            $this->Session->write('User.campaign_name', $campaign_name);
            $this->Session->write('User.first_campaign', $first_campaign);

            return new CakeResponse(array(
                'body' => $sid,
                'type' => 'text',
                'status' => '201'
            ));
        }
    }

    public function customer_details_update()
    {
        if ($this->request->is('put') || $this->request->is('post')) {
            $step1 = $this->Session->read('User.step1');

            $sid = (isset($this->request->data['sid']) && $this->request->data['sid']) ? $this->request->data['sid'] : '';
            $campaign_id = (isset($this->request->data['campaign_id']) && $this->request->data['campaign_id']) ? $this->request->data['campaign_id'] : $step1['campaign_id'];
            $campaign_name = (isset($this->request->data['campaign_name']) && $this->request->data['campaign_name']) ? $this->request->data['campaign_name'] : $step1['campaign_name'];
            $first_campaign = (isset($this->request->data['first_campaign']) && $this->request->data['first_campaign']) ? $this->request->data['first_campaign'] : $step1['first_campaign'];
            $current_step = (isset($this->request->data['current_step']) && $this->request->data['current_step']) ? $this->request->data['current_step'] : 1;
            $lead_action = (isset($this->request->data['lead_action']) && $this->request->data['lead_action']) ? $this->request->data['lead_action'] : '';

            switch ($current_step) {
                case '1':
                    $nmi = (isset($this->request->data['nmi'])) ? $this->request->data['nmi'] : '';
                    $nmi_distributor = '';
                    if ($nmi) {
                        $nmi_mapping = $this->ElectricityNmiDistributor->findByNmi(strtoupper(substr($nmi, 0, 2)));
                        if ($nmi_mapping) {
                            $nmi_distributor = $nmi_mapping['ElectricityNmiDistributor']['distributor'];
                        }
                    }
                    $data = array(
                        'campaign_id' => (isset($this->request->data['campaign_id'])) ? $this->request->data['campaign_id'] : '',
                        'campaign_name' => (isset($this->request->data['campaign_name'])) ? $this->request->data['campaign_name'] : '',
                        'first_campaign' => (isset($this->request->data['first_campaign'])) ? $this->request->data['first_campaign'] : '',
                        'campaign_source' => (isset($this->request->data['campaign_source'])) ? $this->request->data['campaign_source'] : '',
                        'centre_name' => (isset($this->request->data['centre_name'])) ? $this->request->data['centre_name'] : '',
                        'lead_origin' => (isset($this->request->data['lead_origin'])) ? $this->request->data['lead_origin'] : '',
                        'plan_type' => (isset($this->request->data['plan_type'])) ? $this->request->data['plan_type'] : '',
                        'is_broadband' => (isset($this->request->data['is_broadband']) && $this->request->data['is_broadband']) ? 1 : 0,
                        'customer_type' => (isset($this->request->data['customer_type'])) ? $this->request->data['customer_type'] : '',
                        'is_soho' => (isset($this->request->data['is_soho']) && $this->request->data['is_soho']) ? 1 : 0,
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
                        'elec_current_discount_choice' => (isset($this->request->data['elec_current_discount_choice'])) ? $this->request->data['elec_current_discount_choice'] : '',
                        'elec_current_discount' => (isset($this->request->data['elec_current_discount'])) ? $this->request->data['elec_current_discount'] : '',
                        'elec_current_discount_type' => (isset($this->request->data['elec_current_discount_type'])) ? $this->request->data['elec_current_discount_type'] : '',
                        'elec_current_discount_applies' => (isset($this->request->data['elec_current_discount_applies'])) ? $this->request->data['elec_current_discount_applies'] : '',
                        'gas_current_discount_choice' => (isset($this->request->data['gas_current_discount_choice'])) ? $this->request->data['gas_current_discount_choice'] : '',
                        'gas_current_discount' => (isset($this->request->data['gas_current_discount'])) ? $this->request->data['gas_current_discount'] : '',
                        'gas_current_discount_type' => (isset($this->request->data['gas_current_discount_type'])) ? $this->request->data['gas_current_discount_type'] : '',
                        'gas_current_discount_applies' => (isset($this->request->data['gas_current_discount_applies'])) ? $this->request->data['gas_current_discount_applies'] : '',
                        'nmi' => $nmi,
                        'nmi_distributor' => $nmi_distributor,
                        'tariff_parent' => (isset($this->request->data['tariff_parent'])) ? $this->request->data['tariff_parent'] : '',
                        'tariff1' => (isset($this->request->data['tariff1'])) ? $this->request->data['tariff1'] : '',
                        'tariff2' => (isset($this->request->data['tariff2'])) ? $this->request->data['tariff2'] : '',
                        'tariff3' => (isset($this->request->data['tariff3'])) ? $this->request->data['tariff3'] : '',
                        'tariff4' => (isset($this->request->data['tariff4'])) ? $this->request->data['tariff4'] : '',
                        'solar_generated' => (isset($this->request->data['solar_generated'])) ? $this->request->data['solar_generated'] : '',
                        'inverter_capacity' => (isset($this->request->data['inverter_capacity'])) ? $this->request->data['inverter_capacity'] : '',
                        'tenant_owner' => (isset($this->request->data['tenant_owner'])) ? $this->request->data['tenant_owner'] : '',
                        'battery_storage_solution' => (isset($this->request->data['battery_storage_solution'])) ? $this->request->data['battery_storage_solution'] : '',
                        'battery_storage_solar_solution' => (isset($this->request->data['battery_storage_solar_solution'])) ? $this->request->data['battery_storage_solar_solution'] : '',
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
                        'referring_agent' => (isset($this->request->data['referring_agent'])) ? $this->request->data['referring_agent'] : '',
                    );
                    $this->Session->write('User.step1', $data);
                    // Post to velocify
                    $submission = array();
                    $submission['submitted']['FuelType'] = $data['plan_type'];
                    $submission['BusOrResidential'] = ($data['customer_type'] == 'SME' || $data['is_soho'] == 1) ? 'Business' : 'Residential';
                    $submission['submitted']['SaleType'] = ($data['customer_type'] == 'SME') ? 'BUS' : 'RES';
                    $submission['submitted']['MoveinOrTransfer'] = ($data['looking_for'] == 'Move Properties') ? 'Move In' : 'Transfer';
                    $submission['submitted']['FirstName'] = $this->request->data['first_name'];
                    $submission['submitted']['surname'] = $this->request->data['surname'];

                    $submission['submitted']['MobileNumber'] = (isset($this->request->data['mobile']) && $this->request->data['mobile']) ? $this->request->data['mobile'] : 0;
                    $submission['submitted']['primaryPhone'] = (isset($this->request->data['mobile']) && $this->request->data['mobile']) ? $this->request->data['mobile'] : '';
                    $submission['submitted']['HomePhone'] = (isset($this->request->data['home_phone']) && $this->request->data['home_phone']) ? $this->request->data['home_phone'] : 0;
                    $submission['submitted']['WorkNumber'] = (isset($this->request->data['work_number']) && $this->request->data['work_number']) ? $this->request->data['work_number'] : 0;

                    $submission['submitted']['EmailM'] = $this->request->data['email'];
                    $submission['submitted']['SuburbSupply'] = $this->request->data['suburb'];
                    $submission['submitted']['PostcodeSupply'] = $this->request->data['postcode'];
                    $submission['submitted']['StateSupply'] = $this->request->data['state'];

                    $outbound = $this->Session->read('User.outbound');
                    if ($outbound) {
                        $contact_code = $this->Session->read('User.contact_code');
                        $submission['submitted']['ContactCode'] = $contact_code;
                        $submission['submitted']['call_medium'] = 'Outbound';
                    }
                    $inbound = $this->Session->read('User.inbound');
                    if ($inbound) {
                        $submission['submitted']['call_medium'] = 'Inbound';
                    }

                    if ($nmi) {
                        $submission['submitted']['NMICode'] = $nmi;
                    }

                    if (in_array($data['plan_type'], array('Elec', 'Dual'))) {
                        if ($data['elec_supplier']) {
                            $submission['submitted']['CurrentRetailerElec'] = $data['elec_supplier'];
                        } else {
                            $submission['submitted']['CurrentRetailerElec'] = $data['elec_supplier2'];
                        }
                    }
                    if (in_array($data['plan_type'], array('Gas', 'Dual'))) {
                        if ($data['gas_supplier']) {
                            $submission['submitted']['CurrentRetailerGas'] = $data['gas_supplier'];
                        } else {
                            $submission['submitted']['CurrentRetailerGas'] = $data['gas_supplier2'];
                        }
                    }

                    $tariffs = array();
                    $solar_specific_plan = false;
                    if ($data['tariff1']) {
                        $tariff1 = explode('|', $data['tariff1']);
                        $tariffs[] = $tariff1[0];
                        if ($tariff1[3] == 'Solar') {
                            $solar_specific_plan = true;
                        }
                    }
                    if ($data['tariff2']) {
                        $tariff2 = explode('|', $data['tariff2']);
                        $tariffs[] = $tariff2[0];
                        if ($tariff2[3] == 'Solar') {
                            $solar_specific_plan = true;
                        }
                    }
                    if ($data['tariff3']) {
                        $tariff3 = explode('|', $data['tariff3']);
                        $tariffs[] = $tariff3[0];
                        if ($tariff3[3] == 'Solar') {
                            $solar_specific_plan = true;
                        }
                    }
                    if ($data['tariff4']) {
                        $tariff4 = explode('|', $data['tariff4']);
                        $tariffs[] = $tariff4[0];
                        if ($tariff4[3] == 'Solar') {
                            $solar_specific_plan = true;
                        }
                    }
                    if (!empty($tariffs)) {
                        $submission['submitted']['MSATSTariffCode'] = implode('/', $tariffs);
                    }
                    if ($solar_specific_plan) {
                        $submission['submitted']['SolarPanels'] = 'Yes';
                    }

                    $submission['submitted']['TenantOwner'] = $data['tenant_owner'];
                    break;
                case '3':
                    $step1 = $this->Session->read('User.step1');
                    $solar_interest = isset($this->request->data['solar_interest']) ? $this->request->data['solar_interest'] : '';
                    if ($solar_interest) {
                        $submission['solar_interest'] = 'Yes';
                        if ($step1['referring_agent']) {
                            $submission['submitted']['referrer_name'] = $step1['referring_agent'];
                        }
                        $submission['submitted']['solarinterestdateregistered'] = date('m/d/Y');
                        $submission['submitted']['status'] = '(Terminated Sales Status) Lead Dormant';
                    }
                    break;
                default:
                    break;
            }

            if ($lead_action) {
                $submission['submitted']['status'] = '(Sales Status) Select No Sale Reason';
            }

            if ($campaign_id) {
                switch ($campaign_id) {
                    case '100':
                        if (!$first_campaign) {
                            $first_campaign = $submission['submitted']['first_campaign_name'] = 'Electrician Inbound Leads';
                        }
                        break;
                    case '95':
                        if (!$first_campaign) {
                            $first_campaign = $submission['submitted']['first_campaign_name'] = 'True Value Solar';
                        }
                        break;
                    case '1':
                        if (!$first_campaign) {
                            $first_campaign = $submission['submitted']['first_campaign_name'] = 'Phone';
                        }
                        break;
                }
            } else {
                if (!$first_campaign) {
                    $first_campaign = $submission['submitted']['first_campaign_name'] = $campaign_name;
                }
            }

            $ban_phone_numbers = unserialize(BAN_PHONE_NUMBERS);
            if (isset($submission['submitted']['MobileNumber']) && in_array($submission['submitted']['MobileNumber'], $ban_phone_numbers)) {
                $submission['submitted']['status'] = '*TestStatus';
            }
            if (isset($submission['submitted']['HomePhone']) && in_array($submission['submitted']['HomePhone'], $ban_phone_numbers)) {
                $submission['submitted']['status'] = '*TestStatus';
            }
            if (isset($submission['submitted']['WorkNumber']) && in_array($submission['submitted']['WorkNumber'], $ban_phone_numbers)) {
                $submission['submitted']['status'] = '*TestStatus';
            }

            $agent_id = '';
            if (isset($this->request->data['agent_id']) && $this->request->data['agent_id']) {
                $agent_id = $this->request->data['agent_id'];
                // Chelsea / Sean / Jolene
                if (in_array($agent_id, array('125', '191', '196'))) {
                    $submission['submitted']['status'] = '*TestStatus';
                }
            }

            if ($sid) {
                $this->update_lead($campaign_id, $sid, $submission);
            } else {
                $sid = $this->create_lead($campaign_id, $submission);
            }

            if ($agent_id) {
                $agent = $this->assign_to_agent($sid, $agent_id);
            }

            $this->Session->write('User.sid', $sid);
            $this->Session->write('User.campaign_id', $campaign_id);
            $this->Session->write('User.campaign_name', $campaign_name);
            $this->Session->write('User.first_campaign', $first_campaign);

            if ($lead_action) {
                $add_action_response = $this->add_lead_action($sid, $lead_action);
            }

            if ($this->Session->check('User.customer')) {
                $customer_id = $this->Session->read('User.customer');
                $customer = $this->Customer->findById($customer_id);
                $customer_key = $customer['Customer']['customer_key'];

                $this->Customer->create();
                $this->Customer->save(array('Customer' => array(
                    'id' => $customer_id,
                    'postcode' => $this->Session->read('User.postcode'),
                    'state' => $this->Session->read('User.state'),
                    'suburb' => $this->Session->read('User.suburb'),
                    'data' => serialize($this->Session->read('User')),
                    'leadid' => $sid,
                )), true, array('postcode', 'state', 'suburb', 'data', 'leadid'));
            } else {
                $this->Customer->create();
                $this->Customer->save(array('Customer' => array(
                    'postcode' => $this->Session->read('User.postcode'),
                    'state' => $this->Session->read('User.state'),
                    'suburb' => $this->Session->read('User.suburb'),
                    'data' => serialize($this->Session->read('User')),
                    'leadid' => $sid,
                )));
                $customer_id = $this->Customer->getInsertID();

                $salt = sha1(time() . rand() . $sid);
                $customer_key = hash('sha1', $customer_id . $salt);
                $this->Customer->create();
                $this->Customer->save(array('Customer' => array(
                    'id' => $customer_id,
                    'customer_key' => $customer_key,
                )), true, array('customer_key'));
            }

            //$submission_new = array();
            //$submission_new['submitted']['last_comparison_link'] = 'https://check.compareconnectsave.com.au/v5/compare/1?customer=' . $customer_key;
            //$submission_new['submitted']['CompareStep1'] = 'https://check.compareconnectsave.com.au/v5/compare/1?customer=' . $customer_key;
            //$submission_new['submitted']['CompareStep2'] = 'https://check.compareconnectsave.com.au/v5/compare/2?customer=' . $customer_key;
            //$submission_new['submitted']['CompareStep3'] = 'https://check.compareconnectsave.com.au/v5/compare/3?customer=' . $customer_key;
            //$this->update_lead($campaign_id, $sid, $submission_new);

            return new CakeResponse(array(
                'body' => $sid,
                'type' => 'text',
                'status' => '201'
            ));
        }
    }

    public function contact_code_save()
    {
        if ($this->request->is('put') || $this->request->is('post')) {
            $outbound = $this->request->data['outbound'];
            $inbound = $this->request->data['inbound'];
            $contact_code = $this->request->data['contact_code'];
            $this->Session->write('User.outbound', $outbound);
            $this->Session->write('User.inbound', $inbound);
            $this->Session->write('User.contact_code', $contact_code);

            return new CakeResponse(array(
                'body' => $contact_code,
                'type' => 'text',
                'status' => '201'
            ));
        }
    }

    private function calculate($usage, $tier_rates, $has_tier_5 = false)
    {

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
                } else {

                    $this->log("OKKEY DEBUG tier1 usage::::::" . $usage, 'debug');
                    $this->log("OKKEY DEBUG tier1 rate 1::::::" . $rate[1], 'debug');


                    $sum[1] = $usage * $rate[1];
                    $return = true;
                }
            } else {
                $this->log("OKKEY DEBUG tier1-negative usage::::::" . $usage, 'debug');
                $this->log("OKKEY DEBUG tier1-negative rate 1::::::" . $rate[1], 'debug');

                $sum[1] = $usage * $rate[1];
                $return = true;
            }
        }
        $sum[2] = 0;
        if (!$return && $rate[2]) {
            if ($tier[2]) {
                if ($usage >= ($tier[1] + $tier[2])) {
                    $sum[2] = $rate[2] * $tier[2];
                } else if ($usage > $tier[1] && $usage < ($tier[1] + $tier[2])) {
                    $sum[2] = $rate[2] * ($usage - $tier[1]);
                    $return = true;
                }
            } else {
                if ($usage > $tier[1]) {
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
                } else if ($usage > ($tier[1] + $tier[2]) && $usage < ($tier[1] + $tier[2] + $tier[3])) {
                    $sum[3] = $rate[3] * ($usage - $tier[1] - $tier[2]);
                    $return = true;
                }
            } else {
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
                } else if ($usage > ($tier[1] + $tier[2] + $tier[3]) && $usage < ($tier[1] + $tier[2] + $tier[3] + $tier[4])) {
                    $sum[4] = $rate[4] * ($usage - $tier[1] - $tier[2] - $tier[3]);
                    $return = true;
                }
            } else {
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
        } else {
            $sum[5] = 0;
            if (!$return && $rate[5]) {
                if ($tier[5]) {
                    if ($usage >= ($tier[1] + $tier[2] + $tier[3] + $tier[4] + $tier[5])) {
                        $sum[5] = $rate[5] * $tier[5];
                    } else if ($usage > ($tier[1] + $tier[2] + $tier[3] + $tier[4]) && $usage < ($tier[1] + $tier[2] + $tier[3] + $tier[4] + $tier[5])) {
                        $sum[5] = $rate[5] * ($usage - $tier[1] - $tier[2] - $tier[3] - $tier[4]);
                    }
                } else {
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

    public function conversion_tracked()
    {
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

    public function get_rates($details = false)
    {
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
                    case 'ActewAGL':
                        $plan['Plan']['solar_rate']['retailer'] = $solar_rebate_scheme_rate['SolarRebateScheme']['actewagl'];
                        break;
                    case 'Elysian Energy':
                        $plan['Plan']['solar_rate']['retailer'] = $solar_rebate_scheme_rate['SolarRebateScheme']['elysian_energy'];
                        break;
                    case 'Testing Retailer':
                        $plan['Plan']['solar_rate']['retailer'] = $solar_rebate_scheme_rate['SolarRebateScheme']['testing_retailer'];
                        break;
                }
                if ($plan['Plan']['solar_boost_fit']) {
                    $plan['Plan']['solar_rate']['retailer'] = $plan['Plan']['solar_boost_fit'];
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
                $view_output = $view->render('/Elements/rate_details_v5');
            } else {
                $view_output = $view->render('/Elements/rates_v5');
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

    public function get_default_consumption()
    {
        if ($this->request->is('put') || $this->request->is('post')) {
            $state = $this->request->data['state'];
            $customer_type = $this->request->data['customer_type'];
            //$elec_meter_type = $this->request->data['elec_meter_type'];
            $elec_usage_level = $this->request->data['elec_usage_level'];
            $gas_usage_level = $this->request->data['gas_usage_level'];
            $default_consumption = $this->Consumption->findByStateAndResSme($state, $customer_type);
            $elec_billing_days = $default_consumption['Consumption']['elec_billing_days'];
            $gas_billing_days = $default_consumption['Consumption']['gas_billing_days'];
            $default_elec_peak = explode('/', $default_consumption['Consumption']['elec_peak']);
            $default_gas_peak = explode('/', $default_consumption['Consumption']['gas_peak']);

            $elec_peak = 0;
            if ($elec_usage_level) {
                switch ($elec_usage_level) {
                    case 'Low':
                        $elec_peak = $default_elec_peak[0];
                        break;
                    case 'Medium':
                        $elec_peak = $default_elec_peak[1];
                        break;
                    case 'High':
                        $elec_peak = $default_elec_peak[2];
                        break;
                }
            }

            $gas_peak = 0;
            if ($gas_usage_level) {
                switch ($gas_usage_level) {
                    case 'Low':
                        $gas_peak = $default_gas_peak[0];
                        break;
                    case 'Medium':
                        $gas_peak = $default_gas_peak[1];
                        break;
                    case 'High':
                        $gas_peak = $default_gas_peak[2];
                        break;
                }
            }

            $default = array(
                'elec' => array(
                    'billing_days' => $elec_billing_days,
                    'peak' => $elec_peak,
                ),
                'gas' => array(
                    'billing_days' => $gas_billing_days,
                    'peak' => $gas_peak,
                ),
            );

            return new CakeResponse(array(
                'body' => json_encode($default),
                'type' => 'json',
                'status' => '201'
            ));
        }
    }

    public function signup()
    {
        if ($this->request->is('put') || $this->request->is('post')) {
            $sid = $this->Session->read('User.sid');
            $campaign_id = $this->Session->read('User.campaign_id');
            $plan_id = $this->request->data['plan_id'];
            $elec_rate_id = $this->request->data['elec_rate_id'];
            $gas_rate_id = $this->request->data['gas_rate_id'];
            $ranking = $this->request->data['ranking'];
            $plan = $this->Plan->findById($plan_id);
            $plan['Plan']['ranking'] = $ranking; // ranking
            $elec_rate = $this->ElectricityRate->findById($elec_rate_id);
            $gas_rate = $this->GasRate->findById($gas_rate_id);
            if ($this->Session->check('User.customer')) {
                $customer_id = $this->Session->read('User.customer');
                $customer = $this->Customer->findById($customer_id);
                $customer_key = $customer['Customer']['customer_key'];
                if ($customer['Customer']['signup_data']) {
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
                        'leadid' => $this->Session->read('User.sid'),
                    )));
                    $customer_id = $this->Customer->getInsertID();

                    $salt = sha1(time() . rand() . $plan_id);
                    $customer_key = hash('sha1', $customer_id . $salt);
                    $this->Customer->create();
                    $this->Customer->save(array('Customer' => array(
                        'id' => $customer_id,
                        'customer_key' => $customer_key,
                    )), true, array('customer_key'));
                } else {
                    $this->Customer->create();
                    $this->Customer->save(array('Customer' => array(
                        'id' => $customer_id,
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
                        'leadid' => $this->Session->read('User.sid'),
                    )), true, array('plan_id', 'elec_rate_id', 'gas_rate_id', 'postcode', 'state', 'suburb', 'data', 'plan_data', 'elec_rate_data', 'gas_rate_data', 'leadid'));
                }

            } else {
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
                    'leadid' => $this->Session->read('User.sid'),
                )));
                $customer_id = $this->Customer->getInsertID();

                $salt = sha1(time() . rand() . $plan_id);
                $customer_key = hash('sha1', $customer_id . $salt);
                $this->Customer->create();
                $this->Customer->save(array('Customer' => array(
                    'id' => $customer_id,
                    'customer_key' => $customer_key,
                )), true, array('customer_key'));
            }

            if ($sid) {
                //$submission = array();
                //$submission['submitted']['last_comparison_link'] = 'https://check.compareconnectsave.com.au/v5/compare/1?customer=' . $customer_key;
                //$submission['submitted']['CompareStep1'] = 'https://check.compareconnectsave.com.au/v5/compare/1?customer=' . $customer_key;
                //$submission['submitted']['CompareStep2'] = 'https://check.compareconnectsave.com.au/v5/compare/2?customer=' . $customer_key;
                //$submission['submitted']['CompareStep3'] = 'https://check.compareconnectsave.com.au/v5/compare/3?customer=' . $customer_key;
                //$this->update_lead($campaign_id, $sid, $submission);
            }

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

    public function pause()
    {
        if ($this->request->is('put') || $this->request->is('post')) {
            $sid = $this->Session->read('User.sid');
            $campaign_id = $this->Session->read('User.campaign_id');
            if ($this->Session->check('User.customer')) {
                $customer_id = $this->Session->read('User.customer');
                $customer = $this->Customer->findById($customer_id);
                $customer_key = $customer['Customer']['customer_key'];

                $this->Customer->create();
                $this->Customer->save(array('Customer' => array(
                    'id' => $customer_id,
                    'postcode' => $this->Session->read('User.postcode'),
                    'state' => $this->Session->read('User.state'),
                    'suburb' => $this->Session->read('User.suburb'),
                    'data' => serialize($this->Session->read('User')),
                    'leadid' => $sid,
                )), true, array('postcode', 'state', 'suburb', 'data', 'leadid'));
            } else {
                $this->Customer->create();
                $this->Customer->save(array('Customer' => array(
                    'postcode' => $this->Session->read('User.postcode'),
                    'state' => $this->Session->read('User.state'),
                    'suburb' => $this->Session->read('User.suburb'),
                    'data' => serialize($this->Session->read('User')),
                    'leadid' => $sid,
                )));
                $customer_id = $this->Customer->getInsertID();

                $salt = sha1(time() . rand() . $sid);
                $customer_key = hash('sha1', $customer_id . $salt);
                $this->Customer->create();
                $this->Customer->save(array('Customer' => array(
                    'id' => $customer_id,
                    'customer_key' => $customer_key,
                )), true, array('customer_key'));
            }

            if ($sid) {
                //$submission = array();
                //$submission['submitted']['last_comparison_link'] = 'https://check.compareconnectsave.com.au/v5/compare/1?customer=' . $customer_key;
                //$submission['submitted']['CompareStep1'] = 'https://check.compareconnectsave.com.au/v5/compare/1?customer=' . $customer_key;
                //$submission['submitted']['CompareStep2'] = 'https://check.compareconnectsave.com.au/v5/compare/2?customer=' . $customer_key;
                //$submission['submitted']['CompareStep3'] = 'https://check.compareconnectsave.com.au/v5/compare/3?customer=' . $customer_key;
                //$this->update_lead($campaign_id, $sid, $submission);
            }

            return new CakeResponse(array(
                'body' => json_encode(array(
                    'status' => '1',
                    'id' => $customer_key
                )),
                'type' => 'json',
                'status' => '201'
            ));
        }
    }

    private function create_lead($campaign_id = 1, $submission = array())
    {
        // *TestStatus lead
        $ban_phone_numbers = unserialize(BAN_PHONE_NUMBERS);
        if (isset($submission['submitted']['MobileNumber']) && in_array($submission['submitted']['MobileNumber'], $ban_phone_numbers)) {
            $submission['submitted']['status'] = '*TestStatus';
        }
        if (isset($submission['submitted']['HomePhone']) && in_array($submission['submitted']['HomePhone'], $ban_phone_numbers)) {
            $submission['submitted']['status'] = '*TestStatus';
        }
        if (isset($submission['submitted']['WorkNumber']) && in_array($submission['submitted']['WorkNumber'], $ban_phone_numbers)) {
            $submission['submitted']['status'] = '*TestStatus';
        }

        if (in_array(strtolower($submission['submitted']['FirstName']), array('test'))) {
            $submission['submitted']['status'] = '*TestStatus';
        }

        if (!isset($submission['submitted']['status'])) {
            $submission['submitted']['status'] = 'New (UnActioned)';
        }
        
        //$middleware_id = $this->velocify_middleware_save($campaign_id, null, $submission);
        
        $request = http_build_query($submission, '', '&');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, LEADS360_URL_1 . "/Import.aspx?Provider=RSMSolutions&Client=RSMSolutions&CampaignId={$campaign_id}&XmlResponse=True");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        $response = curl_exec($ch);
        curl_close($ch);
        $lead_id = 0;
        if ($response) {
            if (strpos($response, 'Success') !== false) {
                $result = simplexml_load_string($response);
                foreach ($result->ImportResult[0]->attributes() as $key => $value) {
                    if ($key == 'leadId') {
                        $lead_id = (int)$value;
                    }
                }
            } else {
                $to = 's.liu@seanpro.com';
                $subject = 'Velocify API error - Import';
                $message = $response;
                $headers = 'From: api@seanpro.com' . "\r\n" .
                    'Reply-To: api@seanpro.com' . "\r\n" .
                    'X-Mailer: PHP/' . phpversion();

                mail($to, $subject, $message, $headers);
            }
        }
        
        if (!$lead_id) {
            //$lead_id = $middleware_id;
        }
        
        $this->Submission->create();
        $this->Submission->save(array('Submission' => array(
            'sid' => time(),
            'leadid' => $lead_id,
            'mobile' => isset($submission['submitted']['MobileNumber']) ? $submission['submitted']['MobileNumber'] : null,
            'email' => isset($submission['submitted']['EmailM']) ? $submission['submitted']['EmailM'] : null,
            'request' => $request,
            'response' => $response,
            'submitted' => date('Y-m-d H:i:s'),
            'source' => 'Tools V5',
        )));

        return $lead_id;
    }

    private function update_lead($campaign_id = 1, $id = null, $submission = array())
    {
        // *TestStatus lead
        $ban_phone_numbers = unserialize(BAN_PHONE_NUMBERS);
        if (isset($submission['submitted']['MobileNumber']) && in_array($submission['submitted']['MobileNumber'], $ban_phone_numbers)) {
            $submission['submitted']['status'] = '*TestStatus';
        }
        if (isset($submission['submitted']['HomePhone']) && in_array($submission['submitted']['HomePhone'], $ban_phone_numbers)) {
            $submission['submitted']['status'] = '*TestStatus';
        }
        if (isset($submission['submitted']['WorkNumber']) && in_array($submission['submitted']['WorkNumber'], $ban_phone_numbers)) {
            $submission['submitted']['status'] = '*TestStatus';
        }

        if (in_array(strtolower($submission['submitted']['FirstName']), array('test'))) {
            $submission['submitted']['status'] = '*TestStatus';
        }
        
        //$middleware_id = $this->velocify_middleware_save($campaign_id, $id, $submission);

        $request = http_build_query($submission, '', '&');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, LEADS360_URL_1 . "/Update.aspx?Provider=RSMSolutions&Client=RSMSolutions&CampaignId={$campaign_id}&XmlResponse=True&LeadId={$id}");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        $response = curl_exec($ch);
        curl_close($ch);
        if (strpos($response, 'Success') !== false) {
            //return $id;
        } else {
            $to = 'info@seanpro.com';
            $subject = 'Velocify API error - Update';
            $message = "Lead ID: {$id}\r\n";
            $message .= $response;
            $headers = 'From: api@seanpro.com' . "\r\n" .
                'Reply-To: api@seanpro.com' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();

            mail($to, $subject, $message, $headers);
        }
        $this->Submission->create();
        $this->Submission->save(array('Submission' => array(
            'sid' => time(),
            'leadid' => $id,
            'mobile' => isset($submission['submitted']['MobileNumber']) ? $submission['submitted']['MobileNumber'] : null,
            'email' => isset($submission['submitted']['EmailM']) ? $submission['submitted']['EmailM'] : null,
            'request' => $request,
            'response' => $response,
            'submitted' => date('Y-m-d H:i:s'),
            'source' => 'Tools V5 - Update',
        )));
    }

    private function velocify_middleware_save($campaign_id, $lead_id = null, $submission)
    {
        $existing = false;
        if ($lead_id) {
            $existing = $this->Lead->find('first', array(
                'conditions' => array(
                    'Lead.id' => $lead_id,
                ),
            ));
        }

        $lead = array();

        if (!$existing) {
            $lead['submitted'] = date('Y-m-d H:i:s');
        } else {
            $lead['id'] = $lead_id;
        }

        $lead['lead_campaign'] = $campaign_id;

        if (isset($submission['submitted']['status']) && $submission['submitted']['status']) {
            $lead['status'] = $submission['submitted']['status'];
        }
        if (isset($submission['submitted']['FirstName']) && $submission['submitted']['FirstName']) {
            $lead['first_name'] = $submission['submitted']['FirstName'];
        }
        if (isset($submission['submitted']['surname']) && $submission['submitted']['surname']) {
            $lead['surname'] = $submission['submitted']['surname'];
        }
        if (isset($submission['submitted']['MobileNumber']) && $submission['submitted']['MobileNumber']) {
            $lead['mobile_number'] = $submission['submitted']['MobileNumber'];
        }
        if (isset($submission['submitted']['HomePhone']) && $submission['submitted']['HomePhone']) {
            $lead['home_phone'] = $submission['submitted']['HomePhone'];
        }
        if (isset($submission['submitted']['WorkNumber']) && $submission['submitted']['WorkNumber']) {
            $lead['work_number'] = $submission['submitted']['WorkNumber'];
        }
        if (isset($submission['submitted']['ContactCode']) && $submission['submitted']['ContactCode']) {
            $lead['contact_code'] = $submission['submitted']['ContactCode'];
        }
        if (isset($submission['submitted']['call_medium']) && $submission['submitted']['call_medium']) {
            $lead['call_medium'] = $submission['submitted']['call_medium'];
        }
        if (isset($submission['submitted']['first_campaign_name']) && $submission['submitted']['first_campaign_name']) {
            $lead['first_campaign'] = $submission['submitted']['first_campaign_name'];
        }
        if (isset($submission['submitted']['FuelType']) && $submission['submitted']['FuelType']) {
            $lead['fuel_type'] = $submission['submitted']['FuelType'];
        }
        if (isset($submission['submitted']['SaleType']) && $submission['submitted']['SaleType']) {
            $lead['sale_type'] = $submission['submitted']['SaleType'];
        }
        if (isset($submission['submitted']['MoveinOrTransfer']) && $submission['submitted']['MoveinOrTransfer']) {
            $lead['movein_or_transfer'] = $submission['submitted']['MoveinOrTransfer'];
        }
        if (isset($submission['submitted']['SuburbSupply']) && $submission['submitted']['SuburbSupply']) {
            $lead['suburb_supply'] = $submission['submitted']['SuburbSupply'];
        }
        if (isset($submission['submitted']['PostcodeSupply']) && $submission['submitted']['PostcodeSupply']) {
            $lead['postcode_supply'] = $submission['submitted']['PostcodeSupply'];
        }
        if (isset($submission['submitted']['StateSupply']) && $submission['submitted']['StateSupply']) {
            $lead['state_supply'] = $submission['submitted']['StateSupply'];
        }
        if (isset($submission['submitted']['NMICode']) && $submission['submitted']['NMICode']) {
            $lead['nmi_code'] = $submission['submitted']['NMICode'];
        }
        if (isset($submission['submitted']['CurrentRetailerElec']) && $submission['submitted']['CurrentRetailerElec']) {
            $lead['current_retailer_elec'] = $submission['submitted']['CurrentRetailerElec'];
            $fields[] = 'current_retailer_elec';
        }
        if (isset($submission['submitted']['CurrentRetailerGas']) && $submission['submitted']['CurrentRetailerGas']) {
            $lead['current_retailer_gas'] = $submission['submitted']['CurrentRetailerGas'];
        }
        if (isset($submission['submitted']['MSATSTariffCode']) && $submission['submitted']['MSATSTariffCode']) {
            $lead['msats_tariff_code'] = $submission['submitted']['MSATSTariffCode'];
        }
        if (isset($submission['submitted']['TenantOwner']) && $submission['submitted']['TenantOwner']) {
            $lead['tenant_owner'] = $submission['submitted']['TenantOwner'];
        }
        if (isset($submission['submitted']['MoveinDate']) && $submission['submitted']['MoveinDate']) {
            $lead['movein_date'] = $submission['submitted']['MoveinDate'];
        }
        if (isset($submission['submitted']['ConnectionDate']) && $submission['submitted']['ConnectionDate']) {
            $lead['connection_date'] = $submission['submitted']['ConnectionDate'];
        }
        if (isset($submission['submitted']['BusOrResidential']) && $submission['submitted']['BusOrResidential']) {
            $lead['business_or_residential'] = $submission['submitted']['BusOrResidential'];
            $fields[] = 'business_or_residential';
        }
        if (isset($submission['submitted']['current_electricity_discount']) && $submission['submitted']['current_electricity_discount']) {
            $lead['current_electricity_discounts'] = $submission['submitted']['current_electricity_discount'];
        }
        if (isset($submission['submitted']['discount_condition_electricity']) && $submission['submitted']['discount_condition_electricity']) {
            $lead['discount_condition_electricity'] = $submission['submitted']['discount_condition_electricity'];
        }
        if (isset($submission['submitted']['discount_portion_electricity']) && $submission['submitted']['discount_portion_electricity']) {
            $lead['discount_portion_electricity'] = $submission['submitted']['discount_portion_electricity'];
        }
        if (isset($submission['submitted']['current_gas_discount']) && $submission['submitted']['current_gas_discount']) {
            $lead['current_gas_discount'] = $submission['submitted']['current_gas_discount'];
        }
        if (isset($submission['submitted']['discount_condition_gas']) && $submission['submitted']['discount_condition_gas']) {
            $lead['discount_condition_gas'] = $submission['submitted']['discount_condition_gas'];
        }
        if (isset($submission['submitted']['discount_portion_gas']) && $submission['submitted']['discount_portion_gas']) {
            $lead['discount_portion_gas'] = $submission['submitted']['discount_portion_gas'];
        }
        if (isset($submission['submitted']['SolarPanels']) && $submission['submitted']['SolarPanels']) {
            $lead['do_you_have_solar'] = $submission['submitted']['SolarPanels'];
        }
        if (isset($submission['submitted']['BatteryStorageEOI']) && $submission['submitted']['BatteryStorageEOI']) {
            $lead['battery_storage_eoi'] = $submission['submitted']['BatteryStorageEOI'];
        }
        if (isset($submission['submitted']['BatteryStorageSolarEOI']) && $submission['submitted']['BatteryStorageSolarEOI']) {
            $lead['battery_storage_solar_eoi'] = $submission['submitted']['BatteryStorageSolarEOI'];
        }
        if (isset($submission['submitted']['streaming_frequency']) && $submission['submitted']['streaming_frequency']) {
            $lead['centre_name'] = $submission['submitted']['streaming_frequency'];
        }
        if (isset($submission['submitted']['source']) && $submission['submitted']['source']) {
            $lead['campaign_source'] = $submission['submitted']['source'];
        }
        if (isset($submission['solar_interest']) && $submission['solar_interest']) {
            $lead['solar_interest'] = $submission['solar_interest'];
        }
        if (isset($submission['submitted']['solarinterestdateregistered']) && $submission['submitted']['solarinterestdateregistered']) {
            $lead['solar_interest_date_registered'] = $submission['submitted']['solarinterestdateregistered'];
        }

        if (!$existing) {
            $lead_id = $this->Lead->getLastInsertId();
        }
        
        return $lead_id;

    }

    private function add_lead_action($lead_id, $action_id)
    {
        $username = LEADS360_USERNAME;
        $password = LEADS360_PASSWORD;
        $action_note = urlencode('Tools V5 - Update');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, LEADS360_URL_2 . "/ClientService.asmx/AddLeadAction?username={$username}&password={$password}&leadId={$lead_id}&actionTypeId={$action_id}&actionNote={$action_note}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    private function get_programs()
    {
        $username = LEADS360_USERNAME;
        $password = LEADS360_PASSWORD;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, LEADS360_URL_2 . "/ClientService.asmx/GetPrograms?username={$username}&password={$password}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    private function assign_to_agent($lead_id, $agent_id, $agent_name = null)
    {
        $username = LEADS360_USERNAME;
        $password = LEADS360_PASSWORD;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, LEADS360_URL_2 . "/ClientService.asmx/AssignViaDistribution?username={$username}&password={$password}&leadId={$lead_id}&agentId={$agent_id}&programId=121");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $response = curl_exec($ch);
        curl_close($ch);

        $this->LeadAgent->create();
        $this->LeadAgent->save(array('LeadAgent' => array(
            'sid' => time(),
            'leadid' => $lead_id,
            'agentid' => $agent_id,
            'response' => $response,
            'submitted' => date('Y-m-d H:i:s'),
            'source' => 'Tools V5 - assign to agent',
        )));

        $lead = $this->Lead->find('first', array(
            'conditions' => array(
                'Lead.id' => $lead_id,
            ),
        ));

        if ($lead) {
            $this->Lead->create();
            $this->Lead->save(array('Lead' => array(
                'id' => $lead_id,
                'user_id' => $agent_id,
            )), true, array('user_id'));

            if ($agent_name) {
                $this->Lead->save(array('Lead' => array(
                    'id' => $lead_id,
                    'user' => $agent_name,
                )), true, array('user'));
            }
        }
        return $response;
    }

    public function export()
    {
        $this->set('title_for_layout', 'Export');

        $plans = array();

        $states_arr = unserialize(AU_STATES);

        if ($this->request->is('post')) {

            $lines = array();
            $lines[] = 'State,Elec Distributor,Postcode,Suburb,RES Tariff,SME Tariff,Plan Type,Customer Type,Consumption Level,Retailer,Product Name,Product Description,Discount: Guaranteed Elec,Discount: Guaranteed Gas,Discount: Pay on Time Elec,Discount: Pay on Time Gas,Discount: Dual Fuel Elec,Discount: Dual Fuel Gas,AD:Rankings (Inc Discounts), Ranking 1,Ranking 2,Ranking 3,Date';

            $state = $this->request->data['Export']['state'];
            $postcode = $this->request->data['Export']['postcode'];
            $suburb = ucwords(strtolower($this->request->data['Export']['suburb']));
            $plan_type = $this->request->data['Export']['plan_type'];
            $customer_type = $this->request->data['Export']['customer_type'];
            $nmi = $this->request->data['Export']['nmi'];
            $tariff_code = $this->request->data['Export']['tariff_code'];
            //$distributor = $this->request->data['Export']['distributor'];
            $consumption_level = $this->request->data['Export']['consumption_level'];

            $data = array(
                'state' => $state,
                'postcode' => $postcode,
                'suburb' => $suburb,
                'nmi' => $nmi,
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
            } elseif (in_array($plan_type, array('Dual'))) {
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
                    $ranking1 = round((($cost - $cost1) / $cost) * 100, 2) . "%";
                    $ranking2 = ($cost2) ? round((($cost - $cost2) / $cost) * 100, 2) . "%" : '';
                    $ranking3 = ($cost3) ? round((($cost - $cost3) / $cost) * 100, 2) . "%" : '';
                } elseif (in_array($plan_type, array('Dual'))) {
                    $cost = $plan['Plan']['total_inc_discount_elec'] + $plan['Plan']['total_inc_discount_gas'];
                    $ranking1 = round((($cost - $cost1) / $cost) * 100, 2) . "%";
                    $ranking2 = ($cost2) ? round((($cost - $cost2) / $cost) * 100, 2) . "%" : '';
                    $ranking3 = ($cost3) ? round((($cost - $cost3) / $cost) * 100, 2) . "%" : '';
                }
                $date = date('d/m/Y');
                $line = array(
                    $value[0],
                    $value[1],
                    $value[2],
                    $value[3],
                    //$value[4],
                    $value[5],
                    $value[6],
                    $plan_type,
                    $customer_type,
                    $consumption_level,
                    $plan['Plan']['retailer'],
                    '"' . $plan['Plan']['product_name'] . '"',
                    '"' . $plan['Plan']['product_summary'] . '"',
                    $plan['Plan']['discount_guaranteed_elec'],
                    $plan['Plan']['discount_guaranteed_gas'],
                    $plan['Plan']['discount_pay_on_time_elec'],
                    $plan['Plan']['discount_pay_on_time_gas'],
                    $plan['Plan']['discount_dual_fuel_elec'],
                    $plan['Plan']['discount_dual_fuel_gas'],
                    //(in_array($plan_type, array('Elec', 'Dual'))) ? $plan['Plan']['elec_rate']['distributor'] : '',
                    //(in_array($plan_type, array('Gas', 'Dual'))) ? $plan['Plan']['gas_rate']['distributor'] : '',
                    //(in_array($plan_type, array('Elec', 'Dual'))) ? '$'.$plan['Plan']['total_inc_discount_guaranteed_elec'] : '',
                    //(in_array($plan_type, array('Gas', 'Dual'))) ? '$'.$plan['Plan']['total_inc_discount_guaranteed_gas'] : '',
                    //'$'.($plan['Plan']['total_inc_discount_guaranteed_elec'] + $plan['Plan']['total_inc_discount_guaranteed_gas']),
                    //$j,
                    //(in_array($plan_type, array('Elec', 'Dual'))) ? '$'.$plan['Plan']['total_inc_discount_elec'] : '',
                    //(in_array($plan_type, array('Gas', 'Dual'))) ? '$'.$plan['Plan']['total_inc_discount_gas'] : '',
                    //'$'.($plan['Plan']['total_inc_discount_elec'] + $plan['Plan']['total_inc_discount_gas']),
                    $j,
                    $ranking1,
                    $ranking2,
                    $ranking3,
                    $date
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

    public function export2()
    {
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
                                    '"' . $plan['Plan']['product_name'] . '"',
                                    (in_array($plan_type, array('Elec', 'Dual'))) ? $plan['Plan']['elec_rate']['distributor'] : '',
                                    (in_array($plan_type, array('Gas', 'Dual'))) ? $plan['Plan']['gas_rate']['distributor'] : '',
                                    (in_array($plan_type, array('Elec', 'Dual'))) ? '$' . $plan['Plan']['total_inc_discount_elec'] : '',
                                    (in_array($plan_type, array('Gas', 'Dual'))) ? '$' . $plan['Plan']['total_inc_discount_gas'] : '',
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

    public function export3()
    {
        $spreadsheet_url = 'https://docs.google.com/spreadsheets/d/12bk3Q4fd0mXq_DzHPz5mhozUdS50az0b0lknILlKgQo/pub?single=true&gid=1456169634&output=csv';
        $csv = array();
        if (($handle = fopen($spreadsheet_url, "r")) !== false) {
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                $csv[] = $data;
            }
            fclose($handle);
        }

        $lines = array();
        //$lines[] = 'State,Elec Distributor,Postcode,Suburb,NMI,RES Tariff, SME Tariff, Plan Type,Customer Type,Consumption Level,Retailer,Product Name,Product Description,Electricity Distributor,Gas Distributor,BD:Estimated Electricity Cost,BD:Estimated Gas Cost,BD:Total Cost,BD:Rankings (Inc Discounts),AD:Estimated Electricity Cost,AD:Estimated Gas Cost,AD:Total Cost,AD:Rankings (Inc Discounts), Ranking 1,Ranking 2,Ranking 3';
        $lines[] = 'State,Elec Distributor,Postcode,Suburb,RES Tariff,SME Tariff,Plan Type,Customer Type,Consumption Level,Retailer,Product Name,Product Description,Discount: Guaranteed Elec,Discount: Guaranteed Gas,Discount: Pay on Time Elec,Discount: Pay on Time Gas,Discount: Dual Fuel Elec,Discount: Dual Fuel Gas,AD:Rankings (Inc Discounts), Ranking 1,Ranking 2,Ranking 3,Date';

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
                    } elseif (in_array($plan_type, array('Dual'))) {
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
                            $ranking1 = round((($cost - $cost1) / $cost) * 100, 2) . "%";
                            $ranking2 = ($cost2) ? round((($cost - $cost2) / $cost) * 100, 2) . "%" : '';
                            $ranking3 = ($cost3) ? round((($cost - $cost3) / $cost) * 100, 2) . "%" : '';
                        } elseif (in_array($plan_type, array('Dual'))) {
                            $cost = $plan['Plan']['total_inc_discount_elec'] + $plan['Plan']['total_inc_discount_gas'];
                            $ranking1 = round((($cost - $cost1) / $cost) * 100, 2) . "%";
                            $ranking2 = ($cost2) ? round((($cost - $cost2) / $cost) * 100, 2) . "%" : '';
                            $ranking3 = ($cost3) ? round((($cost - $cost3) / $cost) * 100, 2) . "%" : '';
                        }
                        $date = date('d/m/Y');
                        $line = array(
                            $value[0],
                            $value[1],
                            $value[2],
                            $value[3],
                            //$value[4],
                            $value[5],
                            $value[6],
                            $plan_type,
                            $customer_type,
                            $consumption_level,
                            $plan['Plan']['retailer'],
                            '"' . $plan['Plan']['product_name'] . '"',
                            '"' . $plan['Plan']['product_summary'] . '"',
                            $plan['Plan']['discount_guaranteed_elec'],
                            $plan['Plan']['discount_guaranteed_gas'],
                            $plan['Plan']['discount_pay_on_time_elec'],
                            $plan['Plan']['discount_pay_on_time_gas'],
                            $plan['Plan']['discount_dual_fuel_elec'],
                            $plan['Plan']['discount_dual_fuel_gas'],
                            //(in_array($plan_type, array('Elec', 'Dual'))) ? $plan['Plan']['elec_rate']['distributor'] : '',
                            //(in_array($plan_type, array('Gas', 'Dual'))) ? $plan['Plan']['gas_rate']['distributor'] : '',
                            //(in_array($plan_type, array('Elec', 'Dual'))) ? '$'.$plan['Plan']['total_inc_discount_guaranteed_elec'] : '',
                            //(in_array($plan_type, array('Gas', 'Dual'))) ? '$'.$plan['Plan']['total_inc_discount_guaranteed_gas'] : '',
                            //'$'.($plan['Plan']['total_inc_discount_guaranteed_elec'] + $plan['Plan']['total_inc_discount_guaranteed_gas']),
                            //$j,
                            //(in_array($plan_type, array('Elec', 'Dual'))) ? '$'.$plan['Plan']['total_inc_discount_elec'] : '',
                            //(in_array($plan_type, array('Gas', 'Dual'))) ? '$'.$plan['Plan']['total_inc_discount_gas'] : '',
                            //'$'.($plan['Plan']['total_inc_discount_elec'] + $plan['Plan']['total_inc_discount_gas']),
                            $j,
                            $ranking1,
                            $ranking2,
                            $ranking3,
                            $date
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

    public function cron_job_export()
    {
        $spreadsheet_url = 'https://docs.google.com/spreadsheets/d/12bk3Q4fd0mXq_DzHPz5mhozUdS50az0b0lknILlKgQo/pub?single=true&gid=1456169634&output=csv';
        $csv = array();
        if (($handle = fopen($spreadsheet_url, "r")) !== false) {
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                $csv[] = $data;
            }
            fclose($handle);
        }

        $lines = array();
        //$lines[] = 'State,Elec Distributor,Postcode,Suburb,NMI,RES Tariff, SME Tariff, Plan Type,Customer Type,Consumption Level,Retailer,Product Name,Product Description,Electricity Distributor,Gas Distributor,BD:Estimated Electricity Cost,BD:Estimated Gas Cost,BD:Total Cost,BD:Rankings (Inc Discounts),AD:Estimated Electricity Cost,AD:Estimated Gas Cost,AD:Total Cost,AD:Rankings (Inc Discounts), Ranking 1,Ranking 2,Ranking 3';
        $lines[] = 'State,Elec Distributor,Postcode,Suburb,RES Tariff,SME Tariff,Plan Type,Customer Type,Consumption Level,Retailer,Product Name,Product Description,Discount: Guaranteed Elec,Discount: Guaranteed Gas,Discount: Pay on Time Elec,Discount: Pay on Time Gas,Discount: Dual Fuel Elec,Discount: Dual Fuel Gas,AD:Rankings (Inc Discounts), Ranking 1,Ranking 2,Ranking 3,Date';

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
                    } elseif (in_array($plan_type, array('Dual'))) {
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
                            $ranking1 = round((($cost - $cost1) / $cost) * 100, 2) . "%";
                            $ranking2 = ($cost2) ? round((($cost - $cost2) / $cost) * 100, 2) . "%" : '';
                            $ranking3 = ($cost3) ? round((($cost - $cost3) / $cost) * 100, 2) . "%" : '';
                        } elseif (in_array($plan_type, array('Dual'))) {
                            $cost = $plan['Plan']['total_inc_discount_elec'] + $plan['Plan']['total_inc_discount_gas'];
                            $ranking1 = round((($cost - $cost1) / $cost) * 100, 2) . "%";
                            $ranking2 = ($cost2) ? round((($cost - $cost2) / $cost) * 100, 2) . "%" : '';
                            $ranking3 = ($cost3) ? round((($cost - $cost3) / $cost) * 100, 2) . "%" : '';
                        }
                        $date = date('d/m/Y');
                        $line = array(
                            $value[0],
                            $value[1],
                            $value[2],
                            $value[3],
                            //$value[4],
                            $value[5],
                            $value[6],
                            $plan_type,
                            $customer_type,
                            $consumption_level,
                            $plan['Plan']['retailer'],
                            '"' . $plan['Plan']['product_name'] . '"',
                            '"' . $plan['Plan']['product_summary'] . '"',
                            $plan['Plan']['discount_guaranteed_elec'],
                            $plan['Plan']['discount_guaranteed_gas'],
                            $plan['Plan']['discount_pay_on_time_elec'],
                            $plan['Plan']['discount_pay_on_time_gas'],
                            $plan['Plan']['discount_dual_fuel_elec'],
                            $plan['Plan']['discount_dual_fuel_gas'],
                            //(in_array($plan_type, array('Elec', 'Dual'))) ? $plan['Plan']['elec_rate']['distributor'] : '',
                            //(in_array($plan_type, array('Gas', 'Dual'))) ? $plan['Plan']['gas_rate']['distributor'] : '',
                            //(in_array($plan_type, array('Elec', 'Dual'))) ? '$'.$plan['Plan']['total_inc_discount_guaranteed_elec'] : '',
                            //(in_array($plan_type, array('Gas', 'Dual'))) ? '$'.$plan['Plan']['total_inc_discount_guaranteed_gas'] : '',
                            //'$'.($plan['Plan']['total_inc_discount_guaranteed_elec'] + $plan['Plan']['total_inc_discount_guaranteed_gas']),
                            //$j,
                            //(in_array($plan_type, array('Elec', 'Dual'))) ? '$'.$plan['Plan']['total_inc_discount_elec'] : '',
                            //(in_array($plan_type, array('Gas', 'Dual'))) ? '$'.$plan['Plan']['total_inc_discount_gas'] : '',
                            //'$'.($plan['Plan']['total_inc_discount_elec'] + $plan['Plan']['total_inc_discount_gas']),
                            $j,
                            $ranking1,
                            $ranking2,
                            $ranking3,
                            $date
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
        $filename = "./export/plans_{$time}.csv";

        $fd = fopen ($filename, "w");
        fputs($fd, $content);
        fclose($fd);

        $Email = new CakeEmail();
        $Email->from(array('info@seanpro.com' => 'Deal Expert'))
            ->to('info@seanpro.com')
            ->subject('Tools - Scheduled Ranking Report')
            ->attachments($filename)
            ->send('Scheduled Ranking Report');

        exit;
    }

    private function get_plans($data)
    {
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
        $plan_start_or = array(
            'or' => array(
                'Plan.plan_start' => '0000-00-00',
                'Plan.plan_start <=' => date('Y-m-d'),
            ),
        );
        $conditions[] = $plan_start_or;
        $plan_expiry_or = array(
            'or' => array(
                'Plan.plan_expiry' => '0000-00-00',
                'Plan.plan_expiry >=' => date('Y-m-d'),
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

            if ($solar_rebate_scheme) {
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
                    if ($state == 'NSW' && $customer_type == 'RES') {
                        if ($nmi && in_array(substr($nmi, 0, 2), array(41))) {
                            $conditions['Plan.version'][] = 'Powerdirect Discount Saver (Ausgrid)';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(40, 42, 44, 45))) {
                            $conditions['Plan.version'][] = 'Powerdirect Discount Saver (Essential Energy)';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(43))) {
                            $conditions['Plan.version'][] = 'Powerdirect Discount Saver (Endeavour Energy)';
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

                    if ($state == 'NSW' && $customer_type == 'RES') {
                        if ($nmi && in_array(substr($nmi, 0, 2), array(41))) {
                            $conditions['Plan.version'][] = 'Origin Saver Ausgrid';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(40, 42, 44, 45))) {
                            $conditions['Plan.version'][] = 'Origin Saver Essential Energy';
                        }
                    }
                    if ($state == 'VIC' && $customer_type == 'RES') {
                        if ($nmi && in_array(substr($nmi, 0, 2), array(62))) {
                            $conditions['Plan.version'][] = 'Origin Bill Saver Powercor';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(61))) {
                            $conditions['Plan.version'][] = 'Origin Bill Saver Citipower';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(64))) {
                            $conditions['Plan.version'][] = 'Origin Bill Saver United Energy';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(60))) {
                            $conditions['Plan.version'][] = 'Origin Bill Saver Jemena';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(63))) {
                            $conditions['Plan.version'][] = 'Origin Bill Saver Ausnet';
                        }
                    }

                    if ($state == 'VIC' && $customer_type == 'SME') {
                        if ($nmi && in_array(substr($nmi, 0, 2), array(61))) {
                            $conditions['Plan.version'][] = 'BusinessSaver (Citipower)';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(62))) {
                            $conditions['Plan.version'][] = 'BusinessSaver (Powercor)';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(60))) {
                            $conditions['Plan.version'][] = 'BusinessSaver (Jemena)';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(64))) {
                            $conditions['Plan.version'][] = 'BusinessSaver (United Energy)';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(63))) {
                            $conditions['Plan.version'][] = 'BusinessSaver (Ausnet)';
                        }
                    }
                    if ($state == 'NSW' && $customer_type == 'SME') {
                        if ($nmi && in_array(substr($nmi, 0, 2), array(41))) {
                            $conditions['Plan.version'][] = 'BusinessSaver (Ausgrid)';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(43))) {
                            $conditions['Plan.version'][] = 'BusinessSaver (Endeavour Energy)';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(40, 42, 44, 45))) {
                            $conditions['Plan.version'][] = 'BusinessSaver (Essential Energy)';
                        }

                        if ($nmi && in_array(substr($nmi, 0, 2), array(40, 42, 43, 44, 45))) {
                            $conditions['Plan.version'][] = 'BusinessSaver (Essential & Endeavour)';
                        }
                    }

                    if ($state == 'NSW' && $customer_type == 'RES') {
                        if ($nmi && in_array(substr($nmi, 0, 2), array(40, 41, 42, 44, 45))) {
                            $conditions['Plan.version'][] = 'Origin Saver (Ausgrid+Essential)';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(43))) {
                            $conditions['Plan.version'][] = 'Origin Saver (Endeavour)';
                        }

                        if ($nmi && in_array(substr($nmi, 0, 2), array(41))) {
                            $conditions['Plan.version'][] = 'Origin Max Saver (Ausgrid)';
                            $conditions['Plan.version'][] = 'Origin Flexi (Ausgrid)';
                        }

                        if ($nmi && in_array(substr($nmi, 0, 2), array(43))) {
                            $conditions['Plan.version'][] = 'Origin Max Saver (Endeavour Energy)';
                            $conditions['Plan.version'][] = 'Origin Flexi (Endeavour Energy)';
                        }

                        if ($nmi && in_array(substr($nmi, 0, 2), array(40, 42, 44, 45))) {
                            $conditions['Plan.version'][] = 'Origin Max Saver (Essential Energy)';
                            $conditions['Plan.version'][] = 'Origin Flexi (Essential Energy)';
                        }
                    }

                    if ($state == 'VIC' && $customer_type == 'RES') {
                        if ($nmi && in_array(substr($nmi, 0, 2), array(63))) {
                            $conditions['Plan.version'][] = 'Origin Max Saver (Ausnet)';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(61))) {
                            $conditions['Plan.version'][] = 'Origin Max Saver (Citipower)';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(60))) {
                            $conditions['Plan.version'][] = 'Origin Max Saver (Jemena)';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(62))) {
                            $conditions['Plan.version'][] = 'Origin Max Saver (Powercor)';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(64))) {
                            $conditions['Plan.version'][] = 'Origin Max Saver (United Energy)';
                        }
                    }
                }
                if ($distributor_elec['ElectricityPostcodeDistributor']['lumo_energy_distributor']) {
                    $distributor_retailer_arr[] = 'Lumo Energy';
                }
                if ($distributor_elec['ElectricityPostcodeDistributor']['momentum_distributor']) {
                    $distributor_retailer_arr[] = 'Momentum';
                }
                if ($distributor_elec['ElectricityPostcodeDistributor']['powershop_distributor']) {
                    $distributor_retailer_arr[] = 'Powershop';
                }
                if ($distributor_elec['ElectricityPostcodeDistributor']['alinta_energy_distributor']) {
                    $distributor_retailer_arr[] = 'Alinta Energy';

                    if ($state == 'NSW' && $customer_type == 'SME') {
                        if ($nmi && in_array(substr($nmi, 0, 2), array(40, 41, 42, 44, 45))) {
                            $conditions['Plan.version'][] = 'Corporate Saver (Ausgrid+Essential)';
                        }
                    }
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
                    }
                    if ($state == 'VIC' && $customer_type == 'RES') {
                    }
                    if ($state == 'NSW' && $customer_type == 'RES') {
                        if ($nmi && in_array(substr($nmi, 0, 2), array(40, 42, 43, 44, 45))) {
                            $conditions['Plan.version'][] = 'Flexi Saver Essential Endeavour';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(41))) {
                            $conditions['Plan.version'][] = 'Flexi Saver Ausgrid';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(40, 42, 44, 45))) {
                            $conditions['Plan.version'][] = 'Flexi Saver Essential';
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
                if ($distributor_elec['ElectricityPostcodeDistributor']['actewagl_distributor']) {
                    $distributor_retailer_arr[] = 'ActewAGL';
                }
                if ($distributor_elec['ElectricityPostcodeDistributor']['elysian_energy_distributor']) {
                    $distributor_retailer_arr[] = 'Elysian Energy';
                }
                if ($distributor_elec['ElectricityPostcodeDistributor']['testing_retailer_distributor']) {
                    $distributor_retailer_arr[] = 'Testing Retailer';
                }
                if ($distributor_elec['ElectricityPostcodeDistributor']['ovo_energy_distributor']) {
                    $distributor_retailer_arr[] = 'OVO Energy';
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
                if ($distributor_gas['GasPostcodeDistributor']['momentum_distributor']) {
                    $distributor_retailer_arr[] = 'Momentum';
                }
                if ($distributor_gas['GasPostcodeDistributor']['powershop_distributor']) {
                    $distributor_retailer_arr[] = 'Powershop';
                }
                if ($distributor_gas['GasPostcodeDistributor']['alinta_energy_distributor']) {
                    $distributor_retailer_arr[] = 'Alinta Energy';
                }
                if ($distributor_gas['GasPostcodeDistributor']['energy_australia_distributor']) {
                    $distributor_retailer_arr[] = 'Energy Australia';
                }
                if ($distributor_gas['GasPostcodeDistributor']['sumo_power_distributor']) {
                    $distributor_retailer_arr[] = 'Sumo Power';
                }
                if ($distributor_gas['GasPostcodeDistributor']['actewagl_distributor']) {
                    $distributor_retailer_arr[] = 'ActewAGL';
                }
                if ($distributor_gas['GasPostcodeDistributor']['elysian_energy_distributor']) {
                    $distributor_retailer_arr[] = 'Elysian Energy';
                }
                if ($distributor_gas['GasPostcodeDistributor']['testing_retailer_distributor']) {
                    $distributor_retailer_arr[] = 'Testing Retailer';
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

                    if ($state == 'NSW' && $customer_type == 'RES') {
                        if ($nmi && in_array(substr($nmi, 0, 2), array(41))) {
                            $conditions['Plan.version'][] = 'Origin Saver Ausgrid';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(40, 42, 44, 45))) {
                            $conditions['Plan.version'][] = 'Origin Saver Essential Energy';
                        }
                    }

                    if ($state == 'VIC' && $customer_type == 'RES') {
                        if ($nmi && in_array(substr($nmi, 0, 2), array(62))) {
                            $conditions['Plan.version'][] = 'Origin Bill Saver (Powercor)';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(61))) {
                            $conditions['Plan.version'][] = 'Origin Bill Saver (Citipower)';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(64))) {
                            $conditions['Plan.version'][] = 'Origin Bill Saver (United Energy)';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(60))) {
                            $conditions['Plan.version'][] = 'Origin Bill Saver (Jemena)';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(63))) {
                            $conditions['Plan.version'][] = 'Origin Bill Saver (Ausnet)';
                        }
                    }

                    if ($state == 'VIC' && $customer_type == 'SME') {
                        if ($nmi && in_array(substr($nmi, 0, 2), array(61))) {
                            $conditions['Plan.version'][] = 'BusinessSaver (Citipower)';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(62))) {
                            $conditions['Plan.version'][] = 'BusinessSaver (Powercor)';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(60))) {
                            $conditions['Plan.version'][] = 'BusinessSaver (Jemena)';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(64))) {
                            $conditions['Plan.version'][] = 'BusinessSaver (United Energy)';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(63))) {
                            $conditions['Plan.version'][] = 'BusinessSaver (Ausnet)';
                        }
                    }
                    if ($state == 'NSW' && $customer_type == 'SME') {
                        if ($nmi && in_array(substr($nmi, 0, 2), array(41))) {
                            $conditions['Plan.version'][] = 'BusinessSaver (Ausgrid)';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(43))) {
                            $conditions['Plan.version'][] = 'BusinessSaver (Endeavour Energy)';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(40, 42, 44, 45))) {
                            $conditions['Plan.version'][] = 'BusinessSaver (Essential Energy)';
                        }

                        if ($nmi && in_array(substr($nmi, 0, 2), array(40, 42, 43, 44, 45))) {
                            $conditions['Plan.version'][] = 'BusinessSaver (Essential & Endeavour)';
                        }
                    }

                    if ($state == 'NSW' && $customer_type == 'RES') {
                        if ($nmi && in_array(substr($nmi, 0, 2), array(40, 41, 42, 44, 45))) {
                            $conditions['Plan.version'][] = 'Origin Saver (Ausgrid+Essential)';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(43))) {
                            $conditions['Plan.version'][] = 'Origin Saver (Endeavour)';
                        }

                        if ($nmi && in_array(substr($nmi, 0, 2), array(41))) {
                            $conditions['Plan.version'][] = 'Origin Max Saver (Ausgrid)';
                            $conditions['Plan.version'][] = 'Origin Flexi (Ausgrid)';
                        }

                        if ($nmi && in_array(substr($nmi, 0, 2), array(43))) {
                            $conditions['Plan.version'][] = 'Origin Max Saver (Endeavour Energy)';
                            $conditions['Plan.version'][] = 'Origin Flexi (Endeavour Energy)';
                        }

                        if ($nmi && in_array(substr($nmi, 0, 2), array(40, 42, 44, 45))) {
                            $conditions['Plan.version'][] = 'Origin Max Saver (Essential Energy)';
                            $conditions['Plan.version'][] = 'Origin Flexi (Essential Energy)';
                        }
                    }

                    if ($state == 'VIC' && $customer_type == 'RES') {
                        if ($nmi && in_array(substr($nmi, 0, 2), array(63))) {
                            $conditions['Plan.version'][] = 'Origin Max Saver (Ausnet)';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(61))) {
                            $conditions['Plan.version'][] = 'Origin Max Saver (Citipower)';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(60))) {
                            $conditions['Plan.version'][] = 'Origin Max Saver (Jemena)';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(62))) {
                            $conditions['Plan.version'][] = 'Origin Max Saver (Powercor)';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(64))) {
                            $conditions['Plan.version'][] = 'Origin Max Saver (United Energy)';
                        }
                    }
                }
                if ($distributor_elec['ElectricityPostcodeDistributor']['lumo_energy_distributor'] && $distributor_gas['GasPostcodeDistributor']['lumo_energy_distributor']) {
                    $distributor_retailer_arr[] = 'Lumo Energy';
                }
                if ($distributor_elec['ElectricityPostcodeDistributor']['momentum_distributor'] && $distributor_gas['GasPostcodeDistributor']['momentum_distributor']) {
                    $distributor_retailer_arr[] = 'Momentum';
                }
                if ($distributor_elec['ElectricityPostcodeDistributor']['powershop_distributor'] && $distributor_gas['GasPostcodeDistributor']['powershop_distributor']) {
                    $distributor_retailer_arr[] = 'Powershop';
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
                    }
                    if ($state == 'VIC' && $customer_type == 'RES') {
                        if ($nmi && (in_array(substr($nmi, 0, 4), array(6102, 6203, 6407)) || in_array(substr($nmi, 0, 3), array(600)))) {
                            $conditions['Plan.version'][] = 'Flexi Saver Citipower Powercor Jemena United';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 4), array(6305))) {
                            $conditions['Plan.version'][] = 'Flexi Saver Ausnet';
                        }
                    }
                    if ($state == 'NSW' && $customer_type == 'RES') {
                        if ($nmi && in_array(substr($nmi, 0, 2), array(40, 42, 43, 44, 45))) {
                            $conditions['Plan.version'][] = 'Flexi Saver Essential Endeavour';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(41))) {
                            $conditions['Plan.version'][] = 'Flexi Saver Ausgrid';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 2), array(40, 42, 44, 45))) {
                            $conditions['Plan.version'][] = 'Flexi Saver Essential';
                        }
                    }
                }
                if ($distributor_elec['ElectricityPostcodeDistributor']['sumo_power_distributor'] && $distributor_gas['GasPostcodeDistributor']['sumo_power_distributor']) {
                    $distributor_retailer_arr[] = 'Sumo Power';
                }
                if ($distributor_elec['ElectricityPostcodeDistributor']['pd_agl_distributor'] && $distributor_gas['GasPostcodeDistributor']['pd_agl_distributor']) {
                    $distributor_retailer_arr[] = 'Powerdirect and AGL';
                    if ($state == 'VIC' && $customer_type == 'RES') {
                        if ($nmi && in_array(substr($nmi, 0, 4), array(6102, 6001, 6203))) {
                            $conditions['Plan.version'][] = 'Residential (Citipower, Jemena & Powercor) + AGL Savers';
                        }
                        if ($nmi && in_array(substr($nmi, 0, 4), array(6407, 6305))) {
                            $conditions['Plan.version'][] = 'Residential (United & SP Ausnet) + AGL Savers';
                        }
                    }
                }
                if ($distributor_elec['ElectricityPostcodeDistributor']['actewagl_distributor'] && $distributor_gas['GasPostcodeDistributor']['actewagl_distributor']) {
                    $distributor_retailer_arr[] = 'ActewAGL';
                }
                if ($distributor_elec['ElectricityPostcodeDistributor']['elysian_energy_distributor'] && $distributor_gas['GasPostcodeDistributor']['elysian_energy_distributor']) {
                    $distributor_retailer_arr[] = 'Elysian Energy';
                }
                if ($distributor_elec['ElectricityPostcodeDistributor']['testing_retailer_distributor'] && $distributor_gas['GasPostcodeDistributor']['testing_retailer_distributor']) {
                    $distributor_retailer_arr[] = 'Testing Retailer';
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

            if ($tariff['Tariff']['agl_unsupported_tariff'] == 'Unsupported') {
                if (($key = array_search('AGL', $distributor_retailer_arr)) !== false) {
                    unset($distributor_retailer_arr[$key]);
                }
            }
            if ($tariff['Tariff']['origin_energy_unsupported_tariff'] == 'Unsupported') {
                if (($key = array_search('Origin Energy', $distributor_retailer_arr)) !== false) {
                    unset($distributor_retailer_arr[$key]);
                }
            }

            if ($tariff['Tariff']['powershop_unsupported_tariff'] == 'Unsupported') {
                if (($key = array_search('Powershop', $distributor_retailer_arr)) !== false) {
                    unset($distributor_retailer_arr[$key]);
                }
            }
            if ($tariff['Tariff']['powerdirect_unsupported_tariff'] == 'Unsupported') {
                if (($key = array_search('Powerdirect', $distributor_retailer_arr)) !== false) {
                    unset($distributor_retailer_arr[$key]);
                }
            }
            if ($tariff['Tariff']['momentum_unsupported_tariff'] == 'Unsupported') {
                if (($key = array_search('Momentum', $distributor_retailer_arr)) !== false) {
                    unset($distributor_retailer_arr[$key]);
                }
            }
            if ($tariff['Tariff']['sumo_power_unsupported_tariff'] == 'Unsupported') {
                if (($key = array_search('Sumo Power', $distributor_retailer_arr)) !== false) {
                    unset($distributor_retailer_arr[$key]);
                }
            }
            if ($tariff['Tariff']['erm_unsupported_tariff'] == 'Unsupported') {
                if (($key = array_search('ERM', $distributor_retailer_arr)) !== false) {
                    unset($distributor_retailer_arr[$key]);
                }
            }
            if ($tariff['Tariff']['pd_agl_unsupported_tariff'] == 'Unsupported') {
                if (($key = array_search('Powerdirect and AGL', $distributor_retailer_arr)) !== false) {
                    unset($distributor_retailer_arr[$key]);
                }
            }
            if ($tariff['Tariff']['lumo_energy_unsupported_tariff'] == 'Unsupported') {
                if (($key = array_search('Lumo Energy', $distributor_retailer_arr)) !== false) {
                    unset($distributor_retailer_arr[$key]);
                }
            }
            if ($tariff['Tariff']['next_business_energy_unsupported_tariff'] == 'Unsupported') {
                if (($key = array_search('Next Business Energy', $distributor_retailer_arr)) !== false) {
                    unset($distributor_retailer_arr[$key]);
                }
            }
            if ($tariff['Tariff']['actewagl_unsupported_tariff'] == 'Unsupported') {
                if (($key = array_search('ActewAGL', $distributor_retailer_arr)) !== false) {
                    unset($distributor_retailer_arr[$key]);
                }
            }
            if ($tariff['Tariff']['elysian_energy_unsupported_tariff'] == 'Unsupported') {
                if (($key = array_search('Elysian Energy', $distributor_retailer_arr)) !== false) {
                    unset($distributor_retailer_arr[$key]);
                }
            }
            if ($tariff['Tariff']['testing_retailer_unsupported_tariff'] == 'Unsupported') {
                if (($key = array_search('Testing Retailer', $distributor_retailer_arr)) !== false) {
                    unset($distributor_retailer_arr[$key]);
                }
            }
            if ($tariff['Tariff']['ovo_energy_unsupported_tariff'] == 'Unsupported') {
                if (($key = array_search('OVO Energy', $distributor_retailer_arr)) !== false) {
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

                $elec_rate_start_or = array(
                    'or' => array(
                        'ElectricityRate.rate_start' => '0000-00-00',
                        'ElectricityRate.rate_start <=' => date('Y-m-d'),
                    ),
                );
                $conditions[] = $elec_rate_start_or;

                $elec_rate_expire_or = array(
                    'or' => array(
                        'ElectricityRate.rate_expire' => '0000-00-00',
                        'ElectricityRate.rate_expire >=' => date('Y-m-d'),
                    ),
                );
                $conditions[] = $elec_rate_expire_or;

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
                        case 'Momentum':
                            $distributor_field = 'momentum_distributor';
                            break;
                        case 'Powershop':
                            $distributor_field = 'powershop_distributor';
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
                        case 'ActewAGL':
                            $distributor_field = 'actewagl_distributor';
                            break;
                        case 'Elysian Energy':
                            $distributor_field = 'elysian_energy_distributor';
                            break;
                        case 'Testing Retailer':
                            $distributor_field = 'testing_retailer_distributor';
                            break;
                        case 'OVO Energy':
                            $distributor_field = 'ovo_energy_distributor';
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
                        $gst = 1;
                        if (isset($rate['ElectricityRate']['gst_rates']) && $rate['ElectricityRate']['gst_rates'] == 'Yes') {
                            $gst = 1.1;
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
                            $rate['ElectricityRate']['peak_tier_1'] = ($rate['ElectricityRate']['peak_tier_1'] / $period) * $elec_billing_days;
                            $rate['ElectricityRate']['peak_tier_2'] = ($rate['ElectricityRate']['peak_tier_2'] / $period) * $elec_billing_days;
                            $rate['ElectricityRate']['peak_tier_3'] = ($rate['ElectricityRate']['peak_tier_3'] / $period) * $elec_billing_days;
                            $rate['ElectricityRate']['peak_tier_4'] = ($rate['ElectricityRate']['peak_tier_4'] / $period) * $elec_billing_days;
                        }
                        $tier_rates = array(
                            array('tier' => $rate['ElectricityRate']['peak_tier_1'], 'rate' => $rate['ElectricityRate']['peak_rate_1'] / $gst),
                            array('tier' => $rate['ElectricityRate']['peak_tier_2'], 'rate' => $rate['ElectricityRate']['peak_rate_2'] / $gst),
                            array('tier' => $rate['ElectricityRate']['peak_tier_3'], 'rate' => $rate['ElectricityRate']['peak_rate_3'] / $gst),
                            array('tier' => $rate['ElectricityRate']['peak_tier_4'], 'rate' => $rate['ElectricityRate']['peak_rate_4'] / $gst),
                            array('tier' => 0, 'rate' => $rate['ElectricityRate']['peak_rate_5'] / $gst)
                        );
                        $usage_sum = $peak_sum = $this->calculate($elec_peak, $tier_rates);
                        $stp_sum_elec = 0;
                        $discount_guaranteed_elec = 0;
                        $discount_elec = 0;
                        if ($rate['ElectricityRate']['stp_period'] == 'Y') {
                            $elec_billing = $elec_billing_days / 365;
                        } else if ($rate['ElectricityRate']['stp_period'] == 'Q') {
                            $elec_billing = $elec_billing_days / 91.25;
                        } else if ($rate['ElectricityRate']['stp_period'] == 'M') {
                            $elec_billing = $elec_billing_days / 30.42;
                        } else {
                            $elec_billing = $elec_billing_days;
                        }
                        $stp_sum_elec = ($elec_billing * $rate['ElectricityRate']['stp']) / $gst;
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

                $gas_rate_start_or = array(
                    'or' => array(
                        'GasRate.rate_start' => '0000-00-00',
                        'GasRate.rate_start <=' => date('Y-m-d'),
                    ),
                );
                $conditions[] = $gas_rate_start_or;

                $gas_rate_expire_or = array(
                    'or' => array(
                        'GasRate.rate_expire' => '0000-00-00',
                        'GasRate.rate_expire >=' => date('Y-m-d'),
                    ),
                );
                $conditions[] = $gas_rate_expire_or;

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
                        case 'Momentum':
                            $distributor_field = 'momentum_distributor';
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
                        case 'Powerdirect and AGL':
                            $distributor_field = 'pd_agl_distributor';
                            break;
                        case 'ActewAGL':
                            $distributor_field = 'actewagl_distributor';
                            break;
                        case 'Elysian Energy':
                            $distributor_field = 'elysian_energy_distributor';
                            break;
                        case 'Testing Retailer':
                            $distributor_field = 'testing_retailer_distributor';
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
                        $gst = 1;
                        if (isset($rate['GasRate']['gst_rates']) && $rate['GasRate']['gst_rates'] == 'Yes') {
                            $gst = 1.1;
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
                            $rate['GasRate']['peak_tier_1'] = ($rate['GasRate']['peak_tier_1'] / $period) * $gas_billing_days;
                            $rate['GasRate']['peak_tier_2'] = ($rate['GasRate']['peak_tier_2'] / $period) * $gas_billing_days;
                            $rate['GasRate']['peak_tier_3'] = ($rate['GasRate']['peak_tier_3'] / $period) * $gas_billing_days;
                            $rate['GasRate']['peak_tier_4'] = ($rate['GasRate']['peak_tier_4'] / $period) * $gas_billing_days;
                            $rate['GasRate']['peak_tier_5'] = ($rate['GasRate']['peak_tier_5'] / $period) * $gas_billing_days;
                        }
                        $peak_tier_rates = array(
                            array('tier' => $rate['GasRate']['peak_tier_1'], 'rate' => $rate['GasRate']['peak_rate_1'] / 100 / $gst),
                            array('tier' => $rate['GasRate']['peak_tier_2'], 'rate' => $rate['GasRate']['peak_rate_2'] / 100 / $gst),
                            array('tier' => $rate['GasRate']['peak_tier_3'], 'rate' => $rate['GasRate']['peak_rate_3'] / 100 / $gst),
                            array('tier' => $rate['GasRate']['peak_tier_4'], 'rate' => $rate['GasRate']['peak_rate_4'] / 100 / $gst),
                            array('tier' => $rate['GasRate']['peak_tier_5'], 'rate' => $rate['GasRate']['peak_rate_5'] / 100 / $gst),
                            array('tier' => 0, 'rate' => $rate['GasRate']['peak_rate_6'] / 100 / $gst),
                        );
                        $peak_sum = $this->calculate($gas_peak, $peak_tier_rates, true);
                        $off_peak_sum = 0;

                        $usage_sum = $peak_sum + $off_peak_sum;
                        $stp_sum_gas = 0;
                        $discount_guaranteed_gas = 0;
                        $discount_gas = 0;
                        if ($rate['GasRate']['stp_period'] == 'Y') {
                            $gas_billing = $gas_billing_days / 365;
                        } else if ($rate['GasRate']['stp_period'] == 'Q') {
                            $gas_billing = $gas_billing_days / 91.25;
                        } else if ($rate['GasRate']['stp_period'] == 'M') {
                            $gas_billing = $gas_billing_days / 30.42;
                        } else {
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
            } else if ($plan_type == 'Gas') {
                $plans[$plan['Plan']['total_inc_discount_gas'] * 10000 + $plan['Plan']['id']] = $plan;
            } else if ($plan_type == 'Dual') {
                $plans[($plan['Plan']['total_inc_discount_elec'] + $plan['Plan']['total_inc_discount_gas']) * 10000 + $plan['Plan']['id']] = $plan;
            }
        }
        ksort($plans);

        return $plans;
    }

    public function form1()
    {
        $this->set('title_for_layout', 'Lead Details');

        $sid = $this->Session->read('User.sid');

        $step = '4';

        $step1 = false;
        if ($this->Session->check('User.step1')) {
            $step1 = $this->Session->read('User.step1');
        }

        $step2 = false;
        if ($this->Session->check('User.step2')) {
            $step2 = $this->Session->read('User.step2');
        }

        $states = array(
            '' => 'Please select',
            'VIC' => 'Victoria',
            'NSW' => 'New South Wales',
            'QLD' => 'Queensland',
            'SA' => 'South Australia'
        );

        $owner_renter = array(
            '' => 'Please select',
            'Renter' => 'Renter',
            'Owner' => 'Owner',
        );
        $solar = array(
            '' => 'Please select',
            'Yes' => 'Yes',
            'No' => 'No',
        );
        $batter_storage = array(
            '' => 'Please select',
            'Yes' => 'Yes',
            'No' => 'No',
        );
        $batter_storage_solar = array(
            '' => 'Please select',
            'Yes' => 'Yes',
            'No' => 'No',
        );

        if ($this->request->is('put') || $this->request->is('post')) {
            $submission = array();

            if (isset($this->request->data['agent_id']) && $this->request->data['agent_id']) {
                $agent_id = $this->request->data['agent_id'];
                // Chelsea / Sean / Jolene
                if (in_array($agent_id, array('125', '191', '196'))) {
                    $submission['submitted']['status'] = '*TestStatus';
                }
            }

            $name_arr = explode(' ', $this->request->data['name']);
            if (count($name_arr) == 2) {
                $submission['submitted']['FirstName'] = ucfirst($name_arr[0]);
                $submission['submitted']['Surname'] = ucfirst($name_arr[1]);
            } else {
                $submission['submitted']['name'] = ucfirst($this->request->data['name']);
            }
            $submission['submitted']['MobileNumber'] = (isset($this->request->data['phone_number']) && $this->request->data['phone_number']) ? $this->request->data['phone_number'] : 0;
            $submission['submitted']['primaryPhone'] = (isset($this->request->data['phone_number']) && $this->request->data['phone_number']) ? $this->request->data['phone_number'] : '';
            $submission['submitted']['EmailM'] = $this->request->data['email'];
            $submission['submitted']['SuburbSupply'] = $this->request->data['suburb'];
            $submission['submitted']['PostcodeSupply'] = $this->request->data['postcode'];
            $submission['submitted']['StateSupply'] = $this->request->data['state'];
            $submission['submitted']['StreetNameSupply'] = $this->request->data['street'];

            $submission['submitted']['TenantOwner'] = $this->request->data['tenant_owner'];
            $submission['submitted']['SolarPanels'] = $this->request->data['solar'];
            if ($this->request->data['solar'] == 'Yes') {
                $submission['submitted']['BatteryStorageEOI'] = $this->request->data['batter_storage'];
            } else {
                $submission['submitted']['BatteryStorageSolarEOI'] = $this->request->data['batter_storage_solar'];
            }

            if ($this->request->data['sid']) {
                $sid = $this->request->data['sid'];
                $campaign_id = $this->request->data['campaign_id'];
                $campaign_name = $this->request->data['campaign_name'];
                $first_campaign = $this->request->data['first_campaign'];
                if ($campaign_id) {
                    switch ($campaign_id) {
                        case '100':
                            if (!$first_campaign) {
                                $first_campaign = $submission['submitted']['first_campaign_name'] = 'Electrician Inbound Leads';
                            }
                            break;
                        case '95':
                            if (!$first_campaign) {
                                $first_campaign = $submission['submitted']['first_campaign_name'] = 'True Value Solar';
                            }
                            break;
                        case '1':
                            if (!$first_campaign) {
                                $first_campaign = $submission['submitted']['first_campaign_name'] = 'Phone';
                            }
                            break;
                    }
                } else {
                    if (!$first_campaign) {
                        $first_campaign = $submission['submitted']['first_campaign_name'] = $campaign_name;
                    }
                }
                $this->update_lead($campaign_id, $sid, $submission);
            } else {
                $campaign_id = 1;
                $first_campaign = $campaign_name = 'Phone';
                $submission['submitted']['first_campaign_name'] = $first_campaign;

                $agent_id = '';
                if (isset($this->request->data['agent_id']) && $this->request->data['agent_id']) {
                    $agent_id = $this->request->data['agent_id'];
                    // Chelsea / Sean / Jolene
                    if (in_array($agent_id, array('125', '191', '196'))) {
                        $submission['submitted']['status'] = '*TestStatus';
                    }
                }
                $sid = $this->create_lead($campaign_id, $submission);

                if ($agent_id) {
                    $agent = $this->assign_to_agent($sid, $agent_id);
                }
            }

            $this->Session->setFlash(__('The lead has been saved'), 'flash_success');
            $this->redirect(array('action' => 'index'));

        }

        $this->set(compact('sid', 'step', 'step1', 'step2', 'states', 'owner_renter', 'solar', 'batter_storage', 'batter_storage_solar'));
    }

    private function broadband($data)
    {
        $username = 'tester';
        $password = 'k6TakUkusA';
        $url = "https://cbb2.calldynamics.net.au/api/addreferral/?key=qJeDGpYgOFwVsVOTVPZoRyCIVLJf3S7X"; // staging
        //$url = "https://comparebroadband.com.au/api/addreferral/?key=qJeDGpYgOFwVsVOTVPZoRyCIVLJf3S7X"; // live
        try {
            $headers = array('Content-Type: application/json', 'Authorization:Basic ' . base64_encode($username . ':' . $password)); // staging
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers); // staging
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $curl_response = curl_exec($curl);
            $response = json_decode($curl_response, true);
            curl_close($curl);

            $ln = "./logs/broadband-" . date('YmdHis') . ".log";
            $log = fopen($ln, "a");
            $content = $data . "\n";
            $content .= print_r($response, true);
            fwrite($log, $content . "\n");
            fclose($log);

            return $response;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function repost()
    {
        $ids = array();

        $idsNew = array();

        foreach ($ids as $id) {
            //$submission = $this->Submission->findById($id);
            $submissions = $this->Submission->find('all', array(
                'conditions' => array(
                    'Submission.leadid' => $id,
                ),
                'order' => 'Submission.id ASC'
            ));

            if (!empty($submissions)) {
                foreach ($submissions as $submission) {
                    $request = $submission['Submission']['request'];

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, "https://secure.velocify.com/Import.aspx?Provider=RSMSolutions&Client=RSMSolutions&CampaignId=2&XmlResponse=True");
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
                    $response = curl_exec($ch);
                    curl_close($ch);
                    $leadId = 0;
                    if ($response) {
                        $result = simplexml_load_string($response);
                        foreach ($result->ImportResult[0]->attributes() as $key => $value) {
                            if ($key == 'leadId') {
                                $leadId = (int)$value;
                            }
                        }
                    }
                    $idsNew[] = $leadId;
                    break;

                    /*
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, "https://secure.velocify.com/Update.aspx?Provider=RSMSolutions&Client=RSMSolutions&CampaignId=2&XmlResponse=True&LeadId={$id}");
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
                    $response = curl_exec($ch);
                    curl_close($ch);
                    if (strpos($response, 'Success') !== false) {
                        //return $id;
                    } else {
                        $to = 'info@seanpro.com';
                        $subject = 'Velocify API error - Update - Repost';
                        $message = "Lead ID: {$id}\r\n";
                        $message .= $response;
                        $headers = 'From: api@seanpro.com' . "\r\n" .
                            'Reply-To: api@seanpro.com' . "\r\n" .
                            'X-Mailer: PHP/' . phpversion();

                        mail($to, $subject, $message, $headers);
                    }
                    */
                }
            }
        }
        print_r($idsNew);
        exit;
    }

}

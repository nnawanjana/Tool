<?php

App::uses('AppController', 'Controller');

class ToolsController extends AppController
{
    public $uses = array('Plan', 'Location', 'ElectricityRate', 'GasRate', 'Tariff', 'ElectricityPostcodeDistributor', 'ElectricityPostcodeDistributor2', 'GasPostcodeDistributor', 'GasPostcodeDistributor2', 'ElectricityNmiDistributor', 'Consumption', 'SolarRebateScheme', 'Holiday', 'StreetType', 'Product', 'Sale', 'Pdf', 'LeadType', 'RetailerCommission', 'Tool', 'TermCondition', 'ElectricityBpid', 'GasBpid', 'OffshoreLeadExceptions', 'DmoVdo', 'MoveInInfo', 'ElectricianName', 'OriginLpg', 'Option', 'GasConversion', 'User');
    public $helpers = array('Html', 'Icon');

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->Auth->allow('postcode_to_suburb', 'suburb_options', 'get_state_by_suburb', 'tariff_options', 'tariff_options2', 'get_usage_level', 'street_type', 'sales_rep', 'get_lead_fields', 'salestool', 'electrician_name', 'test');

        if (!in_array($this->request->clientIp(), unserialize(STAFF_IPS))) {
            //$this->redirect(MAIN_SITE);
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
        $this->redirect(MAIN_SITE);
    }

    public function import($type = 'plans')
    {
        if (!isset($this->request->query['de']) || $this->request->query['de'] != 1) {
            $this->redirect(MAIN_SITE);
        }
        App::import('vendor', 'CSVImporter');

        $ln = "files/import.log";
        $log = fopen($ln, "a");
        $now = date('Y-m-d H:i:s');
        $content = "{$now} {$type}";
        fwrite($log, $content . "\n");
        fclose($log);

        switch ($type) {
            case 'plans':
                $this->Plan->query('TRUNCATE TABLE plans;');
                $importer = new CSVImporter("files/Plans - Plans.csv", true, ',');
                $rows = $importer->get();
                foreach ($rows as $row) {
                    $row['Plan Expiry'] = ($row['Plan Expiry']) ? str_replace('/', '-', $row['Plan Expiry']) : '';
                    $plan = array('Plan' => array(
                        'state' => trim($row['State']),
                        'retailer' => trim($row['Retailer']),
                        'res_sme' => trim($row['RES/SME']),
                        'package' => trim($row['Package']),
                        'product_name' => trim($row['Product Name']),
                        'rate_name' => trim($row['Rate Name']),
                        'acq_ret' => trim($row['Acq/Ret']),
                        'pdf' => $row['PDF'],
                        'discount_bonuses_description' => $row['Discount & Bonuses Description'],
                        'discount_guaranteed_elec' => $row['Discount: Guaranteed Elec'],
                        'discount_guaranteed_gas' => $row['Discount: Guaranteed Gas'],
                        'discount_guaranteed_description' => $row['Discount: Guaranteed Description'],
                        'discount_pay_on_time_elec' => $row['Discount: Pay on Time Elec'],
                        'discount_pay_on_time_gas' => $row['Discount: Pay on Time Gas'],
                        'discount_pay_on_time_description' => $row['Discount: Pay on Time Description'],
                        'discount_direct_debit_elec' => $row['Discount: Direct Debit Elec'],
                        'discount_direct_debit_gas' => $row['Discount: Direct Debit Gas'],
                        'discount_direct_debit_description' => $row['Discount: Direct Debit Description'],
                        'discount_credit_elec' => $row['Discount: Credit Elec'],
                        'discount_credit_gas' => $row['Discount: Credit Gas'],
                        'discount_credit_description' => $row['Discount: Credit Description'],
                        'discount_dual_fuel_elec' => $row['Discount: Dual Fuel Elec'],
                        'discount_dual_fuel_gas' => $row['Discount: Dual Fuel Gas'],
                        'discount_dual_fuel_description' => $row['Discount: Dual Fuel Description'],
                        'discount_prepay_elec' => $row['Discount: Prepay Elec'],
                        'discount_bonus_sumo' => $row['Discount: Bonus - Sumo'],
                        'discount_applies' => $row['Discount Applies'],
                        'discount_applies_gas' => $row['Discount Applies Gas'],
                        'special_offer' => $row['Special Offer'],
                        'product_summary' => $row['Product Summary'],
                        'signup_summary' => $row['Sign Up Summary'],
                        'terms' => $row['Terms'],
                        'benefit1_title' => $row['Benefit 1 Title'],
                        'benefit1_description' => $row['Benefit 1 Description'],
                        'benefit2_title' => $row['Benefit 2 Title'],
                        'benefit2_description' => $row['Benefit 2 Description'],
                        'benefit3_title' => $row['Benefit 3 Title'],
                        'benefit3_description' => $row['Benefit 3 Description'],
                        'benefit4_title' => $row['Benefit 4 Title'],
                        'benefit4_description' => $row['Benefit 4 Description'],
                        'unique_benefit' => $row['Unique Benefit'],
                        'short_description' => $row['Short Description'],
                        'full_description' => $row['Full Description'],
                        'contract_length' => $row['Contract Length'],
                        'exit_fee' => $row['Exit Fee'],
                        'conditional_discount' => $row['Conditional Discount'],
                        'bill_smoothing' => $row['Bill Smoothing'],
                        'bill_smoothing_details' => $row['Bill Smoothing: Details'],
                        'online_account_management' => $row['Online Account Management'],
                        'online_account_management_details' => $row['Online Account Management: Details'],
                        'energy_monitoring_tools' => $row['Energy Monitoring Tools'],
                        'energy_monitoring_tools_details' => $row['Energy Monitoring Tools: Details'],
                        'membership_reward_programs' => $row['Membership Reward Programs'],
                        'membership_reward_programs_details' => $row['Membership Reward Programs: Details'],
                        'renewable_energy' => $row['Renewable Energy'],
                        'renewable_energy_details' => $row['Renewable Energy: Details'],
                        'rate_freeze' => $row['Rate Freeze'],
                        'rate_freeze_details' => $row['Rate Freeze: Details'],
                        'pay_on_time' => $row['Pay on Time'],
                        'no_contract_plan' => $row['No Contract Plan'],
                        'no_contract_plan_details' => $row['No Contract Plan: Details'],
                        'solar_specific_plan' => $row['Solar-Specific Plan'],
                        'total_bill_discount' => $row['Total Bill Discount'],
                        'paper_billing' => $row['Paper Billing'],
                        'e_bill' => $row['E-Bill'],
                        'billing_period' => $row['Billing Period'],
                        'late_payment_fee' => $row['Late Payment Fee'],
                        'dishonoured_payment_fee' => $row['Dishonoured Payment Fee'],
                        'card_payment_fee' => $row['Card Payment Fee'],
                        'other_fees' => $row['Other Fees'],
                        'solar_metering_charge' => $row['Solar Metering Charge'],
                        'direct_debit' => $row['Direct Debit'],
                        'bpay' => $row['BPAY'],
                        'credit_card' => $row['Credit Card (Visa or MasterCard)'],
                        'easipay' => $row['EasiPay'],
                        'online' => $row['Online'],
                        'centrepay' => $row['Centrepay'],
                        'cash' => $row['Cash (Aust Post)'],
                        'cheque' => $row['Cheque'],
                        'post_billpay' => $row['POST billpay'],
                        'pay_by_phone' => $row['Pay by Phone'],
                        'amex' => $row['AMEX'],
                        'new_connection' => $row['New Connection'],
                        'usage' => $row['Usage'],
                        'product_code_elec' => $row['Product Code Elec'],
                        'product_code_gas' => $row['Product Code Gas'],
                        'campaign_code_elec' => $row['Campaign Code Elec'],
                        'campaign_code_gas' => $row['Campaign Code Gas'],
                        'offshore_lead_generation' => $row['Offshore Lead Generation'],
                        'version' => ($row['Version']) ? trim($row['Version']) : 'All',
                        'sd' => (trim($row['SD'])) ? trim($row['SD']) : 'No',
                        'status' => trim($row['Status']),
                        'plan_start' => ($row['Plan Start']) ? date('Y-m-d', strtotime(str_replace('/', '-', $row['Plan Start']))) : '0000-00-00',
                        'plan_expiry' => ($row['Plan Expiry']) ? date('Y-m-d', strtotime(str_replace('/', '-', $row['Plan Expiry']))) : '0000-00-00',
                        'solar_boost_fit' => trim($row['Solar Boost FiT']),
                        'solar_boost_cap' => trim($row['Solar Boost Cap']),
                    ));
                    $this->Plan->create();
                    $this->Plan->save($plan);
                }
                echo 'Plans have been imported.';
                break;
            case 'electricity_rates':
                $this->Plan->query('TRUNCATE TABLE electricity_rates;');
                $importer = new CSVImporter("files/Rates - Electricity.csv", true, ',');
                $rows = $importer->get();
                foreach ($rows as $row) {
                   $rate = array('ElectricityRate' => array(
                       'state' => trim($row['State']),
                       'distributor' => trim($row['Distributor']),
                       'res_sme' => trim($row['RES/SME']),
                       'retailer' => trim($row['Retailer']),
                       'tariff_type' => trim($row['Tariff Type']),
                       'tariff_class' => trim($row['Tariff Class']),
                       'rate_name' => $row['Rate Name'],
                       'rate_tier_period' => ($row['Rate Tier Period']) ? trim($row['Rate Tier Period']) : 'D',
                       'stp_period' => trim($row['STP Period']),
                       'peak_tier_1' => $row['Peak Tier 1'],
                       'peak_tier_2' => $row['Peak Tier 2'],
                       'peak_tier_3' => $row['Peak Tier 3'],
                       'peak_tier_4' => $row['Peak Tier 4'],
                       'controlled_load_tier_1' => $row['Controlled Load Tier 1'],
                       'peak_rate_1' => $row['Peak Rate 1'],
                       'peak_rate_2' => $row['Peak Rate 2'],
                       'peak_rate_3' => $row['Peak Rate 3'],
                       'peak_rate_4' => $row['Peak Rate 4'],
                       'peak_rate_5' => $row['Peak Rate 5'],
                       'shoulder_rate' => $row['Shoulder Rate'],
                       'controlled_load_1_rate_1' => $row['Controlled Load 1 Rate 1'],
                       'controlled_load_1_rate_2' => $row['Controlled Load 1 Rate 2'],
                       'controlled_load_2_rate' => $row['Controlled Load 2 Rate'],
                       'controlled_load_2_stp' => $row['Controlled Load 2 STP'],
                       'controlled_load_peak_rate' => $row['Controlled Load Peak Rate'],
                       'controlled_load_off_peak_rate' => $row['Controlled Load Off Peak Rate'],
                       'off_peak_rate' => $row['Off Peak Rate'],
                       'demand_uom' => $row['Demand UOM'],
                       'demand_frequency' => $row['Demand Frequency'],
                       'capacity_charge' => $row['Capacity Charge'],
                       'summer_capacity_charge' => $row['Summer Capacity Charge'],
                       'non_summer_capacity_charge' => $row['Non Summer Capacity Charge'],
                       'non_summer_capacity_period' => $row['Non Summer Capacity Period'],
                       'high_demand_charge' => $row['High Demand Charge'],
                       'low_demand_charge' => $row['Low Demand Charge'],
                       'high_demand_period' => $row['High Demand Period'],
                       'low_demand_period' => $row['Low Demand Period'],
                       'climate_saver_rate' => $row['Climate Saver Rate'],
                       'demand_kva' => $row['Demand KVA'],
                       'demand_rolling_kva' => $row['Demand Rolling KVA'],
                       'solar_meter_charge' => $row['Solar Meter Charge'],
                       //'single_capacity_charge' => $row['Single Capacity Charge'],
                       //'summer_non_summer_charge' => $row['Summer/Non Summer Charge'],
                       'stp' => $row['STP'],
                       'status' => $row['Status'],
                       'rate_start' => ($row['Rates Start']) ? date('Y-m-d', strtotime(str_replace('/', '-', $row['Rates Start']))) : '0000-00-00',
                       'rate_expire' => ($row['Rates Expire']) ? date('Y-m-d', strtotime(str_replace('/', '-', $row['Rates Expire']))) : '0000-00-00',
                       'gst_rates' => $row['GST Rates'],
                   ));
                    $this->ElectricityRate->create();
                    $this->ElectricityRate->save($rate);
                }
                echo 'Rates have been imported.';
                break;
            case 'gas_rates':
                $this->Plan->query('TRUNCATE TABLE gas_rates;');
                $importer = new CSVImporter("files/Rates - Gas.csv", true, ',');
                $rows = $importer->get();
                foreach ($rows as $row) {
                    $rate = array('GasRate' => array(
                        'state' => trim($row['State']),
                        'distributor' => trim($row['Distributor']),
                        'res_sme' => trim($row['RES/SME']),
                        'retailer' => trim($row['Retailer']),
                        'rate_name' => trim($row['Rate Name']),
                        'rate_tier_period' => ($row['Rate Tier Period']) ? trim($row['Rate Tier Period']) : 'D',
                        'stp_period' => trim($row['STP Period']),
                        'peak_tier_1' => $row['Peak Tier 1'],
                        'peak_tier_2' => $row['Peak Tier 2'],
                        'peak_tier_3' => $row['Peak Tier 3'],
                        'peak_tier_4' => $row['Peak Tier 4'],
                        'peak_tier_5' => $row['Peak Tier 5'],
                        'peak_rate_1' => $row['Peak Rate 1'],
                        'peak_rate_2' => $row['Peak Rate 2'],
                        'peak_rate_3' => $row['Peak Rate 3'],
                        'peak_rate_4' => $row['Peak Rate 4'],
                        'peak_rate_5' => $row['Peak Rate 5'],
                        'peak_rate_6' => $row['Peak Rate 6'],
                        'off_peak_tier_1' => $row['Off Peak Tier 1'],
                        'off_peak_tier_2' => $row['Off Peak Tier 2'],
                        'off_peak_tier_3' => $row['Off Peak Tier 3'],
                        'off_peak_tier_4' => $row['Off Peak Tier 4'],
                        'off_peak_rate_1' => $row['Off Peak Rate 1'],
                        'off_peak_rate_2' => $row['Off Peak Rate 2'],
                        'off_peak_rate_3' => $row['Off Peak Rate 3'],
                        'off_peak_rate_4' => $row['Off Peak Rate 4'],
                        'off_peak_rate_5' => $row['Off Peak Rate 5'],
                        'stp' => $row['STP'],
                        'peak_start_date' => $row['Peak Start Date (D-M)'],
                        'peak_end_date' => $row['Peak End Date (D-M)'],
                        'status' => $row['Status'],
                        'rate_start' => ($row['Rates Start']) ? date('Y-m-d', strtotime(str_replace('/', '-', $row['Rates Start']))) : '0000-00-00',
                        'rate_expire' => ($row['Rates Expire']) ? date('Y-m-d', strtotime(str_replace('/', '-', $row['Rates Expire']))) : '0000-00-00',
                        'gst_rates' => $row['GST Rates'],
                    ));
                    $this->GasRate->create();
                    $this->GasRate->save($rate);
                }
                echo 'Rates have been imported.';
                break;
            case 'tariffs':
                $this->Plan->query('TRUNCATE TABLE tariffs;');
                $importer = new CSVImporter("files/Tariff Mapping - Tariffs.csv", true, ',');
                $rows = $importer->get();
                foreach ($rows as $row) {
                    if (strpos($row['RES/SME'], '/') !== false) {
                        $res_sme_arr = explode('/', $row['RES/SME']);
                        foreach ($res_sme_arr as $res_sme) {
                            $tariff = array('Tariff' => array(
                                'tariff_code' => trim($row['Tariff Code']),
                                'res_sme' => trim($res_sme),
                                'state' => trim($row['State']),
                                'distributor' => trim($row['Distributor']),
                                'internal_tariff' => trim($row['Internal Tariff']),
                                'pricing_group' => trim($row['Pricing Group']),
                                'tariff_class' => trim($row['Tariff Class']),
                                'tariff_type' => trim($row['Tariff Type']),
                                'child_tariff' => trim($row['Child Tariff']),
                                'solar_rebate_scheme' => trim($row['Solar Rebate Scheme']),
                                'net_gross_tariff' => trim($row['Net/Gross Tariff']),
                                'agl_unsupported_tariff' => $row['AGL Unsupported Tariff'],
                                'origin_energy_unsupported_tariff' => $row['Origin Energy Unsupported Tariff'],
                                'powershop_unsupported_tariff' => $row['Powershop Unsupported Tariff'],
                                'powerdirect_unsupported_tariff' => $row['Powerdirect Unsupported Tariff'],
                                'momentum_unsupported_tariff' => $row['Momentum Unsupported Tariff'],
                                'alinta_energy_unsupported_tariff' => $row['Alinta Energy Unsupported Tariff'],
                                'energy_australia_unsupported_tariff' => $row['Energy Australia Unsupported Tariff'],
                                'sumo_power_unsupported_tariff' => $row['Sumo Power Unsupported Tariff'],
                                'erm_unsupported_tariff' => $row['ERM Unsupported Tariff'],
                                'pd_agl_unsupported_tariff' => $row['Powerdirect and AGL Unsupported Tariff'],
                                'lumo_energy_unsupported_tariff' => $row['Lumo Energy Unsupported Tariff'],
                                'next_business_energy_unsupported_tariff' => $row['Next Business Energy Unsupported Tariff'],
                                'actewagl_unsupported_tariff' => $row['ActewAGL Unsupported Tariff'],
                                'elysian_energy_unsupported_tariff' => $row['Elysian Energy Unsupported Tariff'],
                                'testing_retailer_unsupported_tariff' => $row['Testing Retailer Unsupported Tariff'],
                                'ovo_energy_unsupported_tariff' => $row['OVO Energy Unsupported Tariff'],
                                'tango_energy_unsupported_tariff' => $row['Tango Energy Unsupported Tariff'],
                                'red_energy_unsupported_tariff' => $row['Red Energy Unsupported Tariff'],
                            ));
                            $this->Tariff->create();
                            $this->Tariff->save($tariff);
                        }
                    } else {
                        $tariff = array('Tariff' => array(
                            'tariff_code' => trim($row['Tariff Code']),
                            'res_sme' => trim($row['RES/SME']),
                            'state' => trim($row['State']),
                            'distributor' => trim($row['Distributor']),
                            'internal_tariff' => trim($row['Internal Tariff']),
                            'pricing_group' => trim($row['Pricing Group']),
                            'tariff_class' => trim($row['Tariff Class']),
                            'tariff_type' => trim($row['Tariff Type']),
                            'child_tariff' => trim($row['Child Tariff']),
                            'solar_rebate_scheme' => trim($row['Solar Rebate Scheme']),
                            'net_gross_tariff' => trim($row['Net/Gross Tariff']),
                            'agl_unsupported_tariff' => $row['AGL Unsupported Tariff'],
                            'origin_energy_unsupported_tariff' => $row['Origin Energy Unsupported Tariff'],
                            'powershop_unsupported_tariff' => $row['Powershop Unsupported Tariff'],
                            'powerdirect_unsupported_tariff' => $row['Powerdirect Unsupported Tariff'],
                            'momentum_unsupported_tariff' => $row['Momentum Unsupported Tariff'],
                            'alinta_energy_unsupported_tariff' => $row['Alinta Energy Unsupported Tariff'],
                            'energy_australia_unsupported_tariff' => $row['Energy Australia Unsupported Tariff'],
                            'sumo_power_unsupported_tariff' => $row['Sumo Power Unsupported Tariff'],
                            'erm_unsupported_tariff' => $row['ERM Unsupported Tariff'],
                            'pd_agl_unsupported_tariff' => $row['Powerdirect and AGL Unsupported Tariff'],
                            'lumo_energy_unsupported_tariff' => $row['Lumo Energy Unsupported Tariff'],
                            'next_business_energy_unsupported_tariff' => $row['Next Business Energy Unsupported Tariff'],
                            'actewagl_unsupported_tariff' => $row['ActewAGL Unsupported Tariff'],
                            'elysian_energy_unsupported_tariff' => $row['Elysian Energy Unsupported Tariff'],
                            'testing_retailer_unsupported_tariff' => $row['Testing Retailer Unsupported Tariff'],
                            'ovo_energy_unsupported_tariff' => $row['OVO Energy Unsupported Tariff'],
                            'tango_energy_unsupported_tariff' => $row['Tango Energy Unsupported Tariff'],
                            'red_energy_unsupported_tariff' => $row['Red Energy Unsupported Tariff'],
                        ));
                        $this->Tariff->create();
                        $this->Tariff->save($tariff);
                    }
                }
                echo 'Tariffs have been imported.';
                break;
            case 'electricity_postcode_distributor':
                $this->Plan->query('TRUNCATE TABLE electricity_postcode_distributors;');
                $importer = new CSVImporter("files/Postcode _ Distributor Mapping - ELEC.csv", true, ',');
                $rows = $importer->get();
                foreach ($rows as $row) {
                    $postcode_distributor = array('ElectricityPostcodeDistributor' => array(
                        'state' => trim($row['State']),
                        'postcode' => trim($row['Postcode']),
                        'suburb' => ucwords(strtolower($row['Suburb'])),
                        'agl_distributor' => trim($row['AGL Distributor']),
                        'powerdirect_distributor' => trim($row['Powerdirect Distributor']),
                        'origin_energy_distributor' => trim($row['Origin Energy Distributor']),
                        'origin_energy_special_product_name' => trim($row['Origin Energy Special Product Name']),
                        'origin_energy_origin_saver_patch' => trim($row['Origin Energy Origin Saver Patch']),
                        'origin_energy_businesssaver_hv' => trim($row['Origin Energy BusinessSaver HV (Patch Offer)']),
                        'lumo_energy_distributor' => trim($row['Lumo Energy Distributor']),
                        'momentum_distributor' => trim($row['Momentum Distributor']),
                        'powershop_distributor' => trim($row['Powershop Distributor']),
                        'alinta_energy_distributor' => trim($row['Alinta Energy Distributor']),
                        'energy_australia_distributor' => trim($row['Energy Australia Distributor']),
                        'sumo_power_distributor' => trim($row['Sumo Power Distributor']),
                        'erm_distributor' => trim($row['ERM Distributor']),
                        'pd_agl_distributor' => trim($row['Powerdirect and AGL Distributor']),
                        'next_business_energy_distributor' => trim($row['Next Business Energy Distributor']),
                        'actewagl_distributor' => trim($row['ActewAGL Distributor']),
                        'elysian_energy_distributor' => trim($row['Elysian Energy Distributor']),
                        'testing_retailer_distributor' => trim($row['Testing Retailer Distributor']),
                        'tango_energy_distributor' => trim($row['Tango Energy Distributor']),
                        'red_energy_distributor' => trim($row['Red Energy Distributor']),
                        'ovo_energy_distributor' => trim($row['OVO Energy Distributor']),
                        'master_distributor' => trim($row['Master Distributor']),
                    ));
                    $this->ElectricityPostcodeDistributor->create();
                    $this->ElectricityPostcodeDistributor->save($postcode_distributor);
                }
                echo 'Distributors have been imported.';
                break;
            case 'electricity_postcode_distributor2':
                $this->Plan->query('TRUNCATE TABLE electricity_postcode_distributors2;');
                $importer = new CSVImporter("files/Postcode _ Distributor Mapping - Public Compare - Elec.csv", true, ',');
                $rows = $importer->get();
                foreach ($rows as $row) {
                    $postcode_distributor = array('ElectricityPostcodeDistributor2' => array(
                        'state' => trim($row['State']),
                        'postcode' => trim($row['Postcode']),
                        'suburb' => ucwords(strtolower($row['Suburb'])),
                        'agl_distributor' => trim($row['AGL Distributor']),
                        'powerdirect_distributor' => trim($row['Powerdirect Distributor']),
                        'origin_energy_distributor' => trim($row['Origin Energy Distributor']),
                        'origin_energy_special_product_name' => trim($row['Origin Energy Special Product Name']),
                        'origin_energy_origin_saver_patch' => trim($row['Origin Energy Origin Saver Patch']),
                        'origin_energy_businesssaver_hv' => trim($row['Origin Energy BusinessSaver HV (Patch Offer)']),
                        'lumo_energy_distributor' => trim($row['Lumo Energy Distributor']),
                        'momentum_distributor' => trim($row['Momentum Distributor']),
                        'powershop_distributor' => trim($row['Powershop Distributor']),
                        'alinta_energy_distributor' => trim($row['Alinta Energy Distributor']),
                        'energy_australia_distributor' => trim($row['Energy Australia Distributor']),
                        'sumo_power_distributor' => trim($row['Sumo Power Distributor']),
                        'erm_distributor' => trim($row['ERM Distributor']),
                        'pd_agl_distributor' => trim($row['Powerdirect and AGL Distributor']),
                        'next_business_energy_distributor' => trim($row['Next Business Energy Distributor']),
                        'actewagl_distributor' => trim($row['ActewAGL Distributor']),
                        'elysian_energy_distributor' => trim($row['Elysian Energy Distributor']),
                        'testing_retailer_distributor' => trim($row['Testing Retailer Distributor']),
                        'tango_energy_distributor' => trim($row['Tango Energy Distributor']),
                        'red_energy_distributor' => trim($row['Red Energy Distributor']),
                        'ovo_energy_distributor' => trim($row['OVO Energy Distributor']),
                        'master_distributor' => trim($row['Master Distributor']),
                        'climate_zone' => trim($row['Climate Zone']),
                    ));
                    $this->ElectricityPostcodeDistributor2->create();
                    $this->ElectricityPostcodeDistributor2->save($postcode_distributor);
                }
                echo 'Distributors have been imported.';
                break;
            case 'gas_postcode_distributor':
                $this->Plan->query('TRUNCATE TABLE gas_postcode_distributors;');
                $importer = new CSVImporter("files/Postcode _ Distributor Mapping - GAS.csv", true, ',');
                $rows = $importer->get();
                foreach ($rows as $row) {
                    $postcode_distributor = array('GasPostcodeDistributor' => array(
                        'state' => trim($row['State']),
                        'postcode' => trim($row['Postcode']),
                        'suburb' => ucwords(strtolower($row['Suburb'])),
                        'agl_distributor' => trim($row['AGL Distributor']),
                        'origin_energy_distributor' => trim($row['Origin Energy Distributor']),
                        'origin_energy_origin_saver_patch' => trim($row['Origin Energy Origin Saver Patch']),
                        'lumo_energy_distributor' => trim($row['Lumo Energy Distributor']),
                        'momentum_distributor' => trim($row['Momentum Distributor']),
                        'powershop_distributor' => trim($row['Powershop Distributor']),
                        'alinta_energy_distributor' => trim($row['Alinta Energy Distributor']),
                        'energy_australia_distributor' => trim($row['Energy Australia Distributor']),
                        'sumo_power_distributor' => trim($row['Sumo Power Distributor']),
                        'pd_agl_distributor' => trim($row['Powerdirect and AGL Distributor']),
                        'actewagl_distributor' => trim($row['ActewAGL Distributor']),
                        'elysian_energy_distributor' => trim($row['Elysian Energy Distributor']),
                        'testing_retailer_distributor' => trim($row['Testing Retailer Distributor']),
                        'tango_energy_distributor' => trim($row['Tango Energy Distributor']),
                        'red_energy_distributor' => trim($row['Red Energy Distributor']),
                        'master_distributor' => trim($row['Master Distributor']),
                    ));
                    $this->GasPostcodeDistributor->create();
                    $this->GasPostcodeDistributor->save($postcode_distributor);
                }
                echo 'Distributors have been imported.';
                break;
            case 'gas_postcode_distributor2':
                $this->Plan->query('TRUNCATE TABLE gas_postcode_distributors2;');
                $importer = new CSVImporter("files/Postcode _ Distributor Mapping - Public Compare - Gas.csv", true, ',');
                $rows = $importer->get();
                foreach ($rows as $row) {
                    $postcode_distributor = array('GasPostcodeDistributor2' => array(
                        'state' => trim($row['State']),
                        'postcode' => trim($row['Postcode']),
                        'suburb' => ucwords(strtolower($row['Suburb'])),
                        'agl_distributor' => trim($row['AGL Distributor']),
                        'origin_energy_distributor' => trim($row['Origin Energy Distributor']),
                        'origin_energy_origin_saver_patch' => trim($row['Origin Energy Origin Saver Patch']),
                        'lumo_energy_distributor' => trim($row['Lumo Energy Distributor']),
                        'momentum_distributor' => trim($row['Momentum Distributor']),
                        'powershop_distributor' => trim($row['Powershop Distributor']),
                        'alinta_energy_distributor' => trim($row['Alinta Energy Distributor']),
                        'energy_australia_distributor' => trim($row['Energy Australia Distributor']),
                        'sumo_power_distributor' => trim($row['Sumo Power Distributor']),
                        'pd_agl_distributor' => trim($row['Powerdirect and AGL Distributor']),
                        'actewagl_distributor' => trim($row['ActewAGL Distributor']),
                        'elysian_energy_distributor' => trim($row['Elysian Energy Distributor']),
                        'testing_retailer_distributor' => trim($row['Testing Retailer Distributor']),
                        'tango_energy_distributor' => trim($row['Tango Energy Distributor']),
                        'red_energy_distributor' => trim($row['Red Energy Distributor']),
                        'master_distributor' => trim($row['Master Distributor']),
                    ));
                    $this->GasPostcodeDistributor2->create();
                    $this->GasPostcodeDistributor2->save($postcode_distributor);
                }
                echo 'Distributors have been imported.';
                break;
            case 'electricity_nmi_distributor':
                $this->Plan->query('TRUNCATE TABLE electricity_nmi_distributors;');
                $importer = new CSVImporter("files/Tariff Mapping - NMI _ Distributor.csv", true, ',');
                $rows = $importer->get();
                foreach ($rows as $row) {
                    if (strpos($row['NMI'], '/') !== false) {
                        $nmi_arr = explode('/', $row['NMI']);
                        foreach ($nmi_arr as $nmi) {
                            $nmi_distributor = array('ElectricityNmiDistributor' => array(
                                'nmi' => $nmi,
                                'distributor' => $row['Distributor'],
                            ));
                            $this->ElectricityNmiDistributor->create();
                            $this->ElectricityNmiDistributor->save($nmi_distributor);
                        }
                    } else {
                        $nmi_distributor = array('ElectricityNmiDistributor' => array(
                            'nmi' => $row['NMI'],
                            'distributor' => $row['Distributor'],
                        ));
                        $this->ElectricityNmiDistributor->create();
                        $this->ElectricityNmiDistributor->save($nmi_distributor);
                    }

                }
                echo 'Distributors have been imported.';
                break;
            case 'gas_conversion':
                $this->Plan->query('TRUNCATE TABLE gas_conversion;');
                $importer = new CSVImporter("files/Gas Conversion - Sheet1.csv", true, ',');
                $rows = $importer->get();
                foreach ($rows as $row) {
                    $gas_conversion = array('GasConversion' => array(
                        'state' => $row['State'],
                        'official_network_designation' => $row['Official Network Designation'],
                        'zone' => $row['Zone'],
                        'mirn' => $row['MIRN Prefix'],
                    ));
                    $this->GasConversion->create();
                    $this->GasConversion->save($gas_conversion);
                }
                echo 'Gas Conversion have been imported.';
                break;
            case 'consumptions':
                $this->Plan->query('TRUNCATE TABLE consumptions;');
                $importer = new CSVImporter("files/Consumption - Consumption Data.csv", true, ',');
                $rows = $importer->get();
                foreach ($rows as $row) {
                    $consumption = array('Consumption' => array(
                        'state' => $row['State'],
                        'res_sme' => $row['RES/SME'],
                        'elec_peak' => $row['Elec Peak'],
                        'elec_cl1' => $row['Elec Controlled Load 1'],
                        'elec_cl2' => $row['Elec Controlled Load 2'],
                        'elec_shoulder' => $row['Elec Shoulder'],
                        'elec_offpeak' => $row['Elec Off Peak'],
                        'elec_cs' => $row['Elec Climate Saver'],
                        'elec_cs_billing_start' => $row['Elec Climate Saver Start Date'],
                        'elec_billing_days' => $row['Elec Billing Days'],
                        'gas_peak' => $row['Gas Peak'],
                        'gas_billing_days' => $row['Gas Billing Days'],
                        'gas_billing_start' => $row['Gas Start Date'],
                    ));
                    $this->Consumption->create();
                    $this->Consumption->save($consumption);
                }
                echo 'Consumptions have been imported.';
                break;
            case 'solar_rebate_schemes':
                $this->Plan->query('TRUNCATE TABLE solar_rebate_schemes;');
                $importer = new CSVImporter("files/Tariff Mapping - Solar Rebate Scheme Mapping.csv", true, ',');
                $rows = $importer->get();
                foreach ($rows as $row) {
                    $solar_rebate_scheme = array('SolarRebateScheme' => array(
                        'state' => $row['State'],
                        'scheme' => $row['Scheme'],
                        'government' => $row['Government'],
                        'agl' => $row['AGL'],
                        'lumo_energy' => $row['Lumo Energy'],
                        'momentum' => $row['Momentum'],
                        'origin_energy' => $row['Origin Energy'],
                        'powerdirect' => $row['Powerdirect'],
                        'powershop' => $row['Powershop'],
                        'sumo_power' => $row['Sumo Power'],
                        'alinta_energy' => $row['Alinta Energy'],
                        'erm' => $row['ERM'],
                        'pd_agl' => $row['Powerdirect and AGL'],
                        'energy_australia' => $row['Energy Australia'],
                        'next_business_energy' => $row['Next Business Energy'],
                        'actewagl' => trim($row['ActewAGL']),
                        'elysian_energy' => trim($row['Elysian Energy']),
                        'testing_retailer' => trim($row['Testing Retailer']),
                        'tango_energy' => trim($row['Tango Energy']),
                        'red_energy' => trim($row['Red Energy']),
                        'ovo_energy' => trim($row['OVO Energy']),
                    ));
                    $this->SolarRebateScheme->create();
                    $this->SolarRebateScheme->save($solar_rebate_scheme);
                }
                echo 'Solar Rebate Schemes have been imported.';
                break;
            case 'holidays':
                $this->Plan->query('TRUNCATE TABLE holidays;');
                $importer = new CSVImporter("files/Move Ins - Public Holidays.csv", true, ',');
                $rows = $importer->get();
                foreach ($rows as $row) {
                    $holiday = array('Holiday' => array(
                        'state' => $row['State'],
                        'holiday' => $row['Holiday'],
                        'holiday_date' => $row['2014'],
                    ));
                    $this->Holiday->create();
                    $this->Holiday->save($holiday);
                    $holiday = array('Holiday' => array(
                        'state' => $row['State'],
                        'holiday' => $row['Holiday'],
                        'holiday_date' => $row['2015'],
                    ));
                    $this->Holiday->create();
                    $this->Holiday->save($holiday);
                }
                echo 'Holidays have been imported.';
                break;
            case 'street_types':
                $this->Plan->query('TRUNCATE TABLE street_types;');
                $importer = new CSVImporter("files/Field _ Velocify Mapping - Street Type Values.csv", true, ',');
                $rows = $importer->get();
                foreach ($rows as $row) {
                    $street_type = array('StreetType' => array(
                        'name' => $row['Name'],
                    ));
                    $this->StreetType->create();
                    $this->StreetType->save($street_type);
                }
                echo 'Street Types have been imported.';
                break;
            case 'products':
                $this->Plan->query('TRUNCATE TABLE products;');
                $importer = new CSVImporter("files/Field _ Velocify Mapping - Products.csv", true, ',');
                $rows = $importer->get();
                foreach ($rows as $row) {
                    $product = array('Product' => array(
                        'fuel' => trim($row['Fuel']),
                        'state' => trim($row['State']),
                        'retailer' => trim($row['Retailer']),
                        'res_sme' => trim($row['RES/SME']),
                        'product_name' => trim($row['Product Name']),
                        'field_value' => trim($row['Field Value']),
                    ));
                    $this->Product->create();
                    $this->Product->save($product);
                }
                echo 'Products have been imported.';
                break;
            case 'sales':
                $this->Plan->query('TRUNCATE TABLE sales;');
                $importer = new CSVImporter("files/Sales - Sheet1.csv", true, ',');
                $rows = $importer->get();
                foreach ($rows as $row) {
                    $sale = array('Sale' => array(
                        'agent_id' => $row['ID'],
                        'name' => $row['First Name'] . ' ' . $row['Last Name'],
                        'email' => $row['Email'],
                    ));
                    $this->Sale->create();
                    $this->Sale->save($sale);
                }
                echo 'Sales have been imported.';
                break;
            case 'pdfs':
                $this->Plan->query('TRUNCATE TABLE pdfs;');
                $importer = new CSVImporter("files/PDFs - Sheet1.csv", true, ',');
                $rows = $importer->get();
                foreach ($rows as $row) {
                    $pdf = array('Pdf' => array(
                        'state' => trim($row['State']),
                        'retailer' => trim($row['Retailer']),
                        'res_sme' => trim($row['RES/SME']),
                        'filename' => trim($row['Filename']),
                    ));
                    $this->Pdf->create();
                    $this->Pdf->save($pdf);
                }
                echo 'PDFs have been imported.';
                break;
            case 'lead_types':
                $this->Plan->query('TRUNCATE TABLE lead_types;');
                $importer = new CSVImporter("files/POST LeadType & Status - Sheet1.csv", true, ',');
                $rows = $importer->get();
                foreach ($rows as $row) {
                    $lead_type = array('LeadType' => array(
                        'retailer' => trim($row['Retailer']),
                        'res_sme' => trim($row['RES/SME']),
                        'looking_for' => trim($row['Transfer/Move In']),
                        'lead_type' => trim($row['LeadType']),
                        'lead_status' => trim($row['Status']),
                    ));
                    $this->LeadType->create();
                    $this->LeadType->save($lead_type);
                }
                echo 'Lead Types have been imported.';
                break;
            case 'retailer_commissions':
                $this->Plan->query('TRUNCATE TABLE retailer_commissions;');
                $importer = new CSVImporter("files/Metrixa Mapping - Simple Cost Mapping.csv", true, ',');
                $rows = $importer->get();
                foreach ($rows as $row) {
                    $retailer_commission = array('RetailerCommission' => array(
                        'nmi_acq_ret' => trim($row['NMI Acq/Ret']),
                        'mirn_acq_ret' => trim($row['MIRN Acq/Ret']),
                        'res_sme' => trim($row['Type']),
                        'package' => trim($row['Fuel']),
                        'cost' => trim($row['Cost']),
                    ));
                    $this->RetailerCommission->create();
                    $this->RetailerCommission->save($retailer_commission);
                }
                echo 'Retailer Commissions have been imported.';
                break;
            case 'terms_conditions':
                $this->Plan->query('TRUNCATE TABLE terms_conditions;');
                $importer = new CSVImporter("files/Terms and Conditions - Terms and Conditions.csv", true, ',');
                $rows = $importer->get();
                foreach ($rows as $row) {
                    $retailer_commission = array('TermCondition' => array(
                        'retailer' => trim($row['Retailer']),
                        'state' => trim($row['State']),
                        'res_sme' => trim($row['RES/SME']),
                        'fuel' => trim($row['Fuel']),
                        'plan' => trim($row['Plan']),
                        'term' => trim($row['Term']),
                        'group' => trim($row['Group']),
                        'sort' => trim($row['Sort']),
                    ));
                    $this->TermCondition->create();
                    $this->TermCondition->save($retailer_commission);
                }
                echo 'Terms and conditions have been imported.';
                break;
            case 'electricity_bpid':
                $this->Plan->query('TRUNCATE TABLE electricity_bpid;');
                $importer = new CSVImporter("files/BPID_Retailer_Mapping - BPID Elec.csv", true, ',');
                $rows = $importer->get();
                foreach ($rows as $row) {
                    $bpid = array('ElectricityBpid' => array(
                        'res_sme' => trim($row['RES/SME']),
                        'state' => trim($row['State']),
                        'distributor' => trim($row['Distributor']),
                        'tariff_type' => trim($row['Tariff Type']),
                        'tariff_class' => trim($row['Tariff Class']),
                        'retailer' => trim($row['Retailer']),
                        'eme_id' => trim($row['EME ID']),
                        'plan' => trim($row['Plan']),
                        'climate_zone' => trim($row['Climate Zone']),
                        'bpid_link' => trim($row['BPID Link']),
                        'start_date' => ($row['Start Date']) ? date('Y-m-d', strtotime(str_replace('/', '-', $row['Start Date']))) : '0000-00-00',
                        'expiry_date' => ($row['Expiry Date']) ? date('Y-m-d', strtotime(str_replace('/', '-', $row['Expiry Date']))) : '0000-00-00',
                    ));
                    $this->ElectricityBpid->create();
                    $this->ElectricityBpid->save($bpid);
                }
                echo 'Electricity BPID have been imported.';
                break;
            case 'gas_bpid':
                $this->Plan->query('TRUNCATE TABLE gas_bpid;');
                $importer = new CSVImporter("files/BPID_Retailer_Mapping - BPID Gas.csv", true, ',');
                $rows = $importer->get();
                foreach ($rows as $row) {
                    $bpid = array('GasBpid' => array(
                        'res_sme' => trim($row['RES/SME']),
                        'state' => trim($row['State']),
                        'distributor' => trim($row['Distributor']),
                        'retailer' => trim($row['Retailer']),
                        'eme_id' => trim($row['EME ID']),
                        'plan' => trim($row['Plan']),
                        'bpid_link' => trim($row['BPID Link']),
                        'start_date' => ($row['Start Date']) ? date('Y-m-d', strtotime(str_replace('/', '-', $row['Start Date']))) : '0000-00-00',
                        'expiry_date' => ($row['Expiry Date']) ? date('Y-m-d', strtotime(str_replace('/', '-', $row['Expiry Date']))) : '0000-00-00',
                    ));
                    $this->GasBpid->create();
                    $this->GasBpid->save($bpid);
                }
                echo 'Gas BPID have been imported.';
                break;
            case 'offshore_lead_exceptions':
                $this->Plan->query('TRUNCATE TABLE offshore_lead_exceptions;');
                $importer = new CSVImporter("files/Offshore Lead Exceptions - Sheet1.csv", true, ',');
                $rows = $importer->get();
                foreach ($rows as $row) {
                    $offshore_lead_exceptions = array('OffshoreLeadExceptions' => array(
                        'centre_name' => trim($row['Centre Name']),
                        'agl' => trim($row['AGL']),
                        'powerdirect' => trim($row['Powerdirect']),
                        'origin_energy' => trim($row['Origin Energy']),
                        'alinta_energy' => trim($row['Alinta Energy']),
                        'sumo_power' => trim($row['Sumo Power']),
                        'lumo_energy' => trim($row['Lumo Energy']),
                        'momentum' => trim($row['Momentum']),
                        'next_business_energy' => trim($row['Next Business Energy']),
                        'powershop' => trim($row['Powershop']),
                        'actewagl' => trim($row['ActewAGL']),
                        'elysian_energy' => trim($row['Elysian Energy']),
                        'testing_retailer' => trim($row['Testing Retailer']),
                        'tango_energy' => trim($row['Tango Energy']),
                        'red_energy' => trim($row['Red Energy']),
                        'ovo_energy' => trim($row['OVO Energy']),
                    ));
                    $this->OffshoreLeadExceptions->create();
                    $this->OffshoreLeadExceptions->save($offshore_lead_exceptions);
                }
                echo 'Offshore Lead Exceptions have been imported.';
                break;
            case 'dmo_vdo':
                $this->Plan->query('TRUNCATE TABLE dmo_vdo;');
                $importer = new CSVImporter("files/Rates - DMO_VDO-Web.csv", true, ',');
                $rows = $importer->get();
                foreach ($rows as $row) {
                    $dmo_vdo = array('DmoVdo' => array(
                        'state' => trim($row['State']),
                        'package' => trim($row['Elec/Gas']),
                        'res_sme' => trim($row['Customer Type']),
                        'retailer' => trim($row['Retailer']),
                        'distributor' => trim($row['DB']),
                        'tariff_type' => trim($row['Tariff Type']),
                        'tariff_class' => trim($row['Tariff Class']),
                        'plan' => trim($row['Plan']),
                        'cl' => trim($row['CL Y/N']),
                        'conditional' => trim($row['Conditional']),
                        'default_offer_kwh' => trim($row['Default Offer KWH']),
                        'default_offer_cost' => trim($row['Default Offer Cost']),
                        'default_retailer_cost' => trim($row['Default Retailer Cost']),
                        'default_offer_difference' => trim($row['Default Offer Difference $']),
                        'default_difference_type' => trim($row['Default $ Difference Less/Equal/More']),
                        'default_offer_difference_percent' => trim($row['Default Offer Difference %']),
                        'default_offer_difference_percent_type' => trim($row['Default % Difference Less/Equal/More']),
                        'conditional_difference' => trim($row['Conditional Difference $']),
                        'conditional_difference_percent' => trim($row['Conditional Difference %']),
                        'dmo' => trim($row['DMO']),
                        'version' => ($row['Version']) ? trim($row['Version']) : 'All',
                        'start_date' => ($row['Start Date']) ? date('Y-m-d', strtotime(str_replace('/', '-', $row['Start Date']))) : '0000-00-00',
                        'expiry_date' => ($row['Expiry Date']) ? date('Y-m-d', strtotime(str_replace('/', '-', $row['Expiry Date']))) : '0000-00-00',
                    ));
                    $this->DmoVdo->create();
                    $this->DmoVdo->save($dmo_vdo);
                }
                echo 'DMO VDO have been imported.';
                break;
            case 'move_in_info':
                $this->Plan->query('TRUNCATE TABLE move_in_info;');
                $importer = new CSVImporter("files/New Connection Fees - MoveIn.csv", true, ',');
                $rows = $importer->get();
                foreach ($rows as $row) {
                    if (strpos($row['NMI / MIRN'], '/') !== false) {
                        $nmi_mirn_arr = explode('/', $row['NMI / MIRN']);
                        foreach ($nmi_mirn_arr as $nmi_mirn) {
                            $move_in_info = array('MoveInInfo' => array(
                                'state' => trim($row['State']),
                                'package' => trim($row['Fuel']),
                                'retailer' => trim($row['Retailer']),
                                'nmi_mirn' => trim($nmi_mirn),
                                'distributor' => trim($row['Distributor']),
                                'normal _next_avail_date' => trim($row['Normal - Next Avail. Date']),
                                'normal_inc_gst' => trim($row['Normal (inc. GST)']),
                                'remote' => trim($row['Remote']),
                                'sdfi_next_avail_date' => trim($row['SDFI - Next Avail. Date']),
                                'sdfi_inc_gst' => trim($row['SDFI (inc. GST)']),
                                'notes' => trim($row['Notes']),
                                'sdfi_cutofftime' => trim($row['SDFI CutOffTime']),
                                'sdfi_next_available_date' => trim($row['SDFI Next Available Date']),
                                'normal_cutofftime_mon_thu' => trim($row['Normal CutOffTime (Mon-Thu)']),
                                'normal_cutofftime_fri' => trim($row['Normal CutOffTime (Fri)']),
                                'business_days' => trim($row['Business Days']),
                                'adjusted_business_days' => trim($row['Adjusted Business Days']),
                                'normal_next_available_date' => trim($row['Normal Next Available Date']),
                            ));
                            $this->MoveInInfo->create();
                            $this->MoveInInfo->save($move_in_info);
                        }
                    } else {
                        $move_in_info = array('MoveInInfo' => array(
                            'state' => trim($row['State']),
                            'package' => trim($row['Fuel']),
                            'retailer' => trim($row['Retailer']),
                            'nmi_mirn' => trim($row['NMI / MIRN']),
                            'distributor' => trim($row['Distributor']),
                            'normal _next_avail_date' => trim($row['Normal - Next Avail. Date']),
                            'normal_inc_gst' => trim($row['Normal (inc. GST)']),
                            'remote' => trim($row['Remote']),
                            'sdfi_next_avail_date' => trim($row['SDFI - Next Avail. Date']),
                            'sdfi_inc_gst' => trim($row['SDFI (inc. GST)']),
                            'notes' => trim($row['Notes']),
                            'sdfi_cutofftime' => trim($row['SDFI CutOffTime']),
                            'sdfi_next_available_date' => trim($row['SDFI Next Available Date']),
                            'normal_cutofftime_mon_thu' => trim($row['Normal CutOffTime (Mon-Thu)']),
                            'normal_cutofftime_fri' => trim($row['Normal CutOffTime (Fri)']),
                            'business_days' => trim($row['Business Days']),
                            'adjusted_business_days' => trim($row['Adjusted Business Days']),
                            'normal_next_available_date' => trim($row['Normal Next Available Date']),
                        ));
                        $this->MoveInInfo->create();
                        $this->MoveInInfo->save($move_in_info);
                    }
                }
                echo 'Move In Info have been imported.';
                break;
            case 'electrician_name':
                $this->Plan->query('TRUNCATE TABLE electrician_name;');
                $importer = new CSVImporter("files/Electrician Name - Sheet1.csv", true, ',');
                $rows = $importer->get();
                foreach ($rows as $row) {
                    $electrician_name = array('ElectricianName' => array(
                        'name' => trim($row['Name']),
                    ));
                    $this->ElectricianName->create();
                    $this->ElectricianName->save($electrician_name);
                }
                echo 'Electrician Names have been imported.';
                break;
            case 'origin_lpg':
                $this->Plan->query('TRUNCATE TABLE origin_lpg;');
                $importer = new CSVImporter("files/Origin LPG LOOKUP - Sheet1.csv", true, ',');
                $rows = $importer->get();
                foreach ($rows as $row) {
                    $origin_lpg = array('OriginLpg' => array(
                        'suburb' => trim($row['TOWN/SUBURB']),
                        'state' => trim($row['STATE']),
                        'postcode' => trim($row['POSTCODE']),
                        'in_situ' => trim($row['IN SITU']),
                        'exchange' => trim($row['EXCHANGE']),
                        'exchange_lead_type' => trim($row['Exchange Lead Type / DELIVERY TYPE']),
                        'unique_string' => trim($row['vlookup Unique String']),
                    ));
                    $this->OriginLpg->create();
                    $this->OriginLpg->save($origin_lpg);
                }
                echo 'Origin LPG have been imported.';
                break;
        }
        $this->autoRender = false;
        $this->layout = false;
        $this->render(false);
    }

    public function postcode_to_suburb()
    {
        $states_arr = unserialize(AU_STATES_ABBREVS);
        if (isset($this->request->query) && !empty($this->request->query)) {
            $return = array();
            $term = $this->request->query['term'];
            $callback = $this->request->query['callback'];
            $locations = $this->ElectricityPostcodeDistributor->find('all', array(
                'conditions' => array('ElectricityPostcodeDistributor.postcode LIKE' => $term . '%'),
                'order' => array('ElectricityPostcodeDistributor.postcode ASC', 'ElectricityPostcodeDistributor.suburb ASC')
            ));
            if (!empty($locations)) {
                foreach ($locations as $location) {
                    if (!in_array($location['ElectricityPostcodeDistributor']['state'], $states_arr)) {
                        continue;
                    }
                    $return['items'][] = array(
                        'postcode' => $location['ElectricityPostcodeDistributor']['postcode'],
                        'suburb' => $location['ElectricityPostcodeDistributor']['suburb'],
                        'state' => $location['ElectricityPostcodeDistributor']['state'],
                    );
                }
            }
            return new CakeResponse(array(
                'body' => $callback . "(" . json_encode($return) . ");",
                'type' => 'json',
                'status' => '201'
            ));
        }
    }

    public function suburb_options()
    {
        $states_arr = unserialize(AU_STATES_ABBREVS);
        if (isset($this->request->query) && !empty($this->request->query)) {
            $return = array();
            $postcode = $this->request->query['postcode'];
            $locations = $this->ElectricityPostcodeDistributor->find('all', array(
                'conditions' => array('ElectricityPostcodeDistributor.postcode' => $postcode),
                'order' => array('ElectricityPostcodeDistributor.postcode ASC', 'ElectricityPostcodeDistributor.suburb ASC')
            ));
            if (!empty($locations)) {
                foreach ($locations as $location) {
                    if (!in_array($location['ElectricityPostcodeDistributor']['state'], $states_arr)) {
                        continue;
                    }
                    $return[] = array(
                        'postcode' => $location['ElectricityPostcodeDistributor']['postcode'],
                        'suburb' => $location['ElectricityPostcodeDistributor']['suburb'],
                        'state' => $location['ElectricityPostcodeDistributor']['state'],
                        'selected' => ($this->Session->read('User.suburb') == $location['ElectricityPostcodeDistributor']['suburb']) ? 1 : 0,
                    );
                }
            }

            return new CakeResponse(array(
                'body' => json_encode($return),
                'type' => 'json',
                'status' => '201'
            ));
        }
        exit;
    }

    public function get_state_by_suburb()
    {
        if (isset($this->request->query) && !empty($this->request->query)) {
            $return = array();
            $suburb = $this->request->query['suburb'];
            $postcode = $this->request->query['postcode'];
            $locations = $this->ElectricityPostcodeDistributor->find('first', array(
                'conditions' => array(
                    'ElectricityPostcodeDistributor.suburb' => $suburb,
                    'ElectricityPostcodeDistributor.postcode' => $postcode,
                ),
            ));

            if (!empty($locations)) {
                $return['state'] = $locations['ElectricityPostcodeDistributor']['state'];
            }
            return new CakeResponse(array(
                'body' => json_encode($return),
                'type' => 'json',
                'status' => '201'
            ));
        }
        exit;
    }

    public function tariff_options()
    {
        if (isset($this->request->query) && !empty($this->request->query)) {
            $step1 = $this->Session->read('User.step1');
            $return = array();
            $state = (isset($this->request->query['state'])) ? $this->request->query['state'] : $this->Session->read('User.state');
            $customer_type = (isset($this->request->query['customer_type'])) ? $this->request->query['customer_type'] : $step1['customer_type'];
            $nmi = $this->request->query['nmi'];
            $field = $this->request->query['field'];
            $distributor_nmi = $this->ElectricityNmiDistributor->findByNmi(strtoupper(substr($nmi, 0, 2)));
            $distributors = explode('/', $distributor_nmi['ElectricityNmiDistributor']['distributor']);
            $tariffs = $this->Tariff->find('all', array(
                'conditions' => array(
                    'Tariff.distributor' => $distributors,
                    'Tariff.res_sme' => $customer_type,
                    'Tariff.state' => $state,
                    //'Tariff.internal_tariff !=' => 'DMD'
                ),
                'order' => array('Tariff.id ASC')
            ));
            if (!empty($tariffs)) {
                foreach ($tariffs as $tariff) {
                    $tariff_type = ($tariff['Tariff']['tariff_type'] == 'Solar') ? 'Solar' : 'Market';
                    $child_tariff = ($tariff['Tariff']['child_tariff'] != '') ? 1 : 0;
                    $tariff_value = $tariff['Tariff']['tariff_code'] . '|' . $tariff['Tariff']['pricing_group'] . '|' . $child_tariff . '|' . $tariff_type . '|' . $tariff['Tariff']['solar_rebate_scheme'];
                    $return[] = array(
                        'tariff_code' => $tariff['Tariff']['tariff_code'],
                        'pricing_group' => trim($tariff['Tariff']['pricing_group']),
                        'tariff_type' => $tariff_type,
                        'child_tariff' => $child_tariff,
                        'solar_rebate_scheme' => $tariff['Tariff']['solar_rebate_scheme'],
                        'selected' => (isset($step1[$field]) && $step1[$field] == $tariff_value) ? 1 : 0,
                        'distributor' => $distributors[0],
                    );
                }
            }

            return new CakeResponse(array(
                'body' => json_encode($return),
                'type' => 'json',
                'status' => '201'
            ));
        }
        exit;
    }

    public function tariff_options2()
    {
        if (isset($this->request->query) && !empty($this->request->query)) {
            $step1 = $this->Session->read('User.step1');
            $return = array();
            $state = (isset($this->request->query['state'])) ? $this->request->query['state'] : $this->Session->read('User.state');
            $customer_type = (isset($this->request->query['customer_type'])) ? $this->request->query['customer_type'] : $step1['customer_type'];
            $nmi = $this->request->query['nmi'];
            $field = $this->request->query['field'];
            $distributor_nmi = $this->ElectricityNmiDistributor->findByNmi(strtoupper(substr($nmi, 0, 2)));
            $distributors = explode('/', $distributor_nmi['ElectricityNmiDistributor']['distributor']);
            $tariffs = $this->Tariff->find('all', array(
                'conditions' => array(
                    'Tariff.distributor' => $distributors,
                    'Tariff.res_sme' => $customer_type,
                    'Tariff.state' => $state,
                    //'Tariff.internal_tariff !=' => 'DMD'
                ),
                'order' => array('Tariff.id ASC')
            ));
            if (!empty($tariffs)) {
                foreach ($tariffs as $tariff) {
                    $tariff_type = ($tariff['Tariff']['tariff_type'] == 'Solar') ? 'Solar' : 'Market';
                    $child_tariff = ($tariff['Tariff']['child_tariff'] != '') ? 1 : 0;
                    $tariff_value = $tariff['Tariff']['tariff_code'] . '|' . $tariff['Tariff']['pricing_group'] . '|' . $child_tariff . '|' . $tariff_type . '|' . $tariff['Tariff']['solar_rebate_scheme'];
                    $return[] = array(
                        'tariff_code' => $tariff['Tariff']['tariff_code'],
                        'pricing_group' => trim($tariff['Tariff']['pricing_group']),
                        //'pricing_group' => 'Transitional Time of Use + CL1',
                        'tariff_type' => $tariff_type,
                        'child_tariff' => $child_tariff,
                        'solar_rebate_scheme' => $tariff['Tariff']['solar_rebate_scheme'],
                        'selected' => (isset($step1[$field]) && $step1[$field] == $tariff_value) ? 1 : 0,
                        'distributor' => $distributors[0],
                    );
                }
            }

            return new CakeResponse(array(
                'body' => json_encode($return),
                'type' => 'json',
                'status' => '201'
            ));
        }
        exit;
    }

    public function get_usage_level()
    {
        if ($this->request->is('put') || $this->request->is('post')) {
            $this->autoRender = false;
            $view = new View($this, false);
            $view->layout = 'ajax';
            $step1 = $this->Session->read('User.step1');
            $plan_type = $this->request->data['plan_type'];
            $customer_type = $this->request->data['customer_type'];
            $version = (isset($this->request->data['version']) && $this->request->data['version']) ? $this->request->data['version'] : 4;
            $view->set(compact('step1', 'customer_type'));
            if ($plan_type == 'Elec') {
                $view_output = $view->render("/Elements/elec_usage_level_fields_v{$version}");
            } elseif ($plan_type == 'Gas') {
                $view_output = $view->render("/Elements/gas_usage_level_fields_v{$version}");
            }

            return new CakeResponse(array(
                'body' => json_encode(array(
                    'html' => $view_output,
                )),
                'type' => 'json',
                'status' => '201'
            ));
        }
        exit;
    }

    public function street_type()
    {
        if (isset($this->request->query) && !empty($this->request->query)) {
            $return = array();
            $term = $this->request->query['term'];
            $callback = $this->request->query['callback'];
            $street_types = $this->StreetType->find('all', array(
                'conditions' => array('StreetType.name LIKE' => $term . '%'),
                'order' => array('StreetType.name ASC')
            ));
            if (!empty($street_types)) {
                foreach ($street_types as $street_type) {
                    $return['items'][] = array(
                        'name' => $street_type['StreetType']['name'],
                    );
                }
            }

            return new CakeResponse(array(
                'body' => $callback . "(" . json_encode($return) . ");",
                'type' => 'json',
                'status' => '201'
            ));
        }
        exit;
    }

    public function sales_rep()
    {
        if (isset($this->request->query) && !empty($this->request->query)) {
            $return = array();
            $term = $this->request->query['term'];
            $callback = $this->request->query['callback'];
            $sales = $this->User->find('all', array(
                'conditions' => array('User.name LIKE' => $term . '%'),
                'order' => array('User.name ASC')
            ));
            if (!empty($sales)) {
                foreach ($sales as $sale) {
                    $return['items'][] = array(
                        'id' => $sale['User']['agent_id'],
                        'name' => $sale['User']['name'],
                        'email' => $sale['User']['email'],
                    );
                }
            }

            return new CakeResponse(array(
                'body' => $callback . "(" . json_encode($return) . ");",
                'type' => 'json',
                'status' => '201'
            ));
        }
        exit;
    }

    public function electrician_name()
    {
        if (isset($this->request->query) && !empty($this->request->query)) {
            $return = array();
            $term = $this->request->query['term'];
            $callback = $this->request->query['callback'];
            $electrician_names = $this->ElectricianName->find('all', array(
                'conditions' => array('ElectricianName.name LIKE' => $term . '%'),
                'order' => array('ElectricianName.name ASC')
            ));
            if (!empty($electrician_names)) {
                foreach ($electrician_names as $electrician_name) {
                    $return['items'][] = array(
                        'id' => $electrician_name['ElectricianName']['id'],
                        'name' => $electrician_name['ElectricianName']['name'],
                    );
                }
            }

            return new CakeResponse(array(
                'body' => $callback . "(" . json_encode($return) . ");",
                'type' => 'json',
                'status' => '201'
            ));
        }
        exit;
    }

    public function get_lead_fields()
    {
        $lead = array();
        if ($this->request->is('post') || $this->request->is('put')) {
            $id = $this->request->data['lead_id'];

            $app_key = $this->request->data['app_key'];
            if ($app_key != APP_KEY || !is_numeric($id)) {
                return new CakeResponse(array(
                    'body' => '',
                    'type' => 'json',
                    'status' => '201'
                ));
            }

            $response = $this->get_lead($id);
            if (strpos($response, '<Leads>') !== false) {
                $lead['id'] = $id;
                $lead['last_name'] = '';
                $lead['email'] = '';
                $xml = simplexml_load_string($response);
                $lead_array = json_decode(json_encode($xml), true);
                $lead['campaign_id'] = $lead_array['Lead']['Campaign']['@attributes']['CampaignId'];
                $lead['campaign_name'] = $lead_array['Lead']['Campaign']['@attributes']['CampaignTitle'];
                $lead['first_campaign'] = '';
                $lead['status'] = $lead_array['Lead']['Status']['@attributes']['StatusTitle'];
                $lead['sale_completion_date'] = '';
                foreach ($lead_array['Lead']['Fields']['Field'] as $field) {
                    if ($field['@attributes']['FieldTitle'] == 'First Name') {
                        $lead['first_name'] = $field['@attributes']['Value'];
                    }
                    if ($field['@attributes']['FieldTitle'] == 'Last Name') {
                        $lead['last_name'] = $field['@attributes']['Value'];
                    }
                    if ($field['@attributes']['FieldTitle'] == 'Mobile Number') {
                        $lead['mobile'] = $field['@attributes']['Value'];
                    }
                    if ($field['@attributes']['FieldTitle'] == 'Home Phone') {
                        $lead['home_phone'] = $field['@attributes']['Value'];
                    }
                    if ($field['@attributes']['FieldTitle'] == 'Work Number') {
                        $lead['work_number'] = $field['@attributes']['Value'];
                    }
                    if ($field['@attributes']['FieldTitle'] == 'eMail') {
                        $lead['email'] = $field['@attributes']['Value'];
                    }
                    if ($field['@attributes']['FieldTitle'] == 'Sales Rep Name') {
                        $lead['sales_rep_name'] = $field['@attributes']['Value'];
                    }
                    if ($field['@attributes']['FieldTitle'] == 'Postcode (Supply)') {
                        $lead['postcode'] = $field['@attributes']['Value'];
                    }
                    if ($field['@attributes']['FieldTitle'] == 'State (Supply)') {
                        $lead['state'] = $field['@attributes']['Value'];
                    }
                    if ($field['@attributes']['FieldTitle'] == 'Suburb (Supply)') {
                        $lead['suburb'] = $field['@attributes']['Value'];
                    }
                    if ($field['@attributes']['FieldTitle'] == 'Fuel Type') {
                        $lead['plan_type'] = $field['@attributes']['Value'];
                    }
                    if ($field['@attributes']['FieldTitle'] == 'Customer Type') {
                        $lead['customer_type'] = $field['@attributes']['Value'];
                    }
                    if ($field['@attributes']['FieldTitle'] == 'MoveIn OR Transfer') {
                        $lead['looking_for'] = $field['@attributes']['Value'];
                    }
                    if ($field['@attributes']['FieldTitle'] == 'Business or Residential') {
                        $lead['business_or_residential'] = $field['@attributes']['Value'];
                    }
                    if ($field['@attributes']['FieldTitle'] == 'Unit (Supply)') {
                        $lead['unit'] = $field['@attributes']['Value'];
                    }
                    if ($field['@attributes']['FieldTitle'] == 'Street Number (Supply)') {
                        $lead['street_number'] = $field['@attributes']['Value'];
                    }
                    if ($field['@attributes']['FieldTitle'] == 'Street Name (Supply)') {
                        $lead['street_name'] = $field['@attributes']['Value'];
                    }
                    if ($field['@attributes']['FieldTitle'] == 'Street Type (Supply)') {
                        $lead['street_type'] = $field['@attributes']['Value'];
                    }
                    if ($field['@attributes']['FieldTitle'] == 'Sale Completion Date') {
                        if ($field['@attributes']['Value']) {
                            $lead['sale_completion_date'] = date('d/m/Y', strtotime($field['@attributes']['Value']));
                        }
                    }
                    if ($field['@attributes']['FieldTitle'] == 'First Campaign') {
                        $lead['first_campaign'] = $field['@attributes']['Value'];
                    }
                    if ($field['@attributes']['FieldTitle'] == 'Tenant / Owner') {
                        $lead['tenant_owner'] = $field['@attributes']['Value'];
                    }
                    if ($field['@attributes']['FieldTitle'] == 'Campaign Medium') {
                        $lead['campaign_medium'] = $field['@attributes']['Value'];
                    }
                    if ($field['@attributes']['FieldTitle'] == 'Campaign Keyword') {
                        $lead['campaign_keyword'] = $field['@attributes']['Value'];
                    }
                    if ($field['@attributes']['FieldTitle'] == 'Campaign Source') {
                        $lead['campaign_source'] = $field['@attributes']['Value'];
                    }
                    if ($field['@attributes']['FieldTitle'] == 'Centre Name') {
                        $lead['centre_name'] = $field['@attributes']['Value'];
                    }
                    if ($field['@attributes']['FieldTitle'] == 'Referrer Name') {
                        $lead['referring_agent'] = $field['@attributes']['Value'];
                    }
                    if ($field['@attributes']['FieldTitle'] == 'Solar Interest') {
                        $lead['solar_interest'] = $field['@attributes']['Value'];
                    }
                    if ($field['@attributes']['FieldTitle'] == 'Solar Appointment') {
                        $lead['solar_appointment'] = $field['@attributes']['Value'];
                    }
                    if ($field['@attributes']['FieldTitle'] == 'Solar Sale Confirmed') {
                        $lead['solar_sale_confirmed'] = $field['@attributes']['Value'];
                    }
                    if ($field['@attributes']['FieldTitle'] == 'Lead Origin') {
                        $lead['lead_origin'] = $field['@attributes']['Value'];
                    }
                }
            } else {
                $ip = $this->request->clientIp();
                $to = 'info@seanpro.com';
                $subject = 'Velocify API error - GetLead - Tools';
                $message = "Lead ID: {$id}\r\n";
                $message .= "IP: {$ip}\r\n";
                $message .= $response;
                $headers = 'From: api@seanpro.com' . "\r\n" .
                    'Reply-To: api@seanpro.com' . "\r\n" .
                    'X-Mailer: PHP/' . phpversion();

                mail($to, $subject, $message, $headers);
            }
        }

        return new CakeResponse(array(
            'body' => json_encode($lead),
            'type' => 'json',
            'status' => '201'
        ));
    }

    private function get_lead($lead_id = '')
    {
        $username = LEADS360_USERNAME;
        $password = LEADS360_PASSWORD;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->leads360_url_2."/ClientService.asmx/GetLead?username={$username}&password={$password}&leadId={$lead_id}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function export()
    {
        $d = $this->Tool->exportDatabase();
        if ($d) {
            $this->response->file(WWW_ROOT . $d['file'], array('download' => true));
            return $this->response;
        }
    }

    public function test()
    {
        //$res = $this->get_lead(1857627);
        //echo $res;
        exit;
    }
}

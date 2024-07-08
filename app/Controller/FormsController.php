<?php
App::uses('AppController', 'Controller');

class FormsController extends AppController {
	public $uses = array('Submission', 'LeadAgent');
	
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow();
		
		$this->layout = 'forms';
	}
	
	public function salestool($centre_name) {
    	$centre_names = array('Amrish', 'Novotus', '3Konnect', 'DotNet', 'Dreizac', 'IValue', 'ExtremeOS', 'STD', 'RC');
    	$centre_name_decoded = base64_decode($centre_name);
    	if (!in_array($centre_name_decoded, $centre_names)) {
        	$this->redirect( MAIN_SITE );
    	}
        if ($this->request->is('put') || $this->request->is('post')) {
            $leadId = 0;
            $data = array(
                'agent_name' => (isset($this->request->data['agent_name'])) ? $this->request->data['agent_name'] : '',
                'agent_email' => (isset($this->request->data['agent_email'])) ? $this->request->data['agent_email'] : '',
                'agent_id' => (isset($this->request->data['agent_id'])) ? $this->request->data['agent_id'] : '',
                'plan_name' => (isset($this->request->data['plan_name'])) ? $this->request->data['plan_name'] : '',
                'plan_discounts' => (isset($this->request->data['plan_discounts'])) ? $this->request->data['plan_discounts'] : '',
                'solar' => (isset($this->request->data['solar']) && $this->request->data['solar']) ? 1 : 0,
                'fuel' => (isset($this->request->data['fuel'])) ? $this->request->data['fuel'] : '',
                'customer_type' => (isset($this->request->data['customer_type'])) ? $this->request->data['customer_type'] : '',
                'title' => (isset($this->request->data['title'])) ? $this->request->data['title'] : '',
                'first_name' => (isset($this->request->data['firstname'])) ? $this->request->data['firstname'] : '',
                'last_name' => (isset($this->request->data['lastname'])) ? $this->request->data['lastname'] : '',
                'mobile' => (isset($this->request->data['mobile'])) ? $this->request->data['mobile'] : '',
                'work_number' => (isset($this->request->data['work_number'])) ? $this->request->data['work_number'] : '',
                'home_phone' => (isset($this->request->data['home_phone'])) ? $this->request->data['home_phone'] : '',
                'email' => (isset($this->request->data['email'])) ? $this->request->data['email'] : '',
                'dob' => (isset($this->request->data['dob'])) ? $this->request->data['dob'] : '',
                'nmi' => (isset($this->request->data['nmi'])) ? $this->request->data['nmi'] : '',
                'mirn' => (isset($this->request->data['mirn'])) ? $this->request->data['mirn'] : '',
                'abn' => (isset($this->request->data['abn'])) ? $this->request->data['abn'] : '',
                'connection_type' => (isset($this->request->data['connection_type'])) ? $this->request->data['connection_type'] : '',
                'connection_date' => (isset($this->request->data['connection_date'])) ? $this->request->data['connection_date'] : '',
                'prefered_contact_method' => (isset($this->request->data['prefered_contact_method'])) ? $this->request->data['prefered_contact_method'] : '',
                'bill_delivery' => (isset($this->request->data['bill_delivery'])) ? $this->request->data['bill_delivery'] : '',
                'communications_delivery' => (isset($this->request->data['communications_delivery'])) ? $this->request->data['communications_delivery'] : '',
                'secret_question' => (isset($this->request->data['secret_question'])) ? $this->request->data['secret_question'] : '',
                'secret_answer' => (isset($this->request->data['secret_answer'])) ? $this->request->data['secret_answer'] : '',
                'payment_method' => (isset($this->request->data['payment_method'])) ? $this->request->data['payment_method'] : '',
                'has_identification' => (isset($this->request->data['has_identification']) && $this->request->data['has_identification']) ? 1 : 0,
                'identification_document_type' => (isset($this->request->data['identification_document_type'])) ? $this->request->data['identification_document_type'] : '',
                'identification_document_id' => (isset($this->request->data['identification_document_id'])) ? $this->request->data['identification_document_id'] : '',
                'identification_document_expiry' => (isset($this->request->data['identification_document_expiry'])) ? $this->request->data['identification_document_expiry'] : '',
                'identification_document_state' => (isset($this->request->data['identification_document_state'])) ? $this->request->data['identification_document_state'] : '',
                'identification_document_country' => (isset($this->request->data['identification_document_country'])) ? $this->request->data['identification_document_country'] : '',
                'life_support' => (isset($this->request->data['life_support']) && $this->request->data['life_support']) ? 1 : 0,
                'life_support_machine_type' => (isset($this->request->data['life_support_machine_type'])) ? $this->request->data['life_support_machine_type'] : '',
                'has_concessions' => (isset($this->request->data['has_concessions']) && $this->request->data['has_concessions']) ? 1 : 0,
                'concession_name' => (isset($this->request->data['concession_number'])) ? $this->request->data['concession_number'] : '',
                'concession_number' => (isset($this->request->data['concession_number'])) ? $this->request->data['concession_number'] : '',
                'concessions_start_date' => (isset($this->request->data['concessions_start_date'])) ? $this->request->data['concessions_start_date'] : '',
                'concessions_expiry_date' => (isset($this->request->data['concessions_expiry_date'])) ? $this->request->data['concessions_expiry_date'] : '',
                'electronic' => (isset($this->request->data['electronic']) && $this->request->data['electronic']) ? 1 : 0,
                'has_supply_address' => (isset($this->request->data['has_supply_address']) && $this->request->data['has_supply_address']) ? 1 : 0,
                'supply_unit' => (isset($this->request->data['supply_unit'])) ? $this->request->data['supply_unit'] : '',
                'supply_lot' => (isset($this->request->data['supply_lot'])) ? $this->request->data['supply_lot'] : '',
                'supply_floor' => (isset($this->request->data['supply_floor'])) ? $this->request->data['supply_floor'] : '',
                'supply_building_name' => (isset($this->request->data['supply_building_name'])) ? $this->request->data['supply_building_name'] : '',
                'supply_street_number' => (isset($this->request->data['supply_street_number'])) ? $this->request->data['supply_street_number'] : '',
                'supply_street_name' => (isset($this->request->data['supply_street_name'])) ? $this->request->data['supply_street_name'] : '',
                'supply_suburb' => (isset($this->request->data['supply_suburb'])) ? $this->request->data['supply_suburb'] : '',
                'supply_postcode' => (isset($this->request->data['supply_postcode'])) ? $this->request->data['supply_postcode'] : '',
                'supply_state' => (isset($this->request->data['supply_state'])) ? $this->request->data['supply_state'] : '',
                'is_mirn_address_different_supply_address' => (isset($this->request->data['is_mirn_address_different_supply_address']) && $this->request->data['is_mirn_address_different_supply_address']) ? 1 : 0,
                'mirn_unit' => (isset($this->request->data['mirn_unit'])) ? $this->request->data['mirn_unit'] : '',
                'mirn_lot' => (isset($this->request->data['mirn_lot'])) ? $this->request->data['mirn_lot'] : '',
                'mirn_floor' => (isset($this->request->data['mirn_floor'])) ? $this->request->data['mirn_floor'] : '',
                'mirn_building_name' => (isset($this->request->data['mirn_building_name'])) ? $this->request->data['mirn_building_name'] : '',
                'mirn_street_number' => (isset($this->request->data['mirn_street_number'])) ? $this->request->data['mirn_street_number'] : '',
                'mirn_street_name' => (isset($this->request->data['mirn_street_name'])) ? $this->request->data['mirn_street_name'] : '',
                'mirn_suburb' => (isset($this->request->data['mirn_suburb'])) ? $this->request->data['mirn_suburb'] : '',
                'mirn_postcode' => (isset($this->request->data['mirn_postcode'])) ? $this->request->data['mirn_postcode'] : '',
                'mirn_state' => (isset($this->request->data['mirn_state'])) ? $this->request->data['mirn_state'] : '',
                'is_billing_address_different_supply_address' => (isset($this->request->data['is_billing_address_different_supply_address']) && $this->request->data['is_billing_address_different_supply_address']) ? 1 : 0,
                'billing_unit' => (isset($this->request->data['billing_unit'])) ? $this->request->data['billing_unit'] : '',
                'billing_lot' => (isset($this->request->data['billing_lot'])) ? $this->request->data['billing_lot'] : '',
                'billing_floor' => (isset($this->request->data['billing_floor'])) ? $this->request->data['billing_floor'] : '',
                'billing_building_name' => (isset($this->request->data['billing_building_name'])) ? $this->request->data['billing_building_name'] : '',
                'billing_street_number' => (isset($this->request->data['billing_street_number'])) ? $this->request->data['billing_street_number'] : '',
                'billing_street_name' => (isset($this->request->data['billing_street_name'])) ? $this->request->data['billing_street_name'] : '',
                'billing_suburb' => (isset($this->request->data['billing_suburb'])) ? $this->request->data['billing_suburb'] : '',
                'billing_postcode' => (isset($this->request->data['billing_postcode'])) ? $this->request->data['billing_postcode'] : '',
                'billing_state' => (isset($this->request->data['billing_state'])) ? $this->request->data['billing_state'] : '',
                'add_secondary_contact' => (isset($this->request->data['add_secondary_contact']) && $this->request->data['add_secondary_contact']) ? 1 : 0,
                'secondary_title' => (isset($this->request->data['secondary_title'])) ? $this->request->data['secondary_title'] : '',
                'secondary_first_name' => (isset($this->request->data['secondary_firstname'])) ? $this->request->data['secondary_firstname'] : '',
                'secondary_last_name' => (isset($this->request->data['secondary_lastname'])) ? $this->request->data['secondary_lastname'] : '',
                'secondary_dob' => (isset($this->request->data['secondary_dob'])) ? $this->request->data['secondary_dob'] : '',
            );
            
            $post['BusOrResidential'] = $data['customer_type'];
            $post['submitted'] = array(
                'title' => $data['title'],
                'FirstName' => $data['first_name'],
                'surname' => $data['last_name'],
                'MobileNumber' => $data['mobile'],
                'primaryPhone' => $data['mobile'],
                'HomePhone' => $data['home_phone'],
                'WorkNumber' => $data['work_number'],
                'EmailM' => $data['email'],
                'DateOfBirthDate' => $data['dob'],
                'url' => $data['url'],
                'NMICode' => $data['nmi'],
                'MIRNNumber' => $data['mirn'],
                'CustomerType' => $data['customer_type'],
                'MoveinOrTransfer' => $data['connection_type'],
                'ConnectionDate' => $data['connection_date'],
                'LifeSupportActive' => ($data['life_support']) ? 'Y' : 'N',
                'RegisterforEBill' => ($data['bill_delivery'] == 'EMAIL') ? 'Y' : 'N',
                'ElectronicMarketingInfo' => ($data['electronic']) ? 'Y' : 'N',
                'life_support_machine_type' => $data['life_support_machine_type'],
                'SecretQuestion' => $data['secret_question'],
                'SecretAnswer' => $data['secret_answer'],
            );
            
            if ($data['abn']) {
                $post['submitted']['ABN'] = $data['abn'];
            }
            
            if (isset($data['has_identification']) && $data['has_identification']) {
                 $post['submitted']['DocumentType'] = $data['identification_document_type'];
                 $post['submitted']['DocumentIDNumber'] = $data['identification_document_id'];
                 $post['submitted']['DocumentExpiry'] = $post['submitted']['DocumentExpiry1'] = $data['identification_document_expiry'];
                 $post['submitted']['DLState'] = $data['identification_document_state'];
                 $post['submitted']['DocumentCountryofIssue'] = $data['identification_document_country'];
            }
            
            if (isset($data['has_concessions']) && $data['has_concessions']) {
                $post['submitted']['NameonConcessionCard'] = $data['concession_name'];
                $post['submitted']['ConcessionCardNumber'] = $data['concession_number'];
                $post['submitted']['ConcessionCardStartDate'] = $data['concessions_start_date'];
                $post['submitted']['ConcessionCardExpiryDate'] = $data['concessions_expiry_date'];
            }
            
            if (isset($data['has_supply_address']) && $data['has_supply_address']) {
                $post['submitted']['UnitSupply'] = $data['supply_unit'];
                $post['submitted']['LotSupply'] = $data['supply_lot'];
                $post['submitted']['FloorSupply'] = $data['supply_floor'];
                $post['submitted']['BuildingName'] = $data['supply_building_name'];
                $post['submitted']['StreetNumberSupply'] = $data['supply_street_number'];
                $post['submitted']['StreetNumberSupply'] = $data['supply_street_name'];
                $post['submitted']['SuburbSupply'] = $data['supply_suburb'];
                $post['submitted']['PostcodeSupply'] = $data['supply_postcode'];
                $post['submitted']['StateSupply'] = $data['supply_state'];
            }
            
            if (isset($data['add_secondary_contact']) && $data['add_secondary_contact']) {
                $post['submitted']['SecondaryContactFirstName'] = $data['secondary_first_name'];
                $post['submitted']['SecondaryContactSurname'] = $data['secondary_last_name'];
                $post['submitted']['Secondary_Contact_DOB'] = $data['secondary_dob'];
                $post['submitted']['SecondaryContactTitle'] = $data['secondary_title'];
            }
            
            if (isset($data['is_billing_address_different_supply_address']) && $data['is_billing_address_different_supply_address']) {
                $post['submitted']['BillingAddressDifferent'] = 'Y';
                $post['submitted']['UnitBilling'] = $data['billing_unit'];
                $post['submitted']['LotBilling'] = $data['billing_lot'];
                $post['submitted']['FloorBilling'] = $data['billing_floor'];
                $post['submitted']['BuildingNameBilling'] = $data['billing_building_name'];
                $post['submitted']['StreetNumberBilling'] = $data['billing_street_number'];
                $post['submitted']['StreetNameBilling'] = $data['billing_street_name'];
                $post['submitted']['SuburbBilling'] = $data['billing_suburb'];
                $post['submitted']['PostcodeBilling'] = $data['billing_postcode'];
                $post['submitted']['StateBilling'] = $data['billing_state'];
            } else {
                $post['submitted']['BillingAddressDifferent'] = 'N';
                $post['submitted']['UnitBilling'] = $data['supply_unit'];
                $post['submitted']['LotBilling'] = $data['supply_lot'];
                $post['submitted']['FloorBilling'] = $data['supply_floor'];
                $post['submitted']['BuildingNameBilling'] = $data['supply_building_name'];
                $post['submitted']['StreetNumberBilling'] = $data['supply_street_number'];
                $post['submitted']['StreetNameBilling'] = $data['supply_street_name'];
                $post['submitted']['SuburbBilling'] = $data['supply_suburb'];
                $post['submitted']['PostcodeBilling'] = $data['supply_postcode'];
                $post['submitted']['StateBilling'] = $data['supply_state'];
            }
            
            if (isset($data['is_mirn_address_different_supply_address']) && $data['is_mirn_address_different_supply_address']) {
                $post['submitted']['MIRNAddressDifferent'] = 'Y';
                $post['submitted']['UnitMIRN'] = $data['mirn_unit'];
                $post['submitted']['LotMIRN'] = $data['mirn_lot'];
                $post['submitted']['FloorMIRN'] = $data['mirn_floor'];
                $post['submitted']['BuildingNameMIRN'] = $data['mirn_building_name'];
                $post['submitted']['StreetNumberMIRN'] = $data['mirn_street_number'];
                $post['submitted']['StreetNameMIRN'] = $data['mirn_street_name'];
                $post['submitted']['SuburbMIRN'] = $data['mirn_suburb'];
                $post['submitted']['PostcodeMIRN'] = $data['mirn_postcode'];
                $post['submitted']['StateMIRN'] = $data['mirn_state'];
            } else {
                $post['submitted']['MIRNAddressDifferent'] = 'N';
                $post['submitted']['UnitBilling'] = $data['supply_unit'];
                $post['submitted']['UnitMIRN'] = $data['supply_lot'];
                $post['submitted']['FloorMIRN'] = $data['supply_floor'];
                $post['submitted']['BuildingNameMIRN'] = $data['supply_building_name'];
                $post['submitted']['StreetNumberMIRN'] = $data['supply_street_number'];
                $post['submitted']['StreetNameMIRN'] = $data['supply_street_name'];
                $post['submitted']['SuburbMIRN'] = $data['supply_suburb'];
                $post['submitted']['PostcodeMIRN'] = $data['supply_postcode'];
                $post['submitted']['StateMIRN'] = $data['supply_state'];
            }
                
            if (strtolower($data['first_name']) == 'test' || in_array($data['mobile'], unserialize(BAN_PHONE_NUMBERS))) {
                $post['submitted']['status'] = '*TestStatus';
            }
            
            $post['submitted']['status'] = '(Ops Status) Ready to Process';
            
            $post['submitted']['SaleCompletionDate'] = date('m/d/Y');
            $post['submitted']['SaleDateTime'] = date('m/d/Y h:i:s A');
            $post['submitted']['sale_completion_time'] = date('g:i A');
            
            $post['submitted']['streaming_frequency'] = $centre_name_decoded;
            
            $post['submitted']['SalesRepName'] = $data['agent_name'];
            $post['submitted']['sales_rep_if_applicable'] = $data['agent_email'];
            
            $ban_string = unserialize(BAN_STRING);
            foreach($ban_string as $item) {
                if (strpos(strtolower($data['first_name']), $item) !== false || strpos(strtolower($data['mobile']), $item) !== false || strpos(strtolower($data['phone']), $item) !== false || strpos(strtolower($data['email']), $item) !== false) {
                    $post['submitted']['status'] = '*TestStatus';
                    break;
                }
            }
            $postQuery = http_build_query($post, '', '&');

            // Create a new cURL resource
            $ch = curl_init();


            curl_setopt($ch, CURLOPT_URL, 'https://secure.velocify.com/Import.aspx?Provider=RSMSolutions&Client=RSMSolutions&CampaignId=92&XmlResponse=True');

            // Set the method to POST
            curl_setopt($ch, CURLOPT_POST, 1);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

            // Pass POST data
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postQuery);
            $response = curl_exec($ch); // Post to 3rd Party Service
            curl_close($ch); // close cURL resource

            if ($response) {
                $result = simplexml_load_string($response);
                foreach ($result->ImportResult[0]->attributes() as $key => $value) {
                    if ($key == 'leadId') {
                        $leadId = (int)$value;
                    }
                }
            }
            
            $agent = $this->assign_to_agent($leadId, $data['agent_id']);
            
            $this->Submission->create();
            $this->Submission->save(array('Submission' => array(
                'sid' => time(),
                'leadid' => $leadId,
                'mobile' => $data['mobile'],
                'email' => $data['email'],
                'request' => $postQuery,
                'response' => $response,
                'submitted' => date('Y-m-d H:i:s'),
                'source' => "SalesTool",
            )));

            $this->Session->setFlash(__('Thanks your lead was submitted!'), 'flash_success');
            
            //$this->redirect(array('action' => 'salestool', $centre_name));
        }
        
    }
    
    private function assign_to_agent($lead_id, $agent_id) {
		$username = LEADS360_USERNAME;
		$password = LEADS360_PASSWORD;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://service.leads360.com/ClientService.asmx/AssignViaDistribution?username={$username}&password={$password}&leadId={$lead_id}&agentId={$agent_id}&programId=121");
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
		    'source' => 'Forms - assign to agent',
        )));

		return $response; 
	}
}

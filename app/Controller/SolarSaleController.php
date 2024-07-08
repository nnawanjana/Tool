<?php

App::uses('AppController', 'Controller');

class SolarSaleController extends AppController
{
    public $uses = array('Plan', 'Location', 'ElectricityRate', 'GasRate', 'Tariff', 'ElectricityPostcodeDistributor', 'GasPostcodeDistributor', 'ElectricityNmiDistributor', 'Consumption', 'SolarRebateScheme', 'Customer', 'Sale', 'Submission', 'LeadAgent', 'BroadbandLog', 'OffshoreLeadExceptions', 'DmoVdo');
    public $helpers = array('Html', 'Icon');

    public function beforeFilter()
    {

        parent::beforeFilter();

        $this->Auth->allow();

        $this->layout = 'solar_sale';

        if (!in_array($this->request->clientIp(), unserialize(STAFF_IPS))) {
            $this->redirect(MAIN_SITE);
        }
    }

    public function index()
    {
        $this->set('title_for_layout', 'Solar Sale');

        if ($this->request->is('put') || $this->request->is('post')) {
            $action = (isset($this->request->data['action']) && $this->request->data['action']) ? $this->request->data['action'] : '';
            $sid = (isset($this->request->data['sid']) && $this->request->data['sid']) ? $this->request->data['sid'] : '';
            $campaign_id = (isset($this->request->data['campaign_id']) && $this->request->data['campaign_id']) ? $this->request->data['campaign_id'] : 1;
            $referring_agent = (isset($this->request->data['referring_agent']) && $this->request->data['referring_agent']) ? $this->request->data['referring_agent'] : '';

            if ($action && $sid) {
                switch ($action) {
                    case 'solar_interest':
                        $submission['solar_interest'] = 'Yes';
                        $submission['submitted']['solarinterestdateregistered'] = date('m/d/Y');
                        break;
                    case 'solar_appointment':
                        $submission['solar_appointment'] = 'Yes';
                        break;
                    case 'solar_sale_confirmed':
                        $submission['solar_sale_confirmed'] = 'Yes';
                        break;
                }

                if ($referring_agent) {
                    $submission['submitted']['referrer_name'] = $referring_agent;
                }

                $this->update_lead($campaign_id, $sid, $submission);
            }

            return new CakeResponse(array(
                'body' => $sid,
                'type' => 'text',
                'status' => '201'
            ));
        }
    }

    private function update_lead($campaign_id = 1, $id = null, $submission = array())
    {
        $request = http_build_query($submission, '', '&');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://secure.velocify.com/Update.aspx?Provider=RSMSolutions&Client=RSMSolutions&CampaignId={$campaign_id}&XmlResponse=True&LeadId={$id}");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        $response = curl_exec($ch);
        curl_close($ch);
        if (strpos($response, 'Success') !== false) {
            //return $id;
        }
        $this->Submission->create();
        $this->Submission->save(array('Submission' => array(
            'sid' => time(),
            'leadid' => $id,
            'mobile' => '',
            'email' => '',
            'request' => $request,
            'response' => $response,
            'submitted' => date('Y-m-d H:i:s'),
            'source' => 'Solar Sale - Update',
        )));
    }
}
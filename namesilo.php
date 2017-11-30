<?php
class Namesilo{
    #### by default use the sandbox in development
    public $api_url = 'https://www.namesilo.com/api/';
    public $api_key;
    public $debug = false;
    public function __construct($api_key,$sandbox=false,$debug=false){
        if($sandbox == true){
            $this->api_url = 'http://sandbox.namesilo.com/api/';
        }
        $this->api_key = $api_key;
        $this->debug = false;
    }
    public function create_contact($fn,$ln,$ad,$cy,$st,$zp,$ct,$em,$ph){
        $result = $this->request('contactAdd',[
            ['fn',$fn], // first name
            ['ln',$ln], // last name
            ['ad',$ad], // address
            ['cy',$cy], // city
            ['st',$st], // state
            ['zp',$zp], // zip
            ['ct',$ct], // country
            ['em',$em], // email
            ['ph',$ph], // phone number
        ]);
        if($this->request_successp($result))
            return $result['reply']['contact_id'];
        return false;
    }
    public function update_nameservers($domain,$ns1,$ns2){
        $result = $this->request('changeNameServers',[
            ['domain',$domain],
            ['ns1',$ns1],
            ['ns2',$ns2]
        ]);
        if($this->request_successp($result))
            return true;
        return false;
    }
    public function update_contact_by_domain($domain,$fn,$ln,$ad,$cy,$st,$zp,$ct,$em,$ph){
        $contact_id = $this->get_contact_id_by_domain($domain);
        $result = $this->request('contactUpdate',[
            ['contact_id',$contact_id],
            ['fn',$fn], // first name
            ['ln',$ln], // last name
            ['ad',$ad], // address
            ['cy',$cy], // city
            ['st',$st], // state
            ['zp',$zp], // zip
            ['ct',$ct], // country
            ['em',$em], // email
            ['ph',$ph], // phone number
        ]);
        if($this->request_successp($result))
            return true;
        return false;
    }
    public function delete_contact($contact_id){
        $result = $this->request('contactDelete',[
            ['contact_id',$contact_id]
        ]);
        if($this->request_successp($result))
            return true;
        return false;
    }
    public function register_domain_by_contact_id($domain,$contact_id,$years=1){
        $result = $this->request('registerDomain',[
            ['domain',$domain],
            ['years',$years],
            ['private',1],
            ['auto_renew',0],
            ['contact_id',$contact_id],
        ]);
        if(!$this->request_successp($result)){
            $this->delete_contact($contact_id);
            return false;
        }
        return true;
    }
    public function register_domain($domain,$fn,$ln,$ad,$cy,$st,$zp,$ct,$em,$ph,$years=1){
        $contact_id = $this->create_contact($fn,$ln,$ad,$cy,$st,$zp,$ct,$em,$ph);
        if(!$contact_id)
            return false;
        $result = $this->request('registerDomain',[
            ['domain',$domain],
            ['years',$years],
            ['private',1],
            ['auto_renew',0],
            ['contact_id',$contact_id],
        ]);
        if(!$this->request_successp($result)){
            return false;
        }
        return true;
    }
    public function add_dns_record($domain,$type,$host,$value,$distance='',$ttl=''){
        $result = $this->request('dnsAddRecord',[
            ['domain',$domain],
            ['rrtype',$type],
            ['rrhost',$host],
            ['rrvalue',$value],
            ['rrdistance',$distance],
            ['rrttl',$ttl],
        ]);
        if($this->request_successp($result))
            return true;
        else
            return false;
    }
    public function delete_dns_record($domain,$record_id){
        $result = $this->request('dnsDeleteRecord',[
            ['domain',$domain],
            ['rrid',$record_id],
        ]);
        if($this->request_successp($result))
            return true;
        else
            return false;
    }
    public function get_dns_records($domain){
        $result = $this->request('dnsListRecords',[
            ['domain',$domain],
        ]);
        if($this->request_successp($result)){
            if(!isset($result['reply']['resource_record'][0])){
                $temp_arr = [];
                $temp_arr[0] = $result['reply']['resource_record'];
                return $temp_arr;
            }else{
                return $result['reply']['resource_record'];
            }
        }else{
            return false;
        }
    }
    public function is_domain_available($domain){
        $result = $this->request('checkRegisterAvailability',[['domains',$domain]]);
        if($this->request_successp($result) && isset($result['reply']['available']))
            return 'available';
        if($this->request_successp($result) && isset($result['reply']['invalid']))
            return 'invalid';
        if($this->request_successp($result) && isset($result['reply']['unavailable']))
            return 'unavailable';
        return false;
    }
    public function send_auth_code($domain){
        return $this->request_successp($this->request('retrieveAuthCode',[['domain',$domain]]));
    }
    public function get_contact_by_id($contact_id){
        $result = $this->request('contactList',[['contact_id',$contact_id]]);
        if(!$this->request_successp($result))
            return false;
        return $result['reply']['contact'];
    }
    public function get_all_contacts(){
        $result = $this->request('contactList');
        if(!$this->request_successp($result))
            return false;
        return $result['reply']['contact'];
    }
    public function get_contact_by_domain($domain){
        $contact_id = $this->get_contact_id_by_domain($domain);
        if(!$contact_id)
            return false;
        return $this->get_contact_by_id($contact_id);
    }
    public function list_domains(){
        $result = $this->request('listDomains');
        if(!$this->request_successp($result))
            return false;
        return $result['reply']['domains']['domain'];
    }
    public function get_nameservers($domain){
        $domain_info = $this->get_domain_info($domain);
        if(!$domain_info)
            return false;
        if(is_array($domain_info['nameservers']))
            return $domain_info['nameservers']['nameserver'];
        else
            return false;
    }
    public function privacy_status($domain){
        $domain_info = $this->get_domain_info($domain);
        if(!$domain_info)
            return false;
        $private = strtolower($domain_info['private']);
        if($private == 'yes')
            return true;
        else
            return false;
    }
    public function add_privacy($domain){
        $result = $this->request('addPrivacy',[['domain',$domain]]);
        return $this->request_successp($result);
    }
    public function remove_privacy($domain){
        $result = $this->request('removePrivacy',[['domain',$domain]]);
        return $this->request_successp($result);
    }
    public function lock_status($domain){
        $domain_info = $this->get_domain_info($domain);
        if(!$domain_info)
            return false;
        $private = strtolower($domain_info['locked']);
        if($private == 'yes')
            return true;
        else
            return false;
    }
    public function domain_lock($domain){
        $result = $this->request('domainLock',[['domain',$domain]]);
        return $this->request_successp($result);
    }
    public function domain_unlock($domain){
        $result = $this->request('domainUnlock',[['domain',$domain]]);
        return $this->request_successp($result);
    }
    public function get_contact_id_by_domain($domain){
        $domain_info = $this->get_domain_info($domain);
        if(!$domain_info)
            return false;
        $contact_id = $domain_info['contact_ids']['registrant'];
        return $contact_id;
    }
    public function get_domain_info($domain){
        $result = $this->request('getDomainInfo',[['domain',$domain]]);
        if($this->request_successp($result))
            return $result['reply'];
        else
            return false;
    }
    public function get_account_balance(){
        $result = $this->request('getAccountBalance');
        if($this->request_successp($result))
            return $result['reply']['balance'];
        else
            return false;
    }
    // main public functions
    private function request($command,$options=''){
        $created_options = '';
        if(!empty($options)){
            foreach($options as $pair){
                $created_options .= '&';
                $created_options .= $pair[0];
                $created_options .= '=';
                $created_options .= urlencode($pair[1]);
            }
        }
        $command_ready = $this->api_url . $command . '?version=1&type=xml&key=' . $this->api_key . $created_options;
        $str = file_get_contents($command_ready);
        $result =  $this->xml_to_arr($str);
        if($this->debug){
            echo '<pre>';
            print_r($result);
            echo '</pre>';
        }
        if(!$this->request_successp($result)){
            throw new Exception($result['reply']['detail']);
        }
        return $result;
    }
    private function xml_to_arr($str){
        $str = trim($str);
        $xml = simplexml_load_string($str);
        $json = json_encode($xml);
        $array = json_decode($json,TRUE);
        return $array;
    }
    private function request_successp($arr){
        if($arr['reply']['code'] == 300 ||
           $arr['reply']['code'] == 301 ||
           $arr['reply']['code'] == 302)
            return true;
        else
            return false;
    }
}
<?php
$ns_url = 'http://sandbox.namesilo.com/api/'; // for development
//$ns_url = 'https://www.namesilo.com/api/'; // for production
$ns_key = 'the key provided by namesilo';
$ns_error = ''; // used for internal errors keep it empty!
$ns_debug = false; // weather to turn debuging on or off
function ns_create_contact($fn,$ln,$ad,$cy,$st,$zp,$ct,$em,$ph){
	$result = ns_request('contactAdd',[
		['fn',$fn], // first name
		['ln',$ln], // last name
		['ad',$ad], // address
		['cy',$cy], // city
		['st',$st], // state
		['zp',$zp], // zip
		['ct',$ct], // country
        ['em',$em], // email 
        ['ph',$ph],	// phone number			
	]);
	if(ns_request_successp($result))
		return $result['reply']['contact_id'];
	return false;
}
function ns_update_nameservers($domain,$ns1,$ns2){
	$result = ns_request('changeNameServers',[
		['domain',$domain],
		['ns1',$ns1],
		['ns2',$ns2]
	]);
	if(ns_request_successp($result))
		return true;
	return false;
}
function ns_update_contact_by_domain($domain,$fn,$ln,$ad,$cy,$st,$zp,$ct,$em,$ph){
	$contact_id = ns_get_contact_id_by_domain($domain);
	$result = ns_request('contactUpdate',[
		['contact_id',$contact_id],
		['fn',$fn], // first name
		['ln',$ln], // last name
		['ad',$ad], // address
		['cy',$cy], // city
		['st',$st], // state
		['zp',$zp], // zip
		['ct',$ct], // country
        ['em',$em], // email 
        ['ph',$ph],	// phone number			
	]);
	if(ns_request_successp($result))
		return true;
	return false;
}
function ns_delete_contact($contact_id){
	$result = ns_request('contactDelete',[
		['contact_id',$contact_id]
	]);
	if(ns_request_successp($result))
		return true;
	return false;
}
function ns_register_domain_by_contact_id($domain,$contact_id,$years=1){
	$result = ns_request('registerDomain',[
		['domain',$domain],
		['years',$years],
		['private',1],
		['auto_renew',0],
        ['contact_id',$contact_id],
	]);
	if(!ns_request_successp($result)){
		ns_delete_contact($contact_id);
		return false;
	}
	return true;
}
function ns_register_domain($domain,$fn,$ln,$ad,$cy,$st,$zp,$ct,$em,$ph,$years=1){
	$contact_id = ns_create_contact($fn,$ln,$ad,$cy,$st,$zp,$ct,$em,$ph);
	if(!$contact_id)
		return false;
	$result = ns_request('registerDomain',[
		['domain',$domain],
		['years',$years],
		['private',1],
		['auto_renew',0],
        ['contact_id',$contact_id],				
	]);
	if(!ns_request_successp($result)){
		ns_delete_contact($contact_id);
		return false;
	}
	return true;
}
function ns_add_dns_record($domain,$type,$host,$value,$distance='',$ttl=''){
	$result = ns_request('dnsAddRecord',[
		['domain',$domain],
		['rrtype',$type],
		['rrhost',$host],
		['rrvalue',$value],
		['rrdistance',$distance],
        ['rrttl',$ttl],				
	]);
	if(ns_request_successp($result))
		return true;
	else
		return false;
}
function ns_delete_dns_record($domain,$record_id){
	$result = ns_request('dnsDeleteRecord',[
		['domain',$domain],
		['rrid',$record_id],
	]);
	if(ns_request_successp($result))
		return true;
	else
		return false;
}
function ns_get_dns_records($domain){
	$result = ns_request('dnsListRecords',[
		['domain',$domain],
	]);
	if(ns_request_successp($result)){
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
function ns_is_domain_available($domain){
	$result = ns_request('checkRegisterAvailability',[['domains',$domain]]);
	if(ns_request_successp($result) && isset($result['reply']['available']))
		return 'available';
	if(ns_request_successp($result) && isset($result['reply']['invalid']))
		return 'invalid';
	if(ns_request_successp($result) && isset($result['reply']['unavailable']))
		return 'unavailable';
	return false;
}
function ns_send_auth_code($domain){
	return ns_request_successp(ns_request('retrieveAuthCode',[['domain',$domain]]));
}
function ns_get_contact_by_id($contact_id){
	$result = ns_request('contactList',[['contact_id',$contact_id]]);
	if(!ns_request_successp($result))
		return false;
	return $result['reply']['contact'];
}
function ns_get_all_contacts(){
	$result = ns_request('contactList');
	if(!ns_request_successp($result))
		return false;
	return $result['reply']['contact'];
}
function ns_get_contact_by_domain($domain){
	$contact_id = ns_get_contact_id_by_domain($domain);
	if(!$contact_id)
		return false;
	return ns_get_contact_by_id($contact_id);
}
function ns_list_domains(){
	$result = ns_request('listDomains');
	if(!ns_request_successp($result))
		return false;
	return $result['reply']['domains']['domain'];
}
function ns_get_nameservers($domain){
	$domain_info = ns_get_domain_info($domain);
	if(!$domain_info)
		return false;
	if(is_array($domain_info['nameservers']))
		return $domain_info['nameservers']['nameserver'];
	else
		return false;
}
// domain privacy
function ns_privacy_status($domain){
	$domain_info = ns_get_domain_info($domain);
	if(!$domain_info)
		return false;
	$private = strtolower($domain_info['private']);
	if($private == 'yes')
		return true;
	else
		return false;
}
function ns_add_privacy($domain){
	$result = ns_request('addPrivacy',[['domain',$domain]]);
	return ns_request_successp($result);
}
function ns_remove_privacy($domain){
	$result = ns_request('removePrivacy',[['domain',$domain]]);
	return ns_request_successp($result);
}
// domain lock
function ns_lock_status($domain){
	$domain_info = ns_get_domain_info($domain);
	if(!$domain_info)
		return false;
	$private = strtolower($domain_info['locked']);
	if($private == 'yes')
		return true;
	else
		return false;
}
function ns_domain_lock($domain){
	$result = ns_request('domainLock',[['domain',$domain]]);
	return ns_request_successp($result);
}
function ns_domain_unlock($domain){
	$result = ns_request('domainUnlock',[['domain',$domain]]);
	return ns_request_successp($result);
}
// other commands
function ns_get_contact_id_by_domain($domain){
	$domain_info = ns_get_domain_info($domain);
	if(!$domain_info)
		return false;
	$contact_id = $domain_info['contact_ids']['registrant'];
	return $contact_id;
}
function ns_get_domain_info($domain){
	$result = ns_request('getDomainInfo',[['domain',$domain]]);
	if(ns_request_successp($result))
		return $result['reply'];
	else
		return false;
}
function ns_get_account_balance(){
	$result = ns_request('getAccountBalance');
	if(ns_request_successp($result))
		return $result['reply']['balance'];
	else
		return false;
}
// main functions
function ns_request($command,$options=''){
	global $ns_url,$ns_key,$ns_debug;
	$created_options = '';
	if(!empty($options)){
		foreach($options as $pair){
			$created_options .= '&';
			$created_options .= $pair[0];
			$created_options .= '=';
			$created_options .= urlencode($pair[1]);
		}
	}
	$command_ready = $ns_url . $command . '?version=1&type=xml&key=' . $ns_key . $created_options;
	$str = file_get_contents($command_ready);
	$result =  xml_to_arr($str);
	if($ns_debug){
		echo '<pre>';
		print_r($result);
		echo '</pre>';
	}
	if(!ns_request_successp($result)){
		$ns_error = $result['reply']['detail'];
	}
	if($result['reply']['code'] == 301 || $result['reply']['code'] == 302){
		$ns_error = $result['reply']['detail'];
	}
	return $result;
}
function xml_to_arr($str){
	$xml = simplexml_load_string($str);
	$json = json_encode($xml);
	$array = json_decode($json,TRUE);
	return $array;
}
function ns_request_successp($arr){
	if($arr['reply']['code'] == 300 || $arr['reply']['code'] == 301 || $arr['reply']['code'] == 302)
		return true;
	else
		return false;
}

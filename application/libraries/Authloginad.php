<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
 * This file is part of Auth_AD.

    authloginad is free software: you can redistribute it and/or modify
    it under the terms of the GNU Lesser General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    authloginad is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

		authloginad is developed based on auth_ad by mark kathmann

    You should have received a copy of the GNU General Public License
    along with authloginad.  If not, see <http://www.gnu.org/licenses/>.
 * 
 */
 
/**
 * authloginad Class
 *
 * Active Directory LDAP authentication library for Code Igniter.
 *
 * @package         authloginad
 * @author          detanto <detanto@gmail.com>
 * @version         0.1
 * @license         GNU Lesser General Public License (LGPL)
 * @copyright       Copyright © 2016 detanto <detanto@gmail.com>
 */

class Authloginad 
{
	// register properties
	private $_hosts;
	private $_ports;
	private $_base_dn;
	private $_ad_domain;
	private $_start_ou;
	private $_user;
	private $_pass;
	private $_ldap_conn;
	
	/**
     * @access public
     */
	function __construct() {
		$this->ci =& get_instance();
		log_message('debug', 'initialize authloginad');
		// load the configuration file
		$this->ci->load->config('authloginad');
		// load the session library
		$this->ci->load->library('session');
		// perform the secondary initialization
		$this->_init();
	}
	private function _init(){
		// check for an active LDAP extension
		if (!function_exists('ldap_connect')) 
		{
			log_message('error', 'Authloginad : LDAP PHP module not found.');
			show_error('LDAP PHP module not found. Please ensure that the module is loaded or compiled in.');
		}		
		// register the configuration variables as properties
		$this->_hosts      = $this->ci->config->item('ad_hosts');
		$this->_ports      = $this->ci->config->item('ad_ports');
		$this->_base_dn    = $this->ci->config->item('base_dn');
		$this->_ad_domain  = $this->ci->config->item('ad_domain');
		$this->_start_ou   = $this->ci->config->item('start_ou');
		$this->_user = $this->ci->config->item('ad_user');
		$this->_pass = $this->ci->config->item('ad_pass');
	}
	function login($username, $password){
		// preset the return marker
		$return = false;		
		// preset the process step marker
		$continue = true;
		// check for non-empty parameters
		if (strlen($username) > 0 && strlen($password) > 0){
			// bind to the AD
			if (!$this->bind_ad()){
				$continue = false;
			}
			
			if ($continue){
				// search for the user in the AD
				// $username = "bagus.pangestu";
				$attr = array('dn', 'cn', "manager", "title");
				if (!$entries = $this->search_ad($username, $attr)){
					$continue = false;
				}
			}
			
			if ($continue){
				// attempt to bind as the requested user
				ini_set('display_errors', 0);
				if (!$bind = ldap_bind($this->_ldap_conn, stripslashes($entries['dn']), $password)){
          			log_message('debug', 'Authloginad: Unable to log in the user.');
					$continue = false;
				}else{
					// bind (i.e. login) for the user was succesful, read the user attributes
					// $this->ci->common->debug_array($entries);
					$cn = $entries['cn'][0];
					$dn = stripslashes($entries['dn']);
					$manager = stripslashes($entries['manager'][0]);

					//pura pura bu fitri
					// $cn = "FITRI SULISTIAWATI";
					// $dn = "CN=FITRI SULISTIAWATI,OU=BIDANG PEMBAYARAN ASURANSI,OU=DIVISI KEUANGAN,OU=DIREKTORAT INVESTASI DAN KEUANGAN,OU=KANTOR PUSAT,OU=KARYAWAN,DC=asabri,DC=co,DC=id";
					// $manager = "CN=SUARDI LATIEF,OU=DIVISI KEUANGAN,OU=DIREKTORAT INVESTASI DAN KEUANGAN,OU=KANTOR PUSAT,OU=KARYAWAN,DC=asabri,DC=co,DC=id";
					// $entries['title'][0] = "KABID PEMBAYARAN ASURANSI";

					//pura pura pak mukti
					// $cn = "MUKTI HARIYANTO";
					// $dn = "CN=MUKTI HARIYANTO,OU=BIDANG PUM KPR & PP,OU=DIVISI KEPESERTAAN,OU=DIREKTORAT OPERASI,OU=KANTOR PUSAT,OU=KARYAWAN,DC=asabri,DC=co,DC=id";
					// $manager = "CN=WINARNO,OU=DIVISI KEPESERTAAN,OU=DIREKTORAT OPERASI,OU=KANTOR PUSAT,OU=KARYAWAN,DC=asabri,DC=co,DC=id";
					// $entries['title'][0] = "KABID PUM KPR & PP";
					// mukti.hariyanto
					//end pura pura
					$unor = $this->getOu($dn);
					log_message('debug', 'Authloginad: Successful login for user ' . $cn . ' (' . $username . ') from IP ' . $this->ci->input->ip_address());
					// set the session data for the user
					$user_info = array(
						'cn' => $cn, 
						'dn' => $dn, 
						'uo' => $unor,
						'username' => $username, 
						'logged_in' => true,
						'manager'=>$manager
					);
					if(isset($entries['title'][0])){
						$user_info = array_merge($user_info, array("title"=>$entries['title'][0]));
					}
					// $this->ci->common->debug_array($user_info);
					$this->ci->session->set_userdata($user_info);
					$this->ci->session->set_userdata('actdir',true);
					// set the return marker
					$return = true;
				}
			}
		}
		// return the login result
		return $return;
	}
	/**
	* @access public
	* @return bool
	*/
	function is_authenticated(){
		if ($this->ci->session->userdata('logged_in')) {
			return true;
		} 
		else {
			return false;
		}
	}
    /**
	* @access public
	*/
	function unbind_ad() {
		if(!isset($this->_ldap_conn)) die('Error, no LDAP connection established');
		ldap_unbind($this->_ldap_conn);
	}
	
	function logout() {
		// ldap_unbind($this->_ldap_conn);
		$this->unbind_ad();
		log_message('info', 'Auth_AD: User ' . $this->ci->session->userdata('username') . ' logged out.');		
		$this->ci->session->set_userdata(array('logged_in' => false));
		$this->ci->session->sess_destroy();
	}
	/**
	* @access public
	* @param string $user_dn
	* @param string $groupname
	* @return bool
	*/
	function in_group($username, $groupname){
		// preset the result
		$result = false;		
		// preset the continuation marker
		$continue = true;		
		// bind to the AD
		if (!$this->bind_ad())
		{
			$continue = false;
		}
		
		if ($continue)
		{
			// get the DN for the username
			$user_search = $this->search_ad($this->ldap_escape($username, false), array('dn'));
			$user_dn     = $user_search['dn'];
			
			// get the DN for the group
			$group_search = $this->search_ad($this->ldap_escape($groupname, false), array('dn'));
			$group_dn     = $group_search['dn'];
			
			// search for the user's object
			$attributes = array('memberof');
			$search = ldap_read($this->_ldap_conn, $user_dn, '(objectclass=*)', $attributes);
			
			// read the entries
			$entries = ldap_get_entries($this->_ldap_conn, $search);
			
			if ($entries['count'] > 0) 
			{
				if (!empty($entries[0]['memberof'])) 
				{
					for ($i = 0; $i < $entries[0]['memberof']['count']; $i++) 
					{
						if ($entries[0]['memberof'][$i] == $group_dn) 
						{
							$result = true;
						}
						elseif ($this->in_group($entries[0]['memberof'][$i], $groupname)) 
						{ 
							$result = true;
						}
					}
				}
			}
		}		
		// return the result
		return $result;
	}
	/**
	* @access private
	* @param string $account
	* @param array $req_attrs
	* @return bool or array
	*/
	private function search_ad($account, $req_attrs = array('dn', 'cn'), $ou=null){
		// preset the result
		$result = array();
		// set up the search parameters
		$filter  = '(sAMAccountName=' . $this->ldap_escape($account, false) . ')';
		if (strlen($this->_start_ou) > 0){
			$search_dn = $this->_start_ou . ',' . $this->_base_dn;
		}else{
			if($ou==null){
				$search_dn = $this->_base_dn;
			}else{
				$search_dn = $ou . "," . $this->_base_dn;
			}
		}
		// $this->ci->common->debug_array($req_attrs);
		// perform the search for the username
		if ($search = ldap_search($this->_ldap_conn, $search_dn, $filter, $req_attrs)){
			if ($entries = ldap_get_entries($this->_ldap_conn, $search)){
				// $this->ci->common->debug_array($entries);
				if ($entries['count'] > 0){
					foreach ($req_attrs as $key => $val){
						$result[$val] = $entries[0][$val];
					}
				}
			}else{
				log_message('error', 'Authloginad: Unable to get entries for account.');
				show_error('Unable to read the AD entries for the account');
			}
		}else{
			log_message('error', 'Authloginad: Unable to perform search for the account.');
			show_error('Unable to search the AD for the account.');
		}
		
		// return the result
		if (count($result) == count($req_attrs))
		{
			return $result;
		}
		else 
		{
			return false;
		}
	}
	/**
	* @access private
	* @return bool
	*/
	private function bind_ad(){
		// preset the continuation marker
		$continue = true;
		
		// attempt to connect to each of the AD servers, stop if a connection is succesful 
		foreach ($this->_hosts as $host) 
		{
			$this->_ldap_conn = ldap_connect($host);
			if ($this->_ldap_conn) 
			{
				break;
			}
			else 
			{
				log_message('info', 'Authloginad: Error connecting to AD server ' . $host);
			}
		}		
		// check for an active LDAP connection
		if (!$this->_ldap_conn){
			log_message('error', "Authloginad: unable to connect to any AD servers.");
			show_error('Error connecting to any Active Directory server(s). Please check your configuration and connections.');
			$continue = false;
		}
		
		if ($continue){
			// set some required LDAP options		
			ldap_set_option($this->_ldap_conn, LDAP_OPT_REFERRALS, 0);
			ldap_set_option($this->_ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
		
			// attempt to bind to the AD using the proxy user or anonymously if no user was configured
			if ($this->_user != null)
			{
				
				if(!ldap_bind($this->_ldap_conn, $this->_user, $this->_pass)){
					$usernya = $this->_user."@".$this->_hosts[0];
					$bind = ldap_bind($this->_ldap_conn, $usernya, $this->_pass);
				}else{
					$bind = ldap_bind($this->_ldap_conn, $this->_user, $this->_pass);
				}
			}
			else 
			{
				$bind = ldap_bind($this->_ldap_conn);
			}
			
			// verify the LDAP binding
			if (!$bind)
			{
				if ($this->_user != null)
				{
					log_message('error', 'Authloginad: Unable to perform LDAP bind using user ' . $this->_user);
					show_error('Unable to bind (i.e. login) to the AD for user ID lookup');
				}
				else
				{
					log_message('error', 'Authloginad: Unable to perform anonymous LDAP bind.');
					show_error('Unable to bind (i.e. login) to the AD for user ID lookup');
				}
				$continue = false;
			}
			else 
			{
				log_message('debug', 'Authloginad: Successfully bound to AD. Performing DN lookup for user');
			}
		}
		
		// return the result
		return $continue;
	}
	/**
	* @access private
	* @param string $str
	* @param bool $for_dn
	* @return string 
	*/
	private function ldap_escape($str, $for_dn = false){
		/**
		* This function courtesy of douglass_davis at earthlink dot net
		* Posted in comments at
		* http://php.net/manual/en/function.ldap-search.php on 2009/04/08
		*
		* see:
		* RFC2254
		* http://msdn.microsoft.com/en-us/library/ms675768(VS.85).aspx
		* http://www-03.ibm.com/systems/i/software/ldap/underdn.html
		*/  
		
		if ($for_dn)
		{
			$metaChars = array(',','=', '+', '<','>',';', '\\', '"', '#');
		}
		else
		{
			$metaChars = array('*', '(', ')', '\\', chr(0));
		}
		
		$quotedMetaChars = array();
		foreach ($metaChars as $key => $value) 
		{
			$quotedMetaChars[$key] = '\\' . str_pad(dechex(ord($value)), 2, '0');
		}
		
		$str = str_replace($metaChars, $quotedMetaChars, $str);
		return $str;  
	}
	public function getMachine_ldap($returnnya='array', $OU=null){
		$arrUsersAd = array();
		foreach ($this->_hosts as $host) 
		{
			$this->_ldap_conn = ldap_connect($host, $this->_ports[0]);
			if ($this->_ldap_conn) 
			{
				break;
			}
			else 
			{
				log_message('info', 'Authloginad: Error connecting to AD server ' . $host);
				show_error('Authloginad: Error connecting to AD server ' . $host);
			}
		}
		ldap_set_option($this->_ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($this->_ldap_conn, LDAP_OPT_REFERRALS, 0);

		$bind = ldap_bind($this->_ldap_conn, $this->_user, $this->_pass);
		if ($bind) {
			$additional = "";
			if(isset($OU)){
						$additional = "OU=," . $OU;
			}
			$filter='objectClass=user';
			// $filter="(|(sn=*))"; => kalau non aktif
			$attr[] = "displayname";
			$attr[] = "samaccountname";
			$attr[] = "useraccountcontrol";
			$attr[] = "sn";
			// $sr=ldap_search($ldapconn, $dn, $filter, $justthese);       
			$results = @ldap_search($this->_ldap_conn, $additional . $this->_base_dn,$filter,$attr);
			if($results){
				$ad_users = ldap_get_entries($this->_ldap_conn, $results);
				array_shift($ad_users);
				foreach($ad_users as $e) {
					$info = $e['dn'];
					if(isset($e['samaccountname'])){
						$info .= ',username='.$e['samaccountname'][0];
					}
					if(isset($e['useraccountcontrol'])){
						$info .= ',uac='.$e['useraccountcontrol'][0];
					}
					$arrUsers[] = $info;
				}
				$arrUsersAd = array();
				for ($n=0;$n<count($arrUsers);$n++){
					$arrorgnztn = array();
					if(stripos($arrUsers[$n], "Computers")){
						$expUsers = explode(",", $arrUsers[$n]);							
						for($y=0;$y<count($expUsers);$y++){
							$expUsers_h = explode("=", $expUsers[$y]);
							switch ($expUsers_h[0]) {
								case 'CN':
									if($expUsers_h[1]!='Computers'){
										$fullname = $expUsers_h[1];	
									}
									break;
							}
						}
						$orgnztn = "";
						$rc = false;
						for($l=0;$l<count($arrorgnztn);$l++){
							if($rc) $orgnztn .=",";
							$orgnztn .= $arrorgnztn[$l];
							$rc =true;
						}
						$arrUsersAd[] = array('id'=>strtoupper($fullname), 'name'=>strtoupper($fullname));
					}
				}
			}
		}
    	// $returnnya = 'json';
    	switch ($returnnya) {
			case 'json':
				$json = "[";

				$ro=false;
				for($e=0;$e<count($arrUsersAd);$e++){
					if($ro) $json .= ",";
				$json .= "{";
					$rc=false;
					foreach ($arrUsersAd[$e] as $key => $value) {
					if($rc) $json .= ",";
					$json .= '"' . $key . '":"'.$value.'"' ;
					$rc=true;
					}
				$json .= "}";
				$ro=true;
				}    		
				$json .= "]";
				$return = $json;
				break;
			default:
				$return = $arrUsersAd;
				break;
    	}
    	return $return;
	}
	function extractDN($dn){
		$arrDN=ldap_explode_dn($dn, 0);
		$out = array();
		foreach($arrDN as $key=>$value){
			if(FALSE !== strstr($value, '=')){
				list($prefix,$data) = explode("=",$value);
				switch ($prefix) {
					case 'DC':
						$delimiter = ".";
						break;
					case 'CN':
						$delimiter = " ";
						break;
					
					default:
						$delimiter = ", ";
						break;
				}
				if(isset($current_prefix) && $prefix == $current_prefix){
					$txt .= $delimiter . $data;
				$out[$prefix] = $txt;
				} else {
					$txt = $data;
					$current_prefix = $prefix;
					$out[$prefix] = $txt;
				}
			}
    	} 
    	return $out;
	}
	function getOu($dn){
		$arrOU = explode("OU=", $dn);
		$count = count($arrOU);
		$loop = 0;
		foreach($arrOU as $key=>$value){
			if($loop!=0 && $loop!=($count-1)){
				$arrUOR[] = substr(trim(str_replace(str_replace(" ", "", strtoupper($this->_base_dn)), "", strtoupper($value))),0, strlen($value)-1);
			}
			$loop++;
		}
		ksort($arrUOR);
		return $arrUOR;
	}
	function getOrganization($parameter=null){
		// $this->ci->common->debug_array($parameter);
		$json = null;
		$show = "name";
		$username="*";
		$return = "json";
		$type="organisasi";
		if(is_array($parameter)){
			foreach($parameter as $key=>$value){
				${$key} = $value;
			}
		}
		if(isset($base_dn)){
			$base_dn = $base_dn . "," . $this->_base_dn;
		}else{
			$base_dn = $this->_base_dn;
		}
		// $this->ci->common->debug_array($base_dn);
		$attr = array('dn', 'cn', "manager", "title");
		// if (!$entries = $this->search_ad($username, $attr)){
		// 	$continue = false;
		// }

		foreach ($this->_hosts as $host) {
			$this->_ldap_conn = ldap_connect($host, $this->_ports[0]);
			if ($this->_ldap_conn){
				break;
			}
			else{
				log_message('info', 'Authloginad: Error connecting to AD server ' . $host);
				show_error('Authloginad: Error connecting to AD server ' . $host);
			}
		}

		ldap_set_option($this->_ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($this->_ldap_conn, LDAP_OPT_REFERRALS, 0);
		ldap_set_option($this->_ldap_conn, LDAP_OPT_SIZELIMIT, 9000); 		
		// $bind = ldap_bind($this->_ldap_conn, $this->_user, $this->_pass);
		// $filter    = "(&(objectClass=user)(objectCategory=person)(sn=*))";
		if($username!="*"){
			$username = "*".$username."*";
		}
		$filter = '(|(samaccountname='.$username.')(displayName='.$username.'))';
		$filtertitle = null;
		if(isset($title)){
			if(is_array($title)){
				$filtertitle = '(|'; //'';
				foreach($title as $key){
					$filtertitle .= '(title=*'.$key.'*)';
				}
				$filtertitle .= ')';
			}else{
				$filtertitle = '(title=*'.$title.'*)';
			}
		}

		$filter = '(|';
		$filter .= '(&(objectClass=user)(objectCategory=person)(samaccountname='.$username.')'.$filtertitle.')';
		$filter .= '(&(objectClass=user)(objectCategory=person)(displayName='.$username.')'.$filtertitle.')';
		$filter .= ')';

		// if($type=="organisasi"){
		// 	$filter="objectClass=organizationalUnit"; 
		// }
		// $this->_base_dn = 'manager:1.2.840.113556.1.4.1941:=CN=manager, OU=DIVISI SISTEM INFORMASI,OU=DIREKTORAT SDM DAN UMUM,OU=KANTOR PUSAT,OU=KARYAWAN,dc=asabri,dc=co, dc=id';
		// $this->_base_dn = 'OU=DIREKTORAT SDM DAN UMUM,OU=KANTOR PUSAT,OU=KARYAWAN,dc=asabri,dc=co, dc=id';
		// $filter='objectClass=user';
		$justthese = array();
		// enable pagination with a page size of 100.
		$pageSize = 20;
		$cookie = '';
		$bind = ldap_bind($this->_ldap_conn, $this->_user, $this->_pass);
		// $attr[] = "givenname";
		$attr[] = "samaccountname"; //Login Name pre 2000 without domain name
		// $attr[] = "userPrincipalName"; //Login Name
		$attr[] = "useraccountcontrol";
		// $attr[] = "sn"; //Last Name
		$attr[] = "displayname";
		$attr[] = "cn"; //Full Name
		$attr[] = "dn";
		$attr[] = "Title";
		$attr[] = "Manager";
		// $attr = array("dn"); 

    	if ($bind) {
			ldap_control_paged_result($this->_ldap_conn, $pageSize, true, $cookie);
			$result  = @ldap_search($this->_ldap_conn, $base_dn, $filter, $attr,0,0);
			// $entry = ldap_first_entry($this->_ldap_conn, $result);
			// $info = ldap_get_values($this->_ldap_conn, $entry, "samaccountname"); 
			$entries = ldap_get_entries($this->_ldap_conn, $result);
		}

		if(isset($entries)){
			$loop = 0;
			foreach($entries as $keyAD=>$valueAD){
				// if($keyAD=="count"){
				// 	$this->ci->common->debug_array($keyAD, false);
				// }
				$cn = $valueAD['cn'][0];
				if($cn!=""){
					$jobtitle = null;
					$manager = null;
					$cn = $valueAD['cn'][0];
					$dn = stripslashes($valueAD['dn']);
					if(isset($valueAD['samaccountname'][0])){
						$account = $valueAD['samaccountname'][0];
					}else{
						$account = $cn;
					}
					if(isset($valueAD['displayname'][0])){
						$displayname = $valueAD['displayname'][0];
					}else{
						$displayname = $cn;
						// $account = $cn;
					}					
					
					if(isset($valueAD['title'][0])){
						$jobtitle = $valueAD['title'][0];
					}
					if(isset($valueAD['manager'][0])){
						$manager = stripslashes($valueAD['manager'][0]);
					}
					$showname = ($show=="name" ? $cn : $displayname);
					$showdisplay = ($show=="name" ? $displayname : $cn);
					
					$json[$loop]["id"] = $account;
					$json[$loop]["name"] = $showname; //$cn;
					$json[$loop]["Nama"] = $showdisplay; //$displayname;
					$json[$loop]["Domain"] = $dn;
					$json[$loop]["Account"] = $account;
					$json[$loop]["JobTitle"] = $jobtitle;
					$json[$loop]["Supervisor"] = $manager;
					$loop++;
				}
			}
		}		
		if($return=="json"){
			$json = json_encode($json);
			return $json;
			// $this->ci->common->debug_array($json);
		}else{
			return $json;
			// $this->ci->common->debug_array($entries);
		}
		die();
	}
	function getMember_ldap($returnnya='array', $OU=null, $active=null, $hasilnya=false){
		putenv("LDAPTLS_CIPHER_SUITE=NORMAL:!VERS-TLS1.2"); 
		$arrUsersAd = array();
		$arrStatus = array(
			'512'=>'Enabled Account',
			'514'=>'Disabled Account',
			'528'=>"Enabled – LOCKOUT",
			'530'=>"ACCOUNTDISABLE – LOCKOUT",
			'544'=>'Enabled, Password Not Required',
			'546'=>'Disabled, Password Not Required',
			'560'=>"Enabled – PASSWD_NOTREQD – LOCKOUT",
			'640'=>"Enabled – ENCRYPTED_TEXT_PWD_ALLOWED",
			'2048'=>"INTERDOMAIN_TRUST_ACCOUNT",
			'2080'=>"INTERDOMAIN_TRUST_ACCOUNT – PASSWD_NOTREQD",
			'4096'=>"WORKSTATION_TRUST_ACCOUNT",
			'8192'=>"SERVER_TRUST_ACCOUNT",
			'66048'=>"Enabled, Password Doesn't Expire",
			'66050'=>"Disabled, Password Doesn't Expire",
			'66064'=>"Enabled – DONT_EXPIRE_PASSWORD – LOCKOUT",
			'66066'=>"ACCOUNTDISABLE – DONT_EXPIRE_PASSWORD – LOCKOUT",
			'66080'=>"Enabled, Password Doesn't Expire & Not Required",
			'66082'=>"Disabled, Password Doesn't Expire & Not Required",
			'66176'=>"Enabled – DONT_EXPIRE_PASSWORD – ENCRYPTED_TEXT_PWD_ALLOWED",
			'131584'=>"Enabled – MNS_LOGON_ACCOUNT",
			'131586'=>"ACCOUNTDISABLE – MNS_LOGON_ACCOUNT",
			'131600'=>"Enabled – MNS_LOGON_ACCOUNT – LOCKOUT",
			'197120'=>"Enabled – MNS_LOGON_ACCOUNT – DONT_EXPIRE_PASSWORD",
			'262656'=>'Enabled, Smartcard Required',
			'262658'=>'Disabled, Smartcard Required',
			'262688'=>'Enabled, Smartcard Required, Password Not Required',
			'262690'=>'Disabled, Smartcard Required, Password Not Required',
			'328192'=>"Enabled, Smartcard Required, Password Doesn't Expire",
			'328194'=>"Disabled, Smartcard Required, Password Doesn't Expire",
			'328224'=>"Enabled, Smartcard Required, Password Doesn't Expire & Not Required",
			'328226'=>"Disabled, Smartcard Required, Password Doesn't Expire & Not Require",
			'532480'=>"SERVER_TRUST_ACCOUNT – TRUSTED_FOR_DELEGATION (Domain Controller)",
			'1049088'=>"Enabled – NOT_DELEGATED",
			'1049090'=>"ACCOUNTDISABLE – NOT_DELEGATED",
			'2097664'=>"Enabled – USE_DES_KEY_ONLY",
			'2687488'=>"Enabled – DONT_EXPIRE_PASSWORD – TRUSTED_FOR_DELEGATION – USE_DES_KEY_ONLY",
			'4194816'=>"Enabled – DONT_REQ_PREAUTH "
		);
		$arrStatus = array(
			'512'=>'Enabled',
			'514'=>'Disabled',
			'528'=>"Enabled",
			'530'=>"Disabled",
			'544'=>'Enabled',
			'546'=>'Disabled',
			'560'=>"Enabled",
			'640'=>"Enabled",
			'2048'=>"Disabled",
			'2080'=>"Disabled",
			'4096'=>"Disabled",
			'8192'=>"Disabled",
			'66048'=>'Enabled',
			'66050'=>'Disabled',
			'66064'=>"Enabled",
			'66066'=>"Disabled",
			'66080'=>'Enabled',
			'66082'=>'Disabled',
			'66176'=>"Enabled",
			'131584'=>"Enabled",
			'131586'=>"Disabled",
			'131600'=>"Enabled",
			'197120'=>"Enabled",
			'262656'=>'Enabled',
			'262658'=>'Disabled',
			'262688'=>'Enabled',
			'262690'=>'Disabled',
			'328192'=>'Enabled',
			'328194'=>'Disabled',
			'328224'=>'Enabled',
			'328226'=>'Disabled',
			'532480'=>"Disabled",
			'1049088'=>"Enabled",
			'1049090'=>"Disabled",
			'2097664'=>"Enabled",
			'2687488'=>"Enabled",
			'4194816'=>"Enabled"
		);
		foreach ($this->_hosts as $host) {
			$this->_ldap_conn = ldap_connect($host, $this->_ports[0]);
			if ($this->_ldap_conn){
				break;
			}
			else{
				log_message('info', 'Authloginad: Error connecting to AD server ' . $host);
				show_error('Authloginad: Error connecting to AD server ' . $host);
			}
		}
		ldap_set_option($this->_ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($this->_ldap_conn, LDAP_OPT_REFERRALS, 0);
		ldap_set_option($this->_ldap_conn, LDAP_OPT_SIZELIMIT, 9000); 

	  	$dn = 'ou=,dc=,dc=';
		// $filter    = "(&(objectClass=user)(objectCategory=person)(sn=*))";
		$filter = '(&(objectCategory=person)(samaccountname=*))';
		// $filter='objectClass=user';
		$justthese = array();
		// enable pagination with a page size of 100.
		$pageSize = 100;
		$cookie = '';
		$bind = ldap_bind($this->_ldap_conn, $this->_user, $this->_pass);
		$attr[] = "givenname";
		$attr[] = "samaccountname";
		$attr[] = "useraccountcontrol";
		$attr[] = "sn";

    	if ($bind) {
		  	do {
				ldap_control_paged_result($this->_ldap_conn, $pageSize, true, $cookie);
				$additional = "OU=," . $OU;
				$result  = @ldap_search($this->_ldap_conn, $this->_base_dn, $filter, $attr);
				$entries = ldap_get_entries($this->_ldap_conn, $result);
				// $dn = "CN=Damianus Haryusutanto,OU=PRODUCT=ENGINEERING,OU=PRODUCT PROCESS ENGINEERING B,OU=KEMAS,DC=kemas,DC=co,DC=id";
				if(!empty($entries)){
					for ($i = 0; $i < $entries["count"]; $i++) {
						$fullname = "";
						$arrDN = $this->extractDN($entries[$i]["dn"]);
						foreach ($arrDN as $key => $value) {
							${$key} = $value;
						}
						$arrUser = array(
										'USR_LOGINS' => $entries[$i]["samaccountname"][0],
										'USR_STATUS' => $arrStatus[$entries[$i]["useraccountcontrol"][0]],
									);

						if(isset($CN)){
							$arrUser = array_merge($arrUser, array('USR_FULNAM' => $CN));
						}
						if(isset($UO)){
							$arrUser = array_merge($arrUser, array('USR_UNIORG' => $UO));
						}
						$arrUsersAd[] = $arrUser;
					}
		    	}
		      	ldap_control_paged_result_response($this->_ldap_conn, $result, $cookie);
		  	} while($cookie !== null && $cookie != '');
		}
		switch ($returnnya) {
			case 'json':
				$json = "[";

				$ro=false;
				for($e=0;$e<count($arrUsersAd);$e++){
					if($ro) $json .= ",";
					$json .= "{";
					$rc=false;
					foreach ($arrUsersAd[$e] as $key => $value) {
						if($rc) $json .= ",";
						$json .= '"' . $key . '":"'.$value.'"' ;
						$rc=true;
					}
					$json .= "}";
					$ro=true;
				}
				$json .= "]";
				$return = $json;
				break;
			default:
				$return = $arrUsersAd;
				break;
		}
    	if($hasilnya){
    		return $return;	
    	}else{
    		echo $return;	
    	}
  	}
}
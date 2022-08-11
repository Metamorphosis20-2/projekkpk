<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * This file is part of Authloginad.

    Authloginad is free software: you can redistribute it and/or modify
    it under the terms of the GNU Lesser General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Authloginad is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Authloginad.  If not, see <http://www.gnu.org/licenses/>.
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

// hosts: an array of AD servers (usually domain controllers) to use for authentication		
$config['ad_hosts'] = array('ldap://192.168.1.1');
// $config['ad_hosts'] = array('corp.detanto.net');

// ports: an array containing the remote port number to connect to (default is 389) 
$config['ad_ports'] = array(389);

// base_dn: the base DN of your Active Directory domain
// $config['base_dn'] = 'dc=corp,dc=detanto, dc=net';
$config['base_dn'] = 'dc=kpk,dc=go, dc=id';

// ad_domain: the domain name to prepend (versions prior to Windows 2000) or append (Windows 2000 and up)
$config['ad_domain'] = 'kpk';

// start_ou: the DN of the OU you want to start searching from. Leave empty to start from domain root.
// examples: 'OU=Users' or 'OU=Corporate,OU=Users'
$config['start_ou'] = '';

// proxy_user: the (distinguished) username of the user that does the querying (AD generally does not allow anonymous binds) 
// $config['ad_user'] = 'Administrator';
$config['ad_user'] = 'test3';
// $config['ad_user'] = 'detanto';

// proxy pass: the password for the proxy_user
// $config['ad_pass'] = '1Qa2ws3ed';
$config['ad_pass'] = 'pass54321@3';
/* End of file auth_ad.php */
/* Location: ./application/config/auth_ad.php */

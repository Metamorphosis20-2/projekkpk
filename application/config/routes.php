<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'welcome';
$route['404_override'] = 'page404';
$route['default_controller'] = 'login';
$route['login/(:num)?$'] = "login/failed/$1";
//==== LOG
$route['master/log/([\w\-]+)?$'] = "master/userlog/index/$1";
// ==================================================== REFERENSI
$route['add/referensi/([\w\-]+)?$'] = "master/referensi/show/add/$1";
$route['edit/referensi'] = "master/referensi/show/edit";
$route['view/referensi'] = "master/referensi/show/view";
$route['master/referensi/([\w\-]+)?$'] = "master/referensi/index/$1";
$route['add/([\w\-]+)/([\w\-]+)/([\w\-]+)?$'] = "$1/$2/$3show/add/";
$route['add/([\w\-]+)?$'] = "$1/show/add/";
$route['add/([\w\-]+)/([\w\-]+)?$'] = "$1/$2/show/add/";
// $route['proses/([\w\-]+)/([\w\-]+)?$'] = "$1/$2/show/proses";

$route['edit/([\w\-]+)?$'] = "$1/show/edit";
$route['view/([\w\-]+)?$'] = "$1/show/view";
$route['save/([\w\-]+)?$'] = "$1/save";
$route['delete/([\w\-]+)?$'] = "$1/delete";
$route['approve/([\w\-]+)?$'] = "$1/show/approve";

$route['master/menuuser'] = "master/user/listusermenu";
$route['([\w\-]+)/master/menuuser'] = "master/user/editUsermenu/$1";

$route['edit/([\w\-]+)/([\w\-]+)?$'] = "$1/$2/show/edit";
$route['view/([\w\-]+)/([\w\-]+)?$'] = "$1/$2/show/view";
$route['save/([\w\-]+)/([\w\-]+)?$'] = "$1/$2/save";
$route['delete/([\w\-]+)/([\w\-]+)?$'] = "$1/$2/delete";
$route['approve/([\w\-]+)/([\w\-]+)?$'] = "$1/$2/show/approve";

$route['analysis/([\w\-]+)?$'] = "analysis/kuesioner/index/$1";
$route['laporan/([\w\-]+)?$'] = "analysis/kuesioner/index/$1";

$route['translate_uri_dashes'] = FALSE;

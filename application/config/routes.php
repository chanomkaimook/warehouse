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
|	http://codeigniter.com/user_guide/general/routing.html
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
//$route['mod_reporter/ctl_report/timereport/:num'] = false;
$route['default_controller'] = 'mod_retaildashboard/ctl_dashboard/dashboard';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// $route['api/staff'] = 'mod_staff/authdatastaff/index';
$route['dash/dashboard'] = 'mod_retaildashboard/ctl_dashboard/dashboard';
$route['api/staff'] = 'mod_staff/authdatastaff/user';
$route['api/staff/add'] = 'mod_staff/authdatastaff/user';
// $route['api/staff/edit'] = 'mod_staff/authdatastaff/user';
// $route['api/staff/delete'] = 'mod_staff/authdatastaff/user';
$route['api/staff/edit/(:any)'] = 'mod_staff/authdatastaff/user/$1';
$route['api/staff/delete/(:any)'] = 'mod_staff/authdatastaff/user/$1';

//  เพราะไม่สามารถจัดการกับ error 405 not allow method ได้ ต้องลบ wabDAV จาก server ซึ่งเสี่ยงที่โดเมนตัวอื่นจะพังตาม
//  Ref:
//  https://stackoverflow.com/questions/29442947/web-api-put-returning-404
// $route['api/staff/edit/(:any)'] = 'mod_staff/authdatastaff/user/$1';

$route['api/token'] = 'mod_staff/authdatastaff/token';
$route['api/verify'] = 'mod_staff/authdatastaff/tokenverify';

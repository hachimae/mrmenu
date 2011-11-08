<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "welcome";
$route['404_override'] = '';

// application routing
$route['app'] = "app/dash";

// rewrite authen url
$route['app/login'] = "app/auth/login";
$route['app/logout'] = "app/auth/logout";
$route['app/register'] = "app/auth/register";
$route['app/send_again'] = "app/auth/send_again";
$route['app/activate'] = "app/auth/activate";
$route['app/forgot_password'] = "app/auth/forgot_password";
$route['app/reset_password'] = "app/auth/reset_password";
$route['app/change_password'] = "app/auth/change_password";
$route['app/change_email'] = "app/auth/change_email";
$route['app/reset_email'] = "app/auth/reset_email";
$route['app/unregister'] = "app/auth/unregister";

// rewrite api
$route['api/restaurant/(:num)/table/(:num)'] = "api/mrmenu/restaurant/id/$1/table/$2/action/get-date";
$route['api/restaurant/(:num)'] = "api/mrmenu/restaurant/id/$1/action/get-date";
$route['api/restaurant/(:num)/table/(:num)/last-update'] = "api/mrmenu/restaurant/id/$1/table/$2/action/last-update";
$route['api/restaurant/(:num)/last-update'] = "api/mrmenu/restaurant/id/$1/action/last-update";
$route['api/order'] = "api/mrmenu/order";
$route['api/order/(:num)'] = "api/mrmenu/order/transaction/$1";
$route['api/dish/(:num)/(:num)'] = "api/mrmenu/dish/transaction/$1/dish/$2";

/* End of file routes.php */
/* Location: ./application/config/routes.php */
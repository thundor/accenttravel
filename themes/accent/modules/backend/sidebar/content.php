<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadLang('backend/menu'); ?>
<?php $user = $this->_ci->user; ?>
<?php
$current_route = $this->_ci->uri->uri_string();
$get = $this->_ci->input->get();
$menus = array(
  'key' => 'side_menu',
  'items' => array(
    'orders' => array(
      'route' => 'backend/trip/orders',
      'allow' => $user->canAny('backend-trip-orders-access','backend-trip-orders-own-access'),
      'items' => array(
        'view' => array(
          'title' => 'Vizualizare rezervare',
          'icon' => '<i class="fa fa-eye"></i>',
          'active' => true,
          'allow' => $current_route == 'backend/trip/orders/view',
        ),
        'add' => array(
          'title' => 'Adaugare rezervare',
          'icon' => '<i class="fa fa-plus"></i>',
          'active' => true,
          'allow' => $current_route == 'backend/trip/orders/add',
        ),
        'edit' => array(
          'title' => 'Editare rezervare',
          'icon' => '<i class="fa fa-pencil"></i>',
          'active' => true,
          'allow' => $current_route == 'backend/trip/orders/edit',
        ),
      ),
    ),
    'customers' => array(
      'route' => 'backend/accounts/customer',
      'allow' => $user->canAny('backend-accounts-customer-access','backend-accounts-customer-own-access'),
      'items' => array(
        'view' => array(
          'title' => 'Vizualizare client',
          'icon' => '<i class="fa fa-eye"></i>',
          'active' => true,
          'allow' => $current_route == 'backend/accounts/customer/view',
        ),
        'add' => array(
          'title' => 'Adaugare client',
          'icon' => '<i class="fa fa-plus"></i>',
          'active' => true,
          'allow' => $current_route == 'backend/accounts/customer/add',
        ),
        'edit' => array(
          'title' => 'Editare client',
          'icon' => '<i class="fa fa-pencil"></i>',
          'active' => true,
          'allow' => $current_route == 'backend/accounts/customer/edit',
        ),
      ),
    ),
    'ticketing' => array(
      'route' => 'backend/ticketing',
      'allow' => $user->canAny('backend-ticketing-access','backend-accounts-own-access'),
      'items' => array(
        'view' => array(
          'title' => 'Vizualizare tichet',
          'icon' => '<i class="fa fa-eye"></i>',
          'active' => true,
          'allow' => $current_route == 'backend/ticketing/view',
        ),
        'add' => array(
          'title' => 'Adaugare tichet',
          'icon' => '<i class="fa fa-plus"></i>',
          'active' => true,
          'allow' => $current_route == 'backend/ticketing/add',
        ),
        'edit' => array(
          'title' => 'Editare tichet',
          'icon' => '<i class="fa fa-pencil"></i>',
          'active' => true,
          'allow' => $current_route == 'backend/ticketing/edit',
        ),
      ),
    ),
    'config' => array(
      'items' => array(
        'homepage' => array(
          'items' => array(
            'advantages' => array(
              'route' => 'backend/static/advantages',
              'allow' => $user->can('backend-config-access'),
            ),
            'partners' => array(
              'route' => 'backend/static/partners',
              'allow' => $user->can('backend-config-access'),
            ),
            'slider' => array(
              'route' => 'backend/sliders/homepage',
              'allow' => $user->can('backend-config-access'),
            ),
            'heroslider' => array(
              'route' => 'backend/sliders/heroslider',
              'allow' => $user->can('backend-config-access'),
            ),
            'background' => array(
              'route' => 'backend/sliders/homebackground',
              'allow' => $user->can('backend-config-access'),
            ),
            'packages' => array(
              'route' => 'backend/trip/packages',
              'allow' => $user->can('backend-config-access'),
            ),
            'recommended' => array(
              'route' => 'backend/offers/recommended',
              'allow' => $user->can('backend-config-access'),
            ),
            'weekend' => array(
              'route' => 'backend/offers/weekend',
              'allow' => $user->can('backend-config-access'),
            ),
            'popular' => array(
              'route' => 'backend/offers/popular',
              'allow' => $user->can('backend-config-access'),
            ),
            'holiday' => array(
              'route' => 'backend/offers/holiday',
              'allow' => $user->can('backend-config-access'),
            ),
            'contact_mobile_footer' => array(
              'title' => 'Contact Footer Mobile',
              'icon' => '<i class="fa fa-tablet"></i> <i class="fa fa-whatsapp"></i>',
              'route' => 'backend/static/contactMobileFooter',
              'allow' => $user->can('backend-config-access'),
            ),
          ),
        ),
        'flights' => array(
          'items' => array(
            'info' => array(
              'route' => 'backend/trip_flight/flight_info',
              'allow' => $user->can('backend-config-access'),
            ),
            'settings' => array(
              'route' => 'backend/trip_flight/flights_settings',
              'allow' => $user->can('backend-config-access'),
            ),
            'airlines' => array(
              'route' => 'backend/trip_flight/airlines',
              'allow' => $user->can('backend-config-access'),
            ),
          ),
        ),
        'sitemap' => array(
          'title' => 'Sitemap',
          'icon' => '<i class="fa fa-sitemap"></i>',
          'items' => array(
            'settings' => array(
              'title' => 'Setari sitemap',
              'icon' => '<i class="fa fa-cog"></i>',
              'route' => 'backend/sitemap/settings',
              'allow' => $user->can('backend-config-access'),
            ),
            'citybreak' => array(
              'title' => 'City Break & Zboruri',
              'icon' => '<i class="fa fa-building"></i> <i class="fa fa-plane"></i>',
              'route' => 'backend/sitemap/citybreak',
              'allow' => $user->can('backend-config-access'),
            ),
            'hotel' => array(
              'title' => 'Orase hoteluri',
              'icon' => '<i class="fa fa-building"></i>',
              'route' => 'backend/sitemap/hotel',
              'allow' => $user->can('backend-config-access'),
            ),
          ),
        ),
        'cms' => array(
          'items' => array(
            'layouts' => array(
              'route' => 'backend/cms/layouts',
              'allow' => $user->can('backend-cms-layouts-access'),
              'items' => array(
                'view' => array(
                  'title' => 'Vizualizare sablon',
                  'icon' => '<i class="fa fa-eye"></i>',
                  'active' => true,
                  'allow' => $current_route == 'backend/cms/layouts/view',
                ),
                'add' => array(
                  'title' => 'Adaugare sablon',
                  'icon' => '<i class="fa fa-plus"></i>',
                  'active' => true,
                  'allow' => $current_route == 'backend/cms/layouts/add',
                ),
                'edit' => array(
                  'title' => 'Editare sablon',
                  'icon' => '<i class="fa fa-pencil"></i>',
                  'active' => true,
                  'allow' => $current_route == 'backend/cms/layouts/edit',
                ),
              ),
            ),
            'pages' => array(
              'allow' => $user->can('backend-cms-pages-access'),
              'items' => array(
                'view' => array(
                  'title' => 'Vizualizare pagina',
                  'icon' => '<i class="fa fa-eye"></i>',
                  'active' => true,
                  'allow' => $current_route == 'backend/cms/pages/view',
                ),
                'add' => array(
                  'title' => 'Adaugare pagina',
                  'icon' => '<i class="fa fa-plus"></i>',
                  'active' => true,
                  'allow' => $current_route == 'backend/cms/pages/add',
                ),
                'edit' => array(
                  'title' => 'Editare pagina',
                  'icon' => '<i class="fa fa-pencil"></i>',
                  'active' => true,
                  'allow' => $current_route == 'backend/cms/pages/edit',
                ),
                'all' => array(
                  'title' => 'Toate paginile',
                  'icon' => '<i class="fa fa-file-text-o"></i>',
                  'route' => 'backend/cms/pages',
                ),
                'static' => array(
                  'title' => 'Statice',
                  'icon' => '<i class="fa fa-file-code-o"></i>',
                  'route' => 'backend/cms/pages/static',
                ),
                'dynamic' => array(
                  'title' => 'Dinamice',
                  'icon' => '<i class="fa fa-file-archive-o"></i>',
                  'route' => 'backend/cms/pages/dynamic',
                ),
                'default' => array(
                  'title' => 'Implicite',
                  'icon' => '<i class="fa fa-file-o"></i>',
                  'route' => 'backend/cms/pages/default',
                ),
              ),
            ),
            'menus' => array(
              'allow' => $user->can('backend-config-access'),
              'title' => 'Meniuri',
              'icon' => '<i class="fa fa-list"></i>',
              'items' => array(
                // 'new' => array(
                  // 'title' => 'Meniu nou',
                  // 'icon' => '<i class="fa fa-star-o"></i>',
                  // 'route' => 'backend/cms/menus',
                // ),
                'navigatie_principal' => array(
                  'title' => 'Navigatie principal',
                  'icon' => '<i class="fa fa-header"></i>',
                  'route' => 'backend/cms/menus/navigatie_principal',
                ),
                'navigatie_principal_dreapta' => array(
                  'title' => 'Navigatie principal 2',
                  'icon' => '<i class="fa fa-header"></i>',
                  'route' => 'backend/cms/menus/navigatie_principal_dreapta',
                ),
                /* 'info_utile_coloana_1' => array(
                  'title' => 'Info utile col 1',
                  'icon' => '<i class="fa fa-info"></i>',
                  'route' => 'backend/cms/menus/info_utile_coloana_1',
                ),
                'info_utile_coloana_2' => array(
                  'title' => 'Info utile col 2',
                  'icon' => '<i class="fa fa-info"></i>',
                  'route' => 'backend/cms/menus/info_utile_coloana_2',
                ), */
                'footer_coloana_1' => array(
                  'title' => 'Footer col 1',
                  'icon' => '<i class="fa fa-terminal"></i>',
                  'route' => 'backend/cms/menus/footer_coloana_1',
                ),
                'footer_coloana_2' => array(
                  'title' => 'Footer col 2',
                  'icon' => '<i class="fa fa-terminal"></i>',
                  'route' => 'backend/cms/menus/footer_coloana_2',
                ),
              ),
            ),
			'resources' => array(
			  'allow' => $user->can('backend-cms-resources-access'),
			  'title' => 'Resurse',
			  'icon' => '<i class="fa fa-files-o"></i>',
			  'route' => 'backend/cms/resources',
			),
          ),
        ),
        'payment' => array(
          'items' => array(
            'agency' => array(
              'route' => 'backend/payment_methods/agency',
              'allow' => $user->can('backend-config-access'),
            ),
            'bank' => array(
              'route' => 'backend/payment_methods/bank',
              'allow' => $user->can('backend-config-access'),
            ),
            'online' => array(
              'items' => array(
                'settings' => array(
                  'route' => 'backend/payment_methods/online',
                  'allow' => $user->can('backend-config-access'),
                ),
                'payu' => array(
                  'icon' => '<img src="' . $this->theme_url . '/assets/images/payment-gateways/PayU-icon.png" style="max-height:20px;max-width:50px;"></i>',
                  'route' => 'backend/payment_gateways/payu',
                  'allow' => $user->can('backend-config-access'),
                ),
                'pay24' => array(
                  'icon' => '<img src="' . $this->theme_url . '/assets/images/payment-gateways/Pay24-icon.png" style="max-height:20px;max-width:50px;"></i>',
                  'route' => 'backend/payment_gateways/pay24',
                  'allow' => $user->can('backend-config-access'),
                ),
              )
            ),
          ),
        ),
        'api' => array(
          'items' => array(
            // 'trip' => array(
              // 'route' => 'backend/trip/settings',
              // 'allow' => $user->can('backend-config-access'),
            // ),
            // 'google_maps' => array(
              // 'route' => 'backend/plugins/google/maps',
              // 'allow' => $user->can('backend-config-access'),
            // ),
            'facebook_login' => array(
              'route' => 'backend/social_networks/facebook',
              'allow' => $user->can('backend-config-access'),
            ),
            'trip_settings' => array(
              'route' => 'backend/trip/settings',
              'title' => 'Configurari TRIP',
              'icon' => '<i class="fa fa-certificate"></i>',
              'allow' => $user->can('backend-config-access'),
            ),
          ),
        ),
        'general_settings' => array(
          'route' => 'backend/general/settings',
          'title' => 'Configurari Generale',
          'icon' => '<i class="fa fa-cogs"></i>',
          'allow' => $user->can('backend-config-access'),
        ),
        'email_settings' => array(
          'route' => 'backend/general/email',
          'title' => 'Email / Office 365',
          'icon' => '<i class="fa fa-envelope"></i>',
          'allow' => $user->can('backend-config-access'),
        ),
      ),
    ),
    'users' => array(
      'items' => array(
        'all' => array(
          'route' => 'backend/accounts/admin',
          'items' => array(
            'view' => array(
              'title' => 'Vizualizare utilizator',
              'icon' => '<i class="fa fa-eye"></i>',
              'active' => true,
              'allow' => $current_route == 'backend/accounts/admin/view',
            ),
            'add' => array(
              'title' => 'Adaugare utilizator',
              'icon' => '<i class="fa fa-plus"></i>',
              'active' => true,
              'allow' => $current_route == 'backend/accounts/admin/add',
            ),
            'edit' => array(
              'title' => 'Editare utilizator',
              'icon' => '<i class="fa fa-pencil"></i>',
              'active' => true,
              'allow' => $current_route == 'backend/accounts/admin/edit',
            ),
          ),
        ),
        'select' => array(
          'items' => array(),
        ),
        'permissions' => array(
          'route' => 'backend/accounts/roles',
          'allow' => $user->canAnyUnder('backend-accounts-roles-access'),
        ),
      ),
    ),
    'newsletter' => array(
      'title' => 'Abonati newsletter',
      'icon' => '<i class="fa fa-list-alt"></i>',
      'route' => 'backend/newsletter/subscribers',
	  'allow' => $user->can('backend-config-access'),
    ),
    'notifications' => array(
      'title' => 'Alerte clienti',
      'icon' => '<i class="fa fa-bell"></i>',
      'route' => 'backend/trip/notifications',
	  'allow' => $user->can('backend-config-access'),
    ),
    'requestoffer' => array(
      'title' => 'Cereri oferta',
      'icon' => '<i class="fa fa-bell-o"></i>',
      'route' => 'backend/trip/requestoffer',
	  'allow' => $user->can('backend-config-access'),
    ),
    'coupons' => array(
      'title' => 'Cupoane',
      'icon' => '<i class="fa fa-credit-card"></i>',
      'route' => 'backend/trip/coupons',
	  'allow' => $user->can('backend-config-access'),
    ),
    'blockemails' => array(
      'title' => 'Blocare email',
      'icon' => '<i class="fa fa-envelope"></i>',
      'route' => 'backend/trip/blockemails',
	  'allow' => $user->can('backend-trip-blockemails-access'),
    ),
    'trip_discounts' => array(
      'title' => 'Discount',
      'icon' => '<i class="fa fa-euro"></i>',
      'items' => array(
        'view' => array(
          'title' => 'Vizualizare discount',
          'icon' => '<i class="fa fa-eye"></i>',
          'active' => true,
          'allow' => $current_route == 'backend/trip/discounts/view',
        ),
        'add' => array(
          'title' => 'Adaugare discount',
          'icon' => '<i class="fa fa-plus"></i>',
          'active' => true,
          'allow' => $current_route == 'backend/trip/discounts/add',
        ),
        'edit' => array(
          'title' => 'Editare discount',
          'icon' => '<i class="fa fa-pencil"></i>',
          'active' => true,
          'allow' => $current_route == 'backend/trip/discounts/edit',
        ),
        'general' => array(
          'route' => 'backend/trip/discounts/general',
          'title' => 'Discount general',
          'icon' => '<i class="fa fa-cog"></i>',
          'allow' => $user->can('backend-config-access'),
        ),
        'packages' => array(
          'route' => 'backend/trip/discounts/package',
          'title' => 'Discount vacante',
          'icon' => '<i class="fa fa-sitemap"></i>',
          'allow' => $user->can('backend-config-access'),
        ),
      ),
    ),
    'trip_reports' => array(
      'title' => 'Rapoarte',
      'icon' => '<i class="fa fa-area-chart"></i>',
      'items' => array(
        'view' => array(
			'route' => 'backend/trip/orders/log',
			'title' => 'Log flights',
			'icon' => '<i class="fa fa-eye"></i>',
			'allow' => $user->can('backend-access'),
        ),
      ),
    ),
  ),
);

$ip = '';
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
	$ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
	$ip = $_SERVER['REMOTE_ADDR'];
}
if(true || $ip == '82.76.174.47'){
	$menus['items']['travelfuse'] = [
		'title' => 'TravelFuse',
		'icon' => '<i class="fa fa-binoculars"></i>',
		'items' => array(
			'countries' => array(
				'route' => 'backend/travelfuse/travelfuse_countries',
				'title' => 'Tari',
				'icon' => '<i class="fa fa-globe"></i>',
				'allow' => $user->can('backend-access'),
			),
			'cities' => array(
				'route' => 'backend/travelfuse/travelfuse_cities',
				'title' => 'Destinatii',
				'icon' => '<i class="fa fa-globe"></i>',
				'allow' => $user->can('backend-access'),
			),
			'hotels' => array(
				'route' => 'backend/travelfuse/travelfuse_hotels',
				'title' => 'Hoteluri',
				'icon' => '<i class="fa fa-building"></i>',
				'allow' => $user->can('backend-access'),
			),
			'facilities' => array(
				'route' => 'backend/travelfuse/travelfuse_facilities',
				'title' => 'Facilitati',
				'icon' => '<i class="fa fa-sitemap"></i>',
				'allow' => $user->can('backend-access'),
			),
			'tours' => array(
				'route' => 'backend/travelfuse/travelfuse_tours',
				'title' => 'Circuite',
				'icon' => '<i class="fa fa-random"></i>',
				'allow' => $user->can('backend-access'),
			),
			'destinations' => array(
				'route' => 'backend/travelfuse/travelfuse_destinations',
				'title' => 'Destinatii Circuit',
				'icon' => '<i class="fa fa-code-fork"></i>',
				'allow' => $user->can('backend-access'),
			),
		),
	];
}
$this->_ci->load->model('Permission_model');
$roles = $this->_ci->Permission_model->roles;
$access_accounts_admin = $user->canAnyUnder('backend-accounts-admin-access') || $user->canAnyUnder('backend-accounts-admin-own-access');
$menus['items']['users']['items']['all']['allow'] = $access_accounts_admin;
if($access_accounts_admin){
  foreach($roles as $role){ 
    $menus['items']['users']['items']['select']['items'][] = array(
      'icon' => lang('menu_icon_' . $role . '/html'),
      'route' => 'backend/accounts/admin',
      'allow' => $user->canAny('backend-accounts-admin-access-' . $role, 'backend-accounts-admin-own-access-' . $role),
      'get' => array('role' => $role),
      'title' => lang('role/' . $role),
    );
  }
}
$menus['items']['users']['items']['select']['allow'] = !empty( array_filter($menus['items']['users']['items']['select']['items'], function($a){ return !empty($a['allow']); }));

if($user->can('backend-config-access')){
  $this->_ci->load->model('Options_model');
  $frontend_menu_path = &$menus['items']['config']['items']['cms']['items']['menus']['items'];
  $frontend_menu_items = $this->_ci->Options_model->getKeys('trip_cms_menu');
  foreach($frontend_menu_items as $frontend_menu_item){
    if(!isset($frontend_menu_path[$frontend_menu_item])){
      $frontend_menu_path[$frontend_menu_item] = array(
        'icon' => '<i class="fa fa-minus"></i>',
        'title' => $frontend_menu_item,
        'route' => 'backend/cms/menus/' . $frontend_menu_item,
      );
    }
  }
}


$this->_ci->load->library('BackendMenuItem',$menus,'backend_menu_items');
$this->_ci->backend_menu_items->render();
?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>
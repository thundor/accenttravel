<?php
if (!defined('BASEPATH')) {
  exit('No direct script access allowed');
}

/*
| Driver: o365 (Microsoft Graph OAuth2) | smtp (Office 365 SMTP / relay)
| Secretele pot fi în backend (ac_option email_settings), email.local.php sau env.
*/
$config['email_driver'] = 'o365';

$config['email_dev_to'] = 'tudor.chirvasa@lisal.ro';

$config['email_default_from'] = 'vanzari@accenttravel.ro';
$config['email_default_from_name'] = 'Accent Travel & Events';

$config['email_default_bcc'] = array(
  'alexandra.oprea@lisal.ro',
);

/*
| Mapare from_email => mailbox Graph (user principal name).
*/
$config['email_senders'] = array(
  'vanzari@accenttravel.ro' => 'vanzari@accenttravel.ro',
  'marketing@accenttravel.ro' => 'marketing@accenttravel.ro',
  '24pay@accenttravel.ro' => '24pay@accenttravel.ro',
);

$config['o365_tenant_id'] = getenv('O365_TENANT_ID') ?: '';
$config['o365_client_id'] = getenv('O365_CLIENT_ID') ?: '';
$config['o365_client_secret'] = getenv('O365_CLIENT_SECRET') ?: '';
$config['o365_token_cache'] = APPPATH . 'cache/o365_token.json';
$config['o365_graph_scope'] = 'https://graph.microsoft.com/.default';

/*
| SMTP (email_driver = smtp)
| - Autentificat: smtp.office365.com:587 + tls + user/pass
| - Relay: *.mail.protection.outlook.com:25 + crypto none (fără user/pass)
*/
$config['smtp_host'] = '';
$config['smtp_port'] = 587;
$config['smtp_crypto'] = 'tls';
$config['smtp_user'] = '';
$config['smtp_pass'] = '';
$config['smtp_accounts'] = array();

if (is_file(APPPATH . 'config/email.local.php')) {
  include APPPATH . 'config/email.local.php';
}

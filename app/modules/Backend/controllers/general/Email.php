<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Email extends MX_Controller {
  protected $settings_defaults = array(
    'email_driver' => 'o365',
    'o365_tenant_id' => '',
    'o365_client_id' => '',
    'o365_client_secret' => '',
    'smtp_host' => '',
    'smtp_port' => '587',
    'smtp_crypto' => 'tls',
    'smtp_user' => '',
    'smtp_pass' => '',
    'email_default_from' => 'vanzari@accenttravel.ro',
    'email_default_from_name' => 'Accent Travel & Events',
    'email_dev_to' => 'tudor.chirvasa@lisal.ro',
    'email_default_bcc' => 'alexandra.oprea@lisal.ro',
  );

  function __construct() {
    parent::__construct();
  }

  public function index() {
    if (!$this->user->can('backend-access')) {
      $this->redirect('backend', 'Acces restricționat', 'error');
    }
    if (!$this->user->can('backend-config-access')) {
      $this->redirect('backend', 'Acces restricționat', 'error');
    }

    $settings = $this->get_settings();
    $settings['has_client_secret'] = strlen((string) $settings['o365_client_secret']) > 0;
    $settings['has_smtp_pass'] = strlen((string) $settings['smtp_pass']) > 0;
    $settings['o365_configured'] = !empty($settings['o365_tenant_id'])
      && !empty($settings['o365_client_id'])
      && $settings['has_client_secret'];
    $settings['smtp_configured'] = !empty($settings['smtp_host']) && !empty($settings['smtp_port']);

    $this->data = $settings;
    $this->theme->view('backend/general/email', $this->data);
  }

  public function save() {
    if (!$this->user->can('backend-access') || !$this->user->can('backend-config-access') || !$this->user->can('backend-config-save')) {
      $this->outputError('Acces restricționat');
    }

    $this->load->library('form_validation');
    $this->form_validation->set_rules('email_driver', 'Metodă trimitere', 'trim|required|in_list[o365,smtp]');
    $this->form_validation->set_rules('o365_tenant_id', 'Tenant ID', 'trim');
    $this->form_validation->set_rules('o365_client_id', 'Client ID', 'trim');
    $this->form_validation->set_rules('o365_client_secret', 'Client Secret', 'trim');
    $this->form_validation->set_rules('smtp_host', 'Host SMTP', 'trim');
    $this->form_validation->set_rules('smtp_port', 'Port SMTP', 'trim|integer');
    $this->form_validation->set_rules('smtp_crypto', 'Criptare SMTP', 'trim|in_list[none,tls,ssl,]');
    $this->form_validation->set_rules('smtp_user', 'Utilizator SMTP', 'trim');
    $this->form_validation->set_rules('smtp_pass', 'Parolă SMTP', 'trim');
    $this->form_validation->set_rules('email_default_from', 'Email expeditor', 'trim|valid_email');
    $this->form_validation->set_rules('email_dev_to', 'Email test', 'trim|valid_email');

    if ($this->form_validation->run() == FALSE) {
      $this->data['errors'] = $this->form_validation->error_array();
      $this->outputError($this->form_validation->error_string());
    }

    $current = $this->get_settings();
    $data = array();
    $data['email_driver'] = trim($this->input->post('email_driver')) ?: 'o365';
    $data['o365_tenant_id'] = trim($this->input->post('o365_tenant_id'));
    $data['o365_client_id'] = trim($this->input->post('o365_client_id'));
    $data['smtp_host'] = trim($this->input->post('smtp_host'));
    $data['smtp_port'] = trim($this->input->post('smtp_port'));
    $crypto = trim((string) $this->input->post('smtp_crypto'));
    $data['smtp_crypto'] = ($crypto === 'none' || $crypto === '') ? 'none' : $crypto;
    $data['smtp_user'] = trim($this->input->post('smtp_user'));
    $data['email_default_from'] = trim($this->input->post('email_default_from'));
    $data['email_default_from_name'] = trim($this->input->post('email_default_from_name'));
    $data['email_dev_to'] = trim($this->input->post('email_dev_to'));
    $data['email_default_bcc'] = trim($this->input->post('email_default_bcc'));

    $new_secret = trim((string) $this->input->post('o365_client_secret'));
    $data['o365_client_secret'] = strlen($new_secret) ? $new_secret : $current['o365_client_secret'];

    $new_smtp_pass = trim((string) $this->input->post('smtp_pass'));
    $data['smtp_pass'] = strlen($new_smtp_pass) ? $new_smtp_pass : $current['smtp_pass'];

    foreach ($data as $k => $v) {
      if (!strlen((string) $v)) {
        $data[$k] = null;
      }
    }

    $this->load->model('Options_model');
    $this->Options_model->set('email_settings', $data);

    $this->load->library('o365_mailer');
    $this->o365_mailer->clear_token_cache();

    $this->addMessage('Setările de email au fost salvate.', 'success');
    $this->output();
  }

  public function test_connection() {
    if (!$this->user->can('backend-access') || !$this->user->can('backend-config-access') || !$this->user->can('backend-config-save')) {
      $this->outputError('Acces restricționat');
    }

    $settings = $this->get_settings();
    $driver = !empty($settings['email_driver']) ? $settings['email_driver'] : 'o365';

    if ($driver === 'smtp') {
      $result = $this->test_smtp_connection($settings);
    } else {
      $this->load->library('o365_mailer');
      $result = $this->o365_mailer->test_connection();
    }

    if (empty($result['ok'])) {
      $this->data['response'] = $result;
      $this->outputError(!empty($result['message']) ? $result['message'] : 'Conexiunea a eșuat.');
    }

    $this->data['response'] = $result;
    $this->addMessage($result['message'], 'success');
    $this->output();
  }

  public function test_send() {
    if (!$this->user->can('backend-access') || !$this->user->can('backend-config-access') || !$this->user->can('backend-config-save')) {
      $this->outputError('Acces restricționat');
    }

    $this->load->library('form_validation');
    $this->form_validation->set_rules('test_to', 'Destinatar test', 'trim|required|valid_email');
    if ($this->form_validation->run() == FALSE) {
      $this->data['errors'] = $this->form_validation->error_array();
      $this->outputError($this->form_validation->error_string());
    }

    $settings = $this->get_settings();
    $to = trim($this->input->post('test_to'));
    $from = !empty($settings['email_default_from']) ? $settings['email_default_from'] : 'vanzari@accenttravel.ro';
    $from_name = !empty($settings['email_default_from_name']) ? $settings['email_default_from_name'] : 'Accent Travel & Events';
    $driver = !empty($settings['email_driver']) ? $settings['email_driver'] : 'o365';

    $mail = array(
      'from_email' => $from,
      'from_name' => $from_name,
      'to' => $to,
      'bcc' => array(),
      'subject' => 'Accent2 — test trimitere email (' . $driver . ')',
      'html' => '<p>Mesaj de test trimis din backend la <strong>' . htmlspecialchars(date('Y-m-d H:i:s')) . '</strong>.</p>'
        . '<p>Dacă ați primit acest email, integrarea funcționează (metodă: ' . htmlspecialchars($driver) . ').</p>',
      'attachments' => array(),
    );

    $this->load->library('o365_mailer');
    $email_config = $this->o365_mailer->get_config();

    if ($driver === 'smtp') {
      $sent = $this->send_test_smtp($email_config, $mail);
      $error = !$sent ? $this->email->print_debugger(array('headers')) : '';
    } else {
      $sent = $this->o365_mailer->send($mail);
      $error = !$sent ? $this->o365_mailer->get_last_error() : '';
    }

    if (!$sent) {
      $this->outputError('Trimiterea a eșuat: ' . ($error ?: 'eroare necunoscută'));
    }

    $this->addMessage('Email de test trimis către ' . $to . '.', 'success');
    $this->output();
  }

  protected function test_smtp_connection($settings) {
    $host = !empty($settings['smtp_host']) ? $settings['smtp_host'] : '';
    $port = !empty($settings['smtp_port']) ? (int) $settings['smtp_port'] : 0;
    if (!$host || !$port) {
      return array(
        'ok' => false,
        'message' => 'Completați hostul și portul SMTP, apoi salvați.',
      );
    }

    $errno = 0;
    $errstr = '';
    $fp = @fsockopen($host, $port, $errno, $errstr, 15);
    if (!$fp) {
      return array(
        'ok' => false,
        'message' => 'Nu s-a putut deschide conexiunea SMTP către ' . $host . ':' . $port . ' — ' . ($errstr ?: ('cod ' . $errno)),
      );
    }

    stream_set_timeout($fp, 10);
    $banner = fgets($fp, 512);
    fclose($fp);

    $banner = trim((string) $banner);
    if ($banner === '' || strpos($banner, '220') !== 0) {
      return array(
        'ok' => false,
        'message' => 'Conexiune TCP reușită, dar răspunsul SMTP este neașteptat: ' . ($banner ?: '(gol)'),
      );
    }

    return array(
      'ok' => true,
      'message' => 'Conexiune SMTP OK către ' . $host . ':' . $port . ' — ' . $banner,
    );
  }

  protected function send_test_smtp($email_config, $mail) {
    $this->load->helper('email');
    $this->load->library('email');

    $from_email = $mail['from_email'];
    $user = '';
    $pass = '';
    if (!empty($email_config['smtp_accounts'][$from_email]['user'])) {
      $user = $email_config['smtp_accounts'][$from_email]['user'];
      $pass = isset($email_config['smtp_accounts'][$from_email]['pass']) ? $email_config['smtp_accounts'][$from_email]['pass'] : '';
    } elseif (!empty($email_config['smtp_user'])) {
      $user = $email_config['smtp_user'];
      $pass = isset($email_config['smtp_pass']) ? $email_config['smtp_pass'] : '';
    }

    $crypto = isset($email_config['smtp_crypto']) ? $email_config['smtp_crypto'] : 'tls';
    if ($crypto === 'none') {
      $crypto = '';
    }

    $config = array(
      'protocol' => 'smtp',
      'mailtype' => 'html',
      'charset' => 'utf-8',
      'newline' => "\r\n",
      'crlf' => "\r\n",
      'useragent' => 'Accent Travel & Events',
      'smtp_host' => !empty($email_config['smtp_host']) ? $email_config['smtp_host'] : '',
      'smtp_port' => !empty($email_config['smtp_port']) ? (int) $email_config['smtp_port'] : 25,
      'smtp_crypto' => $crypto,
      'smtp_user' => $user,
      'smtp_pass' => $pass,
    );

    $this->email->initialize($config);
    $this->email->from($mail['from_email'], $mail['from_name']);
    $this->email->to($mail['to']);
    $this->email->subject($mail['subject']);
    $this->email->message($mail['html']);
    return $this->email->send();
  }

  protected function get_settings() {
    $this->load->model('Options_model');
    $this->config->load('email', true);
    $file_config = $this->config->item('email');
    if (!is_array($file_config)) {
      $file_config = array();
    }

    $defaults = $this->settings_defaults;
    foreach ($defaults as $key => $value) {
      if (isset($file_config[$key]) && $file_config[$key] !== '' && $file_config[$key] !== null) {
        if ($key === 'email_default_bcc' && is_array($file_config[$key])) {
          $defaults[$key] = implode(', ', $file_config[$key]);
        } else {
          $defaults[$key] = (string) $file_config[$key];
        }
      }
    }

    $db = $this->Options_model->get('email_settings', null, $defaults);
    if (!$db || !is_array($db)) {
      $db = $defaults;
    }
    foreach ($defaults as $key => $value) {
      if (!isset($db[$key]) || $db[$key] === null || $db[$key] === '') {
        $db[$key] = $value;
      }
    }
    if ($db['smtp_crypto'] === '' || $db['smtp_crypto'] === null) {
      $db['smtp_crypto'] = 'none';
    }
    return $db;
  }
}

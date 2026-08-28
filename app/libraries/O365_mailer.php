<?php
if (!defined('BASEPATH')) {
  exit('No direct script access allowed');
}

class O365_mailer {

  protected $CI;
  protected $config = array();
  protected $last_error = '';

  public function __construct() {
    $this->CI =& get_instance();
    $this->reload_config();
  }

  public function reload_config() {
    $this->CI->config->load('email', true);
    $config = $this->CI->config->item('email');
    if (!is_array($config)) {
      $config = array();
    }

    $this->CI->load->model('Options_model');
    $db = $this->CI->Options_model->get('email_settings');
    if (is_array($db)) {
      foreach ($db as $key => $value) {
        if ($value === null || $value === '') {
          continue;
        }
        if ($key === 'email_default_bcc' && is_string($value)) {
          $parts = array_filter(array_map('trim', explode(',', $value)));
          $config[$key] = array_values($parts);
        } else {
          $config[$key] = $value;
        }
      }
    }

    if (isset($config['email_default_bcc']) && is_string($config['email_default_bcc'])) {
      $parts = array_filter(array_map('trim', explode(',', $config['email_default_bcc'])));
      $config['email_default_bcc'] = array_values($parts);
    }

    $this->config = $config;
  }

  public function get_config() {
    return $this->config;
  }

  public function get_last_error() {
    return $this->last_error;
  }

  public function is_configured() {
    return !empty($this->config['o365_tenant_id'])
      && !empty($this->config['o365_client_id'])
      && !empty($this->config['o365_client_secret']);
  }

  public function clear_token_cache() {
    $cache_file = $this->token_cache_path();
    if (is_file($cache_file)) {
      @unlink($cache_file);
    }
  }

  public function test_connection() {
    $this->last_error = '';
    $this->reload_config();
    $this->clear_token_cache();

    if (!$this->is_configured()) {
      return array(
        'ok' => false,
        'message' => 'Lipsesc Tenant ID, Client ID sau Client Secret.',
      );
    }

    $token = $this->get_access_token(true);
    if (!$token) {
      return array(
        'ok' => false,
        'message' => $this->last_error ?: 'Nu s-a putut obtine access_token.',
      );
    }

    return array(
      'ok' => true,
      'message' => 'Conexiune OK — access_token obtinut de la Azure AD.',
      'token_preview' => substr($token, 0, 12) . '…',
    );
  }

  public function send($options) {
    $this->last_error = '';
    $this->reload_config();

    if (!$this->is_configured()) {
      $this->last_error = 'Office365: lipsesc tenant_id, client_id sau client_secret.';
      log_message('error', $this->last_error);
      return false;
    }

    $from_email = !empty($options['from_email']) ? $options['from_email'] : $this->config['email_default_from'];
    $from_name = !empty($options['from_name']) ? $options['from_name'] : $this->config['email_default_from_name'];
    $sender = $this->resolve_sender($from_email);

    $token = $this->get_access_token();
    if (!$token) {
      return false;
    }

    $message = array(
      'subject' => (string) ($options['subject'] ?? ''),
      'body' => array(
        'contentType' => 'HTML',
        'content' => (string) ($options['html'] ?? ''),
      ),
      'from' => array(
        'emailAddress' => array(
          'address' => $from_email,
          'name' => $from_name,
        ),
      ),
      'toRecipients' => $this->format_recipients($options['to'] ?? array()),
      'bccRecipients' => $this->format_recipients($options['bcc'] ?? array()),
    );

    $attachments = $this->format_attachments($options['attachments'] ?? array());
    if ($attachments) {
      $message['attachments'] = $attachments;
    }

    $payload = array(
      'message' => $message,
      'saveToSentItems' => true,
    );

    $url = 'https://graph.microsoft.com/v1.0/users/' . rawurlencode($sender) . '/sendMail';
    $response = $this->http_request('POST', $url, $payload, array(
      'Authorization: Bearer ' . $token,
      'Content-Type: application/json',
    ));

    if ($response === true) {
      return true;
    }

    $this->last_error = is_string($response) ? $response : 'Office365: trimitere esuata.';
    log_message('error', 'O365_mailer: ' . $this->last_error);
    return false;
  }

  protected function resolve_sender($from_email) {
    $senders = !empty($this->config['email_senders']) ? $this->config['email_senders'] : array();
    if (!empty($senders[$from_email])) {
      return $senders[$from_email];
    }
    return $from_email;
  }

  protected function token_cache_path() {
    return !empty($this->config['o365_token_cache'])
      ? $this->config['o365_token_cache']
      : APPPATH . 'cache/o365_token.json';
  }

  protected function get_access_token($force_refresh = false) {
    $cache_file = $this->token_cache_path();

    if (!$force_refresh && is_file($cache_file)) {
      $cached = json_decode(file_get_contents($cache_file), true);
      if (!empty($cached['access_token']) && !empty($cached['expires_at']) && $cached['expires_at'] > time() + 60) {
        return $cached['access_token'];
      }
    }

    $tenant = $this->config['o365_tenant_id'];
    $url = 'https://login.microsoftonline.com/' . rawurlencode($tenant) . '/oauth2/v2.0/token';
    $scope = !empty($this->config['o365_graph_scope'])
      ? $this->config['o365_graph_scope']
      : 'https://graph.microsoft.com/.default';

    $body = http_build_query(array(
      'client_id' => $this->config['o365_client_id'],
      'client_secret' => $this->config['o365_client_secret'],
      'scope' => $scope,
      'grant_type' => 'client_credentials',
    ));

    $response = $this->http_request('POST', $url, $body, array(
      'Content-Type: application/x-www-form-urlencoded',
    ), true);

    if (!is_array($response) || empty($response['access_token'])) {
      $this->last_error = 'Office365: nu s-a putut obtine access_token.';
      if (is_string($response)) {
        $this->last_error .= ' ' . $response;
      } elseif (is_array($response) && !empty($response['error_description'])) {
        $this->last_error .= ' ' . $response['error_description'];
      } elseif (is_array($response) && !empty($response['error'])) {
        $this->last_error .= ' ' . (is_string($response['error']) ? $response['error'] : json_encode($response['error']));
      }
      log_message('error', 'O365_mailer: ' . $this->last_error);
      return false;
    }

    $expires_in = !empty($response['expires_in']) ? (int) $response['expires_in'] : 3600;
    $cache_data = array(
      'access_token' => $response['access_token'],
      'expires_at' => time() + $expires_in,
    );

    $cache_dir = dirname($cache_file);
    if (!is_dir($cache_dir)) {
      mkdir($cache_dir, 0777, true);
    }
    file_put_contents($cache_file, json_encode($cache_data));

    return $response['access_token'];
  }

  protected function format_recipients($recipients) {
    $list = array();
    foreach ((array) $recipients as $recipient) {
      if (is_array($recipient)) {
        $address = !empty($recipient['address']) ? $recipient['address'] : '';
        $name = !empty($recipient['name']) ? $recipient['name'] : '';
      } else {
        $address = trim((string) $recipient);
        $name = '';
      }
      if (!$address) {
        continue;
      }
      $entry = array(
        'emailAddress' => array(
          'address' => $address,
        ),
      );
      if ($name) {
        $entry['emailAddress']['name'] = $name;
      }
      $list[] = $entry;
    }
    return $list;
  }

  protected function format_attachments($attachments) {
    $list = array();
    foreach ((array) $attachments as $attachment) {
      if (is_string($attachment)) {
        $file_path = $attachment;
        $file_name = basename($attachment);
      } elseif (is_array($attachment) && !empty($attachment['path'])) {
        $file_path = $attachment['path'];
        $file_name = !empty($attachment['name']) ? $attachment['name'] : basename($file_path);
      } else {
        continue;
      }
      if (!is_file($file_path)) {
        continue;
      }
      $content = file_get_contents($file_path);
      if ($content === false) {
        continue;
      }
      $list[] = array(
        '@odata.type' => '#microsoft.graph.fileAttachment',
        'name' => $file_name,
        'contentBytes' => base64_encode($content),
      );
    }
    return $list;
  }

  protected function http_request($method, $url, $payload = null, $headers = array(), $decode_json = false) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);

    if ($payload !== null) {
      if (is_array($payload)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
      } else {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
      }
    }

    $body = curl_exec($ch);
    $http_code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if ($curl_error) {
      return 'cURL: ' . $curl_error;
    }

    if ($http_code >= 200 && $http_code < 300) {
      if ($decode_json) {
        $decoded = json_decode($body, true);
        return is_array($decoded) ? $decoded : array();
      }
      return true;
    }

    $error_message = 'HTTP ' . $http_code;
    if ($body) {
      $decoded = json_decode($body, true);
      if (!empty($decoded['error_description'])) {
        $error_message .= ': ' . $decoded['error_description'];
      } elseif (!empty($decoded['error']['message'])) {
        $error_message .= ': ' . $decoded['error']['message'];
      } elseif (!empty($decoded['error']) && is_string($decoded['error'])) {
        $error_message .= ': ' . $decoded['error'];
      } else {
        $error_message .= ': ' . substr($body, 0, 500);
      }
    }
    return $error_message;
  }
}

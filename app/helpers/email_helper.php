<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/* TODO Delete function afer dev. */
function test_all() {
  $body = array();
  $to = "lucian.oprea@lisal.ro";
  $bcc = array();
  # $bcc = "lucian.oprea@lisal.ro";
  
  /**
   * Type poate sa fie unul din:
   * account_add, account_pass_reset, ticket_add, ticket_edit, reservation_add, reservation_edit
   */
  
  /* CREATE USER */
  /* ---------------------------------------------------------------------- */
  $body['user_name'] = 'Georgel Georgescu';
  
  /* EDIT PASSWORD USER */
  /* ---------------------------------------------------------------------- */
  $body['user_name'] = 'Georgel Georgescu';
  
  /* TICKET ADD */
  /* ---------------------------------------------------------------------- */
  $body['user_name'] = 'Georgel Georgescu';
  $body['assigned_name'] = 'Georgel Asignatescu';
  $body['ticket_id'] = '0123XXXX';
  $body['ticket_url'] = 'http://accent.lisal.ro/somethickerurl';
  $body['reservation_id'] = '0123RRRRR';
  
  /* TICKET ADD */
  /* ---------------------------------------------------------------------- */
  $body['user_name'] = 'Georgel Georgescu';
  $body['assigned_name'] = 'Georgel Asignatescu';
  $body['ticket_id'] = '0123XXXX';
  $body['ticket_url'] = 'http://accent.lisal.ro/somethickerurl';
  $body['reservation_id'] = '0123RRRRR';
  
  /* RESERVATION ADD */
  /* ---------------------------------------------------------------------- */
  $body['reservation_id'] = '123RRRRR';
  $body['reservation_name'] = 'Cazare Hotel Alpin, Poiana Brasov, 2 adulti + 1 copil, cazare + mic dejun';
  $body['reservation_period'] = 'Perioada: 05-13 ianuarie 2018.';
  $body['reservation_cost'] = 'Total servicii: 3.259 Lei';
  
  $body['reservation_people'] = array(
    array('name' => 'Turist Turitescu1', 'birthdate' => '23.04.1976', 'cnp' => '1760423430058'),
    array('name' => 'Turist Turitescu1', 'birthdate' => '23.04.1976', 'cnp' => '1760423430058'),
  );
  
  /* RESERVATION EDIT */
  /* ---------------------------------------------------------------------- */
  $body['reservation_id'] = '123RRRRR';
  $body['reservation_name'] = 'Cazare Hotel Alpin, Poiana Brasov, 2 adulti + 1 copil, cazare + mic dejun';
  $body['reservation_period'] = 'Perioada: 05-13 ianuarie 2018.';
  $body['reservation_cost'] = 'Total servicii: 3.259 Lei';
  
  $body['reservation_people'] = array(
    array('name' => 'Turist Turitescu1', 'birthdate' => '23.04.1976', 'cnp' => '1760423430058'),
    array('name' => 'Turist Turitescu1', 'birthdate' => '23.04.1976', 'cnp' => '1760423430058'),
  );
  
  /* Send the emails */
//   account_created_email($body, $to);
//   forgot_password_email($body, $to, $bcc);
//   ticket_created_email($body, $to);
//   ticket_assigned_email($body, $to, $bcc);
  reservation_created_email($body, $to, $bcc);
  reservation_changed_email($body, $to, $bcc);
}


if (!function_exists('account_created_email')) {
  /**
   * Send email for new created account. This should be send to both admin and
   * new account.
   */
  function account_created_email($body, $to, $bcc = array()) {
    $body['type'] = 'account_add';
    $subject = "Creare cont utilizator pentru {$body['user_name']} - ACCENT.RO";
 
    send_custom_email($subject, $body, $to, $bcc);
  }
}

if (!function_exists('account_password_email')) {
  /**
   * Send email for forgotten password. This should be send to both admin and
   * new account.
   */
  function forgot_password_email($body, $to, $bcc = array()) {
    $body['type'] = 'account_pass_reset';
    $subject = "Resetare parolă utilizator {$body['user_name']} - ACCENT.RO";

    send_custom_email($subject, $body, $to, $bcc);
  }
}

if (!function_exists('ticket_created_email')) {
  /**
   * New reservation ticket created email.
   */
  function ticket_created_email($body, $to, $bcc = array()) {
    $body['type'] = 'ticket_add';
    $subject = "Creare tichet {$body['ticket_id']}";
    
    send_custom_email($subject, $body, $to, $bcc);
  }
}

if (!function_exists('ticket_assigned_email')) {
  /**
   * Reservation was assigned to a ticket email. Should be send to the operator
   * as well as the sales department.
   */
  function ticket_assigned_email($body, $to, $bcc = array()) {
    $body['type'] = 'ticket_edit';
    $subject = "Editare tichet {$body['ticket_id']}";
    
    send_custom_email($subject, $body, $to, $bcc);
  }
}

if (!function_exists('reservation_created_email')) {
  /**
   * New reservation email. Should be send to the operator as well as the
   * sales department.
   */
  function reservation_created_email($body, $to, $bcc = array()) {
    $body['type'] = 'reservation_add';
    $subject = "Creare rezervare {$body['reservation_id']}";

    send_custom_email($subject, $body, $to, $bcc);
  }
}

if (!function_exists('reservation_changed_email')) {
  /**
   * Reservation detailes have been changed email. Should be send to the
   * operator as well as the sales department.
   */
  function reservation_changed_email($body, $to, $bcc = array()) {
    $body['type'] = 'reservation_edit';
    $subject = "Editare rezervare {$body['reservation_id']}";

    send_custom_email($subject, $body, $to, $bcc);
  }
}

if (!function_exists('send_custom_email')) {
  function send_custom_email($subject, $body_data, $to, $bcc = array()) {
    $body_data['date'] = date("F j, Y"); 
    $body_data['site_url'] = site_url(); 
    $body = $CI->load->view('emails/default_template', $body_data, true);
    
    send_email_to_customer($subject, $body, $to, $bcc);
  }
}
if (!function_exists('send_email_to_customer')) {
  function send_email_to_customer($subject, $body, $to, $bcc = array()) {
    $CI = get_instance();
    $CI->load->library('email');
    $CI->load->helper('url');
    
    /* Add common data to the body. */
    
    $config['mailtype'] = 'html';
    $config['useragent'] = 'Accent Travel & Events - PHP Sendmail';
    
    $CI->email->initialize($config);
    
    $CI->email->from('vanzari@accenttravel.ro', 'Accent Travel & Events');
    
    if (is_array($to)) {
      foreach($to as $email) {
        $CI->email->to($email);
      }
    } else {
      $CI->email->to($to);
    }
    
    if ($bcc) {
      if (is_array($bcc)) {
        foreach($bcc as $email) {
          $CI->email->bcc($email);
        }
      } else {
        $CI->email->bcc($bcc);
      }
    }
    
    $CI->email->subject($subject);
    $CI->email->message($body);
    
    return $CI->email->send();
  }
}
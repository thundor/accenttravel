<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Newsletter extends MX_Controller {
  function subscribe() {
    $this->theme->set_sublayout('frontend/concurs/index');
    $this->theme->view('forms/newsletter/subscribe', $this->data);
  }
  function unsubscribe() {
    $this->theme->set_sublayout('frontend/concurs/index');
    $this->theme->view('forms/newsletter/unsubscribe', $this->data);
  }
}
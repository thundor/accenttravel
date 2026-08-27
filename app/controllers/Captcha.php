<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Captcha extends MX_Controller {
	public function validate() {
		$this->load->helper('cookie');
		$this->session->unset_userdata('is_human');
		// delete_cookie('is_human');
		
		$recaptchaToken = $this->input->post('token');
		$g_recaptcha_response = $this->input->post('g-recaptcha-response');
	  
		$projectId = $this->config->item('recaptcha_v3_project_id');
		$siteKey = $this->config->item('recaptcha_v3_site_key');
		$secretKey = $this->config->item('recaptcha_v3_secret_key');
		
		if(!$projectId || !$projectId || !$projectId || !$recaptchaToken){
			echo -6; exit;
		}
		
		$recaptchaUrl = "https://recaptchaenterprise.googleapis.com/v1/projects/$projectId/assessments?key=$secretKey";

		$data = [
			"event" => [
				"token" => $recaptchaToken,
				"siteKey" => $siteKey,
				"expectedAction" => "LOGIN" // Replace with the action name you used in your client-side integration
			]
		];

		// Make the request to validate the token
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $recaptchaUrl);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Content-Type: application/json',
			'Referer: ' . site_url(''),
		]);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

		// Get the API response
		$response = curl_exec($ch);
		curl_close($ch);

		if ($response === false) {
			echo -5; exit;
		}
		if(!$response){
			echo -4; exit;
		}
		// Decode the response
		$responseData = json_decode($response, true);
		if(!$responseData){
			echo -3; exit;
		}
		$force_v2 = false;
		if (isset($responseData['tokenProperties']['invalidReason']) && $responseData['tokenProperties']['invalidReason'] == 'DUPE') {
			$captcha_v3_valid_token = $this->session->userdata('captcha_v3_valid_token');
			if($captcha_v3_valid_token == md5($recaptchaToken)){
				$force_v2 = true;
			}
		}
		// Check the response for success
		if ($force_v2 || (isset($responseData['tokenProperties']['valid']) && $responseData['tokenProperties']['valid'])) {
			if(!$force_v2){
				$score = $responseData['riskAnalysis']['score'];
				$action = $responseData['tokenProperties']['action'];
			}

			// Ensure the action matches your expected action
			if($force_v2 || $action === "LOGIN"){
				if (!$force_v2 && $score >= 0.5) { // Adjust score threshold as needed
					$this->load->helper('string');
					$human_token = random_string();
					$this->session->set_userdata('is_human', $human_token);
					set_cookie('is_human', $human_token, 86400);
					echo 1;
				} else {
					if(!$force_v2){
						$this->session->set_userdata('captcha_v3_valid_token', md5($recaptchaToken));
					}
					if(!empty($g_recaptcha_response)){
						$response = $this->recaptcha->verifyResponse($g_recaptcha_response);
						
						if ($response && isset($response['success']) and $response['success'] === true) {
							$this->load->helper('string');
							$human_token = random_string();
							$this->session->set_userdata('is_human', $human_token);
							set_cookie('is_human', $human_token, 86400);
						  echo 1;
						} else {
							echo 0;
						}
					} else {
						echo 0;
					}
				}
			} else {
				echo -1;
			}
		} else {
			print_r($responseData['tokenProperties']);
			echo -2;
		}
		exit;
	}
  public function image($image_name) {
    if(preg_match("/^[\d]+\.[\d]+\.(png|jpg)$/u", $image_name)){
      $file_path = APPPATH . 'tmp' . DIRECTORY_SEPARATOR . $image_name;
      if(strpos($image_name,'png')){
        header("Content-type: image/png");
      } else {
        header("Content-type: image/jpeg");
      }
    } else {
      header("Content-type: image/png");
      $file_path = $this->theme->theme_path . 'assets/images/placeholder.png';
    }
    readfile("$file_path");
    exit;
  }
}
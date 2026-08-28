<?php
defined('ENVIRONMENT') OR die('Invalid access');
// if($this->_ci->router->module == 'Backend') return;
if(!config_item('captcha_validate_page')) return;
$this->_ci->load->helper('data');
if(ensure_human_for_internal_ip($this->_ci)){
	return;
}
if(!function_exists('detectCrawler')){
	function detectCrawler() {
		// List of known User-Agent substrings for famous crawlers
		$crawlers = [
			'Googlebot' => 'Googlebot',                  // Google Search
			'Google-Structured-Data' => 'structured-data-testing-tool', // Google Structured Data
			'Google-PageSpeed' => 'Google PageSpeed',   // Google PageSpeed Insights
			'Facebook' => 'facebookexternalhit',        // Facebook
			'WhatsApp' => 'WhatsApp',                   // WhatsApp
			'Twitter' => 'Twitterbot',                  // Twitter
			'LinkedIn' => 'LinkedInBot',                // LinkedIn
			'Bing' => 'bingbot',                        // Bing Search
			'Yahoo' => 'Slurp',                         // Yahoo Search
			'DuckDuckGo' => 'DuckDuckBot',              // DuckDuckGo
			// 'Yandex' => 'YandexBot',                    // Yandex Search
			// 'Baidu' => 'Baiduspider',                   // Baidu Search
		];

		// Get the User-Agent header
		$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

		// Check if the User-Agent matches any of the known crawlers
		foreach ($crawlers as $crawlerName => $crawlerIdentifier) {
			if (stripos($userAgent, $crawlerIdentifier) !== false) {
				return $crawlerName; // Return the name of the crawler
			}
		}

		return false; // No crawler detected
	}
} else {
}
$crawler = detectCrawler();
if($crawler) return;
$this->_ci->load->helper('cookie');
if($this->_ci->session->userdata('is_human') && ($this->_ci->session->userdata('is_human') == get_cookie('is_human'))){
	return;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Loading</title>
  <script src="https://www.google.com/recaptcha/enterprise.js?render=<?php echo $this->_ci->config->item('recaptcha_v3_site_key'); ?>"></script>
<?php $this->_ci->load->library('recaptcha'); ?>
<?php echo $this->_ci->recaptcha->getScriptTag(); ?>
  <style>
    body {
      margin: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      background-color: #f0f0f0;
      font-family: Arial, sans-serif;
    }

    .container {
      text-align: center;
    }

    svg{
	  width: 100px;
	  height: 100px;
	  margin: 20px;
	  display:inline-block;
	}

    .message {
      color: #333;
      font-size: 18px;
    }
	
	.form-sec{
		position: absolute;
		width: 1px;
		height: 1px;
		appearance: none;
		border: 0;
		background: transparent;
		pointer-events: none;
	}
  </style>
</head>
<body>
  <div class="container">
    <svg version="1.1" id="L2" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
  viewBox="0 0 100 100" enable-background="new 0 0 100 100" xml:space="preserve">
<circle fill="none" stroke="#fff" stroke-width="4" stroke-miterlimit="10" cx="50" cy="50" r="48"/>
<line fill="none" stroke-linecap="round" stroke="#fff" stroke-width="4" stroke-miterlimit="10" x1="50" y1="50" x2="85" y2="50.5">
  <animateTransform 
       attributeName="transform" 
       dur="2s"
       type="rotate"
       from="0 50 50"
       to="360 50 50"
       repeatCount="indefinite" />
</line>
<line fill="none" stroke-linecap="round" stroke="#fff" stroke-width="4" stroke-miterlimit="10" x1="50" y1="50" x2="49.5" y2="74">
  <animateTransform 
       attributeName="transform" 
       dur="15s"
       type="rotate"
       from="0 50 50"
       to="360 50 50"
       repeatCount="indefinite" />
</line>
</svg>
    <div class="message">
		<span>Checking your request</span>
		<noscript><br />This site requires javascript!!</noscript>
		
		<form id="validateForm" name="validateForm" action="<?php echo site_url('captcha/validate');?>" style="display:none;" method="POST" onsubmit="event.preventDefault(); validateV2(event); return false;">
			<p>Va rugam sa bifati caseta:</p>
			<?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
			<input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
			<input type="text" name="<?php echo md5($this->_ci->security->get_csrf_hash()); ?>" value="" class="form-sec" />
			<?php } ?>
			<input type="hidden" name="token" value="" class="form-sec" />
			<?php echo $this->_ci->recaptcha->getWidget(['data-callback' => 'clickedOnVerify']); ?>
			<input type="submit" class="form-sec" onclick="return false;"/>
		</form>
		<?php /*
		<pre>
			<?php var_dump($this->_ci->session->userdata('is_human')); ?>
			<?php var_dump(get_cookie('is_human')); ?>
		</pre>
		*/ ?>
	</div>
	<script>
		validateForm.submit = function(){console.warn('blocked')};
		if (validateForm.addEventListener) {
			validateForm.addEventListener("submit", function(evt) {
				evt.preventDefault();
				return false;
			}, true);
		}
		else {
			validateForm.attachEvent('onsubmit', function(evt){
				evt.preventDefault();
				return false;
			});
		}
	  function clickedOnVerify() {
			validateV2();
	  }
	  function validateV2(event) {
		  if(event){
			  event.preventDefault();
			  event.stopPropagation();
			  event.stopImmediatePropagation();
		  }
		  
		  fetch("<?php echo site_url('captcha/validate');?>", {
				method: "POST",
				body: new URLSearchParams(new FormData(validateForm)),
				credentials: 'include',
				headers: {
					'Accept': 'application/json'
				},
			}).then((response) => response.json()).then((r) => {
				if(1 == r){
					window.location = window.location.href.replace(/#.*/, '');
				}
			}).catch((e) => {
				console.error("Failed to check", e);
				// Do nothing
			}).finally(() => {
				console.log("Finally");
			})
		  
		  return false;
	  }
	  function getToken() {
		grecaptcha.enterprise.ready(async () => {
			const token = await grecaptcha.enterprise.execute(<?php echo json_encode($this->_ci->config->item('recaptcha_v3_site_key')); ?>, {action: 'LOGIN'});
		  
			const getPairs = (obj, keys = []) =>
			  Object.entries(obj).reduce((pairs, [key, value]) => {
				if (typeof value === undefined){
				  
				}
				else if (typeof value === 'object'){
				  if(null !== value)
					pairs.push(...getPairs(value, [...keys, key]));
				}
				else
				  pairs.push([[...keys, key], encodeURIComponent(value)]);
				return pairs;
			  }, []);
			const objToSerialize = (obj) => getPairs(obj)
			  .map(([[key0, ...keysRest], value]) =>
				`${key0}${keysRest.map(a => `[${a}]`).join('')}=${value}`)
			  .join('&');
			  
			var data = {
				<?php if ($this->_ci->config->item('csrf_protection')){ ?>
				<?php echo json_encode($this->_ci->security->get_csrf_token_name()); ?>: <?php echo json_encode($this->_ci->security->get_csrf_hash()); ?>,
				<?php } ?>
				token: token,
			};
			validateForm.token.value = token;
			fetch("<?php echo site_url('captcha/validate');?>", {
				method: "POST",
				body: new URLSearchParams(objToSerialize(data)),
				credentials: 'include',
				headers: {
					'Accept': 'application/json'
				},
			}).then((response) => response.json()).then((r) => {
				console.warn(r);
				if(0 == r){
					validateForm.style.display = '';
				} else if(1 == r){
					window.location.href = window.location.href;
				}
			}).catch((e) => {
				console.error("Failed to check", e);
				// Do nothing
			}).finally(() => {
				console.log("Finally");
			})
		});
	  }
	  getToken();
	</script>
  </div>
</body>
</html>
<?php
exit;
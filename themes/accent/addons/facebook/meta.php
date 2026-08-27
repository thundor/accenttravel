<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<meta property="og:title" content="<?php echo @$page_title; ?>"/>
<meta property="og:site_name" content="<?php echo @$site_title;?>"/>
<meta property="og:description" content="<?php echo @$metadescription;?>"/>
<meta property="og:image" content="<?php echo $this->theme_url; ?>assets/images/logo.png"/>
<meta property="og:url" content="<?php echo config_item('base_url');?>/"/>
<meta property="og:publisher" content="https://www.facebook.com/<?php echo @$site_title;?>"/>
<!-- Facebook Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window,document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '1005319669551157'); 
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" src="https://www.facebook.com/tr?id=1005319669551157&ev=PageView&noscript=1"/></noscript>
<!-- End Facebook Pixel Code -->
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
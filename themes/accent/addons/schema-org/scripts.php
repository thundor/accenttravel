<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<script type="application/ld+json">
{
   "@context" : "http://schema.org/"
  ,"@type" : "Organization"
  ,"name" : "<?php echo @$site_title;?>"
  ,"url" : "<?php echo @$site_url;?>/"
  ,"logo" : "<?php echo $this->theme_url; ?>assets/images/logo.png"
  ,"sameAs" : [
    "https://www.facebook.com/<?php echo @$site_title;?>"
    ,"https://twitter.com/<?php echo @$site_title;?>"
    ,"https://www.pinterest.com/<?php echo @$site_title;?>/"
    ,"https://plus.google.com/u/0/<?php echo @$site_title;?>/posts"
  ],
  "contactPoint" : {
    "@type" : "ContactPoint"
    ,"telephone" : "<?php echo @$phone; ?>"
    ,"contactType" : "Customer Service"
  }
}
</script>
<script type="application/ld+json">
{
  "@context":"http://schema.org"
  ,"@type":"WebSite"
  ,"name":"<?php echo @$site_title;?>"
  ,"url":"<?php echo @$site_url;?>"
}
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
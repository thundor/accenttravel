<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php // https://polyfill.io/v3/polyfill.min.js?features=IntersectionObserver,ResizeObserver,WebAnimations,Object.fromEntries,Array.prototype.at ?>
<?php /* <script type="text/javascript" src="<?php echo $this->theme_url; ?>assets/plugins/polyfill/3.111.0/js/polyfill.js"></script> */ ?>
<script src="https://polyfill.io/v3/polyfill.min.js?features=IntersectionObserver,ResizeObserver,WebAnimations,Object.fromEntries,Array.prototype.at"></script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
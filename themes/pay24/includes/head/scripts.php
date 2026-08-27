<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php /*
<script src="https://cdn.jsdelivr.net/npm/vue@2.7.14/dist/vue<?php if(ENVIRONMENT == 'production') echo '.prod'; ?>.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vuetify@2.6.12/dist/vuetify.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vue-router@3.6.5/dist/vue-router.min.js"></script>
*/ ?>
<?php /*<script src="https://polyfill.io/v3/polyfill.min.js?features=IntersectionObserver,ResizeObserver,WebAnimations,Object.fromEntries,Array.prototype.at"></script>
<script src="https://cdn.jsdelivr.net/npm/vue@3.2.45/dist/vue.global<?php if(ENVIRONMENT == 'production') echo '.prod'; ?>.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vuetify@3.0.4/dist/vuetify<?php if(ENVIRONMENT == 'production') echo '.min'; ?>.js"></script>
<!--<script src="https://cdn.jsdelivr.net/npm/vue-router@4.1.6/dist/vue-router.global<?php if(ENVIRONMENT == 'production') echo '.prod'; ?>.js"></script>-->
<script src="https://cdn.jsdelivr.net/npm/vue-router@4.1.6/dist/vue-router.global.prod.js"></script>
<script src="https://unpkg.com/@vuepic/vue-datepicker@latest"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/7.1.96/css/materialdesignicons.min.css" integrity="sha512-NaaXI5f4rdmlThv3ZAVS44U9yNWJaUYWzPhvlg5SC7nMRvQYV9suauRK3gVbxh7qjE33ApTPD+hkOW78VSHyeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />*/ ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>
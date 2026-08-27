<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php /* <script type="module" src="<?php echo $this->theme_url; ?>assets/plugins/<?php echo basename(dirname(__DIR__))?>/<?php echo basename(__DIR__)?>/lib/index.js"></script>
<script type="text/javascript">
window.PhoneNumber = window['PhoneNumber$$module$src$index'];
</script> 
<script type="module">
import { PhoneNumber } from '<?php echo $this->theme_url; ?>assets/plugins/<?php echo basename(dirname(__DIR__))?>/<?php echo basename(__DIR__)?>/lib/index.js';
</script>
<script type="text/javascript">
const PhoneNumber = import '<?php echo $this->theme_url; ?>assets/plugins/<?php echo basename(dirname(__DIR__))?>/<?php echo basename(__DIR__)?>/index.js';
</script>
<script type="module" src="https://cdn.jsdelivr.net/npm/awesome-phonenumber@6.4.0/index.min.js"></script>
*/ ?>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
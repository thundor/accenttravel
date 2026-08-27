<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php $flight_details = $this->view_data['flight_details']; ?>
<script type="text/javascript">
var flight_data = <?php echo json_encode($flight_details); ?>;
console.log(flight_data);
</script>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>
<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<script>
const flight_data = <?php echo $this->view_data['flight_data']; ?>;
const subview = <?php echo json_encode($this->view_data['subview']); ?>;
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
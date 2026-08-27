import BaseFunctionality from '../common/<?php echo basename($a, '.php'); ?>?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	extends: BaseFunctionality,
	data: () => {
		return {
			offer_details_url: `${newux_url}/partials/presearch-wrapper/functionalities/<?php echo basename(dirname($a)); ?>/offer-details.json?${append_url}`,
			<?php switch (basename(dirname($a))){ 
			case 'travelfuse-circuite':
			?>
			text_result_type: ['circuit', 'circuite'],
			show_itinerariu: true,
			<?php 
				break;
			default:
			?>
			result_details_url: `${newux_url}/partials/presearch-wrapper/functionalities/<?php echo basename(dirname($a)); ?>/result-details.json?${append_url}`,
			text_result_type: ['hotel', 'hoteluri'],
			<?php 
				break;
			} ?>
		}
	},
}
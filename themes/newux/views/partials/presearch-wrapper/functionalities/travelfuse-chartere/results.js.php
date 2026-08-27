import BaseFunctionality from '../common/<?php echo basename($a, '.php'); ?>?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	extends: BaseFunctionality,
	data: () => {
		return {
		<?php switch (basename(dirname($a))){ 
			case 'travelfuse-circuite':
			?>
			result_type: ['travelfuse/circuit', 'travelfuse/circuite'],
			text_result_type: ['circuit', 'circuite'],
			text_no_results: 'Niciun circuit gasit',
			<?php 
				break;
			default:
			?>
			result_type: ['travelfuse/charter', 'travelfuse/chartere'],
			text_result_type: ['hotel', 'hoteluri'],
			text_no_results: 'Niciun hotel gasit',
			<?php 
				break;
			} ?>
		}
	},
}
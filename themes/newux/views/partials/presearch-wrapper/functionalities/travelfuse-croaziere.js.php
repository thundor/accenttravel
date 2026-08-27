import BaseFunctionality from './base-functionality.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	extends: BaseFunctionality,
	data: () => {
		return {
			key: '<?php echo basename(__FILE__, '.js.php'); ?>',
			menu: {
				title: 'Croaziere',
				icon: 'mdi-ship-wheel',
			}
		}
	},
}

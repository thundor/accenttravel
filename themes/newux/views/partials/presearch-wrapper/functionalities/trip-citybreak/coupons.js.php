import BaseFunctionality from '../common/<?php echo basename($a, '.php'); ?>?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	emits: ['research'],
	extends: BaseFunctionality,
	props: {
		result: {
          type: Object,
          default: () => (undefined),
		},
	},
	data: () => ({
		coupon_type: 'citybreak',
	}),
}
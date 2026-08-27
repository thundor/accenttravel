<?php include __DIR__ . "/../" . str_replace('circuite', 'chartere', basename(dirname(__FILE__))) . '/' . basename(__FILE__); ?>
<?php /*
import BaseFunctionality from '../common/list-selector.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';

export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	extends: BaseFunctionality,
	props: {
		data: {
			type: Object,
			default: () => ({}),
		},
	},
	data(){
		return {
			key: '<?php echo basename(dirname(__FILE__)) . '/' . basename(__FILE__, '.js.php'); ?>',
			menu: {
				title: 'Transport',
				placeholder: 'Cum calatoresti',
				search_label: 'Mod transport',
				icon: 'mdi-plane-car',
			},
			selected_city_ids: this.data['<?php echo basename(dirname(__FILE__)); ?>/<?php echo basename(__FILE__, '.js.php'); ?>'] && [this.data['<?php echo basename(dirname(__FILE__)); ?>/<?php echo basename(__FILE__, '.js.php'); ?>'] || {}] || undefined,
		}
	},
	computed: {
	},
	methods: {
		loadCities: function(){
			this.cities = [
				{
					Id: 'plane',
					Name: "Avion",
				},
				{
					Id: 'bus',
					Name: "Autobuz",
				}
			];
		}
	},
}
*/ ?>
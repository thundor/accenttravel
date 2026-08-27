import BaseFunctionality from '../common/switch.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';

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
			key: '<?php echo basename(dirname($a)) . '/' . basename($a, '.js'); ?>',
			menu: {
				title: 'Transport',
				placeholder: 'Cum calatoresti',
				search_label: 'Mod transport',
				icon: 'mdi-plane-car',
			},
			selected_city_ids: this.data['<?php echo basename(dirname($a)); ?>/<?php echo basename($a, '.js'); ?>'] && [this.data['<?php echo basename(dirname($a)); ?>/<?php echo basename($a, '.js'); ?>'] || {}] || undefined,
		}
	},
	computed: {
		disabled() {
			return this.search_wrapper_step > 0;
		},
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
					Name: "Autocar",
				}
			];
		}
	},
}

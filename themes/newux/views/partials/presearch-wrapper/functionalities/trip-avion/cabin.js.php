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
			// disable_selected: true,
			key: '<?php echo basename(dirname($a)) . '/' . basename($a, '.js'); ?>',
			menu: {
				title: 'Clasa zbor',
				placeholder: 'Cum calatoresti',
				search_label: 'Clasa zbor',
				icon: 'mdi-plane',
			},
			selected_city_ids: this.data['<?php echo basename(dirname($a)); ?>/<?php echo basename($a, '.js'); ?>'] && [this.data['<?php echo basename(dirname($a)); ?>/<?php echo basename($a, '.js'); ?>'] || {
					Id: 'Y',
					Name: "Economy",
				}] || [],
		}
	},
	computed: {
		disabled() {
			return this.search_wrapper_step > 0;
		},
	},
	methods: {
		loadCities: function(success_callback){
			this.cities = [
				{
					Id: 'Y',
					Name: "Economy",
				},
				{
					Id: 'F',
					Name: "First class",
				},
				{
					Id: 'C',
					Name: "Business class",
				},
				{
					Id: 'W',
					Name: "Premium economy class",
				}
			];
			
			if(success_callback && 'function' === typeof success_callback) success_callback(this)
		}
	},
}

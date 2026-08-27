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
				title: 'Destinatia ta',
				placeholder: 'Unde calatoresti',
				search_label: 'Cauta o destinatie',
				icon: 'mdi-flag-variant',
			},
			selected_city_ids: this.data['<?php echo basename(dirname(__FILE__)); ?>/<?php echo basename(__FILE__, '.js.php'); ?>'] && [this.data['<?php echo basename(dirname(__FILE__)); ?>/<?php echo basename(__FILE__, '.js.php'); ?>'] || {}] || undefined,
			fetch_url: `${newux_url}/partials/presearch-wrapper/functionalities/<?php echo basename(dirname(__FILE__)); ?>/destination-cities.json?${append_url}`,
		}
	},
	computed: {
		fetch_data() {
			if(!this.content_type) return false;
			return {
				// Transport: (this.data['<?php echo basename(dirname(__FILE__)); ?>/transport'] || {}).Id || '',
				// departureCity: (this.data['<?php echo basename(dirname(__FILE__)); ?>/departure-city'] || {}).Id || '',
			}
		},
	},
	watch: {
		'data.<?php echo basename(dirname(__FILE__)); ?>/transport': {
			handler: function(nv,ov){
				this.loadCities();
			},
			immediate: true
		},
		'data.<?php echo basename(dirname(__FILE__)); ?>/departure-city': {
			handler: function(nv,ov){
				this.loadCities();
			},
			immediate: true
		},
	},
	methods: {
	},
}

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
			key: '<?php echo basename(dirname($a)) . '/' . basename($a, '.js'); ?>',
			menu: {
				title: 'Oras plecare',
				placeholder: 'De unde pleci',
				search_label: 'Cauta orasul',
				icon: 'mdi-flag-variant',
			},
			selected_city_ids: this.data['<?php echo basename(dirname($a)); ?>/<?php echo basename($a, '.js'); ?>'] && [this.data['<?php echo basename(dirname($a)); ?>/<?php echo basename($a, '.js'); ?>'] || {}] || undefined,
			fetch_url: `${newux_url}/partials/presearch-wrapper/functionalities/<?php echo basename(dirname($a)); ?>/departure-cities.json?${append_url}`,
		}
	},
	created() {
		if(this.data['<?php echo basename(dirname($a)); ?>/hotel-id']){
			this.autoSelectFirst = true;
		}
		// console.warn('this.autoSelectFirst = true;', this.autoSelectFirst)
	},
	computed: {
		fetch_data() {
			if(!this.content_type) return false;
			return {
				hotelId: this.data['<?php echo basename(dirname($a)); ?>/hotel-id'] || null,
				// Transport: (this.data['<?php echo basename(dirname($a)); ?>/transport'] || {}).Id || '',
				destination: (this.data['<?php echo basename(dirname($a)); ?>/destination-city'] || {}).Id || '',
				destinationType: (this.data['<?php echo basename(dirname($a)); ?>/destination-city'] || {}).type || '',
			}
		},
		disabled() {
			// console.warn('disabled', this.search_wrapper_step, this);
			return this.search_wrapper_step > 0;
		},
	},
	watch: {
		'data.<?php echo basename(dirname($a)); ?>/transport': {
			handler: function(nv,ov){
				this.loadCities();
			},
			immediate: true
		},
		'data.<?php echo basename(dirname($a)); ?>/destination-city': {
			handler: function(nv,ov){
				// console.warn('shdisabled', this.search_wrapper_step, this);
				this.loadCities();
			},
			immediate: true
		},
	},
	methods: {
		listDefaultKey: function(item){
			return item.alias;
		},
	},
}

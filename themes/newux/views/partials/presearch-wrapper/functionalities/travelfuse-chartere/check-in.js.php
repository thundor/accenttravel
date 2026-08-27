import BaseFunctionality from '../common/date-selector.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';

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
			fuse_keys: ['Id', 'Name'],
			menu: {
				title: 'Check-in',
				placeholder: 'Check-in',
				search_label: 'Data',
				icon: 'mdi-flag-variant',
			},
			selected_city_ids: this.data['<?php echo basename(dirname($a)); ?>/<?php echo basename($a, '.js'); ?>'] && [this.data['<?php echo basename(dirname($a)); ?>/<?php echo basename($a, '.js'); ?>'] || {}] || undefined,
			fetch_url: `${newux_url}/partials/presearch-wrapper/functionalities/<?php echo basename(dirname($a)); ?>/<?php echo basename($a, '.js'); ?>.json?${append_url}`,
		}
	},
	created() {
		if(this.data['<?php echo basename(dirname($a)); ?>/hotel-id']){
			this.autoSelectFirst = true;
		}
	},
	computed: {
		fetch_data() {
			var obj = {
				hotelId: this.data['<?php echo basename(dirname($a)); ?>/hotel-id'] || null,
				destination: (this.data['<?php echo basename(dirname($a)); ?>/destination-city'] || {}).Id || '',
				destinationType: (this.data['<?php echo basename(dirname($a)); ?>/destination-city'] || {}).type || '',
				departureCity: (this.data['<?php echo basename(dirname($a)); ?>/departure-city'] || {}).Id || '',
			}
			if(-1 !== Object.values(obj).findIndex(v => '' === v)) return false;
			// Object.assign(obj, {
				// Transport: (this.data['<?php echo basename(dirname($a)); ?>/transport'] || {}).Id || '',
			// });
			return obj;
		},
	},
	watch: {
		'data.<?php echo basename(dirname($a)); ?>/transport': {
			handler: function(nv,ov){
				if(this.content_type == 'menu' && (nv?.Id != ov?.Id)){
					this.loadCities();
				}
			},
			immediate: true
		},
		'data.<?php echo basename(dirname($a)); ?>/destination-city': {
			handler: function(nv,ov){
				if(this.content_type == 'menu' && (nv?.Id != ov?.Id)){
					this.loadCities();
				}
			},
			immediate: true
		},
		'data.<?php echo basename(dirname($a)); ?>/departure-city': {
			handler: function(nv,ov){
				if(this.content_type == 'menu' && (nv?.Id != ov?.Id)){
					this.loadCities();
				}
			},
			immediate: true
		},
	},
	methods: {
	},
}

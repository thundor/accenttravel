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
			fuse_keys: ['Name', 'alias', 'City', 'Country'],
			min_search: 3,
			key: '<?php echo basename(dirname($a)) . '/' . basename($a, '.js'); ?>',
			menu: {
				title: 'Oras plecare',
				placeholder: 'De unde pleci',
				search_label: 'Cauta orasul',
				icon: 'mdi-flag-variant',
			},
			selected_city_ids: this.data['<?php echo basename(dirname($a)); ?>/<?php echo basename($a, '.js'); ?>'] && [this.data['<?php echo basename(dirname($a)); ?>/<?php echo basename($a, '.js'); ?>'] || {}] || undefined,
			fetch_url: [`${newux_url}/partials/presearch-wrapper/functionalities/<?php echo basename(dirname($a)); ?>/departure-cities.json?${append_url}`, `${newux_url}/partials/presearch-wrapper/functionalities/<?php echo basename(dirname($a)); ?>/departure-locations.json?${append_url}`],
		}
	},
	computed: {
		fetch_data() {
			if(!this.content_type) return false;
			return {
			}
		},
		disabled() {
			// console.warn('disabled', this.search_wrapper_step, this);
			return this.search_wrapper_step > 0;
		},
	},
	watch: {
	},
	methods: {
		listDefaultKey: function(item){
			return item.alias;
		},
		listKey: function(item){
			return [item.CityId || 0, item.Id].join(',');
		},
		listTitle: function(item){
			return item.Name + ( item.City ? ' (' + item.City + ')' : '') + ( item.Country ? ' '+item.Country : '');
		},
		presentation: function(item){
			return item && (item.Name + (item.City && (' ' + item.City) || '') + (item.Country && (' ' + item.Country) || ''));
		},
	},
}

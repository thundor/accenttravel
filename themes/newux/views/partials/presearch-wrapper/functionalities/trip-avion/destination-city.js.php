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
				title: 'Destinatie',
				placeholder: 'Unde pleci',
				search_label: 'Cauta destinatia',
				icon: 'mdi-flag-variant',
			},
			selected_city_ids: this.data['<?php echo basename(dirname($a)); ?>/<?php echo basename($a, '.js'); ?>'] && [this.data['<?php echo basename(dirname($a)); ?>/<?php echo basename($a, '.js'); ?>'] || {}] || undefined,
			fetch_url: [`${newux_url}/partials/presearch-wrapper/functionalities/<?php echo basename(dirname($a)); ?>/destination-cities.json?${append_url}`, `${newux_url}/partials/presearch-wrapper/functionalities/<?php echo basename(dirname($a)); ?>/destination-locations.json?${append_url}`],
		}
	},
	computed: {
		fetch_data() {
			if(!this.content_type) return false;
			return {
				// Transport: (this.data['<?php echo basename(dirname($a)); ?>/transport'] || {}).Id || '',
			}
		},
		disabled() {
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
		presentation: function(city){
			return city && (city.Name + ' ' + city.Country) || '';
		},
		<?php /* openIfCondition(transport){
			
			console.warn('openIfCondition', transport.selected_city_ids);
			if(transport.selected_city_ids && transport.selected_city_ids.length){
				setTimeout(() => {
					
				this.opened = true;
				},0)
			}
		} */ ?>
	},
}

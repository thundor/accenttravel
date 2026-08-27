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
			menu: {
				title: 'Check-in',
				placeholder: 'Check-in',
				search_label: 'Data',
				icon: 'mdi-flag-variant',
			},
			selected_city_ids: this.data['<?php echo basename(dirname($a)); ?>/<?php echo basename($a, '.js'); ?>'] && [this.data['<?php echo basename(dirname($a)); ?>/<?php echo basename($a, '.js'); ?>'] || {}] || [],
		}
	},
	mounted() {
		if(this.content_type == 'menu'){
		}
	},
	computed: {
		fetch_data() {
			return {};
		},
	},
	watch: {
	},
	methods: {
	},
}

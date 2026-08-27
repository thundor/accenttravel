import FormLegend from '../../../form/legend.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	props: {
		data: {
		  type: Object,
		  default: () => ({}),
		},
		search_data: {
          type: Object,
          default: () => (undefined),
		},
		result: {
          type: Object,
          default: () => (undefined),
		},
	},
	components:{
		'FormLegend': FormLegend,
	},
	data() {
		return {};
	},
	beforeUnmount() {
	},
	methods: {},
	watch:{},
	computed: {},
	template : `
<div class="bg-background">
	In curs de validare
</div>
	`
}

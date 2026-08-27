import BaseFunctionality from '../common/travelfuse-travellers.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';

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
			key: '<?php echo ($k = basename(dirname($a)) . '/' . basename($a, '.js')); ?>',
			menu: {
				title: 'Calatori',
				placeholder: 'Unde pleci',
				search_label: 'Cauta destinatia',
				icon: 'mdi-flag-variant',
			},
			travellers: {
				ADT: (this.data['<?php echo $k; ?>'] || {}).ADT || 1,
				CHD: (this.data['<?php echo $k; ?>'] || {}).CHD || [],
			},
		}
	},
	watch: {
		<?php /*
		'data.<?php echo basename(dirname($a)) . '/' . basename($a, '.js'); ?>': {
			handler: function(nv,ov){
				if(!this.content_type) return;
				this.getDefaultTravellers(nv);
			},
		},
		*/ ?>
	}
}

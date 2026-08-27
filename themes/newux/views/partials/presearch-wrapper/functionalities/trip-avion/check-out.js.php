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
				title: 'Data retur',
				placeholder: 'Data retur',
				search_label: 'Data',
				icon: 'mdi-flag-variant',
			},
			selected_city_ids: this.data['<?php echo basename(dirname($a)); ?>/<?php echo basename($a, '.js'); ?>'] && [this.data['<?php echo basename(dirname($a)); ?>/<?php echo basename($a, '.js'); ?>'] || {}] || undefined,
		}
	},
	computed: {
		fetch_data() {
			var obj = {
			}
		},
	},
	watch: {
		'data.<?php echo basename(dirname($a)); ?>/check-in': {
			handler: function(nv,ov){
				if(nv === ov) return;
				if(this.content_type == 'menu'){
					this.force_min_date = null;
					if(nv){
						var curdate = new Date(nv.Id)
						var min_date = new Date(Date.UTC(curdate.getFullYear(), curdate.getMonth(), curdate.getDate()));
						<?php switch (basename(dirname($a))){ 
						case 'trip-citybreak':
						?>
						var d = new Date(Date.UTC(curdate.getFullYear(), curdate.getMonth(), curdate.getDate()));
						this.diffdate = new Date(Date.UTC(curdate.getFullYear(), curdate.getMonth(), curdate.getDate()) + (d.getTimezoneOffset() * 60000));
						min_date.setDate(min_date.getDate() + 1);
						<?php 
							break;
						default:
						?>
						min_date.setDate(min_date.getDate());
						<?php 
							break;
						} ?>

						this.force_min_date = min_date.toISOString().replace(/T.*/,'');
						if(!this.selected_city_ids || !this.selected_city_ids.length || this.selected_city_ids[0] && this.selected_city_ids[0].Id < this.force_min_date){
							this.prevent_click_selected = true;
							this.selected_date = this.force_min_date;
							// console.warn('force-select', this.selected_date)
						}
					}
					
				}
			},
			immediate: true
		},

	},
	methods: {
	},
}

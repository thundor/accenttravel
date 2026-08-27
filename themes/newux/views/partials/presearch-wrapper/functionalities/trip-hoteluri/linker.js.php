import BaseFunctionality from '../common/linker.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';

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
			link: 'trip/hotelsasync',
		}
	},
	computed: {
		linkerparams() {
			var allgood = 0;
			var params = [];
			if(this.linkerdata['destination-city']?.alias){
				allgood++;
				params.push('city_name=' + encodeURIComponent(this.linkerdata['destination-city']?.alias));
			}
			if(this.linkerdata['check-in']?.Id){
				allgood++;
				params.push('sdate=' + encodeURIComponent(this.linkerdata['check-in']?.Id));
			}
			if(this.linkerdata['check-out']?.Id){
				allgood++;
				params.push('edate=' + encodeURIComponent(this.linkerdata['check-out']?.Id));
			}
			if(this.linkerdata['travellers']?.[0]?.ADT && this.linkerdata['travellers']?.length){
				var adt_added = false;
				this.linkerdata['travellers'].forEach((r,ri) => {
					if(r.ADT){
						adt_added = true;
						params.push('o['+ ri + '][ADT]=' + r.ADT);
					}
					if(r.CHD && r.CHD.length)
						r.CHD.forEach(c => {
							params.push('o['+ ri + '][CHD][]=' + c);
						})
				});
				if(adt_added){
					allgood++;
				}
			}
			if(allgood == 4){
				params.push('n=1');
			}
			return params;
		},
	},
}

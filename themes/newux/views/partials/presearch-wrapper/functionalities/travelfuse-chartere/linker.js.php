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
			link: 'travelfuse/chartere',
		}
	},
	computed: {
		linkerparams() {
			var allgood = 0;
			var params = [];
			if(this.linkerdata['destination-city']?.alias){
				allgood++;
				params.push('destination=' + encodeURIComponent(this.linkerdata['destination-city']?.alias));
			}
			if(this.linkerdata['departure-city']?.alias){
				allgood++;
				params.push('origin=' + encodeURIComponent(this.linkerdata['departure-city']?.alias));
			}
			if(this.linkerdata['check-in']?.Id){
				allgood++;
				params.push('sdate=' + encodeURIComponent(this.linkerdata['check-in']?.Id));
			}
			if(this.linkerdata['check-out']?.Id){
				allgood++;
				params.push('edate=' + encodeURIComponent(this.linkerdata['check-out']?.Id));
			}
			if(this.linkerdata['travellers']?.ADT){
				allgood++;
				params.push('a=' + this.linkerdata['travellers']?.ADT);
			}
			if(this.linkerdata['travellers']?.CHD && this.linkerdata['travellers']?.CHD.length){
				params.push('c=' + this.linkerdata['travellers']?.CHD.join(','));
			}
			if(allgood == 5){
				params.push('n=1');
			}
			return params;
		},
	},
}

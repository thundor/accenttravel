import BaseFunctionality from '../base-functionality.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
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
			loadtimer: undefined,
			loading: false,
			key: '<?php echo basename(dirname(__FILE__)) . '/' . basename(__FILE__, '.js.php'); ?>',
			menu: {
				title: 'Calatori',
				icon: 'mdi-flag-variant',
			},
			text_child_index_age: 'Varsta copil',
			text_child_leave_age: 'Varsta implinita la plecare',
			text_add_traveller: 'Adauga calator',
			text_adt_desc: 'Peste 18 ani, maxim 5 adulti',
			text_adt_1: 'Adult',
			text_adt_n: 'Adulti',
			text_yth_desc: 'Intre 12 - 17 ani',
			text_yth_1: 'Tanar',
			text_yth_n: 'Tineri',
			text_chd_1: 'Copil',
			text_chd_desc: 'Sub 17 ani, maxim 4 copii',
			text_chd_n: 'Copii',
			opened: false,
			travellers: {
				ADT: 0,
				// YTH: 0,
				CHD: [],
			},
			<?php /*
			travellers: {
				'ADT': this.data['<?php echo $k; ?>'] && !isNaN(parseInt(this.data['<?php echo $k; ?>'].ADT)) && this.data['<?php echo $k; ?>'].ADT > 0 && this.data['<?php echo $k; ?>'].ADT <= 6 && parseInt(this.data['<?php echo $k; ?>'].ADT) || 0, 
				'YTH': this.data['<?php echo $k; ?>'] && !isNaN(parseInt(this.data['<?php echo $k; ?>'].YTH)) && this.data['<?php echo $k; ?>'].YTH > 0 && this.data['<?php echo $k; ?>'].YTH <= 9 && parseInt(this.data['<?php echo $k; ?>'].YTH) || 0, 
				'CHD': this.data['<?php echo $k; ?>'] && Array.isArray(parseInt(this.data['<?php echo $k; ?>'].CHD)) && this.data['<?php echo $k; ?>'].CHD.filter(a => !isNaN(a) && a>0 && a<=11).map(a => parseInt(a)).slice(0,4) || [], 
			},
			*/ ?>
		}
	},
	computed: {
	},
	mounted() {
		if(this.content_type == 'menu'){
		}
		var def = this.getObjectDotPathValue(this.data?.defaults, this.key.replace(/\//g, '.'));
		if(!this.travellers.ADT && undefined !== def){
			console.warn('tf trav', def);
			this.travellers = {
                ADT: def?.ADT || 0,
                CHD: def?.CHD && Array.isArray(def.CHD) && def.CHD || [],
            };
		}
        console.warn('TF TRAV', this.travellers);
	},
	watch: {
		'loading': {
			handler: function(nv,ov){
				// console.warn('loading', nv);
			},
			immediate: true
		},
		'opened': {
			handler: function(nv,ov){
				// console.warn('opened', nv, this.$refs.search);
			},
			immediate: true
		},
		<?php /*
		'data.<?php echo basename(dirname(__FILE__)) . '/' . basename(__FILE__, '.js.php'); ?>': {
			handler: function(nv,ov){
				this.getDefaultTravellers(nv);
			},
			immediate: true,
			deep: true
		}, */ ?>
		'travellers': {
			handler: function(nv,ov){
				// console.warn('should travellers', nv);
				this.setValue(nv);
				<?php /* var travellers = this.data[this.key] || {};
				console.warn('should travellers', this.key, JSON.stringify(this.data[this.key]), JSON.stringify(this.travellers));
				if(JSON.stringify(this.data[this.key]) !== JSON.stringify(this.travellers)){
					console.warn('setting travellers', this.key);
					this.$emit('set-value', {[this.key]: markRaw(this.travellers)});
				} */ ?>
			},
			immediate: true,
			deep: true
		},
	},
	methods: {
		<?php /*
		getDefaultTravellers: function(nv){
			console.warn('<?php echo basename(dirname(__FILE__)) . '/' . basename(__FILE__, '.js.php'); ?>', nv)
			var def = {
				ADT: 0,
				YTH: 0,
				CHD: [],
			};
			var travellers = nv && typeof nv == 'object' && nv || {};
		
			var trav = Object.keys(def).reduce((c, i) => {
				if(undefined !== typeof travellers[i]){
					if(-1 !== ['ADT', 'YTH'].indexOf(i)){
						if(!isNaN(travellers[i])){
							var val = parseInt(travellers[i]);
							if(!isNaN(val) && val >= 0){
								c[i] = val;
							}
						}
					} else if(-1 !== ['CHD'].indexOf(i)){
						if(Array.isArray(travellers[i])){
							c[i] = travellers[i].filter(a => {
								if(!isNaN(a)){
									var val = parseInt(a);
									if(!isNaN(val) && val >= 0 && val <= 11){
										return true;
									}
								}
								return false;
							}).map(v => parseInt(v));
						}
					}
				}
				return c;
			}, {});
			console.warn('<?php echo basename(dirname(__FILE__)) . '/' . basename(__FILE__, '.js.php'); ?>', nv, trav)
		}
		*/ ?>
	},
	template : `
<component :is="loadViewAsync('partials/presearch-wrapper/functionality')" :active_menu="active_menu" :data="data" class="pa-0" :search_wrapper_step="search_wrapper_step">
<template v-slot:menu>
<v-menu
	  v-model="opened"
	  :close-on-content-click="false"
	  location="bottom"
	  class="rounded-xl search-type-ul-menu"
	>
	  <template v-slot:activator="{ props }">
		<v-text-field
			class="pt-2 pb-1 ps-15 pe-2"
			:class="{['menu-' + key + '-wrapper']: 1}"
			:label="this.menu.title"
			:placeholder="this.text_add_traveller"
			persistent-placeholder
			hide-details
			variant="plain"
			v-bind="props"
			readonly
			:type="travellers.ADT ? 'hidden' : 'text'"
			:id="'menu-' + key"
		>

			<template v-slot:default>
				<div v-if="travellers.ADT" class="d-flex flex-column flex-nowrap text-truncate" style="font-size: 12px;line-height: 1;">
					<div class="d-flex ga-3">
					<strong v-if="travellers.ADT == 1" v-text="travellers.ADT + ' ' + text_adt_1"></strong>
					<template v-else-if="travellers.ADT">
					<strong v-text="travellers.ADT + ' ' + text_adt_n"></strong>
					</template>
					</div>
					<div class="d-flex ga-3">
					<span v-if="(travellers.CHD || []).length == 1" v-text="travellers.CHD.length + ' ' + this.text_chd_1"></span>
					<span v-else-if="(travellers.CHD || []).length" v-text="travellers.CHD.length + ' ' + this.text_chd_n"></span>
					<span v-if="travellers.YTH == 1" v-text="travellers.YTH + ' ' + text_yth_1"></span>
					<span v-else-if="travellers.YTH" v-text="travellers.YTH + ' ' + text_yth_n"></span>
					</div>
				</div>
			</template>					
		</v-text-field>
	  
	  </template>
	  <v-card min-width="300">
		<v-list>
		  <v-list-item
			:subtitle="text_adt_desc"
			:title="text_adt_n"
		  >
			<template v-slot:append>
			  <v-btn
				icon="mdi-plus"
				color="success"
				variant="text"
				@click="travellers.ADT < 5 && (travellers.ADT++)"
			  ></v-btn>
			</template>
			<template v-slot:prepend>
			  <v-btn
				icon="mdi-minus"
				color="error"
				variant="text"
				@click="travellers.ADT > 1 && (travellers.ADT--)"
			  ></v-btn>
				<div class="text-h3 ps-2 pe-5" v-text="travellers.ADT"></div>
			</template>
		  </v-list-item>
		  
		  <?php /*
		  <v-list-item
			:subtitle="text_yth_desc"
			:title="text_yth_n"
		  >
			<template v-slot:append>
			  <v-btn
				icon="mdi-plus"
				color="success"
				variant="text"
				@click="travellers.YTH < 9 && (travellers.YTH++)"
			  ></v-btn>
			</template>
			<template v-slot:prepend>
			  <v-btn
				icon="mdi-minus"
				variant="text"
				color="error"
				@click="travellers.YTH >= 1 && (travellers.YTH--)"
			  ></v-btn>
				<div class="text-h3 ps-2 pe-5" v-text="travellers.YTH"></div>
			</template>
		  </v-list-item>
		  */ ?>
		  
		  <v-list-item
			:subtitle="text_chd_desc"
			:title="text_chd_n"
		  >
			<template v-slot:append>
			  <v-btn
				icon="mdi-plus"
				color="success"
				variant="text"
				@click="(travellers.CHD || []).length < 4 && (!travellers.CHD && (travellers.CHD = []), travellers.CHD.push(0))"
			  ></v-btn>
			</template>
			<template v-slot:prepend>
			  <v-btn
				icon="mdi-minus"
				variant="text"
				color="error"
				@click="(travellers.CHD || []).length >= 1 && (travellers.CHD.pop())"
			  ></v-btn>
				<div class="text-h3 ps-2 pe-5" v-text="(travellers.CHD || []).length"></div>
			</template>
		  </v-list-item>
		  
		  <v-list-item
			v-for="(age, child_index) in (travellers.CHD || [])"
			:subtitle="this.text_child_leave_age"
			:title="this.text_child_index_age + ' ' + (child_index + 1)"
		  >
			<template v-slot:append>
			  <v-btn
				icon="mdi-plus"
				color="success"
				variant="text"
				@click="age < 17 && (travellers.CHD[child_index]++)"
			  ></v-btn>
			</template>
			<template v-slot:prepend>
			  <v-btn
				icon="mdi-minus"
				variant="text"
				color="error"
				@click="age >= 1 && (travellers.CHD[child_index]--)"
			  ></v-btn>
				<div class="text-h3 ps-2 pe-5" v-text="age"></div>
			</template>
		  </v-list-item>
		</v-list>


		<v-card-actions>
		  <v-spacer></v-spacer>

		  <v-btn
			variant="text"
			@click="opened = false"
		  >
			Inchide
		  </v-btn>
		</v-card-actions>
	  </v-card>
	</v-menu>
</template>
		<template v-slot:default>
			Pasageri
		</template>
		</component>
	`,
}

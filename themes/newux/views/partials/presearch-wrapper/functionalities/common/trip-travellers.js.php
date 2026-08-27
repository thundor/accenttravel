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
			show_ins: false,
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
			text_adt_desc: 'Peste 18 ani',
			text_rom_1: 'Camera',
			text_rom_n: 'Camere',
			text_adt_1: 'Adult',
			text_adt_n: 'Adulti',
			text_yth_desc: 'Intre 12 - 17 ani',
			text_yth_1: 'Tanar',
			text_yth_n: 'Tineri',
			text_chd_1: 'Copil',
			text_chd_desc: 'Sub 17 ani, maxim 2 copii',
			text_chd_n: 'Copii',
			opened: false,
			travellers: [{
				ADT: 2,
				CHD: [],
				INS: [],
			}],
		}
	},
	computed: {
		passengers() {
			// console.warn(this.travellers);
			return (this.travellers || []).reduce((c, i) => {
				c['ROM'] = c['ROM'] ?? 0;
				c['ROM']++;
				Object.keys(i).forEach(a => {
					if(!i[a]) return;
					if(Array.isArray(i[a])){
						if(!i[a].length) return;
						if(!c[a]) {
							c[a] = i[a];
						} else {
							if(!Array.isArray(c[a])){
								console.error(this.travellers);
								throw "Invalid travellers";
							}
							c[a] = c[a].concat(i[a]);
						}
					} else {
						if(isNaN(i[a])) return;
						if(!c[a]) {
							c[a] = parseInt(i[a]);
						} else {
							if(Array.isArray(c[a])){
								console.error(this.travellers);
								throw "Invalid travellers";
							}
							c[a] = parseInt(c[a]) + parseInt(i[a]);
						}
					}
					// console.warn(a, i[a], i, c);
				});
				return c;
			}, {})
		},
	},
	mounted() {
		if(this.content_type != 'menu'){
			return;
		}
		var def = this.getObjectDotPathValue(this.data?.defaults, this.key.replace(/\//g, '.'));
		// console.log(this.key, def, this.data?.defaults);
		if(undefined !== def){
			this.travellers = def;
		}
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
		'travellers': {
			handler: function(nv,ov){
				// console.warn('should travellers', nv);
				this.setValue(nv);
			},
			immediate: true,
			deep: true
		},
	},
	methods: {
	},
	template : `
<component :is="loadViewAsync('partials/presearch-wrapper/functionality')" :active_menu="active_menu" :data="data" class="pa-0" :search_wrapper_step="search_wrapper_step">
<template v-slot:menu>
<v-menu
	  v-model="opened"
	  :close-on-content-click="false"
	  location="bottom end"
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
			:type="passengers ? 'hidden' : 'text'"
			:id="'menu-' + key"
		>

			<template v-slot:default>
				<div v-if="passengers" class="d-flex flex-column flex-nowrap text-truncate" style="font-size: 12px;line-height: 1;">
					<div class="d-flex ga-3">
					<strong v-if="passengers.ADT == 1" v-text="passengers.ADT + ' ' + text_adt_1"></strong>
					<strong v-else-if="passengers.ADT" v-text="passengers.ADT + ' ' + text_adt_n"></strong>
					<strong v-if="passengers.ROM == 1" v-text="passengers.ROM + ' ' + text_rom_1"></strong>
					<strong v-else-if="passengers.ROM" v-text="passengers.ROM + ' ' + text_rom_n"></strong>
					</div>
					<div class="d-flex ga-3">
					<span v-if="passengers.CHD && passengers.CHD.length == 1" v-text="passengers.CHD.length + ' ' + this.text_chd_1"></span>
					<span v-else-if="passengers.CHD && passengers.CHD.length" v-text="passengers.CHD.length + ' ' + this.text_chd_n"></span>
					<span v-if="passengers.YTH == 1" v-text="passengers.YTH + ' ' + text_yth_1"></span>
					<span v-else-if="passengers.YTH" v-text="passengers.YTH + ' ' + text_yth_n"></span>
					</div>
				</div>
			</template>					
		</v-text-field>
	  
	  </template>
	  <v-card min-width="300">
		<v-list-item
			subtitle="Numar camere"
			title="Camere"
		  >
			<template v-slot:append>
			  <v-btn
				icon="mdi-plus"
				color="success"
				variant="text"
				@click="travellers.length < 3 && (travellers.push({ADT: 1}))"
			  ></v-btn>
			</template>
			<template v-slot:prepend>
			  <v-btn
				icon="mdi-minus"
				color="error"
				variant="text"
				@click="travellers.length > 1 && (travellers.pop())"
			  ></v-btn>
				<div class="text-h3 ps-2 pe-5" v-text="travellers.length"></div>
			</template>
		</v-list-item>
		<div class="d-flex">
			<Travellers v-for="(room_travellers, room_index) in travellers" v-model="room_travellers" :index="room_index" :total="travellers.length" :show_ins="show_ins"></Travellers>
		</div>


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
	components: {
		Travellers: {
			emits: ['update:modelValue'],
			props: {
				modelValue: {
				  type: Object,
				  default: () => ({}),
				},
				total: {
				  type: Number,
				  default: () => (0),
				},
				index: {
				  type: Number,
				  default: () => (0),
				},
				show_ins: {
				  type: Boolean,
				  default: () => (false),
				},
			},
			data: () => ({
				travellers: {
					ADT: 1,
				},
				text_child_index_age: 'Varsta copil',
				text_child_leave_age: 'Varsta implinita la plecare',
				text_add_traveller: 'Adauga calator',
				text_adt_desc: 'Peste 18 ani',
				text_rom_1: 'Camera',
				text_rom_n: 'Camere',
				text_adt_1: 'Adult',
				text_adt_n: 'Adulti',
				text_yth_desc: 'Intre 12 - 17 ani',
				text_yth_1: 'Tanar',
				text_yth_n: 'Tineri',
				text_chd_1: 'Copil',
				text_chd_desc: 'Sub 17 ani, maxim 2 copii',
				text_chd_n: 'Copii',
				text_ins_1: 'Scaun bebe',
				text_ins_desc: 'Scaun separat pentru bebelusi',
				text_ins_n: 'Scaune bebe',
			}),
			mounted() {
			},
			unmounted() {
			},
			methods:{
			},
			computed: {
				total_adults() {
					return (this.travellers.ADT || 0) + (this.travellers.SEN || 0);
				},
				total_infants() {
					return (this.travellers.CHD || []).filter(a => a<=2).length;
				},
			},
			watch: {
				'modelValue': {
					handler(newValue, oldValue){
						this.travellers = newValue || {};
					},
					immediate: true,
				},
				travellers: {
					handler(newValue, oldValue){
						if(this.removeEmptyObjectsJson(newValue) === this.removeEmptyObjectsJson(this.data)){
							return;
						}
						newValue = this.removeEmptyObjects(newValue);
						var d = newValue && Object.keys(newValue).length && newValue || null;
						if(d){
							this.$emit('update:modelValue', d);
						}
					},
					deep: true,
				},
				'total_adults': {
					handler: function(nv,ov){
						if((nv || 0) >= (ov || 0)) return;
						// console.warn('total_adults', nv, ov);
						var total_adults = nv;
						var infants_arr = (this.travellers.CHD||[]).reduce((carry, item, index) => { if(item <=2) carry.push(index); return carry; }, []);
						// console.warn('infants_arr', infants_arr.length);
						if(infants_arr.length > total_adults){
							var remove_infants_arr = infants_arr.splice(- (infants_arr.length - total_adults));
							this.travellers.CHD = this.travellers.CHD.filter((value, index) => -1 === remove_infants_arr.indexOf(index));
							// console.warn('remove_infants_arr', remove_infants_arr);
						}
						// console.warn('this.travellers.CHD', this.travellers.CHD);
					},
					immediate: true,
				},
				'total_infants': {
					handler: function(nv,ov){
						console.warn('total_infants', nv, ov, this.travellers.INS);
						if(typeof this.travellers.INS !== 'number') {
							this.travellers.INS = 0;
						}
						if(this.travellers.INS && this.travellers.INS > nv){
							this.travellers.INS = nv;
						}
					},
					immediate: true,
				},
			},
			template: `
		<v-list>
		  <v-list-subheader v-if="total > 1" inset v-text="'Camera: ' + (1 + index)"></v-list-subheader>
		  <v-list-item
			:subtitle="text_adt_desc"
			:title="text_adt_n"
		  >
			<template v-slot:append>
			  <v-btn
				icon="mdi-plus"
				color="success"
				variant="text"
				@click="(travellers.ADT || 0) < 6 && ((travellers.ADT && travellers.ADT++) || (travellers.ADT = 1))"
			  ></v-btn>
			</template>
			<template v-slot:prepend>
			  <v-btn
				icon="mdi-minus"
				color="error"
				variant="text"
				@click="(travellers.ADT || 0) > 1 && (travellers.ADT--)"
			  ></v-btn>
				<div class="text-h3 ps-2 pe-5" v-text="travellers.ADT || 0"></div>
			</template>
		  </v-list-item>
		  
		  
		  <?php /* <v-list-item
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
		  </v-list-item> */ ?>
		  
		  <v-list-item
			:subtitle="text_chd_desc"
			:title="text_chd_n"
		  >
			<template v-slot:append>
			  <v-btn
				icon="mdi-plus"
				color="success"
				variant="text"
				@click="(travellers?.CHD?.length || 0) < 2 && ((travellers.CHD || (travellers.CHD = [])).push(3))"
			  ></v-btn>
			</template>
			<template v-slot:prepend>
			  <v-btn
				icon="mdi-minus"
				variant="text"
				color="error"
				@click="(travellers?.CHD?.length || 0) >= 1 && (travellers.CHD.pop())"
			  ></v-btn>
				<div class="text-h3 ps-2 pe-5" v-text="travellers?.CHD?.length || 0"></div>
			</template>
		  </v-list-item>
		  
		  <v-list-item
			v-for="(age, child_index) in travellers.CHD"
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
				@click="age >= 1 && (age > 3 || (travellers.CHD || []).filter(a => (a<=2)).length < (total_adults + (age < 3 ? 1 : 0))) && (travellers.CHD[child_index]--)"
			  ></v-btn>
				<div class="text-h3 ps-2 pe-5" v-text="age"></div>
			</template>
		  </v-list-item>
		  <v-list-item v-if="show_ins && (travellers.CHD || []).filter(a => (a<=2)).length"
			:subtitle="text_ins_desc"
			:title="text_ins_n"
		  >
			<template v-slot:append>
			  <v-btn
				icon="mdi-plus"
				color="success"
				variant="text"
				@click="travellers.INS < travellers.CHD.filter(a => (a<=2)).length && (travellers.INS++)"
			  ></v-btn>
			</template>
			<template v-slot:prepend>
			  <v-btn
				icon="mdi-minus"
				color="error"
				variant="text"
				@click="travellers.INS > Math.max((travellers.CHD.filter(a => (a<=2)).length - (total_adults)),0) && (travellers.INS--)"
			  ></v-btn>
				<div class="text-h3 ps-2 pe-5" v-text="travellers.INS"></div>
			</template>
		  </v-list-item>
		</v-list>`
		}
	}
}

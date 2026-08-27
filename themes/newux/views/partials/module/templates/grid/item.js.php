import BaseTemplate from '../base.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
import DOMPurify from 'dompurify';
export default {
	extends: BaseTemplate,
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	data: () => ({
		dialog: false,
		wind: 0,
		rules: {
			required: [
			  v => !!v && !!(v || '').trim() || 'Necesar',
			],
			filename: [
			  v => !v || (/[^0-9a-z_\-]/i.test((v || '')) && 'Doar alfanumeric, _ -' || true),
			],
		}
	}),
	props: {
		index: {
          type: Number,
          default: () => (0),
		},
		test: {
          type: Boolean,
          default: () => (false),
		},
		parent: {
          type: Object,
          default: () => (undefined),
		},
	},
	watch:{
		'form.data.default_mod_template': {
			handler(newValue, oldValue){
				if(oldValue && newValue !== oldValue)
					this.form.data.default_mod_data = null;
			},
			deep: true,
		},
		data: {
			handler(newValue, oldValue){
				// console.warn('UPDATED ROW', this.data);
				// this.$emit('update:modelValue', newValue);
				this.save();
			},
			deep: true,
		},
		dialog: {
			handler(newValue, oldValue){
				if(!newValue){
					this.form.data = JSON.parse(JSON.stringify(this.form.default));
				}
			},
		}
	},
	template : `<div>ROW</div>`,
	methods:{
		addItem(data, items, type){
			// console.warn('addItem', JSON.parse(JSON.stringify({data, items, type})));
			var obj = Object.keys(data).reduce((carry, dataIndex) => {
				if(-1 === ['index', 'sort_order', 'template'].indexOf(dataIndex)){
					carry[dataIndex] = data[dataIndex];
				}
				return carry;
			}, {});
			var remove_index = data.index;
			var name = data.name;
			var sort_order = parseInt('' === data.sort_order ? items.length + 1 : data.sort_order);
			if(isNaN(sort_order)){
				sort_order = items.length + 1;
			}
			sort_order --;
			if(sort_order > items.length) sort_order = items.length;
			if(sort_order < 0) sort_order = 0;
			if(data.template){
				name = null;
				obj = {name: data.template};
			}
			obj = JSON.parse(JSON.stringify(obj));
			
			// console.warn('TOSAVE', name, obj)
			this.saveData(name || undefined, obj, type).then(() => {
				if(name){
					obj = {name: name};
					obj = JSON.parse(JSON.stringify(obj));
				}
				if(undefined !== remove_index){
					// console.warn('removing item', remove_index, JSON.parse(JSON.stringify(items[remove_index])));
					items.splice(remove_index, 1);
				}
				// console.warn('adding item', obj, sort_order, JSON.parse(JSON.stringify(data)), this.data);
				items.splice(sort_order, 0, obj);
			});
		},
	},
	components: {
		Props: {
			emits: ['update:modelValue'],
			props: {
				modelValue: {
				  type: Object,
				  default: () => ({}),
				},
				type: {
				  type: String,
				  default: () => (''),
				},
			},
			data: () => ({
				data: {
				},
			}),
			template: `
			<div>
				<v-switch v-if="type == 'section'"
					color="primary"
					v-model="data.fluid"
					density="compact"
					hide-details
					label="FullWidth"
				></v-switch>
				<v-switch v-if="type == 'row'"
					color="primary"
					v-model="data.noGutters"
					density="compact"
					hide-details
					label="Fara interspatii"
				></v-switch>
				<v-select v-if="type == 'col'" label="Dimensiune" clearable v-model="data.cols" :items="[...Array(13)].map((v, i) => ({value: i && i || null, title: i && ((i / 12 * 100).toFixed(2) + '% (' + (i) + ')') || '- Auto -'}))"></v-select>
				<v-text-field v-model="data.class" label="Clase"></v-text-field>
				<v-textarea v-model="data.style" hide-details label="Stil CSS"></v-textarea>
				<template v-if="type == 'section'">
				<v-text-field v-model="data.height" label="Inaltime"></v-text-field>
				<v-text-field v-model="data.width" label="Latime"></v-text-field>
				<v-text-field v-model="data.minHeight" label="Inaltime minima"></v-text-field>
				<v-text-field v-model="data.minWidth" label="Latime minima"></v-text-field>
				<v-text-field v-model="data.maxHeight" label="Inaltime maxima"></v-text-field>
				<v-text-field v-model="data.maxWidth" label="Latime maxima"></v-text-field>
				</template>
			</div>
			`,
			watch: {
				'modelValue': {
				  handler(newValue, oldValue){
					this.data = newValue || {};
				  },
				  immediate: true,
				},
				data: {
					handler(newValue, oldValue){
						var d = newValue && Object.keys(newValue).length && newValue || null;
						
						if(d){
							console.warn('emitting', d, newValue);
							this.$emit('update:modelValue', d);
						}
					},
					deep: true,
				},
			}
		},
	},
}

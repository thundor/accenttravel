import BaseTemplate from './item.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
import DOMPurify from 'dompurify';
export default {
	extends: BaseTemplate,
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	data: () => ({
		template: '<?php echo basename(__DIR__,'.js.php'); ?>',
		path: '<?php echo basename(__FILE__,'.js.php'); ?>',
		form: {
			default: {status: true},
			edit: false,
			data: {},
		},
		data: {}
	}),
	props: {
		grid: {
          type: Object,
          default: () => (undefined),
		},
		row: {
          type: Object,
          default: () => (undefined),
		},
		section: {
          type: Object,
          default: () => (undefined),
		},
	},
	beforeMount() {
	},
	computed: {
		mod() {
			var mod = this.output;
			var col = this.parent;
			var row = this.row;
			var section = this.section;
			var r = {...JSON.parse(JSON.stringify(mod)),
				mod_template: mod.mod_template || col.default_mod_template || row.default_mod_template || section.default_mod_template,
				default_mod_data: col.default_mod_data || row.default_mod_data || section.default_mod_data || undefined,
			};
			// console.warn({mod, col, row, section, r});
			return r;
		}
	},
	watch:{
		'form.data.mod_template': {
			handler(newValue, oldValue){
				if(oldValue && newValue !== oldValue)
					this.form.data.mod_data = null;
			},
			deep: true,
		},
		/* data: {
			handler(newValue, oldValue){
				console.warn('UPDATED ROW', this.data);
			},
			deep: true,
		}, */
	},
	methods:{
	},
	template : `
	<?php if($this->theme->_can_edit){ ?>
	<div v-if="designerMode" class="border-lg border-warning border-opacity-50" >
		<v-list-item lines="none" :active="form.data.index == index" class="pa-2" :class="{'bg-grey-lighten-3': !mod.status}">
			<template v-slot:prepend>
				<v-menu
				  open-on-hover
				  open-on-click
				>
				  <template v-slot:activator="{ props }">
					<v-btn
					  color="warning"
					  size="xsmall"
					  class="px-1 me-2"
					  v-bind="props"
					>
					  Modul {{ index+1 }} {{ final.name }}
					</v-btn>
				  </template>

				  <v-list>
					<v-list-item  @click.stop="((form.data = JSON.parse(JSON.stringify({...final, sort_order: index+1, index: index}))), dialog = true)">
					  <v-list-item-title><v-icon icon="mdi-pencil"></v-icon> Modifica</v-list-item-title>
					</v-list-item>
					<v-list-item  @click.stop="parent.children.splice(index, 1)">
					  <v-list-item-title><v-icon icon="mdi-delete-forever-outline"></v-icon> Sterge</v-list-item-title>
					</v-list-item>
				  </v-list>
				</v-menu>
			
			  
			</template>
			<template v-slot:append v-if="mod.mod_template">
				<v-btn
				  size="xsmall"
				  icon="mdi-cog"
				  @click.stop="$refs.mod.dialog = true"
				></v-btn>
			</template>
			<v-list-item-title>{{ final.text }}</v-list-item-title>
		</v-list-item>
		
		<component ref="mod" v-if="mod.mod_template" :is="loadViewAsync('partials/module/templates/' + (mod.mod_template))" :default="mod.default_mod_data" v-model="data.mod_data" :editable="false" :custom="custom" v-on:custom="(...arguments) => $emit('custom', ...arguments)" :designParent="true"></component>
		<div v-else>- No template selected -</div>
		
		<v-dialog v-model="dialog">
			<template v-slot:default="{ isActive }">
				<v-card class="align-self-center" style="max-width: min(95vw, 630px);width:630px">
					<v-card-title v-text="'Modul'"></v-card-title>
					<v-card-text>
						<v-form ref="form">
							<template v-if="true">
							<v-text-field v-model="form.data.text" label="Titlu"></v-text-field>
							<v-switch
								:indeterminate="'boolean' != typeof form.data.status"
								color="primary"
								v-model="form.data.status"
								density="compact"
								hide-details
								label="Status"
							></v-switch>
							<v-text-field v-model="form.data.sort_order" :rules="rules.number" label="Sort" :placeholder="'' + (index + 1)"></v-text-field>
							<v-select clearable label="Sablon modul" v-model="form.data.mod_template" :items="templates.mod"></v-select>
							</template>
						</v-form>
					</v-card-text>
					<v-card-actions>
						<v-spacer></v-spacer>
						<v-btn class="d-flex text-none font-weight-normal cancel-button" size="large" variant="outlined" @click.stop="isActive.value = false"><v-icon icon="mdi-arrow-left"></v-icon> Inchide</v-btn>
						<v-btn class="d-flex text-none font-weight-normal save-button" size="large" variant="outlined" @click.stop="$refs.form.validate().then(r => r.valid && (addItem(form.data, parent.children, '<?php echo basename(__FILE__,'.js.php'); ?>'), $refs.form.reset(), (isActive.value = false)))"><v-icon icon="mdi-content-save"></v-icon> Salveaza</v-btn>
					</v-card-actions>
				</v-card>
			</template>
		</v-dialog>
	</div>
	<template v-else>
	<?php } ?>
		<component v-if="mod.status && mod.mod_template" :is="loadViewAsync('partials/module/templates/' + (mod.mod_template))" :default="mod.default_mod_data" v-model="mod.mod_data" :editable="false" :custom="custom" v-on:custom="(...arguments) => $emit('custom', ...arguments)" :designParent="designerMode"></component>
	<?php if($this->theme->_can_edit){ ?>
	</template>
	<?php } ?>
	`,
}

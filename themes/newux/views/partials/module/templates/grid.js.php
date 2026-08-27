import BaseTemplate from './base.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
import DOMPurify from 'dompurify';
export default {
	extends: BaseTemplate,
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	data: () => ({
		template: '<?php echo basename(__FILE__,'.js.php'); ?>',
		data: {
			title: 'VList Module title',
			children: [
				{text:"Demo section", status: 1},
			],
		},
		dialog: false,
		rules: {
			required: [
			  v => !!v && !!(v || '').trim() || 'Necesar',
			],
			filename: [
			  v => !v || (/[^0-9a-z_\-]/i.test((v || '')) && 'Doar alfanumeric, _ -' || true),
			],
		},
		path: null,
		form: {
			default: {},
			data: {},
		},
	}),
	beforeMount() {
		// console.warn('GRID', this.data);
	},
	computed: {
	},
	watch:{
		dialog: {
			handler(newValue, oldValue){
				if(newValue){
					var data = {...JSON.parse(JSON.stringify(this.form.default)), ...JSON.parse(JSON.stringify(this.data))};
					this.form.data = data;
				}
			},
		}
	},
	methods:{},
	template : `
	<?php if($this->theme->_can_edit){ ?>
	<v-btn v-if="name && !designGlobal" class="design-this px-2" icon="mdi-pencil" size="sm" @click="designThis = !designThis"><v-icon icon="mdi-pencil"></v-icon> Edit {{ name }}</v-btn>
	<?php } ?>
	<?php if($this->theme->_can_edit){ ?>
	<v-sheet v-if="designerMode" class="w-100 ga-1 d-flex flex-column border-lg pa-1">
		<div>
		<v-dialog v-model="dialog">
			<template v-slot:activator="{ props }">
				<v-btn
				  color="primary"
				  size="xsmall"
				  class="px-1 me-2"
				  v-bind="props"
				>
				  <v-icon icon="mdi-cog"></v-icon>
				  Grid {{ data.name }}
				</v-btn>
			  </template>
			<template v-slot:default="{ isActive }">
				<v-card class="align-self-center" style="max-width: min(95vw, 630px);width:630px">
					<v-card-title v-text="'Grid'"></v-card-title>
					<v-card-text class="max-height overflow-y-auto">
						<v-form ref="form">
							<v-select clearable label="Incarca Sablon Grid" v-model="form.data.template" :items="templates.grid"></v-select>
							<template v-if="!form.data.template">
								<v-text-field v-model="form.data.name" :rules="rules.filename" label="Salvare ca sablon cu numele:"></v-text-field>
							</template>
						</v-form>
					</v-card-text>
					<v-card-actions>
						<v-spacer></v-spacer>
						<v-btn class="d-flex text-none font-weight-normal cancel-button" size="large" variant="outlined" @click.stop="isActive.value = false"><v-icon icon="mdi-arrow-left"></v-icon> Inchide</v-btn>
						<v-btn class="d-flex text-none font-weight-normal save-button" size="large" variant="outlined" @click="$refs.form.validate().then(r => r.valid && ((data = JSON.parse(JSON.stringify(form.data))), $refs.form.reset(), (isActive.value = false)))"><v-icon icon="mdi-content-save"></v-icon> Salveaza</v-btn>
					</v-card-actions>
				</v-card>
			</template>
		</v-dialog>
		</div>
		<template v-if="final.children">
			<component v-for="(section, sectionIndex) in final.children" :key="sectionIndex" :is="loadViewAsync('partials/module/templates/grid/section')" :custom="custom" v-on:custom="(...arguments) => $emit('custom', ...arguments)" :parent="final" v-model="section" :index="sectionIndex" :designParent="true"></component>
		</template>
		<v-btn class="buton-add-modul" @click="addChild()" size="small" icon="mdi-plus"></v-btn>
	</v-sheet>
	<template v-else>
	<?php } ?>
		<component v-for="(section, sectionIndex) in output.children" :key="sectionIndex" :is="loadViewAsync('partials/module/templates/grid/section')" :custom="custom" v-on:custom="(...arguments) => $emit('custom', ...arguments)" :parent="output" v-model="section" :index="sectionIndex" :designParent="designerMode" ></component>
	<?php if($this->theme->_can_edit){ ?>
	</template>
	<?php } ?>
	`,
}

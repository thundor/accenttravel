import BaseTemplate from './base.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
import DOMPurify from 'dompurify';
export default {
	extends: BaseTemplate,
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	inheritAttrs: false,
	data: () => ({
		template: '<?php echo basename(__FILE__,'.js.php'); ?>',
		dialog: false,
		form: {
			data: {
			},
			rules: {
				required: [
				  v => !!v && !!(v || '').trim() || 'Necesar',
				],
			}
			
		},
		data: {
			title: 'Section title',
		}
	}),
	components: {
		<?php if($this->theme->_can_edit){ ?>
		Edit: {
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
			methods:{
				validate(){
					this.data.html = DOMPurify.sanitize($('#section-title-module-textarea-field').val());
					return this.$refs.form.validate();
				},
				reset(){
					return this.$refs.form.reset();
				},
			},
			template: `
			<v-form ref="form">
				<v-text-field v-model="data.title" label="Titlu"></v-text-field>
				<textarea id="section-title-module-textarea-field" v-model="data.html"></textarea>
			</v-form>
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
							this.$emit('update:modelValue', d);
						}
					},
					deep: true,
				},
			}
		},
		<?php } ?>
	},
	watch:{
		dialog: {
			handler(newValue, oldValue){
				if(newValue){
					this.form.data = JSON.parse(JSON.stringify(this.data));
					setTimeout(() => {
						var editor = this.makeHtmlEditor('#section-title-module-textarea-field');
					},0);
				}
			},
		},
		data: {
			handler(newValue, oldValue){
				this.save();
			},
			deep: true,
		}
	},
	methods:{
	},
	template : `
	<div class="d-flex align-center text-bold lis-section-title">
		<v-divider :thickness="4" class="border-opacity-100 border-md border-warning"></v-divider>
		<div class="flex-0 d-flex align-center" style="max-width: calc(100% - 90px);">
			<div class="text-center px-3" style="width:560px; max-width:100%;">
				<div v-if="data.title" class="lis-section-title-tit" v-html="data.title"></div>
				<div v-if="data.html" class="lis-section-title-sub" v-html="data.html"></div>
			</div>
		</div>
		<v-divider :thickness="4" color="warning" class="border-opacity-100 border-md border-warning"></v-divider>
	</div>
	<?php if($this->theme->_can_edit){ ?>
	<template v-if="designerMode">
		<template v-if="editable && inline">
			<Edit ref="form" v-model="data"></Edit>
		</template>
		<template v-else>
			<v-toolbar v-if="editable" color="transparent">
				<template v-slot:append>
					<v-btn
						primary
						@click="dialog = true"
						icon="mdi-pencil"
					></v-btn>
				</template>
			</v-toolbar>
			<v-dialog v-model="dialog">
				<template v-slot:default="{ isActive }">
					<v-card class="align-self-center" style="max-width: 1200px;width:100%">
						<v-card-title v-text="'Editare Titlu Sectiune'"></v-card-title>
						<v-card-text class="max-height overflow-y-auto">
							<Edit ref="form" v-model="form.data"></Edit>
						</v-card-text>
						<v-card-actions>
							<v-spacer></v-spacer>
							<v-btn class="d-flex text-none font-weight-normal cancel-button" size="large" variant="outlined" @click="isActive.value = false"><v-icon icon="mdi-arrow-left"></v-icon> Inchide</v-btn>
							<v-btn class="d-flex text-none font-weight-normal save-button" size="large" variant="outlined" @click="$refs.form.validate().then(r => r.valid && ((data = JSON.parse(JSON.stringify(form.data))), $refs.form.reset(), (isActive.value = false)))"><v-icon icon="mdi-content-save"></v-icon> Salveaza</v-btn>
						</v-card-actions>
					</v-card>
				</template>
			</v-dialog>
		</template>
	</template>
	<?php } ?>
	`,
}

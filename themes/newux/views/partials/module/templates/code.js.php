import BaseTemplate from './base.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
import DOMPurify from 'dompurify';
export default {
	extends: BaseTemplate,
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	data: () => ({
		template: '<?php echo basename(__FILE__,'.js.php'); ?>',
		dialog: false,
		rerender: false,
		form: {
			data: {},
			rules: {
				required: [
				  v => !!v && !!(v || '').trim() || 'Necesar',
				],
			}
			
		},
		data: {
			code: '<h5 class="text-center">- CODE content-</h5>',
		}
	}),
	components: {
		/* Code: {
			template: `<h1>CODE</h1>`,
		}, */
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
					return this.$refs.form.validate();
				},
				reset(){
					return this.$refs.form.reset();
				},
			},
			template: `
			<v-form ref="form">
				<v-textarea id="code-module-textarea-field" v-model="data.code" height="400"></v-textarea>
				<template v-if="(data.props = data.props || {})" v-for="props in [data.props]">
					<v-text-field v-model="props.class" label="Clase"></v-text-field>
				</template>
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
				}
			},
		},
		data: {
			handler(newValue, oldValue){
				this.rerender = false;
				/* this.$options.components.Code = {
					template: this.output.code,
				}; */
				this.$nextTick(() => {
				  this.rerender = true;
				});
				this.save();
			},
			deep: true,
		}
	},
	methods:{
		renderFunction(template) {
			return {
				emits: ['custom'],
				props: {
					custom: {
					  default: () => ({}),
					},
				},	
				template: template
			}
		}
	},
	template : `
	<div v-bind="output.props || {}"><component :is="renderFunction(this.output.code)" :custom="custom" v-if="rerender" @custom="(...arguments) => $emit('custom', ...arguments)"></component></div>
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
					<v-card class="align-self-center" style="max-width: 100%;width:800px">
						<v-card-title v-text="'Editare COD'"></v-card-title>
						<v-card-text>
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

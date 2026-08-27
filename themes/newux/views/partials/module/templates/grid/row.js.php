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
	},
	beforeMount() {
	},
	computed: {
	},
	watch:{
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
	<div v-if="designerMode" class="pa-2 border-lg border-primary border-opacity-50 w-100 overflow-hidden">
	<v-list-item lines="none" :active="form.data.index == index" class="pa-2">
		<template v-slot:prepend>
			<v-menu
			  open-on-hover
			  open-on-click
			>
			  <template v-slot:activator="{ props }">
				<v-btn
				  color="primary"
				  size="xsmall"
				  class="px-1 me-2"
				  v-bind="props"
				>
				  Rand {{ index+1 }} {{ final.name }}
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
		<template v-slot:append>
			<div class="d-flex ga-2">
			<v-btn
			  color="primary"
			  size="xsmall"
			  class=""
			  icon="mdi-content-duplicate"
			  @click.stop="duplicate(index)"
			>
			</v-btn>
			<v-btn
			  color="success"
			  size="xsmall"
			  class=""
			  icon="mdi-plus"
			  @click.stop="addChild()"
			>
			</v-btn>
			</div>
		</template>
		<v-list-item-title>{{ final.text }}</v-list-item-title>
	</v-list-item>
	<v-row :class="{'bg-grey-lighten-3': !output.status}" v-bind="output.props || {}" class="pa-2">
		<template v-if="final.children && final.children.length || (final.children = [{}])" v-bind="output.props || {}">
			<component v-for="(col, colIndex) in final.children" :key="colIndex" :is="loadViewAsync('partials/module/templates/grid/col')" :parent="final" :section="parent" :grid="grid" v-model="col" :index="colIndex" :default="{status: output.status}" :custom="custom" v-on:custom="(...arguments) => $emit('custom', ...arguments)" :designParent="designerMode"></component>
		</template>
		
		<v-dialog v-model="dialog">
			<template v-slot:default="{ isActive }">
				<v-card class="align-self-center" style="max-width: min(95vw, 630px);width:630px">
					<v-card-title v-text="'Rand'"></v-card-title>
					<v-card-text class="px-0 max-height overflow-y-auto">
						<v-form ref="form">
							<v-layout>
							<v-navigation-drawer v-if="!form.data.template"
								expand-on-hover
								rail
							  >
								<v-list density="compact" nav>
								  <v-list-item prepend-icon="mdi-cog-outline" title="General" :key="0" :value="0" @click.stop="wind=0" :active="wind==0"></v-list-item>
								  <v-list-item prepend-icon="mdi-application-edit-outline" title="Props" :key="1" :value="1" @click.stop="wind=1" :active="wind==1"></v-list-item>
								  <v-list-item prepend-icon="mdi-code-block-braces" title="Sablon" :key="2" :value="2" @click.stop="wind=2" :active="wind==2"></v-list-item>
								</v-list>
							  </v-navigation-drawer>
							<v-main>
							<v-window v-model="wind" class="px-2" style="min-height: 150px;">
								<v-window-item v-if="!form.data.template" :value="0">
									<v-text-field v-model="form.data.text" label="Titlu administrativ"></v-text-field>
									<v-switch
										:indeterminate="'boolean' != typeof form.data.status"
										color="primary"
										v-model="form.data.status"
										density="compact"
										hide-details
										label="Status"
									></v-switch>
									<v-text-field v-model="form.data.sort_order" :rules="rules.number" label="Sort" :placeholder="'' + (index + 1)"></v-text-field>
									<?php /* <pre v-text="JSON.stringify(form.data.default_mod_data || {}, 2)"></pre> */ ?>
								</v-window-item>
								<v-window-item v-if="!form.data.template" :value="1">
									<Props v-model="form.data.props" type="row"></Props>
								</v-window-item>
								<v-window-item :value="2">
									<v-select clearable label="Incarca Sablon Sectiune" v-model="form.data.template" :items="templates[path]"></v-select>
									<template v-if="!form.data.template">
										<v-text-field v-model="form.data.name" :rules="rules.filename" label="Salvare ca sablon cu numele:"></v-text-field>
										<v-select clearable label="Sablon Implicit de modul" v-model="form.data.default_mod_template" :items="templates.mod"></v-select>
										<component v-if="form.data.default_mod_template" :is="loadViewAsync('partials/module/templates/' + form.data.default_mod_template)" v-model="form.data.default_mod_data" :designParent="designerMode"></component>
									</template>
								</v-window-item>
							</v-window>
							</v-main>
							</v-layout>
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
	</v-row>
	</div>
	<template v-else>
	<?php } ?>
		<template v-if="output.children && output.status">
		<template v-if="parent?.carousel">
			<v-carousel-item :value="index">
				<v-container fluid>
					<v-row v-bind="output.props || {}">
						<component v-if="output.children" v-for="(row, index) in output.children" :key="index" :is="loadViewAsync('partials/module/templates/grid/col')" :parent="output" v-model="row" :grid="grid" :section="parent" :index="index" :default="{status: output.status}" :custom="custom" v-on:custom="(...arguments) => $emit('custom', ...arguments)" :designParent="designerMode"></component>
					</v-row>
				</v-container>
			</v-carousel-item>
		</template>
		<v-row v-else v-bind="output.props || {}">
			<component v-if="output.children" v-for="(row, index) in output.children" :key="index" :is="loadViewAsync('partials/module/templates/grid/col')" :parent="output" v-model="row" :grid="grid" :section="parent" :index="index" :default="{status: output.status}" :custom="custom" v-on:custom="(...arguments) => $emit('custom', ...arguments)" :designParent="designerMode"></component>
		</v-row>
		</template>
	<?php if($this->theme->_can_edit){ ?>
	</template>
	<?php } ?>
	`,
}

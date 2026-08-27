<?php 
if($this->theme->_can_edit){
	$module_templates = array_map(function($path){return ['value' => basename($path, '.js.php'), 'title' => basename($path, '.js.php'), ]; }, glob($this->theme->theme_path . 'views/partials/module/templates/*.js.php')); 
}
?>
import DOMPurify from 'dompurify';
export default {
	extends: BaseTemplate,
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	data: () => ({
		template: '<?php echo basename(__DIR__,'.js.php'); ?>',
		path: '<?php echo basename(__FILE__,'.js.php'); ?>',
		data: {}
	}),
	props: {
	},
	beforeMount() {
	},
	computed: {
	},
	watch:{
	},
	methods:{
	},
	template : `
	<?php if($this->theme->_can_edit){ ?>
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
		<v-select clearable label="Sablon modul" v-model="form.data.mod_template" :items="<?php echo htmlspecialchars(json_encode($module_templates), ENT_QUOTES); ?>"></v-select>
		<component v-if="form.data.mod_template" :is="loadViewAsync('partials/module/templates/' + form.data.mod_template)"></component>
		</template>
	</v-form>
	<template v-else>
	<?php } ?>
		<component v-if="mod.status && mod.mod_template" :is="loadViewAsync('partials/module/templates/' + (mod.mod_template))" :default="mod.default_mod_data" v-model="mod.mod_data"></component>
	<?php if($this->theme->_can_edit){ ?>
	</template>
	<?php } ?>
	`,
}

import BaseTemplate from './base.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
import DOMPurify from 'dompurify';
export default {
	extends: BaseTemplate,
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	props: {
		modTemplate: {
		  type: String,
		  default: () => (''),
		},
	},
	data: () => ({
		template: '<?php echo basename(__FILE__,'.js.php'); ?>',
		path: 'custom',
		data: {
			module: {},
		}
	}),
	beforeMount() {
		console.warn('GRID', this.data);
	},
	computed: {
	},
	watch:{},
	methods:{},
	template: `
	<?php if($this->theme->_can_edit){ ?>
	<v-btn v-if="data.name && !designGlobal" class="design-this px-2" icon="mdi-pencil" size="sm" @click="designThis = !designThis"><v-icon icon="mdi-pencil"></v-icon> Edit {{ data.name }}</v-btn>
	<?php } ?>
		<component v-if="modTemplate && modTemplate != 'module'" :is="loadViewAsync('partials/module/templates/' + modTemplate)" v-model="final.module" :designParent="designerMode"></component>
	`
}

import BaseTemplate from '../base.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
export default {
	extends: BaseTemplate,
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	data: () => ({
		template: '<?php echo basename(__FILE__,'.js.php'); ?>',
		data: {
			html: 'VList Item Subtitle',
		}
	}),
	inheritAttrs: false,
	beforeMount() {
	},
	computed: {
	},
	watch:{},
	methods:{},
	template : `
	<?php if($this->theme->_can_edit){ ?>
	<v-text-field  v-if="designerMode && editable" v-model="final.html" label="Text"></v-text-field>
	<?php } ?>
	<v-card-subtitle v-html="output.html" <?php if($this->theme->_can_edit){ ?> v-else <?php } ?>></v-card-subtitle>
	`,
}

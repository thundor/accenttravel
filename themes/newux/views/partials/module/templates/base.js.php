window['newux_modules'] = window['newux_modules'] || {};
import { ref, reactive, watch } from "vue";

let save_timer;
let save_timer2;
export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	emits: ['update:modelValue', 'custom'],
	data: () => ({
		save_timer2: undefined,
		rerender_index: 1,
		designThis: undefined,
		name: undefined,
		path: undefined,
		template: undefined,
		data: undefined,
		internalValue: undefined,
<?php 
if($this->theme->_can_edit){
	$mod_templates = glob($this->theme->theme_path . 'views/partials/module/templates/*.js.php'); 
	$mod_templates = array_filter($mod_templates, function($full_name){
		return 'module' !== basename($full_name, '.js.php');
	});
	$mod_templates = array_map(function($path){return ['value' => basename($path, '.js.php'), 'title' => basename($path, '.js.php'), ]; }, $mod_templates); 
	$mod_templates = array_values($mod_templates);
	$section_templates = array_map(function($path){return ['value' => basename($path, '.json'), 'title' => basename($path, '.json'), ]; }, glob($this->theme->theme_path . 'views/partials/module/saved/grid/section/*.json')); 
	$row_templates = array_map(function($path){return ['value' => basename($path, '.json'), 'title' => basename($path, '.json'), ]; }, glob($this->theme->theme_path . 'views/partials/module/saved/grid/row/*.json')); 
	$col_templates = array_map(function($path){return ['value' => basename($path, '.json'), 'title' => basename($path, '.json'), ]; }, glob($this->theme->theme_path . 'views/partials/module/saved/grid/col/*.json')); 
	$grid_templates = array_map(function($path){return ['value' => basename($path, '.json'), 'title' => basename($path, '.json'), ]; }, glob($this->theme->theme_path . 'views/partials/module/saved/grid/*.json')); 
	
	?>
		templates:{
			section: <?php echo json_encode($section_templates); ?>,
			row: <?php echo json_encode($row_templates); ?>,
			col: <?php echo json_encode($col_templates); ?>,
			mod: <?php echo json_encode($mod_templates); ?>,
			grid: <?php echo json_encode($grid_templates); ?>,
		},
	<?php
}
?>
	}),
	inheritAttrs: false,
	props: {
		forceName: {
          type: String,
          default: () => (undefined),
		},
		custom: {
          default: () => (undefined),
		},
		modelValue: {
          type: Object,
          default: () => (undefined),
		},
		inline: {
          type: Boolean,
          default: () => (false),
		},
		designParent: {
          type: Boolean,
          default: () => (false),
		},
		editable: {
          type: Boolean,
          default: () => (true),
		},
		<?php if($this->theme->_can_edit){ ?>
		<?php } ?>
		default: {
          type: Object,
          default: () => (undefined),
		},
	},
	template : `
	<h1>Module</h1>
	`,
	created() {
		if(this.default || this.modelValue){
			this.data = this.modelValue || {};
		}
		this.internalValue = JSON.parse(JSON.stringify(this.data || {}));
		if(this.forceName){
			this.data.name = this.forceName;
		}
	},
	mounted() {
		// this.internalValue = JSON.parse(JSON.stringify(this.data || {}));
		// console.warn('MOUNTED', this.name, this.template, this.path);
	},
	computed: {
		designerMode() {
			return this.designGlobal || this.designParent || this.designThis;
		},
		final() {
			return (this.data?.name && this.getOrAddNewxModule(this.data.name, this.data, this.path, this.template).data || this.data);
		},
		finalOrOutput() {
			return this.designerMode && this.final || this.output;
		},
		output() {
			return this.extendObj(this.default, this.final);
		},
	},
	methods: {
		changed(){
			// console.log('changed', this.name, this.template, this.path, JSON.stringify(this.data), this.data);
		},
		addChild(){
			// console.warn('ADDING CHILD', this.final, this.parent);
			(this.final.children || (this.final.children = [])).push({});
		},
		duplicate(index, arr){
			arr = arr || this.parent.children;
			arr.splice(index, 0, this.deepCopy(arr[index]));
		},
		getNewxModule(name, path, template) {
			template = template || this.template;
			var fp = template + (path ? '/' + path : '') + '/' + name;
			return window['newux_modules'][fp];
		},
		getOrAddNewxModule(name, data, path, template) {
			template = template || this.template;
			var fp = template + (path ? '/' + path : '') + '/' + name;
			return this.getNewxModule(name, path, template) || this.addNewxModule(name, data, path, template)
		},
		getOrInitiateNewxModule(name, data, path, template) {
			template = template || this.template;
			var fp = template + (path ? '/' + path : '') + '/' + name;
			return this.getNewxModule(name, path, template) || this.initiateNewxModule(name, data, path, template)
		},
		addNewxModule(name, data, path, template) {
			// console.warn('addNewxModule', {name, data, path, template});
			template = template || this.template;
			var fp = template + (path ? '/' + path : '') + '/' + name;
			var saved = this.getSavedData(fp) || {};
			if(data){
				var data_parsed = JSON.parse(JSON.stringify(data));
				for(let k in data_parsed){
					if(undefined === saved[k]){
						saved[k] = data_parsed[k];
					}
				}
			}
			saved.name = name;
			
			return this.initiateNewxModule(name, saved, path, template);
		},
		initiateNewxModule(name, data, path, template) {
			template = template || this.template;
			var fp = template + (path ? '/' + path : '') + '/' + name;
			var r = reactive({data: JSON.parse(JSON.stringify(data))});
			
			watch(r, (newValue, oldValue) => {
				clearTimeout(this.save_timer2);
				this.save_timer2 = setTimeout(() => {
					this.saveData(name, newValue.data, path, template, true);
				});
				// console.log("Data changed:", newValue);
			}, { deep: true });
			
			window['newux_modules'][fp] = r;
			return window['newux_modules'][fp];
		},
		getSavedData(path) {
			const request = new XMLHttpRequest();
			request.open("GET", `<?php echo $this->theme->theme_url; ?>/views/partials/module/saved/` + path + '.json?_=' + Date.now(), false);
			request.send(null);

			if (request.status === 200) {
			  return JSON.parse(request.responseText);
			}
			return null;
		},
		saveData(name, data, path, template, no_update) {
			
			template = template || this.template;
			if(!name) return new Promise((a) => a());
			var fp = template + (path ? '/' + path : '') + '/' + name;
			var tpl = this.getOrInitiateNewxModule(name, {}, path, template);
			
			if(!no_update){
				tpl.data = data;
				return new Promise(a => a());
			}
			
			var fetch_url = `${newux_url}/partials/save_data.json?${append_url}`
			var body = {
				<?php if ($this->config->item('csrf_protection')){ ?>
				<?php echo json_encode($this->security->get_csrf_token_name()); ?>: <?php echo json_encode($this->security->get_csrf_hash()); ?>,
				<?php } ?>
				data: JSON.stringify(data),
				template: template,
				name: name,
				path: path,
			};
			// console.warn('SAVING Data', JSON.parse(JSON.stringify(body)));
			return fetch(fetch_url, {
				method: 'POST',
				headers: {
				  'Accept': 'application/json'
				},
				body: new URLSearchParams(objToSerialize(body))
			}).then((response) => {
				if (!response.ok) {
					if(response.status == 403){
						// CSRF
						window.location = window.location.href.replace(/#.*/, '');
						throw new Error("Network response failed. Redirecting to self", {cause: response });
					}
					throw new Error("Network response was not ok", {cause: response });
				}
				return response;
			}).then((response) => response.json()).then((h) => {
				// console.warn('saved data', h);
				return h;
			}).catch((e) => {
				// console.error("Failed to save", e);
			})
		},
		save() {
			
			var changed = false;
			
			
			
			// if(!JSON.stringify(this.data || {}) !== JSON.stringify(this.internalValue || {})){
				// console.error('EMITTED update:modelValue', this.data, this.name, this.path, this.template);
				this.$emit('update:modelValue', this.data);
				changed = true;
			// }
			// this.internalValue = JSON.parse(JSON.stringify(this.data));
			if(!changed) {
				console.error('NOCHANGE', JSON.parse(JSON.stringify(this.data || {})));
				return;
			}
			if(!this.name) {
				if(!this.forceName && this.data.name && this.template && !this.getNewxModule(this.data.name, this.template, this.path)){
					// console.warn('getOrAddNewxModule', this.template + (this.path ? '/' + this.path : '') + '/' + this.data.name, this.data.name, this.path, this.template, this.data, this);
					// this.getOrAddNewxModule(this.data.name, {}, this.path, this.template);
					// this.data = {name: this.data.name};
				}
				// console.error('NONAME', this.template, this.path, JSON.parse(JSON.stringify(this.data || {})));
				return;
			}
			clearTimeout(save_timer);
			save_timer = setTimeout(() => {
				// console.warn("SAVE",JSON.stringify(this.data || {}),JSON.stringify(this.internalValue || {}));
				var fetch_url = `${newux_url}/partials/save_module.json?${append_url}`
				var data = {
					<?php if ($this->config->item('csrf_protection')){ ?>
					<?php echo json_encode($this->security->get_csrf_token_name()); ?>: <?php echo json_encode($this->security->get_csrf_hash()); ?>,
					<?php } ?>
					data: JSON.stringify(this.data),
					template: this.template,
					name: this.name,
				};
				// console.warn('SAVING', JSON.parse(JSON.stringify(this.data)));
				return fetch(fetch_url, {
					method: 'POST',
					headers: {
					  'Accept': 'application/json'
					},
					body: new URLSearchParams(objToSerialize(data))
				}).then((response) => {
					if (!response.ok) {
						if(response.status == 403){
							// CSRF
							window.location = window.location.href.replace(/#.*/, '');
							throw new Error("Network response failed. Redirecting to self", {cause: response });
						}
						throw new Error("Network response was not ok", {cause: response });
					}
					return response;
				}).then((response) => response.json()).then((h) => {
					console.warn('saved module', h);
				}).catch((e) => {
					console.error("Failed to save", e);
				}).finally(() => {
					
				})
			}, 200);
		},
	},
	watch: {
		'designerMode': {
		  handler(newValue, oldValue){
			  if(!!newValue !== !!oldValue){
				this.rerender_index = 0;
				this.$nextTick(() => {
					// console.warn('rerender');
					  this.rerender_index = true;
				});
			  }
		  },
		},
		'modelValue': {
		  handler(newValue, oldValue){
			if(!this.name && !oldValue?.template) {
				// var nv = newValue;
				if(!this.forceName && this.template ){
					// console.warn('newValue', JSON.parse(JSON.stringify(newValue || {})));
					// console.warn('oldValue', JSON.parse(JSON.stringify(oldValue || {})));
					if(newValue?.template){
						this.getOrAddNewxModule(newValue.template, null, this.path, this.template)?.data || {};
						newValue = {name: newValue.template};
					} else {
						if(oldValue?.name){
							var module = this.getNewxModule(oldValue.name, this.path, this.template);
							if(module){
								newValue = JSON.parse(JSON.stringify({...module.data || {}, name: newValue?.name}))
								// console.warn('newValuenewValue', JSON.parse(JSON.stringify(newValue || {})));
							}
						}
						if(newValue?.name){
							// console.warn('getOrAddNewxModule', this.template + (this.path ? '/' + this.path : '') + '/' + newValue.name, newValue.name, this.path, this.template, JSON.parse(JSON.stringify(newValue || {})), this);
							if(oldValue){
								var module = this.addNewxModule(newValue.name, {}, this.path, this.template);
								Object.assign(module.data, JSON.parse(JSON.stringify(newValue || {})));
							} else {
								this.getOrAddNewxModule(newValue.name, newValue, this.path, this.template);
							}
								
							newValue = {name: newValue.name};
						}
					}
					if(newValue){
						delete(newValue.template);
					}
				}
				// console.error('NONAME', this.template, this.path, JSON.parse(JSON.stringify(newValue || {})));
				if(newValue){
					if(!newValue.template){
						delete(newValue.template)
					}
					if(!newValue.name){
						delete(newValue.name)
					}
				}
			}
			
			  
			this.data = newValue || {};
			// console.error('UPDATED update:modelValue', this);
		  },
		},
		data: {
			handler(newValue, oldValue){
				if(this.removeEmptyObjectsJson(newValue) === this.removeEmptyObjectsJson(this.internalValue)){
					return;
				}
				this.internalValue = JSON.parse(JSON.stringify(this.data));
				
				// newValue = this.removeEmptyObjects(newValue);
				// console.error('UPDATED data', this.template, this.path, this.name, newValue, this);
				// this.$emit('update:modelValue', newValue);
				this.save();
			},
			deep: true,
		},
	}
}

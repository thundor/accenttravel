<?php
?>
import BaseTemplate from './base.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
import DOMPurify from 'dompurify';
export default {
	extends: BaseTemplate,
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	data: () => ({
		template: '<?php echo basename(__FILE__,'.js.php'); ?>',
		rules: {
			required: [
			  v => !!v && !!(v || '') || 'Necesar',
			],
			email:[
				v => v && !/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*\.[a-zA-Z]{2,}$/.test(v) && 'Invalid' || true,
			],
		},
		disabled: false,
		snackbar: false,
		snackbar2: false,
		snackbar2_text: '',
		data: {
			email: '',
		}
	}),
	beforeMount() {
	},
	computed: {
	},
	watch:{},
	
	methods:{
		submitForm() {
			var ref_name = 'newsletter_form';
			let $ref = this.$refs[ref_name];
			if(!$ref) return;
			this.disabled = true;
			console.warn('submitForm', ref_name, this.$refs);
			var ref = ref_name.replace(/_form$/, '');
			$ref.validate().then(validation => {
				if(validation.valid){
					var url = '';
					url = "<?php echo site_url('forms/newsletter/subscribe?force_ajax=1'); ?>";
					var data = {
						<?php if ($this->config->item('csrf_protection')){ ?>
						<?php echo json_encode($this->security->get_csrf_token_name()); ?>: <?php echo json_encode($this->security->get_csrf_hash()); ?>,
						<?php } ?>
						... this.data
					};
					fetch(url, {
						// signal: this.abortController.signal,
						method: 'POST',
						headers: {
						  'Accept': 'application/json'
						},
						body: new URLSearchParams(objToSerialize(data))
					}).then((response) => {
						if (!response.ok) {
							if(response.status == 403){
								// CSRF
								window.location.href = window.location.href;
								return;
							}
							throw new Error("Network response was not ok", {cause: response });
						}
						response.json().then((resp) => {
							console.warn('Response', resp)
							if(resp?.data?.url){
								window.location.replace(resp?.data?.url);
							}
							this.disabled = false;
							
							if(resp?.status == 'success'){
								$ref.reset();
								this.snackbar = true;
							} else {
								this.snackbar2 = true;
								this.snackbar2_text = resp?.message;
							}
						});
						return response;
					}).catch((error) => {
						console.warn('Fetch error', error);
						this.disabled = false;
					}).finally((data) => {
						
					}).then(data => {
						console.log('received data', data);
					});
					
					console.warn('Form is valid');
				} else {
					this.disabled = false;
					console.warn('Form is NOT valid');
				}
			}).catch(e => {
				this.disabled = false;
			})
		}
	},
	template : `
	<v-form  ref="newsletter_form" fast-fail @submit.prevent class="px-4" :disabled="disabled">
		<slot />
		<v-list-subheader>ABONARE NEWSLETTER</v-list-subheader>
		<p>Afla primul despre cele mai bune oferte!</p>	
	  <v-text-field
		validate-on="blur"
		label="Adresa ta de e-mail"
		v-model="data.email"
		 :rules="(rules.required || []).concat(rules.email || [])"
	  ></v-text-field>
	  
	  <v-btn class="mt-2" type="submit" block color="primary" :disabled="disabled" @click.stop="submitForm('newsletter_form')">Aboneaza-te</v-btn>
	  
		<v-snackbar
		  v-model="snackbar"
		  :timeout="5000"
		>
		  Abonare efectuata cu succes. Va multumim!
		  <template v-slot:actions>
			<v-btn
			  color="primary"
			  variant="outlined"
			  @click="snackbar = false"
			>
			  Inchide
			</v-btn>
		  </template>
		</v-snackbar>
		<v-snackbar
		  v-model="snackbar2"
		  :timeout="5000"
		>
			<div v-html="snackbar2_text"></div>
		  <template v-slot:actions>
			<v-btn
			  color="primary"
			  variant="outlined"
			  @click="snackbar2 = false"
			>
			  Inchide
			</v-btn>
		  </template>
		</v-snackbar>
	</v-form>
	`,
}

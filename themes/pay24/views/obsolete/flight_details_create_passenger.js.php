export default {
  emits: ['save'],
  props: {
      referenceDate: {
          type: Date,
          default: () => (new Date()),
      },
      referenceDateEnd: {
          type: Date,
          default: () => (new Date()),
      },
      passengers: {
          type: Array,
          default: () => ([]),
      },
      identification_required: {
          type: Boolean,
          default: () => (false),
      },
      secured: {
          type: Boolean,
          default: () => (false),
      },
  },
	data: () => ({
    kept: undefined,
    saved: {},
    cnt: 0,
    hash: undefined,
    filtered_countries: countries,
    type: 'ADT',
    valid: false,
    year_range: [],
    title: 'mr',
    doctype: '',
    ci: '',
    ci_s: new Date(),
    ci_e: new Date(),
    pass: '',
    pass_s: new Date(),
    pass_e: new Date(),
    pass_c: '',
    min_date: undefined,
    max_date: undefined,
    firstname: '',
    lastname: '',
    date: undefined,
    today: new Date(),
    dob: undefined,
    nationality: '',
    dialog: false,
    errors: [],
    
    firstnameRules: Object.freeze([
      v => !!v && !!v.trim() || 'Prenumele este necesar',
      v => v.trim().length <= 100 || 'Prenumele este prea lung',
    ]),
    nameRules: Object.freeze([
      v => !!v && !!v.trim() || 'Numele este necesar',
      v => v.trim().length <= 100 || 'Numele este prea lung',
    ]),
    dobRules: Object.freeze([
      v => !!v && !!v.toString().trim() || 'Data nasterii este necesara',
    ]),
    docDateRules: Object.freeze([
      v => !!v && !!v.toString().trim() || 'Data emiterii si data expirarii documentului sunt necesare',
    ]),
    nationalityRules:Object.freeze([]),
    texts: Object.freeze({
		'too_young' : 'Varsta aleasa este inferioara pragului si nu se incadreaza',
		'too_old' : 'Varsta aleasa este superioara pragului si nu se incadreaza',
    }),
    validations:Object.freeze([
		function(){ return this.min_date && this.min_date.toISOString().split('T')[0] > this.dob.toISOString().split('T')[0] ? 'too_old' : null },
		// function(){ return this.min_date && this.min_date.toISOString().split('T')[0] > this.dob.toISOString().split('T')[0] ? 'too_old ' + this.min_date.toISOString().split('T')[0] + ' ' + this.dob.toISOString().split('T')[0] : null },
		function(){ return this.max_date && this.max_date.toISOString().split('T')[0] < this.dob.toISOString().split('T')[0] ? 'too_young' : null},
		// function(){ return this.max_date && this.max_date.toISOString().split('T')[0] < this.dob.toISOString().split('T')[0] ? 'too_young ' + this.min_date.toISOString().split('T')[0] + ' ' + this.dob.toISOString().split('T')[0] : null},
		function(){ 
			if(!this.nationality) return "Alegeti nationalitatea";
			var d = this.referenceDate;
			var d_max = this.exp_date;
			if(this.doctype == '1'){
				return this.ci_s 
					&& this.ci_e 
					&& this.ci_s.toISOString().split('T')[0] < this.ci_e.toISOString().split('T')[0] 
					&& this.ci_s.toISOString().split('T')[0] <= d.toISOString().split('T')[0] 
					&& this.ci_e.toISOString().split('T')[0] <= d_max.toISOString().split('T')[0] 
					&& this.ci_e.toISOString().split('T')[0] >= d.toISOString().split('T')[0] 
					|| "Datele de emitere/expirare CI nu sunt corecte";
			}
			if(this.doctype == '0'){
				if(!this.pass_c) return "Alegeti tara emitenta a documentului";
				return this.pass_s 
					&& this.pass_e 
					&& this.pass_s.toISOString().split('T')[0] < this.pass_e.toISOString().split('T')[0] 
					&& this.pass_s.toISOString().split('T')[0] <= d.toISOString().split('T')[0] 
					&& this.pass_e.toISOString().split('T')[0] <= d_max.toISOString().split('T')[0] 
					&& this.pass_e.toISOString().split('T')[0] >= d.toISOString().split('T')[0] 
					|| "Datele de emitere/expirare pasaport nu sunt corecte";
			}
		},
    ]),
  }),
	template : `
<Modal v-model="dialog">
      <v-list lines="two" subheader theme="light" class="ma-4 mt-0 max-height" rounded="theme">
        <v-list-item-title class="pa-4 pb-2 text-h5">Adauga pasager nou</v-list-item-title>
        <v-messages class="pl-4" color="error" :active="!!errors.length" :messages="errors" style="position:sticky;top:0;opacity:1;z-index:3;text-shadow: 0px 0 1px #fff;"></v-messages>
        <v-form ref="form" v-model="valid" class="v-theme--dark">
          <v-radio-group inline color="primary" v-model="title" class="ms-2 mt-2 mb-3" hide-details="true">
            <v-radio label="Dl." value="mr"></v-radio>
            <v-radio label="Dna." value="mrs" class="ms-5"></v-radio>
            <v-radio label="Dra." value="ms" class="ms-5"></v-radio>
          </v-radio-group>
          <div class="px-4">
          <v-text-field ref="firstname"
            class="rounded-theme"
              v-model="firstname"
              :rules="firstnameRules"
              label="Prenume"
              variant="outlined"
              required
            ></v-text-field>
          <v-icon icon="mdi-arrow-expand-vertical" class="text-primary" theme="dark" style="position: absolute;left: 0;margin-top: -22px;" @click="() => {var d = '' + this.firstname; this.firstname = this.lastname; this.lastname = d;}"/>
          <v-text-field ref="lastname"
            class="rounded-theme"
              v-model="lastname"
              :rules="nameRules"
              label="Nume"
              variant="outlined"
              required
            ></v-text-field>

            <div>
            <q-select
              v-model="nationality"
              class="rounded-theme onlight pb-4"
			  :rules="CRules"
              use-input
              input-debounce="0"
              label="Nationalitate"
              :options="filtered_countries"
              emit-value
              map-options
              outlined
			  @update:modelValue="updatedNationality()"
              @filter="filterFn"
            >
            <template v-slot:option="scope">
                <q-item v-bind="scope.itemProps">
                  <q-item-section avatar>
                    <span class="v3q_tel__flag q-mr-sm" :class="{[scope.opt.value.toLowerCase()]: true}"></span>
                  </q-item-section>
                  <q-item-section>
                    <q-item-label>{{ scope.opt.label }}</q-item-label>
                  </q-item-section>
                </q-item>
              </template>
              <template v-slot:no-option>
                <q-item>
                  <q-item-section class="text-grey">
                    Nimic gasit
                  </q-item-section>
                </q-item>
              </template>
            </q-select>
            </div>
			
            <h5>Data nasterii:</h5>
			<IOSDatepicker :start-date="date || max_date" :preview-format="a => formatDate(a)" v-model="dob" month-name-format="long" locale="ro" :year-range="year_range" inline :min-date="min_date" :max-date="max_date" calendar-class-name="v-theme--dark" class="w-100 px-4" :enable-time-picker="false" :rules="dobRules" @click="unfocusElem"></IOSDatepicker>
            <div>
			
			<v-list-item-title class="pa-0 text-h5 d-none">Date referinta validare: <span v-html="formatDate(referenceDate)"></span> - <span v-html="formatDate(referenceDateEnd)"></span> </v-list-item-title>
			
			<h5>Document identificare:</h5>
			<v-radio-group inline color="primary" v-model="doctype" :rules="docTypeRules" class="mt-2 mb-3">
				<v-radio label="- Niciunul -" value=""></v-radio>
				<v-radio label="CI" value="1"></v-radio>
				<?php /*  */ ?>
				<v-radio label="Pasaport" value="0"></v-radio>
			</v-radio-group>
			
			<template v-if="doctype == '1'">
			<v-text-field ref="ci"
            class="rounded-theme"
              v-model="ci"
              :rules="ciRules"
              label="Cod numeric personal"
              variant="outlined"
              required
            ></v-text-field>
			<h5>Data EMITERE carte de identitate:</h5>
			<IOSDatepicker :start-date="ci_s || today" :preview-format="a => formatDate(a)" v-model="ci_s" month-name-format="long" locale="ro" :year-range="[min_date.getFullYear(), today.getFullYear()]" inline :min-date="min_date" :max-date="today" calendar-class-name="v-theme--dark" class="w-100 px-4" :enable-time-picker="false" :rules="docDateRules"></IOSDatepicker>
			<h5>Data EXPIRARE carte de identitate:</h5>
			<IOSDatepicker :start-date="ci_e || today" :preview-format="a => formatDate(a)" v-model="ci_e" month-name-format="long" locale="ro" :year-range="[min_date.getFullYear(), exp_date.getFullYear()]" inline :min-date="min_date" :max-date="exp_date" calendar-class-name="v-theme--dark" class="w-100 px-4" :enable-time-picker="false" :rules="docDateRules"></IOSDatepicker>
			</template>
			<template v-if="doctype == '0'">
			<v-text-field ref="pass"
            class="rounded-theme mb-3"
              v-model="pass"
              :rules="passRules"
              label="Numar pasaport"
              variant="outlined"
              required
            ></v-text-field>
            <q-select
              v-model="pass_c"
              :rules="passCRules"
              class="rounded-theme onlight pb-4"
              use-input
              input-debounce="0"
              label="Tara emitenta pasaport"
              :options="filtered_countries"
              emit-value
              map-options
              outlined
			  @update:modelValue="updatedNationality()"
              @filter="filterFn"
            >
            <template v-slot:option="scope">
                <q-item v-bind="scope.itemProps">
                  <q-item-section avatar>
                    <span class="v3q_tel__flag q-mr-sm" :class="{[scope.opt.value.toLowerCase()]: true}"></span>
                  </q-item-section>
                  <q-item-section>
                    <q-item-label>{{ scope.opt.label }}</q-item-label>
                  </q-item-section>
                </q-item>
              </template>
              <template v-slot:no-option>
                <q-item>
                  <q-item-section class="text-grey">
                    Nimic gasit
                  </q-item-section>
                </q-item>
              </template>
            </q-select>
			<h5>Data EMITERE pasaport:</h5>
			<IOSDatepicker :start-date="pass_s || today" :preview-format="a => formatDate(a)" v-model="pass_s" month-name-format="long" locale="ro" :year-range="[min_date.getFullYear(), today.getFullYear()]" inline :min-date="min_date" :max-date="today" calendar-class-name="v-theme--dark" class="w-100 px-4" :enable-time-picker="false" :rules="docDateRules"></IOSDatepicker>
			<h5>Data EXPIRARE pasaport:</h5>
			<IOSDatepicker :start-date="pass_e || today" :preview-format="a => formatDate(a)" v-model="pass_e" month-name-format="long" locale="ro" :year-range="[min_date.getFullYear(), exp_date.getFullYear()]" inline :min-date="min_date" :max-date="exp_date" calendar-class-name="v-theme--dark" class="w-100 px-4" :enable-time-picker="false" :rules="docDateRules"></IOSDatepicker>
			</template>
            </div>
			
          </div>
        </v-form>
		<?php /* <v-messages color="error" class="v-theme--light pa-4 mt-4 bg-theme bg-background rounded-theme" v-show="!!errors.length" :active="!!errors.length" :messages="errors" style="position:sticky"></v-messages> */ ?>
      </v-list>
      <template v-slot:footer="{ props }">
        <v-btn class="text-none font-weight-normal cancel-button" size="x-large" color="secondary" rounded="theme" @click="dialog = false"><v-icon icon="mdi-arrow-left"></v-icon></v-btn>
        <v-btn class="d-flex text-capitalize font-weight-normal" style="flex:1;" size="x-large" :color="true ? 'primary' : 'secondary'" rounded="theme" @click="validateAndSave().then((v) => v && (dialog = false))" v-html="'Confirma'"></v-btn>
      </template>
</Modal>
	`,

  methods: {
    validCNP( p_cnp ) {
		var i=0 , year=0 , hashResult=0 , cnp=[] , hashTable=[2,7,9,1,4,6,3,5,8,2,7,9];
		if( p_cnp.length !== 13 ) { return false; }
		for( i=0 ; i<13 ; i++ ) {
			cnp[i] = parseInt( p_cnp.charAt(i) , 10 );
			if( isNaN( cnp[i] ) ) { return false; }
			if( i < 12 ) { hashResult = hashResult + ( cnp[i] * hashTable[i] ); }
		}
		hashResult = hashResult % 11;
		if( hashResult === 10 ) { hashResult = 1; }
		year = (cnp[1]*10)+cnp[2];
		switch( cnp[0] ) {
			case 1  : case 2 : { year += 1900; } break;
			case 3  : case 4 : { year += 1800; } break;
			case 5  : case 6 : { year += 2000; } break;
			case 7  : case 8 : case 9 : { year += 2000; if( year > ( parseInt( new Date().getYear() , 10 ) - 14 ) ) { year -= 100; } } break;
			default : { return false; }
		}
		if( year < 1800 || year > 2099 ) { return false; }
		return ( cnp[12] === hashResult );
	},
    unfocusElem () {
		document.activeElement.blur();
	},
    updatedNationality () {
		console.warn('updatedNationality');
		setTimeout(() => {
			document.activeElement.blur();
			
		}, 500)
	},
    filterFn (val, update) {
        if (val === '') {
          update(() => {
            this.filtered_countries = countries

            // here you have access to "ref" which
            // is the Vue reference of the QSelect
          })
          return
        }

        update(() => {
          const needle = val.toLowerCase()
          this.filtered_countries = countries.filter(v => v.label.toLowerCase().indexOf(needle) > -1)
        })
      },
    reset(){
    },
    clearValidations(){
      this.$refs.form.resetValidation();
      this.errors = [];
    },
    changed2(a){
      this.date = a;
      return true;
    },
    changed(a){
      this.dob = a;
      return true;
    },
    formatDate(a){
      return a.toLocaleDateString('ro', {
				year: "numeric",
				month: "long",
				day: "numeric" 
      });
    },
    save(){
		console.warn('saving');
      var o = {
        hash: this.hash,
        title: this.title,
        firstname: this.firstname,
        lastname: this.lastname,
        nationality: this.nationality,
        doctype: this.doctype,
        pass: this.pass,
        pass_c: this.pass_c,
        pass_s: this.pass_s.toISOString().split('T')[0],
        pass_e: this.pass_e.toISOString().split('T')[0],
        ci: this.ci,
        ci_s: this.ci_s.toISOString().split('T')[0],
        ci_e: this.ci_e.toISOString().split('T')[0],
        birth_date: this.dob.toISOString().split('T')[0],
      }
      // this.kept = Object.assign({}, this.saved);
      this.$emit('save', o, this.type, this.cnt);
      this.$refs.form.reset();
      return true;
      // emit
    },
    validateAndSave(){
      this.clearValidations();
      this.validations.every(f => {
        var v = f.bind(this)();
		if(true === v) v = false;
        v && this.errors.push(this.texts[v] || v);
        return !v;
      })
      var valid = !this.errors.length;
      return this.$refs.form.validate().then(v => {
        if(v.valid && valid){
          this.save();
          return true;
        }
      });
    },
  },
  computed: {
    exp_date:{
      get() { 
		var a = new Date(); 
		a.setYear(a.getFullYear() + 100);
        return a;
      },
    },
    validPassengers:{
      get() { 
        return this.passengers;
      },
    },
	CRules: {
		get() { 
			return Object.freeze([
				v => {
					return !!v.trim().length || 'Alegeti nationalitatea';
				},
			])
		}
	},
	passCRules: {
		get() { 
			return Object.freeze([
				v => {
					if(!(this.doctype == '0')) return true; 
					return !!v.trim().length || 'Alegeti tara emitenta a documentului';
				},
			])
		}
	},
	docTypeRules: {
		get() { 
			return Object.freeze([
				v => {
					if(!(this.secured || this.identification_required)) return true; 
					return !!v.trim().length || 'Alegeti tipul documentului';
				},
			])
		}
	},
	ciRules: {
		get() { 
			return Object.freeze([
				v => !/[^0-9]/.test(v.trim()) || 'Numarul de document trebuie sa contina doar cifre',
				v => {
					if(!(this.doctype == '1')) return true; 
					return !!v.trim().length || 'Numarul de document este obligatoriu';
				},
				v => {
					if(!(this.doctype == '1')) return true; 
					if(this.nationality == 'RO'){
						return !/^[0-9]{13}$/.test(v.trim()) && 'Numarul de document trebuie sa contina 13 cifre' || this.validCNP(v) || "CNP Invalid";
					}
					return /^[0-9]{9,20}$/.test(v.trim()) || 'Numarul de document trebuie sa contina 9-20 cifre';
				},
			])
		}
	},
	passRules: {
		get() { 
			return Object.freeze([
				v => !/[^0-9]/.test(v.trim()) || 'Numarul de document trebuie sa contina doar cifre',
				v => {
					if(!(this.doctype == '0')) return true; 
					return !!v.trim().length || 'Numarul de document este obligatoriu';
				},
				v => { 
					if(!(this.doctype == '0')) return true;
					return /^[0-9]{9,20}$/.test(v.trim()) || 'Numarul de document trebuie sa contina 9-20 cifre';
				},
			])
		}
	},
    validPassengers:{
      get() { 
        return this.passengers;
      },
    },
  },
  watch:{
    'dob': {
      handler(newValue, oldValue){
        console.warn('dob',newValue);
      },
      immediate: true,
    },
    'type': {
      handler(newValue, oldValue){
        var max_years = 130;
        var min_years = 0;
        switch(newValue){
          case 'ADT':
            min_years = 18;
            max_years = 60;
          break;
          case 'SEN':
            min_years = 60;
          break;
          case 'YTH':
            min_years = 12;
            max_years = 18;
          break;
          case 'CHD':
            min_years = 2;
            max_years = 12;
          break;
          case 'INF':
          case 'INS':
            min_years = 0;
            max_years = 2;
          break;
        }
        
        var min_date = new Date(this.referenceDate);
        min_date.setFullYear(this.referenceDate.getFullYear() - max_years);

        var max_date = new Date(this.referenceDate);
        max_date.setFullYear(this.referenceDate.getFullYear() - min_years);
        max_date.setDate(max_date.getDate() + 1);
        this.min_date = min_date;
        this.max_date = max_date;
        this.dob = max_date;
        // this.date = max_date;
        this.year_range = [this.min_date.getFullYear(), this.max_date.getFullYear()];

        console.warn('create_pass', this);
      },
      immediate: true
    },
    'modelValue': {
      handler(newValue, oldValue){
        
      },
    },
    'dob': {
      handler(newValue, oldValue){
        console.warn('date', newValue);
      },
    },
    'dialog': {
      handler(newValue, oldValue){
        console.warn('openend create pass', newValue);
      },
    },
  }
}

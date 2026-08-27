export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
  emits: ['save','open_create_passenger', 'remove_passenger', 'assign_passenger'],
  props: {
      /*
      modelValue: {
          type: Object,
          default: () => (undefined),
      },
      */
      referenceDate: {
          type: Date,
          default: () => (new Date()),
      },
      referenceDateEnd: {
          type: Date,
          default: () => (new Date()),
      },
      passport_required: {
          type: Boolean,
          default: () => (false),
      },
      secured: {
          type: Boolean,
          default: () => (false),
      },
      identification_required: {
          type: Boolean,
          default: () => (false),
      },
      type: {
          type: String,
          default: () => ('ADT'),
      },
      assigned_passenger: {
          default: () => (undefined),
      },
      assigned_passengers: {
          type: Object,
          default: () => ({}),
      },
      passengers: {
          default: () => ([]),
      },
  },
	data: () => ({
    kept: undefined,
    saved: {},
    dialog: false,
    errors: [],
    validPassengers: [],
    texts: Object.freeze({
    }),
    validations:Object.freeze([
    ]),
  }),
  mounted(){
	  if(!this.assigned_passenger){
		  this.validPassengers = this.getValidPassengers()
		  var assigned_hashes = Object.values(this.assigned_passengers).map(p => p.hash);
		  var passenger = this.validPassengers.find(p => -1 == assigned_hashes.indexOf(p.hash) && (!(this.secured || this.identification_required) || (p.secured_correct)));
		  if(passenger){
			  this.$emit('assign_passenger', passenger);
		  }
		  console.error({
			  assigned_passengers: this.assigned_passengers,
			  validPassengers: this.validPassengers,
			  assigned_passenger: this.assigned_passenger,
			  passenger: passenger,
		  });
	  }
  },
	template : `
<v-dialog v-model="dialog" class="custom-dialog flight-dialog dialog-detalii-pasager">
  <template v-slot:activator="{ props }">
	<div class="d-flex w-100 justify-space-between flex-wrap align-start ga-2">
		<template v-if="assigned_passenger" v-for="p in [assigned_passenger]" class="d-flex ga-2 flex-wrap">
		<div class="d-flex flex-column">
		<strong v-text="passengerTitle(p.title) + ' ' + p.firstname + ' ' + p.lastname"></strong>
		<div class="d-flex flex-column flex-wrap">
			<span>
				<strong>Detalii: </strong>
				<span v-text="p.nationality"></span>
				<span v-text="p.birth_date"></span>
			</span>
			<div v-if="p.doctype" class="d-flex flex-wrap ga-2">
				<span v-text="p.doctype == '1' ? 'CI' : 'Pasaport'"></span>
				<template v-if="p.doctype == '1'">
					<div><b>CNP: </b><span v-text="p.ci"></span></div>
					<div><b>Serie/Nr: </b><span v-text="p.ci_serie"></span>/<span v-text="p.ci_nr"></span></div>
					<div><b>Valabilitate: </b><span v-text="p.ci_s"></span>/<span v-text="p.ci_e"></span></div>
				</template>
				<template v-else>
					<div><b>Emitent: </b><span v-text="p.pass_c"></span></div>
					<div><b>Nr: </b><span v-text="p.pass"></span></div>
					<div><b>Valabilitate: </b><span v-text="p.pass_s"></span>/<span v-text="p.pass_e"></span></div>
				</template>
			</div>
		</div>
		</div>
		</template>
		<template v-else>
		<span class="selecteaza-pasager">Selecteaza un pasager</span>
		</template>
		<v-btn v-bind="props" class="rounded-theme justify-space-between d-flex py-4 modal-button text-none" append-icon="mdi-chevron-right" variant="outlined">ALEGE</v-btn>
	</div>
  </template>
  <template v-slot:default="{ isActive }">
		<v-card class="align-self-center" style="max-width: min(95vw, 630px);width:630px">
		<v-card-title>Selecteaza un pasager</v-card-title>
		<v-card-text class="max-height overflow-y-auto px-0">
      <v-list subheader theme="light" class="" rounded="theme">
        <v-messages class="pl-4" color="error" :active="!!errors.length" :messages="errors"></v-messages>
        
        <v-list-item theme="light" v-for="passenger in validPassengers" @click="dialog = false;$emit(passenger.hash && (!(secured || identification_required) || (passenger.secured_correct)) ? 'assign_passenger' : 'open_create_passenger', passenger);">
          <strong v-text="passenger.firstname"></strong> <strong v-text="passenger.lastname"></strong>
          <template v-slot:append="{ item }">
            <v-icon v-if="(secured || identification_required) && !passenger.secured_correct" icon="mdi-alert-decagram-outline" class="text-error"></v-icon>
            <v-icon @click.stop="dialog = false;$emit('open_create_passenger', passenger);" icon="mdi-pencil" class="ms-0 me-2"></v-icon>
            <v-icon v-if="passenger.hash" icon="mdi-delete-forever" class="ms-0 me-2"  @click.stop="$emit('remove_passenger', passenger.hash);"></v-icon>
            <v-icon v-if="passenger.hash" icon="mdi-chevron-right" class="ms-0"></v-icon>
          </template>
        </v-list-item>

        <div class="d-flex align-center px-4">
          <v-btn theme="light" rounded="theme" color="secondary" class="square-but-inp me-4 flex-grow-0" @click="dialog = false;$emit('open_create_passenger', {})">
            <v-icon>
              mdi-plus
            </v-icon>
          </v-btn>
          <h5 class="flex-grow-1">Adauga pasager nou</h5>
        </div>
      </v-list>
	
	  </v-card-text>
	  <v-card-actions>
		<v-spacer></v-spacer>
        <v-btn class="text-none font-weight-normal flex-grow-1 cancel-button" size="large" variant="outlined" rounded="theme" @click="dialog = false"><v-icon icon="mdi-arrow-left"></v-icon> Inapoi</v-btn>
	  </v-card-actions>
	  </v-card>
	</template>
</v-dialog>
	`,

  methods: {
    passengerTitle( title ) {
		switch(title){
			case 'mr':
				return 'Dl.';
			case 'mrs':
				return 'Dna.';
			case 'ms':
				return 'Dra.';
		}
		return 'Pers.';
	},
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
    clearValidations(){
      this.errors = [];
    },
    save(){
      this.kept = Object.assign({}, this.saved);
      this.$emit('save', this.saved);
      return true;
      // emit
    },
    validate(){
      this.clearValidations();
      this.validations.every(f => {
        var v = f.bind(this)();
        v && this.errors.push(this.texts[v]);
        return !v;
      })
      return !this.errors.length;
    },
    getValidPassengers(){
      var max_years = 130;
      var min_years = 0;
      switch(this.type){
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

      var max_date = new Date(this.referenceDateEnd);
      max_date.setFullYear(this.referenceDateEnd.getFullYear() - min_years);
      max_date.setDate(max_date.getDate() + 1);
      
      min_date = min_date.toISOString().split('T')[0];
      max_date = max_date.toISOString().split('T')[0];
      // console.warn(min_date, max_date, this.passengers);
      return this.passengers.filter(v => !v.birth_date || (v.birth_date >= min_date && v.birth_date <= max_date) ).sort((a, b) => {
        var r = - (!!a.hash - !!b.hash);
        if(!r){
          r = (a.firstname || '').localeCompare(b.firstname || '')
        }
        if(!r){
          r = (a.lastname || '').localeCompare(b.lastname || '')
        }
        if(!r){
          r = (a.birth_date || '').localeCompare(b.birth_date || '')
        }
        return r;
      }).map((v) => {
		  v.secured_correct = false;
			var n = new Date();
			var d = this.referenceDate;
			var de = this.referenceDateEnd;
			var d_max = new Date(); 
			d_max.setYear(d_max.getFullYear() + 100);
			console.warn('passport_required', this.passport_required, v.doctype);
		  if(this.passport_required && v.doctype == '1'){
			  v.secured_correct = false;
		  } else if(v.doctype == '1'){
			  v.secured_correct = (true || ((v.ci || '') !== '') && ((v.nationality == 'RO' && (/^[0-9]{13}$/.test(v.ci) && this.validCNP(v.ci))) || (v.nationality != 'RO' && (/^[0-9]{9,20}$/.test(v.ci))))) && ((v.ci_s || '') !== '') && ((v.ci_e || '') !== '') 
			  && (v.ci_s < v.ci_e) 
			  && (v.ci_s <= n.toISOString().split('T')[0]) 
			  && (v.ci_e <= d_max.toISOString().split('T')[0]) 
			  && (v.ci_e >= de.toISOString().split('T')[0])
			  && ((v.ci_nr || '') !== '') && !/[^0-9]/.test(v.ci_nr)
			  // && ((v.ci_serie || '') !== '') && !/[^a-zA-Z]/.test(v.ci_serie)
			  ;
		  } else {
			  v.secured_correct = ((v.pass || '') !== '') && (/^[0-9]{9,20}$/.test(v.pass)) && ((v.pass_c || '') !== '') && ((v.pass_s || '') !== '') && ((v.pass_e || '') !== '') 
			  && (v.pass_s < v.pass_e) 
			  && (v.pass_s <= n.toISOString().split('T')[0]) 
			  && (v.pass_e <= d_max.toISOString().split('T')[0]) 
			  && (v.pass_e >= de.toISOString().split('T')[0]);
		  }
		  return v;
	  });


    }
  },
  computed: {
    button_text:{
      get() { 
        return this.assigned_passenger ? (this.passengerTitle(this.assigned_passenger.title) + ' ' + this.assigned_passenger.firstname + ' ' + this.assigned_passenger.lastname) : 'Selecteaza un pasager';
      },
    },
  },
  watch:{
    'passengers': {
      handler(newValue, oldValue){
        if(this.dialog){
          console.warn('passengers changed', newValue)
          this.validPassengers = this.getValidPassengers()
        }
      },
    },
    'assigned_passenger': {
      handler(newValue, oldValue){
          console.warn('assigned_passenger', newValue)
      },
    },
    /*'modelValue': {
      handler(newValue, oldValue){
        
      },
    },*/
    'dialog': {
      handler(newValue, oldValue){
        if(newValue){
          this.validPassengers = this.getValidPassengers()
        }
      },
    },
  }
}

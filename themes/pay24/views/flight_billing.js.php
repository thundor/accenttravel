const isValidIBANNumber = function (input) {
    var CODE_LENGTHS = {
        AD: 24, AE: 23, AT: 20, AZ: 28, BA: 20, BE: 16, BG: 22, BH: 22, BR: 29,
        CH: 21, CR: 21, CY: 28, CZ: 24, DE: 22, DK: 18, DO: 28, EE: 20, ES: 24,
        FI: 18, FO: 18, FR: 27, GB: 22, GI: 23, GL: 18, GR: 27, GT: 28, HR: 21,
        HU: 28, IE: 22, IL: 23, IS: 26, IT: 27, JO: 30, KW: 30, KZ: 20, LB: 28,
        LI: 21, LT: 20, LU: 20, LV: 21, MC: 27, MD: 24, ME: 22, MK: 19, MR: 27,
        MT: 31, MU: 30, NL: 18, NO: 15, PK: 24, PL: 28, PS: 29, PT: 25, QA: 29,
        RO: 24, RS: 22, SA: 24, SE: 24, SI: 19, SK: 24, SM: 27, TN: 24, TR: 26,   
        AL: 28, BY: 28, CR: 22, EG: 29, GE: 22, IQ: 23, LC: 32, SC: 31, ST: 25,
        SV: 28, TL: 23, UA: 29, VA: 22, VG: 24, XK: 20
    };
    var iban = String(input).toUpperCase().replace(/[^A-Z0-9]/g, ''), // keep only alphanumeric characters
            code = iban.match(/^([A-Z]{2})(\d{2})([A-Z\d]+)$/), // match and capture (1) the country code, (2) the check digits, and (3) the rest
            digits;
    // check syntax and length
    if (!code || iban.length !== CODE_LENGTHS[code[1]]) {
        return false;
    }
    // rearrange country code and check digits, and convert chars to ints
    digits = (code[3] + code[1] + code[2]).replace(/[A-Z]/g, function (letter) {
        return letter.charCodeAt(0) - 55;
    });
    // final check
    return mod97(digits);
}

const mod97 = function (string) {
    var checksum = string.slice(0, 2), fragment;
    for (var offset = 2; offset < string.length; offset += 7) {
        fragment = String(checksum) + string.substring(offset, offset + 7);
        checksum = parseInt(fragment, 10) % 97;
    }
    return checksum;
}
const validate_CIF = function(value){
  value = ('' + value).replace(/^RO/i,'');
  value = parseInt(value);
  value = '' + value;
  if(value.length > 10 || value.length < 6){
    return false;
  }
  var v = 753217532;

  var c1 = value % 10;
  value = parseInt(value / 10);

  var t = 0;
  while(value > 0){
    t += (value % 10) * (v % 10);
    value = parseInt(value / 10);
    v = parseInt(v / 10);
  }

  var c2 = t * 10 % 11;
  if(c2 == 10){
    c2 = 0;
  }
  return c1 === c2;
}
export default {
  emits: ['save'],
  props: {
      referenceDate: {
          type: Date,
          default: () => (new Date()),
      },
      passengers: {
          type: Array,
          default: () => ([]),
      },
  },
	data: () => ({
    kept: undefined,
    version: '1',
    saved: {},
    cnt: 0,
    type: 'ADT',
    valid: false,
    year_range: [],
    results: [],
    phoneNumber: '',
    title: 'mr',
    min_date: undefined,
    max_date: undefined,
    firstname: '',
    lastname: '',
    country: 'RO',
    city: '',
    email: '',
    street: '',
    street_no: '',
    phone: '',
    phone_prefix: '',
    postal_code: '',
    invoice: 'pf',
    company: '',
    iban: '',
    bank: '',
    cui: '',
    regcom: '',
    errors: [],
    filtered_countries: countries,
    firstnameRules: Object.freeze([
      v => !!v && !!v.trim() || 'Prenumele este necesar',
      v => v.trim().length <= 255 || 'Textul introdus este prea lung',
    ]),
    nameRules: Object.freeze([
      v => !!v && !!v.trim() || 'Numele este necesar',
      v => v.trim().length <= 255 || 'Textul introdus este prea lung',
    ]),
    dobRules: Object.freeze([
      v => !!v && !!v.trim() || 'Data nasterii este necesara',
      v => v.trim().length != 10 || 'Data nasterii nu are formatul necesar',
    ]),
    streetRules:Object.freeze([
      v => !!v && !!v.trim() || 'Introduceti strada',
      v => v.trim().length <= 255 || 'Textul introdus este prea lung',
    ]),
    streetNoRules:Object.freeze([
		v => !!v && !!v.trim() || 'Numarul strazii este necesar',
		v => v.trim().length <= 20 || 'Textul introdus este prea lung',
    ]),
    emailRules:Object.freeze([
      v => !!v && !!v.trim() || 'Emailul este obligatoriu',
      v => !v || /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*\.[a-zA-Z]{2,}$/.test(v) || 'Introduceti o adresa de email valida' || '',
    ]),
    countryRules:Object.freeze([
      v => !!v && !!v.trim() || 'Alegeti o tara',
      v => v.trim().length != 2 || 'Tara invalida',
    ]),
    companyRules:Object.freeze([
      v => !!v && !!v.trim() || 'Camp PJ obligatoriu',
      v => v.trim().length <= 255 || 'Textul introdus este prea lung',
    ]),
    ibanRules:Object.freeze([
      // v => !!v && !!v.trim() || 'Camp PJ obligatoriu',
      v => !v || isValidIBANNumber(v) || 'IBAN invalid',
    ]),
    cuiRules:Object.freeze([
      v => !!v && !!v.trim() || 'Camp PJ obligatoriu',
      v => v.match(/^([Rr]?[0-9]{5,8})|((1|2)([1-9]{1}[0-9]{1})(0[1-9]|1[012])(0[1-9]|[12][0-9]|3[01])([0-9]{6}))$/) || validate_CIF(v) || 'CUI invalid',
    ]),
    bankRules:Object.freeze([
      // v => !!v && !!v.trim() || 'Camp PJ obligatoriu',
      v => v.trim().length <= 255 || 'Textul introdus este prea lung',
    ]),
    regcomRules:Object.freeze([
      // v => !!v && !!v.trim() || 'Camp PJ obligatoriu',
      v => v.trim().length <= 255 || 'Textul introdus este prea lung',
    ]),
    cityRules:Object.freeze([
      v => !!v && !!v.trim() || 'Introduceti numele orasului',
      v => v.trim().length <= 255 || 'Textul introdus este prea lung',
    ]),
    texts: Object.freeze({
    }),
    validations:Object.freeze([
    ]),
  }),
	template : `
  <v-messages class="pl-4" color="error" :active="!!errors.length" :messages="errors"></v-messages>
  <q-form ref="form" v-model="valid" class="bg-background pa-4 pb-0 rounded-theme">
    <v-radio-group inline color="primary" v-model="invoice" class="ms-0 mt-2 mb-3" hide-details="true">
      <template v-slot:label>
        <div>Facturare persoana</div>
      </template>
      <v-radio label="Fizica" value="pf"></v-radio>
      <v-radio label="Juridica" value="pj" class="ms-2"></v-radio>
    </v-radio-group>

    <div class="">
    <q-input ref="firstname"
        :eager-validate="false"
        class="rounded-theme ondark pb-4"
        v-model="firstname"
        :rules="firstnameRules"
        label="Prenume"
        outlined />
    <q-input ref="lastname"
        :eager-validate="false"
        class="rounded-theme ondark pb-4"
        v-model="lastname"
        :rules="nameRules"
        label="Nume"
        outlined
      />
    <?php /*
    <q-input ref="phone_prefix"
        :eager-validate="false"
        class="rounded-theme ondark pb-4"
        v-model="phone_prefix"
        :rules="phonePrefixRules"
        label="Telefon (prefix tara)"
        outlined
      />
      */ ?>
      <vue3-q-tel-input ref="phone" :rules="phoneRules" :eager-validate="false" v-model:tel="phoneNumber" outlined label="Telefon" class="ondark phone-with-prefix" search-text="Cauta..." default-country="GB" />
    <?php /*
    <q-input ref="phone"
        :eager-validate="false"
        class="rounded-theme ondark pb-4"
        v-model="phone"
        :rules="phoneRules"
        label="Telefon"
        outlined
      />
      */ ?>

    <q-input ref="email"
        :eager-validate="false"
        type="email"
        class="rounded-theme ondark pb-4"
        v-model="email"
        :rules="emailRules"
        label="Adresa de e-mail"
        outlined
      />
      <?php /*
      <q-input
        :eager-validate="false"
        class="rounded-theme ondark pb-4"
        v-model="country"
        ref="country"
        :rules="countryRules"
        label="Tara"
        outlined
      />*/ ?>

      <q-select
        v-model="country"
        class="rounded-theme ondark pb-4"
        use-input
        input-debounce="0"
        label="Tara"
        :options="filtered_countries"
        emit-value
        map-options
        outlined
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

      <q-input
        :eager-validate="false"
        class="rounded-theme ondark pb-4"
        v-model="city"
        ref="city"
        :rules="cityRules"
        label="Oras"
        outlined
      />

      <q-input
        :eager-validate="false"
        class="rounded-theme ondark pb-4"
        v-model="street"
        ref="street"
        :rules="streetRules"
        label="Strada"
        outlined
      />

      <q-input
        :eager-validate="false"
        class="rounded-theme ondark pb-4"
        v-model="street_no"
        ref="street_no"
        :rules="streetNoRules"
        label="Numar"
        outlined
      />

      <q-input
        :eager-validate="false"
        class="rounded-theme ondark pb-4"
        :type="postalCodeType"
        v-model="postal_code"
        ref="postal_code"
        :rules="postalCodeRules"
        label="Cod postal"
        outlined
      />
      
      <q-input
        v-if="invoice=='pj'"
        :eager-validate="false"
        class="rounded-theme ondark pb-4"
        v-model="company"
        ref="company"
        :rules="companyRules"
        label="Nume companie"
        outlined
      />
      <q-input
        v-if="invoice=='pj'"
        :eager-validate="false"
        class="rounded-theme ondark pb-4"
        v-model="bank"
        ref="bank"
        :rules="bankRules"
        label="Banca"
        outlined
      />
      <q-input
        v-if="invoice=='pj'"
        :eager-validate="false"
        class="rounded-theme ondark pb-4"
        v-model="iban"
        ref="iban"
        :rules="ibanRules"
        label="IBAN"
        outlined
      />
      <q-input
        v-if="invoice=='pj'"
        :eager-validate="false"
        class="rounded-theme ondark pb-4"
        v-model="cui"
        ref="cui"
        :rules="cuiRules"
        label="CUI"
        outlined
      />
      <q-input
        v-if="invoice=='pj'"
        :eager-validate="false"
        class="rounded-theme ondark pb-4"
        v-model="regcom"
        ref="regcom"
        :rules="regcomRules"
        label="Nr.Reg.Com."
        outlined
      />
    </div>
  </q-form>
  <?php /*
  <v-btn @click="validateAndSave()">Valideaza</v-btn>
  */ ?>
	`,

  methods: {
    reset(){
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
      var o = {
        firstname: (this.firstname||'').trim(),
        lastname: (this.lastname||'').trim(),
        phone: (this.phone||'').trim(),
        phone_prefix: (this.phone_prefix||'').trim(),
        email: (this.email||'').trim(),
        country: (this.country||'').trim(),
        city: (this.city||'').trim(),
        street: (this.street||'').trim(),
        street_no: (this.street_no||'').trim(),
        postal_code: (this.postal_code||'').trim(),
        invoice: (this.invoice||'pf').trim(),
        company: (this.company||'').trim(),
        bank: (this.bank||'').trim(),
        regcom: (this.regcom||'').trim(),
        iban: (this.iban||'').trim(),
        cui: (this.cui||'').trim(),
      }

      saveStorage('pay24.flight.billing',{...o, version: this.version});
      this.$emit('save', o);
      this.$refs.form.reset();
      return true;
      // emit
    },
    validateAndSave(){
      this.clearValidations();
      this.validations.every(f => {
        var v = f.bind(this)();
        v && this.errors.push(this.texts[v]);
        return !v;
      })
      var valid = !this.errors.length;
      return this.$refs.form.validate().then(v => {
        if(v && valid){
          this.save();
          return true;
        }
      });
    },
  },
  computed: {
    postalCodeRules: function(){
      let _self = this;
      return [
		v => !!v && !!v.trim() || 'Codul postal este necesar',
        (v) => {
          if(typeof v == 'string' && v.length){
            if(_self.country == 'RO'){
              if(v.length != 6 || v.match(/[^0-9]/)) return "Codul postal romanesc contine 6 cifre";
            }
          }
          return true;
        }
      ]
    },
    phoneRules: function(){
      let _self = this;
      return [
        (v) => {
          if(typeof v == 'string' && v.length){
            if(_self.phone_prefix == '+40'){
              if(v.replace(/[^0-9]/g,'').length != 9) return "Telefonul romanesc contine 10 cifre ";
            }
          }
          return true;
        }
      ]
    },
    postalCodeType: function(){
      let _self = this;
      return _self.country == 'RO' ? 'number' : 'text';
    },
    validPassengers:{
      get() { 
        return this.passengers;
      },
    },
  },
  mounted: function() {
    var storageItem = getStorage('pay24.flight.billing','', {}, {}, {});
    if(storageItem && storageItem.version == this.version){
      this.lastname = storageItem.lastname || '';
      this.firstname = storageItem.firstname || '';

      this.invoice = storageItem.invoice || 'pf';
      this.company = storageItem.company || '';
      this.bank = storageItem.bank || '';
      this.iban = storageItem.iban || '';
      this.cui = storageItem.cui || '';
      this.regcom = storageItem.regcom || '';
      this.phone = storageItem.phone || '';
      this.phone_prefix = storageItem.phone_prefix || '';
      this.phoneNumber = this.phone_prefix + ' ' + this.phone;
      this.email = storageItem.email || '';
      this.country = storageItem.country || '';
      this.city = storageItem.city || '';
      this.street = storageItem.street || '';
      this.street_no = storageItem.street_no || '';
      this.postal_code = storageItem.postal_code || '';
    } else if(pay24Account){
      var personal_data;
      if(personal_data = getObjectDotPathValue(pay24Account,'profile.personal_data')){
        this.lastname = (personal_data.first_name || '').trim().replace(/\s+/g, ' ');
        this.firstname = (personal_data.last_name || '').trim().replace(/\s+/g, ' ');
        var country_value = (personal_data.citizenship || '').trim().replace(/\s+/g, ' ');

        var country;
        if(country_value && ('' + country_value).length > 1){
          if(country = this.countries.find(c => c.value == country_value)){
            this.country = country.value;
          } else if(country = this.countries.find(c => c.label.toLowerCase() == country_value.toLowerCase())){
            this.country = country.value;
          } else {

          }
        }

        this.street = (personal_data.adress || '').trim().replace(/\s+/g, ' ');
        this.phoneNumber = (personal_data.phone || '').trim().replace(/\s+/g, ' ');
        this.email = (personal_data.email || '').trim().replace(/\s+/g, ' ');
      }
    }
    // var r =this.validateAndSave();
    // console.warn(r);
  },
  watch:{
    'modelValue': {
      handler(newValue, oldValue){
        
      },
    },
    'phone_prefix': {
      handler(newValue, oldValue){
        console.warn('phone_prefix', newValue)
      },
    },
    'phoneNumber': {
      handler(newValue, oldValue){
        if(newValue){
          var m = ('' + newValue).match(/^\+\d+/);
          if(m){
            this.phone_prefix = m[0];
          }
          var m = ('' + this.phone_prefix).match(/.*?(0+)$/);
          var prefix_zeros = '';
          if(m){
            var prefix_zeros = m[1];
          }
          var m = ('' + newValue).match(/^\+\d+\s+(.*)/);
          if(m){
            this.phone = prefix_zeros + m[1].replace(/[^0-9]/g,'');
          }
        }
      },
    },
    'phone': {
      handler(newValue, oldValue){
        console.warn('phone', newValue)
      },
    },
    'country': {
      handler(newValue, oldValue){
        console.warn('country', newValue)
      },
    },
  }
}

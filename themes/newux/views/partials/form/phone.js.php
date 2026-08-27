export default {
  name: "Phone",
  extends: Vuetify.components.VInput,
  props: {
    prefix: {
      type: [String, Number],
      default: () => null,
    },
    prefix_type: {
      type: [String],
      default: () => '',
    },
    value: {
      type: [String],
      default: () => null,
    },
    number: {
      type: [String, Number],
      default: () => null,
    },
  },
  computed: {
	internalReferenceDate() {
		var referenceDate = this.referenceDate;
		return 'string' === typeof referenceDate && new Date(referenceDate) || (referenceDate ? referenceDate : (new Date()));
	},
  },
  data() {
    return {
      selectedYear: null,
      selectedMonth: null,
      selectedDay: null,
      internalValue: this.value,
      countries: markRaw(<?php 
$countries = $this->query("SELECT iso_2, iso_3, phone_prefix, phone_prefix_numeric, IFNULL(NULLIF(name_RO,''), name) FROM `ac_country` WHERE status=1 AND phone_prefix_numeric > 0")->result('array');
echo json_encode($countries, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>),
    };
  },
  watch: {
    // If value changes externally, sync with the component's internal values
    value: {
      handler(val) {
        this.updateSelectorsFromValue(val);
      },
      immediate: true,
    },
    modelValue: {
      handler(val) {
        this.updateSelectorsFromValue(val);
      },
      immediate: true,
    },
    selectedDay:{
		handler: function(newVal) {
		  this.onDateChange();
		},
		immediate: true,
    },
    selectedYear:{
		handler: function(newVal) {
		  this.onDateChange();
		},
		immediate: true,
    },
    selectedMonth:{
		handler: function(newVal) {
		  this.onDateChange();
		},
		immediate: true,
    }
  },
  mounted() {
	  // console.warn(this.$attrs, this);
	},
  methods: {
    onFocus(name) {
		setTimeout(() => {
			this.$refs[name].$el.querySelector('input').select();
		}, 0)
    },
    autoSelectFirst(name){ 
		if(this.$refs[name].search && this.$refs[name].filteredItems.length && (this.$refs[name].filteredItems.length != this.$refs[name].items.length)){
			this[name] = this.$refs[name].filteredItems[0].value
		}
	},
    openNext(a,name) {
		if(!a) return;
		var to_focus;
		if(null === this.selectedYear){
			to_focus = 'selectedYear';
		} else if(null === this.selectedMonth){
			to_focus = 'selectedMonth';
		} else if(null === this.selectedDay){
			to_focus = 'selectedDay';
		} else {
			if(name == 'selectedYear'){
				to_focus = 'selectedMonth';
			} else if(name == 'selectedMonth'){
				to_focus = 'selectedDay';
			}
		}
		if(to_focus){
			setTimeout(() => {
				if(this.$refs[to_focus].items.length > 1 && !this.$refs[to_focus].isFocused){
					this.$refs[name].menu = false;
					this.$refs[to_focus].focus();
					this.$refs[to_focus].isFocused = true;
					this.$refs[to_focus].menu = true;
				}
			}, 0)
		}
	},
    updateSelectorsFromValue(value) {
      if (value) {
        const date = new Date(value);
		if(!isNaN(date)){
			this.selectedYear = date.getFullYear();
			this.selectedMonth = date.getMonth() + 1;
			this.selectedDay = date.getDate();
		}
      }
    },
    onDateChange() {
		setTimeout(() => {
			let selectedDate;
		  if (null !== this.selectedYear && null !== this.selectedMonth && null !== this.selectedDay) {
			selectedDate = new Date(Date.UTC(this.selectedYear, this.selectedMonth - 1, this.selectedDay)).toISOString().replace(/T.*/,'');
		  } else {
			  selectedDate = null;
		  }
		  this.internalValue = selectedDate;
		  this.$emit("update:modelValue",  selectedDate); // Emit the selected date as YYYY-MM-DD
		}, 0)
    }
  },
  template: `
  <v-input
    v-bind="$props"
    v-model="internalValue"
    :error-messages="errorMessages"
    :rules="rules"
	class="datepicker-select"
  >
    <template v-slot:default="{ isFocused, setTextFieldRef, inputValue, isDisabled }">
      <v-row>
        <!-- Year Selector -->
        <v-col cols="4">
          <v-autocomplete
            ref="selectedYear"
            v-model="selectedYear"
            :items="years"
            label="Year"
            :disabled="isDisabled.value"
			@update:modelValue="v => openNext(v, 'selectedYear')"
			hide-details
			
			@focus="onFocus('selectedYear')"
			@blur="autoSelectFirst('selectedYear')"
          ></v-autocomplete>
        </v-col>

        <!-- Month Selector -->
        <v-col cols="4">
          <v-autocomplete
            ref="selectedMonth"
            v-model="selectedMonth"
            :items="months"
            label="Month"
            :disabled="isDisabled.value"
			@update:modelValue="v => openNext(v, 'selectedMonth')"
			hide-details
			
			@focus="onFocus('selectedMonth')"
			@blur="autoSelectFirst('selectedMonth')"
          ></v-autocomplete>
        </v-col>

        <!-- Day Selector -->
        <v-col cols="4">
          <v-autocomplete
            ref="selectedDay"
            v-model="selectedDay"
            :items="days"
            label="Day"
            :disabled="isDisabled.value"
			@update:modelValue="v => openNext(v, 'selectedDay')"
			hide-details
			
			@blur="autoSelectFirst('selectedDay')"
			@focus="onFocus('selectedDay')"
          ></v-autocomplete>
        </v-col>
      </v-row>
    </template>
  </v-input>
`,
};
import { Country, CountryGuesser, CountryIso2, CountryOrIso2 } from '../types/countries';
import { CountryIconMode, CountryPhoneExample, MessageOptions, MessageResolver } from '../types/options';
import { ParsedPhoneNumber, PhoneNumberFormat } from 'awesome-phonenumber';
import { PropType } from 'vue';
export type VPhoneCountriesItems = ((Country & {
    preferred?: boolean;
}) | {
    divider: boolean;
})[];
export type ValidationResult = string | boolean;
export type ValidationRule = ValidationResult | PromiseLike<ValidationResult> | ((value: any) => ValidationResult) | ((value: any) => PromiseLike<ValidationResult>);
export type ValidateOnValue = 'blur' | 'input' | 'submit';
export type VPhoneInputValidationResult = string | boolean | PromiseLike<string | boolean>;
export type VPhoneInputValidationRule = VPhoneInputValidationResult | ((input: string, phone: ParsedPhoneNumber, messageOptions: MessageOptions) => VPhoneInputValidationResult);
declare const _sfc_main: import("vue").DefineComponent<{
    label: {
        type: PropType<MessageResolver>;
        default: () => MessageResolver;
    };
    ariaLabel: {
        type: PropType<MessageResolver>;
        default: () => MessageResolver;
    };
    countryLabel: {
        type: PropType<MessageResolver>;
        default: () => MessageResolver;
    };
    countryAriaLabel: {
        type: PropType<MessageResolver>;
        default: () => MessageResolver;
    };
    placeholder: {
        type: PropType<MessageResolver>;
        default: () => MessageResolver;
    };
    hint: {
        type: PropType<MessageResolver>;
        default: () => MessageResolver;
    };
    invalidMessage: {
        type: PropType<MessageResolver>;
        default: () => MessageResolver;
    };
    example: {
        type: PropType<CountryPhoneExample | undefined>;
        default: () => CountryPhoneExample | undefined;
    };
    appendIcon: {
        type: StringConstructor;
        default: undefined;
    };
    appendInnerIcon: {
        type: StringConstructor;
        default: undefined;
    };
    prependIcon: {
        type: StringConstructor;
        default: undefined;
    };
    prependInnerIcon: {
        type: StringConstructor;
        default: undefined;
    };
    rules: {
        type: PropType<VPhoneInputValidationRule[]>;
        default: () => never[];
    };
    validateOn: {
        type: PropType<"lazy" | "input lazy" | "blur lazy" | "submit lazy" | "lazy input" | "lazy blur" | "lazy submit" | ValidateOnValue>;
        default: undefined;
    };
    countryIconMode: {
        type: PropType<CountryIconMode>;
        default: () => CountryIconMode;
    };
    allCountries: {
        type: PropType<Country[]>;
        default: () => Country[];
    };
    preferCountries: {
        type: PropType<CountryOrIso2[]>;
        default: () => string[];
    };
    includeCountries: {
        type: PropType<CountryOrIso2[]>;
        default: () => string[];
    };
    excludeCountries: {
        type: PropType<CountryOrIso2[]>;
        default: () => string[];
    };
    defaultCountry: {
        type: PropType<CountryOrIso2 | undefined>;
        default: () => string | undefined;
    };
    countryGuesser: {
        type: PropType<CountryGuesser>;
        default: () => CountryGuesser;
    };
    guessCountry: {
        type: BooleanConstructor;
        default: () => boolean;
    };
    disableGuessLoading: {
        type: BooleanConstructor;
        default: () => boolean;
    };
    enableSearchingCountry: {
        type: BooleanConstructor;
        default: () => boolean;
    };
    displayFormat: {
        type: PropType<PhoneNumberFormat>;
        default: () => PhoneNumberFormat;
    };
    country: {
        type: StringConstructor;
        default: string;
    };
    modelValue: {
        type: PropType<string | null>;
        default: string;
    };
    wrapperProps: {
        type: ObjectConstructor;
        default: () => {};
    };
    countryProps: {
        type: ObjectConstructor;
        default: () => {};
    };
    phoneProps: {
        type: ObjectConstructor;
        default: () => {};
    };
}, {
    wrapperAttrs: import("vue").ComputedRef<{}>;
    VTextField: any;
    countryInput: import("vue").Ref<null>;
    phoneInput: import("vue").Ref<{
        validate: () => void;
    } | null>;
    namespacedSlots: import("vue").Ref<Record<string, Record<string, unknown>>>;
    countrySelectComponent: import("vue").ComputedRef<{
        type: string;
        props: {
            autocomplete: string;
            'aria-autocomplete': string;
        };
    } | {
        type: string;
        props: {
            autocomplete?: undefined;
            'aria-autocomplete'?: undefined;
        };
    }>;
    countryIconComponent: import("vue").ComputedRef<import("vue").DefineComponent<{}, {}, {}, import("vue").ComputedOptions, import("vue").MethodOptions, import("vue").ComponentOptionsMixin, import("vue").ComponentOptionsMixin, {}, string, import("vue").PublicProps, Readonly<import("vue").ExtractPropTypes<{}>>, {}, {}> | import("vue").DefineComponent<{
        readonly country: {
            readonly required: true;
            readonly type: PropType<Country>;
        };
        readonly decorative: {
            readonly type: BooleanConstructor;
            readonly default: false;
        };
    }, () => import("vue").VNode<import("vue").RendererNode, import("vue").RendererElement, {
        [key: string]: any;
    }>, unknown, {}, {}, import("vue").ComponentOptionsMixin, import("vue").ComponentOptionsMixin, {}, string, import("vue").PublicProps, Readonly<import("vue").ExtractPropTypes<{
        readonly country: {
            readonly required: true;
            readonly type: PropType<Country>;
        };
        readonly decorative: {
            readonly type: BooleanConstructor;
            readonly default: false;
        };
    }>>, {
        readonly decorative: boolean;
    }, {}> | undefined>;
    countryAttrs: import("vue").ComputedRef<{
        menuProps: any;
        autocomplete: string;
        'aria-autocomplete': string;
    } | {
        menuProps: any;
        autocomplete?: undefined;
        'aria-autocomplete'?: undefined;
    }>;
    phoneAttrs: import("vue").ComputedRef<{}>;
    countryFocused: import("vue").Ref<boolean>;
    guessingCountry: import("vue").ComputedRef<boolean>;
    mergeRules: () => void;
    lazyCountry: import("vue").Ref<string | undefined>;
    lazyValue: import("vue").Ref<string | null>;
    mergedRules: import("vue").Ref<(ValidationResult | ((value: any) => ValidationResult) | ((value: any) => PromiseLike<ValidationResult>) | {
        then: <TResult1 = ValidationResult, TResult2 = never>(onfulfilled?: ((value: ValidationResult) => TResult1 | PromiseLike<TResult1>) | null | undefined, onrejected?: ((reason: any) => TResult2 | PromiseLike<TResult2>) | null | undefined) => PromiseLike<TResult1 | TResult2>;
    })[]>;
    activeCountry: import("vue").ComputedRef<Country>;
    labels: {
        label: import("vue").ComputedRef<import('../types/options').Message>;
        ariaLabel: import("vue").ComputedRef<import('../types/options').Message>;
        countryLabel: import("vue").ComputedRef<import('../types/options').Message>;
        countryAriaLabel: import("vue").ComputedRef<import('../types/options').Message>;
        placeholder: import("vue").ComputedRef<import('../types/options').Message>;
        hint: import("vue").ComputedRef<import('../types/options').Message>;
        invalidMessage: import("vue").ComputedRef<import('../types/options').Message>;
        messageOptions: import("vue").ComputedRef<{
            country: Country;
            example: string;
        }>;
    };
    countriesItems: import("vue").ComputedRef<({
        name: string;
        iso2: string;
        dialCode: string;
    } | {
        divider: boolean;
    })[]>;
    onCountryFocus: () => void;
    onCountryBlur: () => void;
}, unknown, {}, {}, import("vue").ComponentOptionsMixin, import("vue").ComponentOptionsMixin, {
    'update:modelValue': (_value: string) => true;
    'update:country': (_country: CountryIso2) => true;
}, string, import("vue").PublicProps, Readonly<import("vue").ExtractPropTypes<{
    label: {
        type: PropType<MessageResolver>;
        default: () => MessageResolver;
    };
    ariaLabel: {
        type: PropType<MessageResolver>;
        default: () => MessageResolver;
    };
    countryLabel: {
        type: PropType<MessageResolver>;
        default: () => MessageResolver;
    };
    countryAriaLabel: {
        type: PropType<MessageResolver>;
        default: () => MessageResolver;
    };
    placeholder: {
        type: PropType<MessageResolver>;
        default: () => MessageResolver;
    };
    hint: {
        type: PropType<MessageResolver>;
        default: () => MessageResolver;
    };
    invalidMessage: {
        type: PropType<MessageResolver>;
        default: () => MessageResolver;
    };
    example: {
        type: PropType<CountryPhoneExample | undefined>;
        default: () => CountryPhoneExample | undefined;
    };
    appendIcon: {
        type: StringConstructor;
        default: undefined;
    };
    appendInnerIcon: {
        type: StringConstructor;
        default: undefined;
    };
    prependIcon: {
        type: StringConstructor;
        default: undefined;
    };
    prependInnerIcon: {
        type: StringConstructor;
        default: undefined;
    };
    rules: {
        type: PropType<VPhoneInputValidationRule[]>;
        default: () => never[];
    };
    validateOn: {
        type: PropType<"lazy" | "input lazy" | "blur lazy" | "submit lazy" | "lazy input" | "lazy blur" | "lazy submit" | ValidateOnValue>;
        default: undefined;
    };
    countryIconMode: {
        type: PropType<CountryIconMode>;
        default: () => CountryIconMode;
    };
    allCountries: {
        type: PropType<Country[]>;
        default: () => Country[];
    };
    preferCountries: {
        type: PropType<CountryOrIso2[]>;
        default: () => string[];
    };
    includeCountries: {
        type: PropType<CountryOrIso2[]>;
        default: () => string[];
    };
    excludeCountries: {
        type: PropType<CountryOrIso2[]>;
        default: () => string[];
    };
    defaultCountry: {
        type: PropType<CountryOrIso2 | undefined>;
        default: () => string | undefined;
    };
    countryGuesser: {
        type: PropType<CountryGuesser>;
        default: () => CountryGuesser;
    };
    guessCountry: {
        type: BooleanConstructor;
        default: () => boolean;
    };
    disableGuessLoading: {
        type: BooleanConstructor;
        default: () => boolean;
    };
    enableSearchingCountry: {
        type: BooleanConstructor;
        default: () => boolean;
    };
    displayFormat: {
        type: PropType<PhoneNumberFormat>;
        default: () => PhoneNumberFormat;
    };
    country: {
        type: StringConstructor;
        default: string;
    };
    modelValue: {
        type: PropType<string | null>;
        default: string;
    };
    wrapperProps: {
        type: ObjectConstructor;
        default: () => {};
    };
    countryProps: {
        type: ObjectConstructor;
        default: () => {};
    };
    phoneProps: {
        type: ObjectConstructor;
        default: () => {};
    };
}>> & {
    "onUpdate:modelValue"?: ((_value: string) => any) | undefined;
    "onUpdate:country"?: ((_country: string) => any) | undefined;
}, {
    label: MessageResolver;
    ariaLabel: MessageResolver;
    countryLabel: MessageResolver;
    countryAriaLabel: MessageResolver;
    placeholder: MessageResolver;
    hint: MessageResolver;
    invalidMessage: MessageResolver;
    example: CountryPhoneExample | undefined;
    countryIconMode: CountryIconMode;
    allCountries: Country[];
    preferCountries: CountryOrIso2[];
    includeCountries: CountryOrIso2[];
    excludeCountries: CountryOrIso2[];
    defaultCountry: CountryOrIso2 | undefined;
    countryGuesser: CountryGuesser;
    guessCountry: boolean;
    disableGuessLoading: boolean;
    enableSearchingCountry: boolean;
    displayFormat: PhoneNumberFormat;
    appendIcon: string;
    prependIcon: string;
    modelValue: string | null;
    prependInnerIcon: string;
    validateOn: "lazy" | "input lazy" | "blur lazy" | "submit lazy" | "lazy input" | "lazy blur" | "lazy submit" | ValidateOnValue;
    rules: VPhoneInputValidationRule[];
    appendInnerIcon: string;
    country: string;
    wrapperProps: Record<string, any>;
    countryProps: Record<string, any>;
    phoneProps: Record<string, any>;
}, {}>;
export default _sfc_main;

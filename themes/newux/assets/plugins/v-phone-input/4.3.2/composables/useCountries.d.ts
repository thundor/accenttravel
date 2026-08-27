import { Country, CountryGuesser, CountryIso2, CountryOrIso2 } from '../types/countries';
import { ComputedRef } from 'vue';
interface UseCountriesParams {
    props: {
        allCountries: Country[];
        preferCountries: CountryOrIso2[];
        includeCountries: CountryOrIso2[];
        excludeCountries: CountryOrIso2[];
        guessCountry: boolean;
        countryGuesser: CountryGuesser;
        disableGuessLoading: boolean;
    };
}
export default function useCountries({ props }: UseCountriesParams): {
    countriesCount: import("vue").Ref<number>;
    preferredCountries: import("vue").Ref<{
        name: string;
        iso2: string;
        dialCode: string;
    }[]>;
    otherCountries: import("vue").Ref<{
        name: string;
        iso2: string;
        dialCode: string;
    }[]>;
    guessingCountry: ComputedRef<boolean>;
    findCountry: (iso2?: CountryOrIso2) => Country | undefined;
    firstCountry: ComputedRef<Country>;
    setCountryPreference: (country: CountryIso2) => void;
    guessCountry: () => Promise<string | undefined>;
};
export {};

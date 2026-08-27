interface UseCountrySelectComponentParams {
    props: {
        enableSearchingCountry: boolean;
    };
}
export default function useCountrySelectComponent({ props }: UseCountrySelectComponentParams): {
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
};
export {};

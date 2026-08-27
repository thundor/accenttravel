import { VCountryProps } from '../composables/makeVCountryProps';
interface UseCountriesIconAttrsParams {
    props: VCountryProps;
}
export default function useCountryIconAttrs({ props }: UseCountriesIconAttrsParams): {
    role: import("vue").ComputedRef<"img" | undefined>;
    title: import("vue").ComputedRef<string | undefined>;
};
export {};

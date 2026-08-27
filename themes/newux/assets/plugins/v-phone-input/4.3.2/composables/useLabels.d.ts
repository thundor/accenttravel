import { Country } from '../types/countries';
import { Message, MessageResolver } from '../types/options';
import { ComputedRef } from 'vue';
interface UseLabelsParams {
    props: {
        label: MessageResolver;
        ariaLabel: MessageResolver;
        countryLabel: MessageResolver;
        countryAriaLabel: MessageResolver;
        placeholder: MessageResolver;
        hint: MessageResolver;
        invalidMessage: MessageResolver;
    };
    country: ComputedRef<Country>;
    example: ComputedRef<string>;
}
export default function useLabels({ props, country, example }: UseLabelsParams): {
    label: ComputedRef<Message>;
    ariaLabel: ComputedRef<Message>;
    countryLabel: ComputedRef<Message>;
    countryAriaLabel: ComputedRef<Message>;
    placeholder: ComputedRef<Message>;
    hint: ComputedRef<Message>;
    invalidMessage: ComputedRef<Message>;
    messageOptions: ComputedRef<{
        country: Country;
        example: string;
    }>;
};
export {};

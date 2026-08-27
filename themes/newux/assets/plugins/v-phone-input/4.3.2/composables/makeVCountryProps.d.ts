import { Country } from '../types/countries';
import { PropType } from 'vue';
export interface VCountryProps {
    country: Country;
    decorative?: boolean;
}
export default function makeVCountryProps(): {
    readonly country: {
        readonly required: true;
        readonly type: PropType<Country>;
    };
    readonly decorative: {
        readonly type: BooleanConstructor;
        readonly default: false;
    };
};

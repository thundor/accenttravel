import { CountryIconMode } from '../types/options';
interface UseCountryIconComponentParams {
    props: {
        countryIconMode: CountryIconMode;
    };
}
export default function useCountryIconComponent({ props }: UseCountryIconComponentParams): {
    countryIconComponent: import("vue").ComputedRef<import("vue").DefineComponent<{}, {}, {}, import("vue").ComputedOptions, import("vue").MethodOptions, import("vue").ComponentOptionsMixin, import("vue").ComponentOptionsMixin, {}, string, import("vue").PublicProps, Readonly<import("vue").ExtractPropTypes<{}>>, {}, {}> | import("vue").DefineComponent<{
        readonly country: {
            readonly required: true;
            readonly type: import("vue").PropType<import("..").Country>;
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
            readonly type: import("vue").PropType<import("..").Country>;
        };
        readonly decorative: {
            readonly type: BooleanConstructor;
            readonly default: false;
        };
    }>>, {
        readonly decorative: boolean;
    }, {}> | undefined>;
};
export {};

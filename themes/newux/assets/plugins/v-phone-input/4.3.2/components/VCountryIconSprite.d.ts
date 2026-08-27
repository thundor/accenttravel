declare const _default: import("vue").DefineComponent<{
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
}, {}>;
export default _default;

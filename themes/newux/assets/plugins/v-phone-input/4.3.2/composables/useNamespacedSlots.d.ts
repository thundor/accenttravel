export default function useNamespacedSlots(slots: Record<string, unknown>, namespaces: string[]): {
    namespacedSlots: import("vue").Ref<Record<string, Record<string, unknown>>>;
};

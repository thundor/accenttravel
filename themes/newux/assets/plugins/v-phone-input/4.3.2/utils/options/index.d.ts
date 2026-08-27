import { PluginOptions } from '../../types/options';
export declare const DEFAULT_OPTIONS: PluginOptions;
export declare const options: PluginOptions;
export declare function mergeOptions(newOptions: Partial<PluginOptions>): void;
export declare function getOption<T extends keyof PluginOptions>(key: T): PluginOptions[T];

import { CountryIso2, PreferableCountryGuesser } from '../../types/countries';
import Ip2cCountryGuesser from '../../utils/countries/ip2cCountryGuesser';
interface StorageMemoIp2cCountryGuesserOptions {
    storage?: Storage;
    key?: string;
}
export default class StorageMemoIp2cCountryGuesser extends Ip2cCountryGuesser implements PreferableCountryGuesser {
    private readonly storage;
    private readonly key;
    constructor(options?: StorageMemoIp2cCountryGuesserOptions);
    guess(): Promise<CountryIso2 | undefined>;
    setPreference(country: CountryIso2): void;
    private retrieveStoredCountry;
    private saveStoredCountry;
    getStorage(): Storage;
    getKey(): string;
}
export {};

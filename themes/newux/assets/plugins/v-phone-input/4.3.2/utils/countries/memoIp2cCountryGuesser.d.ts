import { CountryIso2, PreferableCountryGuesser } from '../../types/countries';
import Ip2cCountryGuesser from '../../utils/countries/ip2cCountryGuesser';
export default class MemoIp2cCountryGuesser extends Ip2cCountryGuesser implements PreferableCountryGuesser {
    private memoCountry;
    guess(): Promise<CountryIso2 | undefined>;
    setPreference(country: CountryIso2): void;
}

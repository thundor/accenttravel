import { CountryGuesser, CountryIso2 } from '../../types/countries';
export default class Ip2cCountryGuesser implements CountryGuesser {
    static readonly IP2C_URL = "https://ip2c.org/s";
    guess(): Promise<CountryIso2 | undefined>;
}

export class Validator {
    static isEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(String(email).toLowerCase());
    }

    static isRequired(value) {
        return value !== null && value !== undefined && String(value).trim() !== '';
    }
}

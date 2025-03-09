import { Validator } from 'vee-validate';

Validator.extend("validate-amount", {
    validate: (value) => value > 0,
    getMessage: "The amount must be greater than 0.00",
});

Validator.extend("integer", {
    validate: value => Number.isInteger(Number(value)),
    getMessage: () => "Please enter a valid integer.",
});
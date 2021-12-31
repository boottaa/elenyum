let validator = {
    phone: (value) => {
        let returnClass = 'invalid-phone';
        let regex = /^((8|\+7)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{7,10}$/;
        if (regex.test(value)) {
            return {
                success: true,
                message: '',
                class: returnClass,
            };
        } else {
            return {
                success: false,
                message: 'Номер телефона не корректный',
            };
        }
    },
    notEmpty: (value) => {
        if (value === null || value === '' || value === undefined || value?.length === 0 || value === {}) {
            return {
                success: false,
                message: 'Обязательно для заполнения',
            };
        } else {
            return {
                success: true,
                message: '',
            };
        }
    },
}

export function isValid(items) {
    let result = null;

    for (const [key, value] of Object.entries(items)) {
        value.validators.forEach((item) => {
            if (typeof validator[item] === 'function') {
                let validResult = validator[item](value.value);
                if (validResult.success === false) {
                    addErrorMessage(key, validResult.message);

                    if (typeof result === 'object') {
                        result = false;
                    }
                } else {
                    $(key).parent().find('.invalid-feedback').remove();
                    $(key).removeClass('is-invalid');
                }
            }
        })
    }

    if (typeof result === 'boolean') {
        return result;
    } else {
        return true;
    }
}

export function addErrorMessage(itemId, message) {
    $(itemId).parent().find('.invalid-feedback').remove();
    $(itemId).addClass('is-invalid');
    $(itemId).parent().append(`<div class="invalid-feedback">${message} </div>`);
}
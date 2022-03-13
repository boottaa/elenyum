let validator = {
    phone: (value) => {
        value = value.value;
        let regex = /^((8|\+7)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{7,10}$/;
        if (value !== null && value !== '' && ! regex.test(value)) {
            return {
                success: false,
                message: 'Номер телефона не корректный',
            };
        } else {
            return {
                success: true,
            };
        }
    },
    password: (data) => {
        let repeat = data.repeat;
        let value = data.value;
        let regex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]{8,}$/;

        if (!regex.test(value)) {
            return {
                success: false,
                message: 'Пароль должен содержать хотя бы одну заглавную букву и одну цифру и должен быть не меньше 8 символов',
            };
        } else if (repeat !== value) {
            return {
                success: false,
                message: 'Пароли не совпадают, повторите пароль',
            };
        } else {
            return {
                success: true,
            };
        }
    },
    fio: (value) => {
        value = value.value;
        if (value === null || value.split(' ').length < 2) {
            return {
                success: false,
                message: 'Необходимо ввести имя и фамилию через пробел (пример: Александр Жуков)',
            };
        } else {
            return {
                success: true,
            };

        }
    },
    email: (value) => {
        value = value.value;
        let regex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

        if (Boolean(value) && !regex.test(value)) {
            return {
                success: false,
                message: 'Email не корректный',
            };
        } else {
            return {
                success: true,
            };

        }
    },
    notEmpty: (data) => {
        let value = data.value;
        if (value === null || value === '' || value === undefined || value?.length === 0 || value === {}) {
            return {
                success: false,
                message: 'Обязательно для заполнения',
            };
        } else {
            return {
                success: true,
            };
        }
    },
}

export function isValid(items) {
    let result = null;
    $('.invalid-feedback').remove();

    for (const [key, value] of Object.entries(items)) {
        value.validators.forEach((item) => {
            if (typeof validator[item] === 'function') {
                let validResult = validator[item](value);
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
    $(itemId).addClass('is-invalid');
    $(itemId).parent().append(`<div class="invalid-feedback">${message}</div>`);
}
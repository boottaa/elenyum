import 'jquery.cookie';

export function setToken(token) {
    $.cookie('token', token, {
        expires: 7,
        path: '/'
    });
}

export function getToken() {
    return $.cookie('token');
}

export function removeToken() {
    $.removeCookie('token');
}

export function post(url = '', data = {}, successFunc = (result) => {}, errorFunc = (result) => {}) {
    let loaderId = 'id_' + parseInt(Math.random() * 10000);
    let params = {
        type: "POST",
        url: url,
        data: JSON.stringify(data, (key, value) => {
            return value
        }),
        contentType: "application/json",
        dataType: 'json',
        success: (result) => successFunc(result),
        error: (result) => errorFunc(result),
        beforeSend: () => {
            $('body').append(`<div class="loaderMask" id="${loaderId}"><div class="loader">Loading...</div></div>`);
        },
        complete: () => {
            $('#' + loaderId).remove();
        }
    };
    $.ajax(params);
}

export function put(url = '', id = null, data = {}, successFunc = (result) => {}) {
    let loaderId = 'id_' + parseInt(Math.random() * 10000);
    if (id === null) {
        console.log('Error: id can`t is null');
        return;
    }
    let params = {
        type: "PUT",
        url: url + '/' + id,
        data: JSON.stringify(data, (key, value) => {
            return value
        }),
        contentType: "application/json",
        dataType: 'json',
        success: (result) => successFunc(result),
        beforeSend: () => {
            $('body').append(`<div class="loaderMask" id="${loaderId}"><div class="loader">Loading...</div></div>`);
        },
        complete: () => {
            $('#' + loaderId).remove();
        }
    };
    $.ajax(params);
}

export function get(url = '', successFunc = (result) => {}) {
    let loaderId = 'id_' + parseInt(Math.random() * 10000);
    let params = {
        type: "GET",
        url: url,
        contentType: "application/json",
        dataType: 'json',
        success: (result) => successFunc(result),
        beforeSend: () => {
            $('body').append(`<div class="loaderMask" id="${loaderId}"><div class="loader">Loading...</div></div>`);
        },
        complete: () => {
            $('#' + loaderId).remove();
        }
    };

    $.ajax(params);
}

export function del(url = '', successFunc = (result) => {}, error = (result) => {}) {
    let loaderId = 'id_' + parseInt(Math.random() * 10000);
    let params = {
        type: "DELETE",
        url: url,
        contentType: "application/json",
        dataType: 'json',
        success: (result) => successFunc(result),
        error: (result) => error(result),
        beforeSend: () => {
            $('body').append(`<div class="loaderMask" id="${loaderId}"><div class="loader">Loading...</div></div>`);
        },
        complete: () => {
            $('#' + loaderId).remove();
        }
    };

    $.ajax(params);
}
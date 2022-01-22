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

export function post(url = '', data = {}, successFunc = (result) => {}) {
    let params = {
        type: "POST",
        url: url,
        data: JSON.stringify(data, (key, value) => {
            return value
        }),
        contentType: "application/json",
        dataType: 'json',
        success: (result) => successFunc(result)
    };
    $.ajax(params);
}

export function put(url = '', id = null, data = {}, successFunc = (result) => {}) {
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
        success: (result) => successFunc(result)
    };
    $.ajax(params);
}

export function get(url = '', successFunc = (result) => {}) {
    let params = {
        type: "GET",
        url: url,
        contentType: "application/json",
        dataType: 'json',
        success: (result) => successFunc(result)
    };

    $.ajax(params);
}

export function del(url = '', successFunc = (result) => {}, error = (result) => {}) {
    let params = {
        type: "DELETE",
        url: url,
        contentType: "application/json",
        dataType: 'json',
        success: (result) => successFunc(result),
        error: (result) => error(result),
    };

    $.ajax(params);
}
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

export function post(url = '', data = {}, successFunc = (result) => {}, withToken = true) {
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
    if (withToken) {
        params.headers = {"X-AUTH-TOKEN": getToken()};
    }
    $.ajax(params);
}
export function setToken(token) {
    let date = new Date().addHours(144);
    date = date.toUTCString();
    document.cookie = `token=${token}; path=/; expires=${date}`;
}

export function getToken() {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; token=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
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
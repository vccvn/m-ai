var APIMethods = {
    urls: {},
    init_list: ["urls"],
    url: function (action) {
        return typeof this.urls[action] != "undefined" ? this.urls[action] : null;
    },

    callApi: function (method, url, data, headers, controller) {
        if (!headers) headers = {};
        var apiUrl = this.url(url) || url;
        // console.log(method, apiUrl, data);
        var requestData = {
            method: method,
            data: data,
            dataType: "JSON",
            cookie: true,
            headers: headers
        };
        if(controller){
            requestData.signal = controller.signal;
        }
        return App.axios(apiUrl, requestData).then(function (response) {
            var res = response.data;
            if ((response.status != 200 && response.statusText != "OK" ) || !res) {
                throw new Error("Lỗi không xác định");
            } else {
                return res;
            }
        });
    },
    upload: function(url, data, options){
        return App.axiosUpload(url, data, options).then(function (response) {
            var res = response.data;
            if (response.statusText != "OK" || !res) {
                throw new Error("Lỗi không xác định");
            } else {
                return res;
            }
        });
    }
};

["get", "post", "put", "patch", "delete", "head", "options"].map(function (method) {
    var mt = method.toUpperCase();
    /**
     * gửi request dạng {mt} 
     * @param {string|option} url url hoặc option
     * @param {object|null|undefined} data tham số tùy chọn
     * @param {object|null|undefined} headers tham số tùy chọn
     */

    APIMethods[method] = function (url, data, headers, controller) {
        return this.callApi(mt, url, data, headers, controller);
    }
});

App.extend({
    api: APIMethods
});









if (typeof window.apiInit == "function" || typeof window.custoAapiInit == "function") {
    if (typeof window.apiInit == "function") {
        window.apiInit();
    }
}


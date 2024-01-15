const defaultHeaders = {
    Authorization: "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdW....."
};
const parseUrl = (url, params) => url +( params? (url.split("?").length > 1? '&': "?") + Object.keys(params).map(key => key + "=" + encodeURIComponent(params[key])).join("&"): '')
const Api = {
    get: (url, params, headers) => fetch(parseUrl(url, params), {
        method: "GET",
        headers: Object.assign({}, defaultHeaders, headers?headers:{})
    }),
    post: (url, body, headers) => fetch(url, {
        method: "POST",
        body:JSON.stringify(body),
        headers: Object.assign({}, defaultHeaders, headers?headers:{})
    }),
    put: (url, body, headers) => ({status: true, data: null, message: "Thao tác thành công", errors: []}),
    patch: (url, body, headers) => ({status: true, data: null, message: "Thao tác thành công", errors: []}),
    delete: (url, params, headers) => ({status: true, data: null, message: "Thao tác thành công", errors: []}),
};
let response = {};

// Account Info
Api.get('/api/account/info', null, {})
response = {
    status: true,
    data: {
        name: "Họ và tên",
        balance: 2000000, // số dư
        expired_at: "YYYY-MM-DD HH:ii:ss"// 2024-02-14 23:00:00
    },
    message: "",
}

//packages
Api.get('/api/payment/packages')
response = {
    status: true,
    data: [
        {
            id: 123, // id gói
            name: "Tên gói",
            description: "Mô tả gói",
            month: 3, // số tháng
            price: 123000, // giá
            discount: 20, // khuyến mãi %
            currency: "VND", // USD, EUR, ...
        },
        //...
    ],
    message: "",
    errors: []
}
//Methods
Api.get('/api/payment/methods')
response = {
    status: true,
    data: [
        {
            id: 1, // id gói
            name: "Tên phương thức",
            description: "Mô tả phương thức",
            logo: null, // url logo

        },
        //...
    ],
    message: "",
    errors: []
}

// checkout
Api.post('/api/payment/checkout', {
    package_id: 1,
    payment_method_id: 1
})
response = {
    status: true,
    data: {
        status: "payment",
        action: "payment",
        payment: {
            transaction_code: "ALE00RYY3",
            price_format: "49.500₫",
            check_status_url: "https://trekka.vcc.vn/api/payment/status",
            checkout_url: "https://alepay-v3-sandbox.nganluong.vn/checkout/vi/v3/index/97d2d85ab97f4605ad1f43f96651dd75"
        }
    },
    message: "",
    errors: []
}

// check status
Api.post('/api/payment/checkout', {
    transaction_code: "ALE00RYY3"
})
response = {
    status: true,
    data: {
        status: false,
        stop: false,// cho biết có cần kiểm tra nữa hay không
        payment: {}
    },
    message: "chưa có thông tin",
    errors: []
}


const fs = require("fs");
const cli = require('./src/app/cli');
const converter = require('./src/app/converter');
const { default: axios } = require("axios");
require('dotenv').config();
//CREATE EXPRESS APP

axios.defaults.timeout = 5000;
const rootPath = process.env.BASE_PATH?process.env.BASE_PATH:'/var/www/html/thithuthptqg/';

const NOTIFICATION_EMAIL = process.env.NOTIFICATION_EMAIL?process.env.NOTIFICATION_EMAIL:'doanln16@gmail.com';



const checkHealth = async function () {
    var status = 'ERROR';
    status = await axios.get('https://trekka.vcc.vn/check-health')
    .then(rs => rs.data)
    .catch(e => e.message)
    return status;
}

const doCheck = async () => {

    var status = await checkHealth();
    if(status != "OK"){
        // await cli.exec("systemctl restart httpd");
        await cli.exec("sudo systemctl restart postgresql.service");
    }
    // console.log(status);
}

doCheck();

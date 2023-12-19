const fs = require("fs");
const express = require('express');
const cli = require('./src/app/cli');
const converter = require('./src/app/converter');
const bodyParser = require('body-parser');
const { default: axios } = require("axios");
require('dotenv').config();
//CREATE EXPRESS APP

const app = express();



app.use(bodyParser.urlencoded({ extended: true }))

// parse application/json
app.use(bodyParser.json())
// var server_password = process.env.NODE_SERVER_PASSWORD;

const rootPath = process.env.BASE_PATH?process.env.BASE_PATH:'/var/www/html/mangala-ar/';
const LARAVEL_PORT = process.env.LARAVEL_PORT ? process.env.LARAVEL_PORT : 8080;
const NODE_API_PORT = process.env.NODE_API_PORT ? process.env.NODE_API_PORT : 4321;



const EMAIL = 'doanln16@gmail.com';

const TMPPATH = rootPath + 'storage/tmp/';

const API_KEY = 'DoanLN!=DF6M$%';


const updateExamData = function (data) {
    axios.post('http://127.0.0.1:' + LARAVEL_PORT + '/api/exams/update-export-data', data)
    .then(rs => console.log(rs.data))
    .catch(e => console.log(e))
}
const updateExamFileStatus = function (data) {
    console.log(data);
    return ;
    axios.post('http://127.0.0.1:' + LARAVEL_PORT + '/api/exams/update-export-file-data', data)
        .then(rs => console.log(rs.data))
        .catch(e => console.log(e))
}

const updateModelTracking = function (url,data) {
    axios.post(url, data)
    .then(rs => rs.data)
    .catch(e => console.log(e))
}

// var server = require('http').createServer(app);
// trang chu

app.get('/', function (req, res) {
    res.send("<h1>Doan Dep trai</h1>");
});

app.post('/zip-folder', async (req, res) => {
    const apiKey = req.body.api_key;
    const folder = req.body.folder;
    const filename = req.body.filename ? req.body.filename : folder + ".zip";
    if (apiKey != API_KEY) {
        res.json({
            status: false,
            message: "Api Key không chính xác"
        });
    }
    else if (folder == '') {
        res.json({
            status: false,
            message: "folder không được bỏ trống"
        });
    }

    else {
        var status = await cli.execList([
            // "cd " + examExportPath + " && zip -r " + filename + " " + folder//,
            // "sudo chown -Rf "+USER_GROUP+" " + examExportPath + filename,
            // "sudo chown -Rf "+USER_GROUP+" " + examExportPath + folder
        ]);
        if (status) {

            res.json({
                status: true,
                message: "Thao tác thành công"
            });

        } else {
            res.json({
                status: false,
                message: "Không thể xử lý file"
            });
        }
    }
});

app.post('/command', async (req, res) => {
    const apiKey = req.body.api_key;
    const command = req.body.command;
    const filename = req.body.filename ? req.body.filename : folder + ".zip";
    if (apiKey != API_KEY) {
        res.send('Api Key không chính xác')
    }
    else if (command == '') {
        res.send("command ko dc bỏ trống");
    }

    else {

        var r = false;
        var status = await cli.exec()
    }
});


app.post('/nft-creator', async (req, res) => {
    let folder = req.body.folder;
    let secret = req.body.secret;
    let image = req.body.image;
    let relative_folder = req.body.relative_folder;
    let callbackUrl = req.body.callback;
    let cd = "cd " + rootPath;
    if(!fs.existsSync(folder + "/" + image)){
        res.json({
            status: false,
            message: "Tạo NFT thành công"
        });
        return;
    }

    res.json({
        status: true,
        message: "Tạo NFT thành công"
    });

    let createNftStatus = await cli.run(cd + " && node nft-creator.js -i " + relative_folder + "/" + image + " -o " + relative_folder);
    let data = {secret:secret,image:image, status:0};
    if (createNftStatus) {
        data.status = 1;
    }

    updateModelTracking(callbackUrl,data);
    let chownStatus = await cli.run("chown -Rf www-data:www-data " + folder);
});



app.listen(NODE_API_PORT, () => {
    console.log('App listening on port ' + NODE_API_PORT)
});

console.log(LARAVEL_PORT, NODE_API_PORT);

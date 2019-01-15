var formidable = require('formidable'),
    fs = require('fs');
upload_middleware = require('./upload_middleware');

const customConfig = require('../../../../../common/config/amos-uploader-config');

const express = require('express');
const app = express();

var directoryUploader = 'common/uploads/uploader/';
var fullUploadDirectory = customConfig.uploadDirectory + directoryUploader;

app.use(function (req, res, next) {
    res.header("Access-Control-Allow-Origin", customConfig.serverUrl);
    res.header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
    next();
});

app.listen(customConfig.port, function () {
    if(customConfig.printUploadStatusInConsole){
        console.log('\nyour upload folder is:\n' + fullUploadDirectory);
    }
});

app.post('/upload', upload_middleware.upload,  function (req, res, next) {
    var form = new formidable.IncomingForm();

    var fileinfo = req.form.fileInfo;
    var filesize = req.form.fileInfo.filesize;

    checkDirectory();

    var randomString = makerandomstring();
    var filename = 'upload_'+randomString+'.zip';
    var directory = fullUploadDirectory+filename;

    while(fs.existsSync(directory)) {
        randomString = makerandomstring();
        filename = 'upload_'+randomString+'.zip';
        directory = fullUploadDirectory+filename;
    }

    var relativeUploadDirectory = directoryUploader+filename;

    if(customConfig.printUploadStatusInConsole) {
        console.log('\n-----------------------------\n\nstarted uploading file:\n');
        printCurrentDateInConsolle();
        printFileInConsolle(relativeUploadDirectory);
        console.log('\n-----------------------------\n');
    }

    var out = fs.createWriteStream(directory, {
        mode: 0777
    });

    form.uploadDir = customConfig.uploadDirectory;
    form.keepExtensions = true;
    form.hash = 'sha1';

    if(customConfig.printProgressInConsole) {
        req.form.on('progress', function(bytesReceived, bytesExpected) {
            console.log(relativeUploadDirectory + ' > ' + bytesReceived);
        });
    }

    req.form.speedTarget = 20000;

    req.form.onChunk = function(data, callback) {
        out.write(data);

        //Change Mode
        fs.chmodSync(directory,fs.constants.S_IROTH);

        callback();
    };

    req.form.on('end', function() {
        setTimeout(function(){
            out.end();
            if(customConfig.printUploadStatusInConsole) {
                console.log('\n-----------------------------\n\nfile uploaded successfully:\n');
                printCurrentDateInConsolle();
                printFileInConsolle(relativeUploadDirectory);
                console.log('\n-----------------------------\n');
            }
            var resultFiles = [];
            resultFiles.push({
                'size': fileinfo.filesize,
                'path': relativeUploadDirectory,
                'name': fileinfo.filename,
                'type': '',
                'hash': randomString,
            });
            res.send(JSON.stringify({
                'files': resultFiles
            }));
        }, 1000);
    });

    req.form.on('error', function(error) {
        if(customConfig.printUploadStatusInConsole) {
            console.log('\n-----------------------------\n\nerror:\n');
            printCurrentDateInConsolle();
            printFileInConsolle(relativeUploadDirectory + '\n');
            console.log(error);
            console.log('\n-----------------------------\n');
        }
    });

    req.form.on('aborted', function() {
        if(customConfig.printUploadStatusInConsole) {
            console.log('\n-----------------------------\n\nclient has disconnected:\n');
            printCurrentDateInConsolle();
            printFileInConsolle(relativeUploadDirectory);
            console.log('\n-----------------------------\n');
        }
    });

    // simulate async operation here (read can also be called sync)
    setTimeout(function() {
        req.form.read();
    }, 500);

    return;
}, upload_middleware.errorHandler);

function checkDirectory() {
    if (!fs.existsSync(fullUploadDirectory)) {
        fs.mkdirSync(fullUploadDirectory);
    }
}

function printFileInConsolle(file) {
    console.log('file: ' + file);
}

function printCurrentDateInConsolle() {
    var currentDateStringified = new Date().toISOString().replace(/T/, ' ').replace(/\..+/, '');
    console.log('on: ' + currentDateStringified + ' UTC\n');
}

function makerandomstring() {
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for (var i = 0; i < 50; i++)
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
}
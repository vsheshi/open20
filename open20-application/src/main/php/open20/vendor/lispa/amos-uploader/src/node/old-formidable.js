var formidable = require('formidable'),
    http = require('http'),
    util = require('util'),
    fs = require('fs');

var port = 8080; //TODO set the port number for node service
var serverUrl = "http://pcd20.open2.0.appdemoweb.org"; //TODO REPLACE with server url from which accept calls

var uploadDirectory = 'common/uploads/uploader/';

const express = require('express');
const app = express();

app.use(function (req, res, next) {
    res.header("Access-Control-Allow-Origin", serverUrl);
    res.header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
    next();
});

app.listen(port, function () {

});

app.post('/upload', function (req, res) {
    var form = new formidable.IncomingForm();

    checkDirectory();

    form.uploadDir = uploadDirectory;
    form.keepExtensions = true;
    form.hash = 'sha1';

    form.parse(req, function (err, fields, files) {
        var resultFiles = [];

        for (a in files) {
            var file = files[a];

            resultFiles.push({
                'size': file.size,
                'path': file.path,
                'name': file.name,
                'type': file.type,
                'hash': file.hash
            });
        }

        res.writeHead(200, {'content-type': 'application/json'});
        //res.write('received upload:\n\n');
        res.end(JSON.stringify({
            'files': resultFiles
        }));
    });

    return;
});

function checkDirectory() {
    if (!fs.existsSync(uploadDirectory)) {
        fs.mkdirSync(uploadDirectory);
    }
}
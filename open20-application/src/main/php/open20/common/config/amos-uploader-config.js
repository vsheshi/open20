/*
    CUSTOM PARAMS FOR amos-uploader node.js uploader script
 */

/*
    The following are the params mandatory to configure the formidable instance if you need to run it over http or https.
 */

exports.port = 8080; // TODO set the port number for node service
exports.serverUrl = "https://pcd.servizirl.it/"; // TODO REPLACE with server url from which accept calls

exports.uploadDirectory = '/var/www/pcd20-lispa-2_0/httpdocs/'; // TODO REPLACE with you amos directory

exports.printProgressInConsole = false; // TODO REPLACE with true or false for displaying how much bytes are received from client in console
exports.printUploadStatusInConsole = true; // TODO REPLACE with true or false for displaying upload status in console
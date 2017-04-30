/**
 * Created by ravil on 18.04.17.
 */

var clients = {};

exports.subscribe = function (req, res, clientId) {
    if (clients[clientId] == undefined ){
        clients[clientId] = [];
    }
    clients[clientId].push(res);
    res.on('close', function () {
        clients[clientId].splice(clients[clientId].indexOf(res), 1);
    });
};

exports.publish = function (message, clientIds) {
    clientIds.forEach(function (clientId) {
        if (clients[clientId] instanceof Array) {
            clients[clientId].forEach(function (res) {
                res.end(message);
            });
        }
    });
};

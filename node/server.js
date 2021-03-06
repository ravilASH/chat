/**
 * Created by ravil on 18.04.17.
 */

var http = require('http');
var chat = require('./chat');
var url = require('url');

http.createServer(function (req, res) {
    urlParsed = url.parse(req.url, true);

    switch ( urlParsed.pathname ) {
        case '/subscribe':
            chat.subscribe(req , res, urlParsed.query.id);
            break;
        case '/publish' :
            var body = '';
            req
                .on('readable', function () {
                    var tmp = req.read();
                    body += (tmp) ? tmp : '';
                })
                .on('end', function () {
                    // todo сделать try - catch и логи
                    bodyParsed = JSON.parse(body);
                    clientIds = bodyParsed._pushTo;
                    if(clientIds instanceof Array) {
                        chat.publish(body, clientIds);
                    }
                    res.end('ok');
                });
            break;
        default :
            res.statusCode = 404;
            res.end('Not Found');
    }
}).listen(9090);
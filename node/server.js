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
                    // здесь надо переделать способ передаци айдишников на более универсальный
                    // todo сделать try - catch и логи
                    bodyParsed = JSON.parse(body);
                    clientIds = bodyParsed.data.chat.userIds;
                    chat.publish(body, clientIds);
                    res.end('ok');
                });
            break;
        default :
            res.statusCode = 404;
            res.end('Not Found');
    }
}).listen(9090);
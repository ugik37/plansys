var sse = require('connect-sse')();
var express = require('express');
var path = require('path');
var mysql = require('mysql');
var rekuire = require('rekuire');
var dateFormat = require('dateformat');
var nodemailer = require('nodemailer');
var smtpTransport = require('nodemailer-smtp-transport');
var validator = require('validator');
var htmlToText = require('html-to-text');
var fs = require('fs');
var swig = require('swig');
var app = express();
var config = rekuire('../../config/settings.json');
var pool = mysql.createPool({
    connectionLimit: 100,
    host: config.db.server,
    user: config.db.username,
    password: config.db.password
});

var resPool = {};
var sentEmail = [];

var getTemplate = function(name) {
    fs.stat(name, function(err, stat) {
        if(err == null) {
            return name;
        }else{
            return false;
        }
    });
};

var updateEmail = function(err, data, conn, type){
    var ids = [];
    for (i in data) {
        ids.push(data[i].id);
        data[i].created_on = dateFormat(data[i].created_on, "yyyy-mm-dd HH:MM:ss")
    }
    if(type === 'notif'){
        var sql = "UPDATE p_nfy_messages set status = 1 WHERE id IN (" + ids.join(",") + ")";
        conn.query(sql, function(){
            sendNotif(err,data,conn);
        });
    }else if(type === 'email'){
        var sql = "UPDATE p_email_queue set status = 1 WHERE id IN (" + ids.join(",") + ")";
        conn.query(sql, function(){
            sendEmail(err,data,conn);
        });
    }
};

var sendEmail = function(err,rows,conn){
    for (var i in rows) {
        var row = rows[i];
        if (resPool[row.id]) {
            resPool[row.id].json([row]);
            console.log("Email #" + row.id + " sent: " + JSON.stringify([row]));
        }
    }
    
    //send mail
    if (config.email && config.email.from && config.email.transport) {
        var transport = nodemailer.createTransport(smtpTransport(config.email.transport));
        for (var j in rows) {
            var row = rows[j];
            
            if (sentEmail.indexOf(row.id) >= 0) {
                continue;
            }

            if (!validator.isEmail(row.email)) {
                continue;
            }
            
            if (!validator.isEmail(row.email_sender)) {
                continue;
            }
            
            var mailOptions = {
                from: row.sender+ ' <'+row.email_sender+'>', // sender address
                to: row.email, // list of receivers
                subject: row.subject, // Subject line,
                html: row.body,
                text: htmlToText.fromString(row.body, {
                        wordwrap: 130
                      })
            };

            transport.sendMail(mailOptions, function (error, info) {
                if (error) {
                    console.log(error);
                } else {
                    console.log('Email #' + row.id + ' mail sent:'
                            + info.response + "\n");
                }
            });

            sentEmail.push(row.id);
        }
        transport.close();
    }
};

var sendNotif = function(err,rows,conn){
    for (var i in rows) {
        var row = rows[i];
        if (resPool[row.subscription_id]) {
            resPool[row.subscription_id].json([row]);
            console.log("Subscriber #" + row.subscription_id + " sent: " + JSON.stringify([row]));
        }
    }
    
    // send e-mail notification
    if (config.email && config.email.from && config.email.transport) {
        var transport = nodemailer.createTransport(smtpTransport(config.email.transport));
        for (var j in rows) {
            var row = rows[j];
            
            if (sentEmail.indexOf(row.id) >= 0) {
                continue;
            }

            if (!validator.isEmail(row.to_email)) {
                continue;
            }
            var body = JSON.parse(row.body);
            var url = config.app.host + body.url;
            var subject = config.app.name ? config.app.name + " - " + body.message : body.message;
            var mailOptions = {
                from: config.email.from, // sender address
                to: row.to_email, // list of receivers
                subject: subject, // Subject line,
                url: url,
                appName: config.app.name
            };
            
            var template = swig.compileFile(path.resolve(__dirname, "../../../plansys/static/email/notification.twig"));
            mailOptions.html = template(mailOptions);
            mailOptions.text = htmlToText.fromString(mailOptions.html, {
                wordwrap: 130
            });

            transport.sendMail(mailOptions, function (error, info) {
                if (error) {
                    console.log(error);
                } else {
                    console.log('Subscriber #' + row.subscription_id + ' mail sent:'
                            + info.response + "\n");
                }
            });

            sentEmail.push(row.id);
        }
        transport.close();
    }
};

streamQuery = setInterval(function () {
    pool.getConnection(function (err, conn) { 
        conn.query("USE " + config.db.dbname, function (err, rows) {
            // notif
            if(!!config.notif.email){
                var notif = 'select u.email as to_email, u.fullname as to_name, "notification" AS template ,a.* from \
                            ((SELECT t.*, "Notification" as sender_name, "SYSTEM" as sender_role FROM p_nfy_messages t \
                            WHERE sender_id = 0 AND status = 0 AND subscription_id is not null)  \
                            UNION \
                            (SELECT t.*, p.fullname as sender_name, r.role_name as sender_role FROM p_nfy_messages t \
                            inner join p_user p      on t.sender_id = p.id \
                            inner join p_user_role q on q.user_id = p.id \
                            inner join p_role r      on q.role_id = r.id \
                            WHERE sender_id <> 0 AND status = 0 AND subscription_id is not null) \
                            ) a \
                            inner join p_nfy_subscriptions p on p.id = a.subscription_id \
                            inner join p_user u on p.subscriber_id = u.id';
                conn.query(notif, function(err, rows) {
                    if (typeof rows !== "undefined" && rows.length > 0) {
                        updateEmail(err,rows,conn,'notif');
                    }
                });
            }
            
            // email
            if(config.email.transport.service != 'none'){
                var email  = "SELECT e.*,u.fullname AS sender, u.email AS email_sender FROM p_email_queue e \
                              INNER JOIN p_user u ON e.user_id = u.id \
                              WHERE e.status = 0"
                conn.query(email, function(err, rows) {
                    if (typeof rows !== "undefined" && rows.length > 0) {
                        updateEmail(err,rows,conn,'email');
                    }
                });
            }
            
            conn.release();
        });
    });
}, 2000);

app.set('port', 8981);
app.use(express.static(path.join(__dirname, 'public')));
app.all('*', function (req, res, next) {
    res.header("Access-Control-Allow-Origin", "*");
    res.header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
    next();
});
app.get('/:id', sse, function (req, res) {
    console.log("Subscriber #" + req.params.id + " connected");

    resPool[req.params.id] = res;

    res.on("error", function () {
        clearInterval(res.streamQuery);
        console.log("Subscriber #" + req.params.id + " error");
    });
    res.on("close", function () {
        clearInterval(res.streamQuery);
        console.log("Subscriber #" + req.params.id + " disconnected");
    });
});
app.listen(app.get('port'));
console.log('\
----------------------------------------\n\
Welcome To Plansys Notification Server\n\
Nfy server listening on port ' + app.get('port') + "\n\
---------------------------------------\n\
");
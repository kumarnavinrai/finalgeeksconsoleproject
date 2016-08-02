var app = require('http').createServer(handler),
  io = require('socket.io').listen(app),
  fs = require('fs'),
  url = require('url'),
  express = require('express'),
  mysql = require('mysql'),
  connectionsArray = [],
  connection = mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'pcoptimiserpanel',
    port: 3306
  }),
  POLLING_INTERVAL = 3000,
  pollingTimer;

// If there is an error connecting to the database
connection.connect(function(err) {
  // connected! (unless `err` is set)
  if (err) {
    console.log(err);
  }
});

// creating the server ( localhost:8000 )
app.listen(8000);

// on server started we can load our client.html page
function handler(req, res) {
    var parsedUrl = url.parse(req.url, true);
    var query = parsedUrl.query;


    console.log(query);
        console.log(req.headers.host);

  fs.readFile(__dirname + '/navin.php', function(err, data) {
    if (err) {
      console.log(err);
      res.writeHead(500);
      return res.end('Error loading client.html');
    }
    res.writeHead(200);
    res.end(data);
  });
}



/*
 *
 * HERE IT IS THE COOL PART
 * This function loops on itself since there are sockets connected to the page
 * sending the result of the database query after a constant interval
 *
 */
var oldusers = [];
var pollingLoop = function(host) {

    //console.log(connectionsArray);
    //console.log("--------------");
   
  //SELECT * FROM temponline LEFT JOIN appdata ON temponline.ip = appdata.ip WHERE status = 1 AND version = 1
  //SELECT * FROM temponline WHERE status = 1 
  //SELECT temponline.id as id, temponline.user_name as user_name, temponline.ip as ip, temponline.port as port, temponline.source, appdata.version as version  FROM temponline INNER JOIN appdata ON temponline.ip = appdata.ip WHERE status = 1 GROUP BY temponline.id
  var q = "SELECT temponline.id as id, temponline.user_name as user_name, temponline.ip as ip, temponline.port as port, temponline.source, appdata.version as version  FROM temponline INNER JOIN appdata ON temponline.ip = appdata.ip WHERE status = 1 GROUP BY temponline.id";
  // Doing the database query
  var query = connection.query(q),
    users = []; // this array will contain the result of our db query
   
  // setting the query listeners
  query
    .on('error', function(err) {
      // Handle error, and 'end' event will be emitted after this as well
      console.log(err);
      updateSockets(err);
    })
    .on('result', function(user) {
      // it fills our array looping on each user row inside the db
      users.push(user);
    })
    .on('end', function() {
      // loop on itself only if there are sockets still connected
      if (connectionsArray.length) {

        pollingTimer = setTimeout(pollingLoop, POLLING_INTERVAL);
        console.log(users.length);
        console.log(oldusers.length);
        //if(users.length != oldusers.length){
          console.log("updating sockets");
          updateSockets({
            users: users
          });
        //}  

        oldusers = users;
      } else {

        console.log('The server timer was stopped because there are no more socket connections on the app')

      }
    });
};


// creating a new websocket to keep the content updated without any AJAX request
io.sockets.on('connection', function(socket) {
  // Get server host
    var host = socket.handshake.headers.host;

    // Remove port number together with colon
    host = host.replace(/:.*$/,"");

    // To test it, output to console
   // console.log(host);

  console.log('Number of connections:' + connectionsArray.length);
  // starting the loop only if at least there is one user connected
  if (!connectionsArray.length) {
    pollingLoop(host);
  }

  socket.on('disconnect', function() {
    var socketIndex = connectionsArray.indexOf(socket);
    console.log('socketID = %s got disconnected', socketIndex);
    if (~socketIndex) {
      connectionsArray.splice(socketIndex, 1);
    }
  });

  console.log('A new socket is connected!');
  console.log(socket);
  connectionsArray.push(socket);

});

var updateSockets = function(data) {
  //console.log(data); 

  // adding the time of the last update
  data.time = new Date();
  console.log('Pushing new data to the clients connected ( connections amount = %s ) - %s', connectionsArray.length , data.time);
  // sending new data to all the sockets connected
  connectionsArray.forEach(function(tmpSocket) {
    
    var o = tmpSocket.handshake.headers.origin;
    var myarr = o.split(".");
    var str = myarr[0]; 
    var res = str.replace("http://", "");
    sortDataForSocket({
            data: data, host: res
          });
    console.log(data);
    tmpSocket.volatile.emit('notification', data);
  });
};


var sortDataForSocket = function(data){
  //console.log(data.data);
  var origdatacame = data.data;
  var host = data.host;
  var data = data.data.users;
  //console.log(host);

   var datatoreturn = { users: []};
   var versionofhost;
   switch(host) {
    case "admin":
      versionofhost = 99;
      //return data;
      break;
    case "adminone":
      versionofhost = 1;
      break;
    case "admintwo":
      versionofhost = 2;
      break;
    case "adminthree":
      versionofhost = 3;
      break;
    case "adminfour":
      versionofhost = 4;
      break;
    case "adminfive":
      versionofhost = 5;
      break;
    case "adminsix":
      versionofhost = 6;
      break;
    case "adminseven":
      versionofhost = 7;
      break;
    case "admineight":
      versionofhost = 8;
      break;          
    // add the default keyword here

  }

  data.forEach(function(value){
    //console.log(value.version);
    //console.log(versionofhost);
    if(value.version == versionofhost)
    {
      datatoreturn.users.push(value);
    }
  });
  
  if(versionofhost == 99)
  {
     return origdatacame;
  }
  //console.log(datatoreturn);
  return datatoreturn;
  

};

console.log('Please use your browser to navigate to http://localhost:8000');

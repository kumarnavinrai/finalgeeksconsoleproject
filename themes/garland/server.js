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
var connectionArrayOldLength;
var connectionCalled = 0;
var pollingLoop = function(host) {

    //console.log(connectionsArray);
    console.log("--------------");
   

  // Doing the database query
  var query = connection.query('SELECT * FROM temponline'),
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
        if(users.length != oldusers.length || connectionArrayOldLength != connectionsArray.length || connectionCalled == 1){
          console.log("Updating ................");
          updateSockets({
            users: users
          });
        }
        connectionCalled = 0;
        connectionArrayOldLength =  connectionsArray.length; 
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
    connectionCalled = 1;
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
  connectionsArray.push(socket);

});

var updateSockets = function(data) {

  // adding the time of the last update
  data.time = new Date();
  console.log('Pushing new data to the clients connected ( connections amount = %s ) - %s', connectionsArray.length , data.time);
  // sending new data to all the sockets connected
  connectionsArray.forEach(function(tmpSocket) {
    tmpSocket.volatile.emit('notification', data);
  });
};

console.log('Please use your browser to navigate to http://localhost:8000');

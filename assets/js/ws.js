function connect() {
  console.log('Trying to connect with connect()');
  exampleSocket = new WebSocket("wss://" + window.location.hostname + "/socketserver");
  exampleSocket.onopen = function (event) {
    console.log("Connection established!");
    exampleSocket.send("Client connected to network");
  };

  exampleSocket.onclose = function (event) {
    console.log("Connection closed!");
    setTimeout(() => {connect()}, 1000);
  };

  exampleSocket.addEventListener("message", function(e) {
      try {
        let json = JSON.parse(e.data);
        document.dispatchEvent(new CustomEvent(json.type, { detail: json }));
        return true;
      } catch (e) {
          console.log('failed to decode json data');
      }

      document.dispatchEvent(new CustomEvent('ws', { detail: e.data }));
      return true;
  });
}


function sendText() {
  var msg = {
    type: "message",
    date: Date.now()
  };

  // Send the msg object as a JSON-formatted string.
  exampleSocket.send(JSON.stringify(msg));
}

let startPlayEvent = function() {
  var msg = {
    type: "startPlay",
    date: Date.now()
  };
  
    // Send the msg object as a JSON-formatted string.
  exampleSocket.send(JSON.stringify(msg));
}

window.startPlayEvent = startPlayEvent;

(function () {
  console.log('init async');
  connect();
}());
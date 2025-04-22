<?php
// Set the IP and port for the socket server
$host = '127.0.0.1'; // Localhost
$port = 8080; // Port number

// Create a TCP/IP socket
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if (!$socket) {
    die("Socket creation failed: " . socket_strerror(socket_last_error()) . "\n");
}

// Bind the socket to the specified IP and port
if (!socket_bind($socket, $host, $port)) {
    die("Socket bind failed: " . socket_strerror(socket_last_error($socket)) . "\n");
}

// Start listening for connections
if (!socket_listen($socket)) {
    die("Socket listen failed: " . socket_strerror(socket_last_error($socket)) . "\n");
}

echo "Socket server started on $host:$port\n";

while (true) {
    // Accept incoming connections
    $clientSocket = socket_accept($socket);
    if ($clientSocket === false) {
        echo "Socket accept failed: " . socket_strerror(socket_last_error($socket)) . "\n";
        continue;
    }

    // Read data from the client
    $input = socket_read($clientSocket, 1024);
    echo "Received: $input\n";

    // Send a response back to the client
    $response = "Message received: " . trim($input);
    socket_write($clientSocket, $response, strlen($response));

    // Close the client socket
    socket_close($clientSocket);
}

// Close the main socket
socket_close($socket);
?>
const io = require('socket.io')(3000, {
  cors: {
    origin: "*",
  }
});

let clients = {};

io.on('connection', (socket) => {
  console.log('Client connected:', socket.id);

  // Register userId with socket
  socket.on('register', (userId) => {
    clients[userId] = socket;
    console.log(`User ${userId} registered.`);
  });

  // Handle status updates from PHP
  socket.on('statusUpdate', ({ userId, status }) => {
    if (clients[userId] && status === 'accepted') {
      clients[userId].emit('approved');
    }
  });

  socket.on('disconnect', () => {
    console.log('Client disconnected:', socket.id);
    for (const id in clients) {
      if (clients[id] === socket) {
        delete clients[id];
      }
    }
  });
});

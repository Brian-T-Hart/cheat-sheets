# Node-React App

### Create app directory and cd into it
```
mkdir <appName>
cd <appName>
```

### Create Express Backend
```
npx express-generator --no-view
npm install
```

### Update Server Port to 3001 in bin/www
```
var port = normalizePort(process.env.PORT || '3001');
```

### Install Concurrently
```
npm i concurrently
```

### Update Start Script
```
"start": "concurrently \"node ./bin/www\" \"cd client && npm start\""
```

### Install MySQL
```
npm i mysql2
```

### Install Sequelize
```
npm i sequelize
```

### Initialize Sequelize
```
npx sequelize-cli init
```

### Update config/config.json with db credentials
```
"development": {
    "username": "root",
    "password": "root",
    "database": "mih2_dev",
    "host": "127.0.0.1",
    "dialect": "mysql"
}
```

### Create Database
```
npx sequelize-cli db:create
```

***

### Create React Frontend
```
npx create-react-app client
```

### Add proxy to client/package.json
```
"proxy": "http://localhost:3001",
```

### Test Connection
Add the following to client/src/app.js
```
fetch('/users')
.then(response => response.text())
.then(data => console.log({data}));
```

***
## Extras
### Start server in debug mode
```
DEBUG=express:* node ./bin/www
```


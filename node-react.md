# Node-React App

## Create app directory and cd into it

```bash
mkdir <appName>
cd <appName>
```

## Create Express Backend

```bash
npx express-generator --no-view
npm install
```

## Update Server Port to 3001 in bin/www

```bash
var port = normalizePort(process.env.PORT || '3001');
```

## Install Concurrently

```bash
npm i concurrently
```

## Update Start Script

```bash
"start": "concurrently \"node ./bin/www\" \"cd client && npm start\""
```

## Install MySQL

```bash
npm i mysql2
```

## Install Sequelize

```bash
npm i sequelize
```

## Initialize Sequelize

```bash
npx sequelize-cli init
```

## Update config/config.json with db credentials

```json
"development": {
    "username": "root",
    "password": "root",
    "database": "mih2_dev",
    "host": "127.0.0.1",
    "dialect": "mysql"
}
```

## Create Database

```bash
npx sequelize-cli db:create
```

***

## Create React Frontend

```bash
npx create-react-app client
```

## Add proxy to client/package.json

```json
"proxy": "http://localhost:3001",
```

## Test Connection

Add the following to client/src/app.js

```js
fetch('/users')
.then(response => response.text())
.then(data => console.log({data}));
```

***

## Extras

## Start server in debug mode

```bash
DEBUG=express:* node ./bin/www
```

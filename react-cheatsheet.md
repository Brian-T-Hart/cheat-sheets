# React Cheatsheet

## Command Line

### Create a project

```bash
npx create-react-app <app-name>
```

### Create project with TypeScript

```bash
npx create-react-app <app-name> --template typescript
```

### Create project with Redux

```bash
npx create-react-app <app-name> --template redux
```

### Start it up

```bash
cd <app-name>
npm start
```

### Create minified bundle for production

```bash
npm run build
```

### Run test watcher

```bash
npm test
```

### Class component

```js
import React, { Component } from 'react';

class myComponent extends Component {
  constructor(props) {
    super(props);
    this.state = { 
        property: value
    };
  }

  render() {
    return (
        <p>Hello World</p>
    );
  }
}
```

### Functional Component

```js
function Welcome(props) {
  return <h1>Hello, {props.name}</h1>;
}
```

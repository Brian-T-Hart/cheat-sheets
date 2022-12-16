# React Cheatsheet

## Command Line

### Create a project
```
npx create-react-app <app-name>
```

### Create project with TypeScript
```
npx create-react-app <app-name> --template typescript
```

### Create project with Redux
```
npx create-react-app <app-name> --template redux
```

### Start it up
```
cd <app-name>
npm start
```

### Create minified bundle for production
```
npm run build
```

### Run test watcher
```
npm test
```

### Class component
```
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
```
function Welcome(props) {
  return <h1>Hello, {props.name}</h1>;
}
```
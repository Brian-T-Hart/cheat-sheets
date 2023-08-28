# Redux Cheatsheet

## General Info

- **store** is a single js object representing the current state of the application
- **action** is a js object that describes what just happened (Event)
- **reducer** function takes current instance of store and returns a new instance

```js
function reducer(store, action) {
    const updated = { ...store };
    updated.products = ???
}
```

## Steps

- Design the store
- Define the actions
- Create a reducer

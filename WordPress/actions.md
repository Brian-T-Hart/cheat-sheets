# WordPress Actions

Actions provide a way for running a function at a specific point in the execution of WordPress Core, plugins, and themes.
Callback functions for an Action do not return anything back to the calling Action hook.

Example 1: Simple

```php
function my_callback_function() {
    // do something here
}
add_action( 'init', 'my_callback_function');
```

Normal priority is 10, but can be set with 3rd parameter (optional)
The fourth parameter (optional) specifies how many arguments are provided to the callback function

Example 2: Pass parameters to callback

```php
function my_callback_function($param1, $param2) {
    // do something here
}
add_action( 'init', 'my_callback_function', 11, 2);

// params are passed when do action is called
do_action('init', 'param 1', 'param 2');
```

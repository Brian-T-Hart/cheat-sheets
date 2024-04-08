# WordPress Filters

Filters give you the ability to change data during the execution of WordPress Core, plugins, and themes. Callback functions for Filters will accept a variable, modify it, and return it. They are meant to work in an isolated manner, and should never have side effects such as affecting global variables and output. Filters expect to have something returned back to them.

```php
add_filter( string $hook_name, callable $callback, int $priority = 10, int $accepted_args = 1 ): true
```

Example 1: Simple

```php
function example_callback( $example ) {
    // Maybe modify $example in some way.
    return $example;
}
add_filter( 'example_filter', 'example_callback' );
```

Example 2: With Priority

```php
add_filter('use_block_editor_for_post', '__return_false', 10);
```

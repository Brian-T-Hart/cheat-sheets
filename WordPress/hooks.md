# WordPress Hooks

[WordPress Hooks](https://developer.wordpress.org/plugins/hooks/) are a way for one piece of code to interact/modify another piece of code at specific, pre-defined spots. They make up the foundation for how plugins and themes interact with WordPress Core, but they’re also used extensively by Core itself.

There are two types of hooks: *Actions* and *Filters*.

## Actions

- [Wordpress Actions](https://developer.wordpress.org/plugins/hooks/actions/) allow you to add data or change how WordPress operates. Actions will run at a specific point in the execution of WordPress Core, plugins, and themes. Callback functions for Actions can perform some kind of a task, like echoing output to the user or inserting something into the database. Callback functions for an Action do not return anything back to the calling Action hook.

## Filters

- [Wordpress Filters](https://developer.wordpress.org/plugins/hooks/filters/) give you the ability to change data during the execution of WordPress Core, plugins, and themes. Callback functions for Filters will accept a variable, modify it, and return it. They are meant to work in an isolated manner, and should never have side effects such as affecting global variables and output. Filters expect to have something returned back to them.

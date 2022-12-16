# Reactpress

A plugin that allows you to add a React Application to a WordPress instance

- [Wordpress Plugin URL](https://wordpress.org/plugins/reactpress/)  
- [Reactpress Home Page](https://rockiger.com/en/easily-embed-react-apps-into-wordpress-with-reactpress-plugin/)

## Setup

### Install Plugin

- open wp-admin **Dashboard**
- click **Plugins**
- click **Add New**
- search for reactpress
- install and activate the plugin

### Create a react application

- cd to wp-content/reactpress/apps
- create react application

    ```bash
    npx create-react-app my-app
    cd my-app
    npm start
    ```

*Note: After starting the react app, you can view it in the browser at localhost://3000. To view it in the Wordpress instance, there are a few more steps.*

### Add slug to plugin

- head back to the WP Admin Dashboard
- click Reactpress (or refresh page if already there)
- add a slug for your react app page (you do not need to create a page in wp first)  

### Develop

- WordPress uses the build directory. In order to view changes you make to the code, you will need to update the build files.

```bash
npm run build
```

### Go Live

Once the app is ready for production you need to

- add the plugin to the live site
- add a folder in the apps directory with the exact same name used on dev (/wp-content/reactpress/apps/my-app)
- upload your build folder to the my-app folder

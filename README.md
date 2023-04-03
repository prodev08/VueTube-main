# VueTube

![VueTubeScreenshot](https://user-images.githubusercontent.com/720883/109431350-c5df4d80-79cb-11eb-83f7-f14b142d4382.png)

Public Staging Server: https://vuetube.jboullion.com/

A YouTube clone where you can save your favorite channels and search / watch them without dealing with the algorithm.

Build your favorite catalog of channels!

### Run Dev Server

npm run serve

### Build

npm run build

## Stack

- [Vue](https://vuejs.org/) - Using Vue 3
- [Bootstrap](https://getbootstrap.com/) - Not using BootstrapVue since it has not yet been updated to Vue3.
- [Fontawesome](https://fontawesome.com/) - Not using the FontAwesome Vue components, didn't seem necessary.
- [MySQL](https://www.mysql.com/)
- [PHP](https://www.php.net/) - Simple Vanilla PHP with a couple classes controlling all the action
- [Firebase](https://firebase.google.com/) - Google Authentication

## Future Plans

Currently using a simple PHP PDO API for simplicity. Ideally we will migrate to a Symfony Skeleton API, but that might be more hassle than it is worth

Adding more admin functionality: Sync Channels.

Adding support for subdomains in order to access separate databases / tables for different catalogs.

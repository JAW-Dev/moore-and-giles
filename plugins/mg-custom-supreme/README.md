## MG Custom Supreme

WordPress plugin for building out the Custom Supreme page. This includes a custom template page and a React app within that page.

### Getting started

1. Clone repo to your `wp-content/plugins` folder
1. In `config.json` change the `proxyURL` to point to your WordPress admin page or plugin page.
1. Modify any WordPress config in `mg-custom-supreme.php`. Rename files/methods/strings as necessary.
1. Activate the plugin in wp-admin
1. `cd` into your checked out folder and run `yarn`
1. Run `yarn start` to get Webpack and BrowserSync running

To build for production run:

`yarn build`

A production-ready WordPress plugin will be built in the `wp-react-boilerplate-built` folder.
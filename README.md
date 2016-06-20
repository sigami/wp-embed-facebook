# WP Embed Facebook #

This is the development version of the [WP Embed Facebook](https://wordpress.org/plugins/wp-embed-facebook/) plugin, main files are sync with svn/trunk.

If you found a bug or wish to add an extra features, this is the place just make a pull request to the master branch.

## Grunt ##

Run `npm install` then `grunt`

The *default* task will create css, js and pot files.

To automatically copy your changes to a test folder first create a **variables.json** file like this:

``````json
{
  "test_dir" : "/full-path/to/localhost/wp-content/plugins/wp-embed-facebook/"
}
``````

Then use `grunt dev` now whenever you change a file it will be automatically copied to your localhost.


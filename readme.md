# WP Embed Facebook #

This is the official development version of the [WP Embed Facebook](https://wordpress.org/plugins/wp-embed-facebook/) plugin.

If you found a bug or wish to add extra features, this is the place.

## Roadmap ##

The idea is to make a fully functional free plugin, with no limitations for the user.

Future things:

- Login button 
- Avatar replace
- Auto publish new post_type on facebook "draft_to_publish"

The premium plugin focuses on extending native facebook features.

## Grunt ##

This is not necessary but it will save you a lot of time if you lear how to use it.

Run `npm install` then `grunt`

The *default* task will create css, js and pot files.

To automatically copy your changes to a test folder first create a **variables.json** file like this:

``````json
{
    "test_dir" : "/full-path/to/localhost/wp-content/plugins/wp-embed-facebook/"
}
``````

Then use 

`grunt dev` 

now whenever you change a file it will be automatically copied to your localhost.


# Shortcode parser for Gather Content

You can use shortcodes in Gather Content [http://www.gathercontent.com](http://www.gathercontent.com) to create interacive elements such as accordions and tabs. You can then use this parser to convert the shortcodes into HTML.

Shortcode parser is based on Wordpress Shortcodes API. More information on shortcodes : [http://codex.wordpress.org/Shortcode_API](http://codex.wordpress.org/Shortcode_API)


## What it does

1. Download all content from Gather Content and saves them as JSON file
2. Prepare and clean the html
    1. Cleans the html by removing any tags that enclose shortcodes: [http://regex101.com/r/bP2aY2](http://regex101.com/r/bP2aY2)
    2. Adds `table table--bordered` classes to all `<table>` markup
    3. Removes any inline styles
    4. Replace malformed quotes `“` to `"`
3. Parse shortcodes using `do_shortcode` Wordpress Shortcodes API

## How to use it

In terminal (or browser with a php server)
    
```
php refresh.php; /* Downloads content from GC */

php parse.php; /* Does what it says */

/* Only required if you need jade templates */

html2jade --bodyless --donotencode -t pages/html/body/*.html -o pages/jade/body/
```


### Configuration

Open `config.php` to change Gather Content configuration

1. Add `shortcode` markup such as accordion or tabs in your Gather Content pages

    Example

    ```
    Hello. I am a paragraph.

    I need a tab here.

    [tabList]
    [tab title="Title 1"] Content goes here [/tab]
    [tab title="Title 2"] Content goes here [/tab]
    [/tabList]

    ```

2. Define your shortcodes in `shortcodes.mapping.php`, just like you do in wordpress
3. The parser will convert the shortcodes based on your mappings.


### License

MIT
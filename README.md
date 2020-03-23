# AnyComment 
[![Build Status](https://travis-ci.org/bologer/anycomment.io.svg?branch=master)](https://travis-ci.org/bologer/anycomment.io)

AnyComment is blazing-fast commenting plugin for WordPress based on React.

It stands for simplicity & speed. We value feedback, so open new [issue](https://github.com/bologer/anycomment.io/issues) if you have any failures.

Feel free to use [this demo page](https://anycomment.io/demo/).

# Installation 
In order to start, you need:

* Install plugin
* Choose social networks you prefer
* Configure social networks you need (we have guides English & Russian to help you with this)
* You are good to go!

# Coding Standard
AnyComment source conforms to [PSR2](https://www.php-fig.org/psr/psr-2/).

# Resources
* [Official website](http://anycomment.io/en/)
* [All guides](https://anycomment.io/en/category/tutorials/)
* [Configure socials](https://anycomment.io/en/category/tutorials/socials/)
* [VK.com group](http://vk.com/anycomment)
* [Telegram group](https://t.me/anycomment)


## Development 

- Clone project to `wp-content/plugins/anycomment` folder of your docker or local WordPress directory
- Go to cloned directory
- Set `ANYCOMMENT_DEBUG` to `true` in `anycomment.php`
- Change directory to `reactjs`, run `npm install` to install required packages
- Run `npm start` to start development server
- Open WordPress admin panel and activate AnyComment in the list
- Go to "Generic" settings tab & enable "Enable Comments" option
- Open some page and you should see AnyComment's comments

## API

By default plugin starts with the following script: 

```html
<div id="anycomment-root"></div>
<script type="text/javascript">
    AnyComment = window.AnyComment || [];
    AnyComment.WP = [];
    AnyComment.WP.push({
        root: 'anycomment-root',
    });
</script>
```

This renders root element with id and passed it as `root` to `AnyComment.WP`, later main script kick in and processing each item in this array. 


Available properties: 

| Property  |  Description  | Required  |
|---|---|---|
| root  | ID of the element to mount comments widget.  | yes |
| events  | List of events associated with plugin. For full reference see details below. | no  |


You may override default script on the page using `anycomment/client/embed-native-script` filter: 

```
add_filter( 'anycomment/client/embed-native-script', 'anycomment_override_native_script', 11, 1 );

function anycomment_override_native_script( WP_Post $post ) {
	return <<<HTML
<script type="text/javascript">
    AnyComment = window.AnyComment || [];
    AnyComment.WP = [];
    AnyComment.WP.push({
        root: 'anycomment-root',
        events: {
            init: function() {
                console.log('Some when comments loaded');
            }
        }
    });
</script>
HTML;
}
```

As you can see, we added `events` to it, so we can have our own logic using plugin events.


### Events


| Event | Description |
|-----|---|
| init | Triggered once plugin was loaded.  |

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
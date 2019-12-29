### anycomment/admin/tabs
Filters list of available tabs.
```php
apply_filters("anycomment/admin/tabs", array $tabs)
```
#### Arguments
* `$tabs` (_array_) An array of available tabs.
### anycomment/admin/tour-steps
Filters Intro.js steps, with ability to add custom steps.
```php
apply_filters("anycomment/admin/tour-steps", array $tabs)
```
#### Arguments
* `$tabs` (_array_) An array of available tabs. This is supposed to be array in the following form: <code>php $steps['your-key'][] = ['element' =&gt; '#some-id-to-find', 'intro' =&gt; 'Some text']; </code>
### anycomment/admin/addons
Filters addon list.
```php
apply_filters("anycomment/admin/addons", array $addons)
```
#### Arguments
* `$addons` (_array_) List of available addons.

=== Custom Field Snippet ===
Contributors: ounziw
Donate link: http://pledgie.com/campaigns/8706
Tags: custom field, snippet, theme
Requires at least: 3.7
Tested up to: 3.9.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Creates Snippets like "echo get_post_meta($post->ID,'FIELD NAME',true);

== Description ==

This plugin creates and shows the snippets which display your custom field data. You can display your custom field data, by pasting these codes to your theme.

This plugin saves the time for theme developers/designers writing codes.

When you are using ACF, tab fields for ACF is displayed. The default tab is hidden.
If you need the default tab while you are using ACF, please add this code below into your theme's functions.php

// start from here
add_action( 'init', 'register_default_tab');
function register_default_tab() {
  register_cfs_tabs('Defaulttab');
  }
// end

Extension plugin is available from <a href="http://wp.php-web.net/?p=275">http://wp.php-web.net/?p=275</a>
This plugin supports Advanced Custom Fields Repeater addon and Advanced Custom Fields Flexible addon.

It may take times to support ACF5, since ACF5 changes dramatically. If you want quick update to support ACF5, you can pay for me http://pledgie.com/campaigns/8706

== Installation ==

1. Upload `custom-field-snippet.php` to the `/wp-content/plugins/` directory

== Frequently asked questions ==

= Do I need Advanced Custom Fields plugin? =
Not necessary. You can enjoy this plugin with WordPress default custom fields.

== Screenshots ==

1. snippet for custom field
1. when you use Advanced Custom Field

== Changelog ==

4.2 ACFnoESC moves to the extention plugin

4.1 supports repeater/flexible conditional

4.0 You can use this plugin for any post_type.

3.9 remove option when uninstall this plugin 

3.8 bug fix for cfs_add_conditional

3.7 bug fix for acfnoesc

3.6 supports repeater & flexible content fields. (one level only.)

3.5 supports the_field (Caution: you have to care escaping by yourself.)

3.4 bug fix. supports multiple choice field for conditional logic

3.3 bug fix. jquery ui tab support, when you do not use Advanced Custom Fields.

3.2 Refactoring. add get_metadata() method, which returns an array of postmeta key/values.

3.1 bug fix. Show only snippets that match the post/page.

3.0 upport for Advanced Custom Fields 4.0

2.1.1 support for Advanced Custom Fields 3.5.7

2.1 add filter: cfs_tabs_class

2.0 JQuery UI tab. Supports user defined tab.

1.2 internationalization ready

1.1 new feature: Advanced Custom Fields repeater field

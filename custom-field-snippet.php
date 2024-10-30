<?php 
/*
Plugin Name: Custom Field Snippet
Plugin URI: http://wp.php-web.net/?p=275
Description: This plugin creates and shows the snippets which display your custom field data. You can display your custom field data, by pasting these codes to your theme.
Version: 4.2
Author: Fumito MIZUNO
Author URI: http://wp.php-web.net/
License: GPL ver.2 or later
 */

load_plugin_textdomain('custom-field-snippet', false, dirname(plugin_basename(__FILE__)).'/lang' );
$GLOBALS['cfs_tabs'] = array();

include('inc/class.php');
/*
    // You can add new tabs, by declaring this.
    // CLASS must be a subclass of Tabdata.
    if (function_exists('register_cfs_tabs')) {
    register_cfs_tabs('CLASS NAME HERE');
    }	
 */
function register_cfs_tabs($class)
{
    $GLOBALS['cfs_tabs'][$class] = $class;
}


function cfs_meta_box($post) {
    if (class_exists('Acf')) {
        register_cfs_tabs('AcfSimple');
        register_cfs_tabs('Acfshortcode');
    } else {
        register_cfs_tabs('Defaulttab');
    }
?>
<div id="customfieldsnippet">
    <script>
    jQuery("document").ready(function() {
        jQuery( "#tabs" ).tabs();
    });
    </script>
    <div id="tabs">
        <ul>
<?php 
        // you can modify the output array of register_cfs_tabs.
        $cfs_tabs_class = apply_filters('cfs_tabs_class',$GLOBALS['cfs_tabs']);
        foreach($cfs_tabs_class as $class) {
            $obj = new $class();
            $cfs_tabs_obj[] = $obj;
        }
        foreach($cfs_tabs_obj as $obj) {
            print '    <li><a href="#tabs'. esc_attr($obj->getname()) .'" class="nav-tab" style="float:left;">' . esc_html($obj->getlabel()) .'</a></li>';
            print '    </li>';
        } 
?>
        </ul>
<?php 
        foreach($cfs_tabs_obj as $obj) {
            print '    <div id="tabs'. esc_attr($obj->getname()) .'">';
            print PHP_EOL;
            $tab_format = '    <textarea readonly style="min-height:200px;width:100%%;">%s</textarea>';
            $data = esc_html($obj->getdata());
            printf(apply_filters('cfs_tab_format',$tab_format),$data);
            print "<hr>";
            print PHP_EOL;
            _e('Please save the post before you paste these codes.','custom-field-snippet');
            print '    </div>';
        } 
?>
    </div>
</div>
<?php
}


function cfs_custom_box() {
    wp_enqueue_script('jquery-ui-tabs');
    $post_types = get_post_types( array( 'public' => true ) );
    foreach ($post_types as $post_type) {
        add_meta_box('customfieldsnippet', __('Custom Field Snippet'), 'cfs_meta_box', $post_type, 'normal', 'default'); 
    }
}

add_action( 'add_meta_boxes', 'cfs_custom_box' );

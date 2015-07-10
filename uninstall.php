<?php

if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

$plugin_name = 'spot-im';
 
delete_option($plugin_name.'-owner');
delete_option($plugin_name.'-step');
delete_option($plugin_name.'-spot');
delete_option($plugin_name.'-export');
delete_option($plugin_name.'-comment-count');
 

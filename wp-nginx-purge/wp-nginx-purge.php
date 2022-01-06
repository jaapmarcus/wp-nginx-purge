<?php
/**
 * Plugin Name: wp-nginx-purge
 * Plugin URI:  https://eris.nu
 * Description: Allow the use of fastcgi_purge or proxy_purge in combination with Nginx + Wordpress 
 * Version:     0.0.1
 * Author:      Eris
 * Author URI:  https://eris.nu
 * License:     GPLv2 or later
 * License URI: ''
 * Text Domain: wp-nginx-purge
 * Domain Path: /languages
 * Requires at least: 5.8
 * Tested up to: 5.8
 * Requires PHP: 7.4.0
*/

class Purge {
    
    const NAME = 'wp-nginx-purge';
    # clear full cache
    private $events_all = array(
        'transition_post_status',
        'transition_comment_status',
        'wp_update_nav_menu',
        'switch_theme',
    );
    # clear only limited cache (homepage, post page and categories)
    private $events_single = array(
        'save_post',
        'post_updated',
        'deleted_post',
        'trashed_post',
        'wp_trash_post',
        'add_attachment',
        'edit_attachment',
        'attachment_updated',
        'publish_phone',
        'clean_post_cache',
        'pingback_post',
        'comment_post',
        'edit_comment',
        'delete_comment',
        'wp_insert_comment',
        'wp_set_comment_status',
        'trackback_post',
    );
    
    private $urls = array();
    
    
    function __construct(){
        add_action('init', array( $this, 'init' ));
        add_action('shutdown', array($this, 'purge'));
    }
    
    function init(){
        foreach ( $this -> events_single as $event ){
            add_action($event, array( $this, 'single_post' ));
        }
        foreach ( $this -> events_all as $event ){
            add_action($event, array( $this, 'purge_everthing' ));
        }
        add_action('shutdown', array($this, 'purge'));
    }
    
    function purge(){
        foreach($this -> urls as $url){
            $this -> purge_url($url);
        }
    }
    
    function single_post($id){
        $this -> _home_page();
        $this -> _blog_post($id);
        $this -> _category($id);
        $this -> _tags($id);
    } 
    
    function purge_everthing(){
    } 
        
    function purge_url($url){
        file_put_contents(plugin_dir_path(__FILE__).'log.log', '['.date('Y-m-d H:i:s').']'.$url."\r\n", FILE_APPEND);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PURGE");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        $result = curl_exec($curl);
        var_dump($result);
    }
    
    function _home_page(){
        if ( function_exists( 'icl_get_home_url' ) ) {
            $homepage_url = trailingslashit( icl_get_home_url() );
        } else {
            $homepage_url = trailingslashit( home_url() );
        }
        $this -> mark_purge($homepage_url);
    }
    
    function _blog_post($post){
        if(is_integer($post)){
            $this -> mark_purge(get_permalink($post));
            $this -> mark_purge(get_permalink($post).'*');
        }else{
            $this -> mark_purge(get_permalink($post -> id));
            $this -> mark_purge(get_permalink($post -> id).'*');          
        }
    }
    
    function _category($post){
        if(is_integer($post)){
            $categories = wp_get_post_categories( $post );
        }else{
            $categories = wp_get_post_categories( $post -> id );
        }
        
        foreach($categories as $category){
            $this -> mark_purge(get_category_link($category));
        }
    } 
    
    function _tags($post){
        if(is_integer($post)){
            $categories = get_the_tags( $post );
        }else{
            $categories = get_the_tags( $post -> id );
        }
        
        foreach($categories as $category){
            $this -> mark_purge(get_the_tags($category));
        }
    }
    
    function mark_purge($url){
        if (!in_array($url, $this -> urls )) {
            $this -> urls[] = $url;
        } 
    }
    
    
}

$purge = new Purge();
<?php

/*
  Plugin Name: Feed Filter
  Plugin URI: http://www.satollo.net/plugins/feed-filter
  Description: Insert ads before and after a post different for each blog author
  Version: 1.0.0
  Author: Satollo
  Author URI: http://www.satollo.net
  Disclaimer: Use at your own risk. No warranty expressed or implied is provided.

  Copyright 2010 Stefano Lissa - http://www.satollo.net - info@satollo.net
 */

$feed_filter = new FeedFilter();

class FeedFilter {

    var $name = 'Feed Filter';
    var $dir = null;
    var $postfix = '';

    function __construct() {
        $this->dir = basename(dirname(__FILE__));

        register_activation_hook(__FILE__, array(&$this, 'hook_activate'));
        register_deactivation_hook(__FILE__, array(&$this, 'hook_deactivate'));

        add_action('init', array(&$this, 'hook_init'));
        add_action('template_redirect', array(&$this, 'hook_template_redirect'));
        add_action('the_excerpt_rss', array(&$this, 'hook_the_excerpt_rss'), 99);
        add_action('the_title_rss', array(&$this, 'hook_the_title_rss'), 99);
        add_action('admin_menu', array(&$this, 'hook_admin_menu'));
    }

    function hook_the_excerpt_rss($excerpt) {
        global $post;

        $image = '';
        $attachments = get_children(array('post_parent' => $post->ID, 'post_status' => 'inherit',
                    'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC',
                    'orderby' => 'menu_order ID'));

        if (!empty($attachments)) {

            foreach ($attachments as $id => $attachment) {
                $images = wp_get_attachment_image_src($id, 'thumbnail');
                $image = $images[0];
            }
        }

        ob_start();
        eval($this->get_option('excerpt_php' . $this->postfix) . "\n");
        ob_end_clean();

        $new_excerpt = str_replace('{excerpt}', $excerpt, $this->get_option('excerpt' . $this->postfix));
        if (!empty($image)) $new_excerpt = str_replace('{image}', '<img src="' . $image . '"/>', $new_excerpt);
        else $new_excerpt = str_replace('{image}', '', $new_excerpt);

        return $new_excerpt;
    }

    function hook_the_title_rss($title) {

        ob_start();
        eval($this->get_option('title_php' . $this->postfix) . "\n");
        ob_end_clean();

        $new_title = str_replace('{title}', $title, $this->get_option('title' . $this->postfix));
        return $new_title;
    }

    function is_good_ip() {

    }

    function hook_init() {
        if (in_array($_SERVER['REMOTE_ADDR'], $this->get_option('ip_bad')))
            $this->postfix = '_bad';
        if (in_array($_SERVER['REMOTE_ADDR'], $this->get_option('ip_good')))
            $this->postfix = '_good';

        add_filter('posts_where', array(&$this, 'hook_posts_where'));
    }

    function hook_posts_where($where = '') {
        if (!is_feed()) return $where;
        $delay = $this->get_option('delay' . $this->postfix, 0);
        //$where .= " AND post_date <= '" . date('Y-m-d', mktime(0, 0, 0, date("m")  , date("d")-$delay, date("Y"))) . " 23:59:59'";
        $where .= " AND post_date <= '" . date('Y-m-d H:i:s', mktime(date('H'), date('i'), 0, date('m'), date('j')-$delay, date('Y'))) . "'";

        return $where;
    }

    function hook_template_redirect() {
        if (!is_feed())
            return;

        file_put_contents(dirname(__FILE__) . '/referrers.txt',
                date('Y-m-d H:i:s') . ' - ' . $_SERVER['REMOTE_ADDR'] . ' - ' .
                $_SERVER['HTTP_USER_AGENT'] . ' - ' . $_SERVER['REQUEST_URI'] . "\n",
                FILE_APPEND);
    }

    function hook_admin_menu() {
        add_options_page($this->name, $this->name, 'manage_options', $this->dir . '/options.php');
    }

    /** Returns an array with default options for this plugin. Here you can modify the
     * code to return localized defaults.
     */
    function get_default_options() {
        return array('title' => '{title}', 'excerpt' => '{excerpt}', 'delay'=>0,
            'title_bad' => '{title}', 'excerpt_bad' => '{excerpt}', 'delay_bad'=>2, 'ip_bad'=>array(),
            'title_good' => '{title}', 'excerpt_good' => '{excerpt}', 'delay_good'=>0, 'ip_good'=>array());
    }

    /** Returns the options array associated with this plugin or an empty array if
     * options are not already stored on WordPress database.
     */
    function get_options() {
        return get_option($this->dir, array());
    }

    /**
     * Updates the options saving them on database.
     */
    function update_options($options) {
        update_option($this->dir, $options);
    }

    /** Returns a plugin option by name, falling back to plugin default option value, if one, or returning the default value. */
    function get_option($name, $default=null) {
        $options = $this->get_options();
        if (!isset($options[$name]))
            return $default;
        return $options[$name];
    }

    /** Activate the plugin updating the actual stored options with the default options, so new
     * entries get a default value.
     */
    function hook_activate() {
        $this->update_options(array_merge($this->get_default_options(), $this->get_options()));
    }

    function hook_deactivate() {

    }

    function trim($text, $len=200, $suspend='...') {
        if (strlen($text) < $len)
            return $text;
        $x = strrpos(substr($text, 0, $len), ' ');
        if ($x === false)
            return $text;
        return substr($text, 0, $x) . $suspend;
    }

}


<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/*
Comments in WordPress are like an open mic.
Sometimes you want to turn the mic off.
Silencing the comment section might just be your ticket to serenity.*/

function codesnippets_disable_comments() {
    // Remove comment support for posts
    remove_post_type_support( 'post', 'comments' );

    // Remove comment support for pages
    remove_post_type_support( 'page', 'comments' );

    // Remove comments from the admin menu
    remove_menu_page( 'edit-comments.php' );

    // Redirect comment-related URLs to the homepage
    add_action( 'template_redirect', 'codesnippets_disable_comments_redirect' );
}

function codesnippets_disable_comments_redirect() {
    global $wp_query;
    if ( is_single() || is_page() || is_attachment() ) {
        if ( have_comments() || comments_open() ) {
            wp_redirect( home_url(), 301 );
            exit;
        }
    }
}

add_action( 'admin_init', 'codesnippets_disable_comments' );

/**
 * We will Dequeue the jQuery UI script as example.
 *
 * Hooked to the wp_print_scripts action, with a late priority (99),
 * so that it is after the script was enqueued.
 */
function wp_remove_scripts() {
    // check if user is admin
     if (current_user_can( 'update_core' )) {
                return;
            }
     else {
        // Check for the page you want to target
        if ( is_page( 'homepage' ) ) {
            // Remove Scripts
      wp_dequeue_style( 'jquery-ui-core' );
         }
     }
    }
    add_action( 'wp_enqueue_scripts', 'wp_remove_scripts', 99 );
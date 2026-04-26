<!doctype html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body id="body" <?php body_class(); ?>>
    <?php wp_body_open(); ?>
    <!--Start site wrap -->
    <div id="site-wrap" class="site-wrap">
        <?php do_action('flip_hook_header'); ?>
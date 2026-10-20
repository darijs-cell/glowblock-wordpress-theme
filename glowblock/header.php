<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#content">Skip to content</a>

<div class="page-glow" aria-hidden="true"></div>

<header class="site-header">
    <div class="container header-inner">
        <a class="brand" href="<?php echo esc_url(home_url('/')); ?>">
            <span class="brand-cube" aria-hidden="true"></span>
            <span class="brand-text">Glow<b>Block</b></span>
        </a>

        <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="primary-nav" data-nav-toggle>
            <span class="screen-reader-text">Menu</span>
            <span class="burger"></span>
        </button>

        <nav class="primary-nav" id="primary-nav" aria-label="Primary">
            <?php
            wp_nav_menu([
                'theme_location' => 'primary',
                'container'      => false,
                'menu_class'     => 'menu',
                'fallback_cb'    => 'glowblock_fallback_menu',
                'depth'          => 1,
            ]);
            ?>
        </nav>

        <a class="cart-link" href="<?php echo esc_url(get_post_type_archive_link('lamp')); ?>" aria-label="Cart (demo)">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/><path d="M2 3h3l2.6 11.4a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 2-1.5L21 7H6"/></svg>
            <span class="cart-count" data-cart-count>0</span>
        </a>
    </div>
</header>

<main id="content">

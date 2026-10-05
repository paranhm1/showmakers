<?php
/** Phase 2: approved static presentation; no CMS records imported. */
defined( 'ABSPATH' ) || exit;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="theme-color" content="#F9E64F">
<?php wp_head(); ?>
</head>
<body <?php body_class( 'page-' . showmakers_page_key() ); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main">Skip to content</a><div class="header-surface"><header class="site-header shell"><a class="brand brand-dark" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="ShowMakers home"><img src="<?php echo esc_url( showmakers_asset_url( 'images/showmakers-logo.webp' ) ); ?>" width="838" height="342" alt="ShowMakers"></a><button hidden class="menu-toggle" aria-expanded="false" aria-controls="primary-navigation">Menu <span aria-hidden="true">+</span></button><nav id="primary-navigation" aria-label="Primary navigation"><a href="<?php echo esc_url( home_url( '/work/' ) ); ?>" data-event="nav_work" <?php if ( is_page( 'work' ) ) echo 'aria-current="page"'; ?> >Work</a><a href="<?php echo esc_url( home_url( '/services/' ) ); ?>" data-event="nav_services" <?php if ( is_page( 'services' ) ) echo 'aria-current="page"'; ?> >Services</a><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>" data-event="nav_about" <?php if ( is_page( 'about' ) ) echo 'aria-current="page"'; ?> >About</a><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" data-event="nav_contact" <?php if ( is_page( 'contact' ) ) echo 'aria-current="page"'; ?> >Contact</a></nav></header></div>
<main id="main">

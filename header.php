<?php
/**
 * @author    Andrea Musso
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	<!--=== GMT head ===-->
	<?php  WPSeed_gtm('head') ?>
    <!--=== gmt end ===-->
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<!--=== GMT body ===-->
<?php WPSeed_gtm('body') ?>
<!--=== gmt end ===-->

<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'foundry' ); ?></a>
	<header id="masthead" class="site-header">
		<div class="container">
			<nav id="site-navigation" class="navbar navbar-expand-lg navbar-light">
				<div class="site-branding navbar-brand">
					<a href="<?= get_bloginfo('url'); ?>">
          				<div class="logo"></div>
       				 </a>
				</div><!-- .site-branding -->
				
				<!-- DESKTOP NAV -->
				<?php
					if ( has_nav_menu( 'mainmenu' ) ) :
					wp_nav_menu([
						'menu_class'=> 'hidden_mobile',
						'menu_id' => 'menu_main',
						'container'=> false,
						'depth' => 1,
						'theme_location' => 'mainmenu'
					]);
					endif;
				?>

				 <!-- MOBILE NAV (BURGER) -->
				 <button class="hamburger hamburger--elastic " id="hamburger" type="button">
					<span class="hamburger-box">
						<span class="hamburger-inner"></span>
					</span>
				</button>



			</nav><!-- #site-navigation -->
		</div><!-- #container -->
	</header><!-- #masthead -->


	<div id="content" class="site-content">

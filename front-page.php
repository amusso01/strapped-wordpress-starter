<?php
/**
 * The template for displaying frontpage by default
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package foundry
 */

get_header();
?>

<div id="primary" class="container content-area" >
	<main id="main" class="site-main">
	<?php
	while ( have_posts() ) :
		the_post();

		get_template_part( 'template-parts/content', 'front' );

	endwhile; // End of the loop.
	?>

	</main><!-- #main -->
	<div class="btn__wrapper">
			<a href="" class="btn"></a>
	</div>

</div><!-- #primary -->


<?php
get_footer();

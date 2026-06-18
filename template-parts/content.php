<?php
/**
 * Template part for displaying posts
 *
 * @author Andrea Musso
 *
 * @package foundry
 */

?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php
	the_content();
	?>

</div><!-- #post-<?php the_ID(); ?> -->

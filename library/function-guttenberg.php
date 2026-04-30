<?php

/*==================================================================================
Register color paette for guttenberg
==================================================================================*/
function ea_setup()
{
	// Disable Custom Colors
	add_theme_support('disable-custom-colors');

	// Editor Color Palette
	add_theme_support('editor-color-palette', array(
		array(
			'name'  => __('Blue', 'ea-starter'),
			'slug'  => 'blue',
			'color'	=> '#165F97',
		),
		array(
			'name'  => __('Orange', 'ea-starter'),
			'slug'  => 'orange',
			'color' => '#F7931C',
		),
	));
}
add_action('after_setup_theme', 'ea_setup');

/*==================================================================================
Allow certain block on Guttenberg
==================================================================================*/

/* function misha_allowed_block_types( $allowed_blocks ) {
 
	return array(
		'acf/fd-greybgtext',
		'acf/fd-sharetitle',
		'acf/fd-bluebg',
		'acf/fd-button',
		'acf/fd-teamcard',
		'acf/fd-imagetext',
		'acf/mediatextareablock',
		'core/image',
		'core/separator',
		'core/spacer',
		'core/paragraph',
		'core/heading',
		'core/list'
	);
 
}

add_filter( 'allowed_block_types', 'misha_allowed_block_types' );*/


/*==================================================================================
Wrap certain blocks with a div (narrow-width / content-block)
==================================================================================*/
function fdry_block_wrapper($block_content, $block)
{
	$block_name = $block['blockName'] ?? '';

	$wrappers = array(
		'core/paragraph'  => 'wp-block-paragraph  content-block',
		'core/heading'    => 'wp-block-title  content-block',
		'core/image'      => 'wp-block-image  content-block',
		'core/separator'  => 'wp-block-separator  content-block',
		'core/list'       => 'wp-block-list  content-block',
		'core/embed'      => 'wp-block-embed__wrapper content-block',
		'core/group'      => 'wp-block-group fd-narrow-width',
	);

	if (!empty($wrappers[$block_name])) {
		return '<div class="' . esc_attr($wrappers[$block_name]) . '">' . $block_content . '</div>';
	}

	return $block_content;
}
add_filter('render_block', 'fdry_block_wrapper', 10, 2);

/*==================================================================================
Register back-end CSS editor
==================================================================================*/
add_theme_support('editor-styles');
add_editor_style('./dist/styles/root.css');
add_editor_style('./dist/styles/main.css');
add_editor_style('./static/editor.css');
/*==================================================================================
Register new category in guttenberg block
==================================================================================*/

function my_foundry_category($categories, $post)
{
	return array_merge(
		$categories,
		array(
			array(
				'slug' => 'foundry-category',
				'title' => __('Foundry', 'foundry'),
			),
		)
	);
}
add_filter('block_categories', 'my_foundry_category', 10, 2);

/*==================================================================================
LOAD CUSTOM ACF-GUTENBERG-BLOCKS
==================================================================================*/

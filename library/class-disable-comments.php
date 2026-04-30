<?php

/**
 * Site-wide comment shutdown (ported from a “disable comments” plugin for use in the theme).
 *
 * Bootstrapped from functions.php via Fdry_Disable_Comments::init().
 *
 * Optional DB purge when switching to this theme (default: off, so comments are not deleted):
 * add_filter( 'fdry_disable_comments_purge_on_theme_switch', '__return_true' );
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Static helpers only; no instance state. Call init() once from the theme.
 *
 * The class is `final` so PHP forbids extending it (`class Foo extends Fdry_Disable_Comments` errors).
 * That signals “not an extension point” and avoids fragile overrides. Drop `final` if you ever
 * need a child class.
 */
final class Fdry_Disable_Comments
{
	/** Registers every hook and filter for disabling comments. */
	public static function init(): void
	{
		add_action('after_switch_theme', [self::class, 'maybe_purge_comments']);
		add_action('admin_init', [self::class, 'disable_post_type_support']);
		add_action('admin_menu', [self::class, 'remove_admin_menus']);
		add_action('admin_init', [self::class, 'block_admin_comments_screen']);
		add_action('admin_init', [self::class, 'remove_dashboard_meta']);
		add_action('wp_before_admin_bar_render', [self::class, 'remove_admin_bar_comments']);
		add_action('pre_comment_on_post', [self::class, 'block_comment_submission']);

		add_filter('comments_open', [self::class, 'comments_closed'], 20, 2);
		add_filter('pings_open', [self::class, 'comments_closed'], 20, 2);
		add_filter('xmlrpc_methods', [self::class, 'disable_xmlrpc_comment']);
		add_filter('rest_endpoints', [self::class, 'disable_rest_comments']);
	}

	/**
	 * Deletes all comments and comment meta from the database.
	 */
	public static function remove_all_comments(): void
	{
		global $wpdb;
		$wpdb->query("DELETE FROM {$wpdb->comments}");
		$wpdb->query("DELETE FROM {$wpdb->commentmeta}");
	}

	/**
	 * Runs on after_switch_theme when the fdry_disable_comments_purge_on_theme_switch filter is true.
	 */
	public static function maybe_purge_comments(): void
	{
		if (apply_filters('fdry_disable_comments_purge_on_theme_switch', false)) {
			self::remove_all_comments();
		}
	}

	/** Strips comments + trackbacks support from every registered post type. */
	public static function disable_post_type_support(): void
	{
		foreach (get_post_types() as $post_type) {
			remove_post_type_support($post_type, 'comments');
			remove_post_type_support($post_type, 'trackbacks');
		}
	}

	/** Forces comments_open and pings_open to false on the front end. */
	public static function comments_closed(): bool
	{
		return false;
	}

	/** Hides the Comments admin menu. */
	public static function remove_admin_menus(): void
	{
		remove_menu_page('edit-comments.php');
	}

	/** Sends users away from edit-comments.php if they hit it directly. */
	public static function block_admin_comments_screen(): void
	{
		global $pagenow;
		if ($pagenow === 'edit-comments.php') {
			wp_safe_redirect(admin_url());
			exit;
		}
	}

	/** Removes the “Recent Comments” dashboard widget. */
	public static function remove_dashboard_meta(): void
	{
		remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
	}

	/**
	 * Disables wp.newComment via XML-RPC.
	 *
	 * @param array<string, mixed> $methods
	 * @return array<string, mixed>
	 */
	public static function disable_xmlrpc_comment(array $methods): array
	{
		$methods['wp.newComment'] = '__return_false';

		return $methods;
	}

	/**
	 * Unregisters the /wp/v2/comments REST route collection.
	 *
	 * @param array<string, mixed> $endpoints
	 * @return array<string, mixed>
	 */
	public static function disable_rest_comments(array $endpoints): array
	{
		unset($endpoints['/wp/v2/comments']);

		return $endpoints;
	}

	/** Removes the Comments node from the admin bar. */
	public static function remove_admin_bar_comments(): void
	{
		global $wp_admin_bar;
		if ($wp_admin_bar instanceof \WP_Admin_Bar) {
			$wp_admin_bar->remove_menu('comments');
		}
	}

	/** Blocks direct POST comment submissions with 403. */
	public static function block_comment_submission(): void
	{
		if (
			isset($_SERVER['REQUEST_METHOD'])
			&& $_SERVER['REQUEST_METHOD'] === 'POST'
			&& !empty($_POST['comment_post_ID'])
		) {
			wp_die(esc_html__('Comments are disabled on this site.', 'foundry'), '', ['response' => 403]);
		}
	}
}

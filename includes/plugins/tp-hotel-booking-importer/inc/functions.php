<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-04-25 15:01:39
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-04-25 15:06:20
 */

/**
 * Wrap given string in XML CDATA tag.
 *
 * @since 2.1.0
 *
 * @param string $str String to wrap in XML CDATA tag.
 * @return string
 */
function hbip_cdata( $str ) {
	if ( ! seems_utf8( $str ) ) {
		$str = utf8_encode( $str );
	}
	// $str = ent2ncr(esc_html($str));
	$str = '<![CDATA[' . str_replace( ']]>', ']]]]><![CDATA[>', $str ) . ']]>';

	return $str;
}

/**
 * Return the URL of the site
 *
 * @since 2.5.0
 *
 * @return string Site URL.
 */
function hbip_site_url() {
	// Multisite: the base URL.
	if ( is_multisite() )
		return network_home_url();
	// WordPress (single site): the blog URL.
	else
		return get_bloginfo_rss( 'url' );
}

/**
 * Output a cat_name XML tag from a given category object
 *
 * @since 2.1.0
 *
 * @param object $category Category Object
 */
function hbip_cat_name( $category ) {
	if ( empty( $category->name ) )
		return;

	echo '<wp:cat_name>' . hbip_cdata( $category->name ) . '</wp:cat_name>';
}

/**
 * Output a category_description XML tag from a given category object
 *
 * @since 2.1.0
 *
 * @param object $category Category Object
 */
function hbip_category_description( $category ) {
	if ( empty( $category->description ) )
		return;

	echo '<wp:category_description>' . hbip_cdata( $category->description ) . '</wp:category_description>';
}

/**
 * Output a tag_name XML tag from a given tag object
 *
 * @since 2.3.0
 *
 * @param object $tag Tag Object
 */
function hbip_tag_name( $tag ) {
	if ( empty( $tag->name ) )
		return;

	echo '<wp:tag_name>' . hbip_cdata( $tag->name ) . '</wp:tag_name>';
}

/**
 * Output a tag_description XML tag from a given tag object
 *
 * @since 2.3.0
 *
 * @param object $tag Tag Object
 */
function hbip_tag_description( $tag ) {
	if ( empty( $tag->description ) )
		return;

	echo '<wp:tag_description>' . hbip_cdata( $tag->description ) . '</wp:tag_description>';
}

/**
 * Output a term_name XML tag from a given term object
 *
 * @since 2.9.0
 *
 * @param object $term Term Object
 */
function hbip_term_name( $term ) {
	if ( empty( $term->name ) )
		return;

	echo '<wp:term_name>' . hbip_cdata( $term->name ) . '</wp:term_name>';
}

/**
 * Output a term_description XML tag from a given term object
 *
 * @since 2.9.0
 *
 * @param object $term Term Object
 */
function hbip_term_description( $term ) {
	if ( empty( $term->description ) )
		return;

	echo '<wp:term_description>' . hbip_cdata( $term->description ) . '</wp:term_description>';
}

/**
 * Output list of authors with posts
 *
 * @since 3.1.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param array $post_ids Array of post IDs to filter the query by. Optional.
 */
function hbip_authors_list( array $post_ids = null ) {
	global $wpdb;

	if ( !empty( $post_ids ) ) {
		$post_ids = array_map( 'absint', $post_ids );
		$and = 'AND ID IN ( ' . implode( ', ', $post_ids ) . ')';
	} else {
		$and = '';
	}

	$authors = array();
	$results = $wpdb->get_results( "SELECT DISTINCT post_author FROM $wpdb->posts WHERE post_status != 'auto-draft' $and" );
	foreach ( (array) $results as $result )
		$authors[] = get_userdata( $result->post_author );

	$authors = array_filter( $authors );

	foreach ( $authors as $author ) {
		echo "\t<wp:author>";
		echo '<wp:author_id>' . intval( $author->ID ) . '</wp:author_id>';
		echo '<wp:author_login>' . hbip_cdata( $author->user_login ) . '</wp:author_login>';
		echo '<wp:author_email>' . hbip_cdata( $author->user_email ) . '</wp:author_email>';
		echo '<wp:author_display_name>' . hbip_cdata( $author->display_name ) . '</wp:author_display_name>';
		echo '<wp:author_first_name>' . hbip_cdata( $author->first_name ) . '</wp:author_first_name>';
		echo '<wp:author_last_name>' . hbip_cdata( $author->last_name ) . '</wp:author_last_name>';
		echo "</wp:author>\n";
	}
}

/**
 * Output list of taxonomy terms, in XML tag format, associated with a post
 *
 * @since 2.3.0
 */
function hbip_post_taxonomy() {
	$post = get_post();

	$taxonomies = get_object_taxonomies( $post->post_type );
	if ( empty( $taxonomies ) )
		return;
	$terms = wp_get_object_terms( $post->ID, $taxonomies );

	foreach ( (array) $terms as $term ) {
		echo "\t\t<category domain=\"{$term->taxonomy}\" nicename=\"{$term->slug}\">" . hbip_cdata( $term->name ) . "</category>\n";
	}
}

/**
 *
 * @param bool   $return_me
 * @param string $meta_key
 * @return bool
 */
function hbip_filter_postmeta( $return_me, $meta_key ) {
	if ( '_edit_lock' == $meta_key )
		$return_me = true;
	return $return_me;
}
add_filter( 'hbip_export_skip_postmeta', 'hbip_filter_postmeta', 10, 2 );

echo '<?xml version="1.0" encoding="' . get_bloginfo('charset') . "\" ?>\n";
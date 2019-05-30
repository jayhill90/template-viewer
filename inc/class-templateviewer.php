<?php
/**
 * Template Viewer
 *
 * This file handles pulling page template data if set in the database.
 *
 * @package TemplateViewer
 */

namespace WPEngine\TemplateViewer;

/**
 * Main class for TemplateViewer
 */
class TemplateViewer {

	/**
	 * Constructor for the class
	 */
	public static function init() {
		$self = new self();
		if ( is_admin() ) {
			add_action( 'restrict_manage_posts', [ $self, 'template_restrict_manage_posts' ] );
			add_filter( 'request', [ $self, 'template_request_query' ], 3 );

		}
	}

	/**
	 * Adds dropdown functionality for selecting page templates in edit.phjp
	 *
	 * @return none
	 */
	public function template_restrict_manage_posts() {
		global $wpdb;
		$post_ids = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT ID FROM $wpdb->posts WHERE post_type = %s",
				'page'
			)
		);
		if ( empty( $post_ids ) ) {
			return false;
		}

		$query  = 'SELECT DISTINCT meta_value ';
		$query .= "FROM $wpdb->postmeta ";
		$query .= "WHERE meta_key = '_wp_page_template' ";
		$query .= 'AND post_id IN( ' . implode( ',', $post_ids ) . ') ';
		$query .= 'ORDER BY meta_value+0 ASC ';

		$template_names = $wpdb->get_results( $query );

		if ( empty( $template_names ) ) {
			return false;
		}
		?>
		<label for="filter-by-template" class="screen-reader-text"><?php esc_html_e( 'Template' ); ?></label>
		<select name="template_name" id="filter-by-template">
			<option value=""><?php esc_html_e( 'All Templates' ); ?></option>
			<?php foreach ( $template_names as $template ) : ?>
				<option value="<?php echo esc_html_e( $template->meta_value ); ?>"><?php echo esc_html_e( $template->meta_value ); ?></option>
			<?php endforeach; ?>
		<select>
		<?php
	}

	/**
	 * Filter the template pages on edit.php
	 *
	 * @param array $vars Array for meta values to get the template name if set.
	 * @return array      Array of meta value with the template name.
	 */
	public function template_request_query( $vars ) {
		global $pagenow;
		global $post_type;

		$possible_post_types = [ 'page' ];

		if ( ! empty( $_GET['template_name'] ) ) {
			$meta_value         = strval( $_GET['template_name'] );
			$vars['meta_key']   = '_wp_page_template';
			$vars['meta_value'] = $meta_value;
		}

		return $vars;
	}
}

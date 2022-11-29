<?php
/**
 * File containing Sensei_Home_Quick_Links_Provider class.
 *
 * @package sensei-lms
 * @since   4.8.0
 */

/**
 * Class responsible for generating the Quick Links structure for Sensei Home screen.
 */
class Sensei_Home_Quick_Links_Provider {

	const ACTION_INSTALL_DEMO_COURSE = 'sensei://install-demo-course';

	/**
	 * Return a list of categories which each contain multiple quick link items.
	 *
	 * @return array[]
	 */
	public function get(): array {
		return [
			$this->create_category(
				__( 'Courses', 'sensei-lms' ),
				[
					$this->create_item( __( 'Create a course', 'sensei-lms' ), admin_url( '/post-new.php?post_type=course' ) ),
					$this->create_demo_link(),
					$this->create_item( __( 'Import a course', 'sensei-lms' ), admin_url( '/admin.php?page=sensei-tools&tool=import-content' ) ),
					$this->create_item( __( 'Reports', 'sensei-lms' ), admin_url( '/admin.php?page=sensei_reports' ) ),
				]
			),
			$this->create_category(
				__( 'Settings', 'sensei-lms' ),
				[
					$this->create_item( __( 'Email notifications', 'sensei-lms' ), admin_url( '/admin.php?page=sensei-settings#email-notification-settings' ) ),
					$this->create_item( __( 'Learning mode', 'sensei-lms' ), admin_url( '/admin.php?page=sensei-settings#appearance-settings' ) ),
					$this->create_item( __( 'WooCommerce', 'sensei-lms' ), admin_url( '/admin.php?page=sensei-settings#woocommerce-settings' ) ),
					$this->create_item( __( 'Content drip', 'sensei-lms' ), admin_url( '/admin.php?page=sensei-settings#sensei-content-drip-settings' ) ),
				]
			),
			$this->create_category(
				__( 'Advanced Features', 'sensei-lms' ),
				[
					$this->create_item( __( 'Interactive blocks', 'sensei-lms' ), 'https://senseilms.com/interactive-blocks' ),
					$this->create_item( __( 'Groups & cohorts', 'sensei-lms' ), 'https://senseilms.com/groups-cohorts' ),
					$this->create_item( __( 'Quizzes', 'sensei-lms' ), 'https://senseilms.com/quizzes' ),
					$this->create_item( __( 'Integrations', 'sensei-lms' ), 'https://senseilms.com/sensei-lms-integrations/' ),
				]
			),
		];
	}

	/**
	 * Return the magical link to create a demo course, or the link to edit the demo course.
	 *
	 * @return array The magical link to create a demo course or the link to edit the demo course.
	 */
	private function create_demo_link() {
		global $wpdb;
		$cache_key   = 'home/metadata/demo-course';
		$cache_group = 'sensei/temporary';
		$result      = wp_cache_get( $cache_key, $cache_group );
		if ( false === $result ) {
			$prefix = $wpdb->esc_like( Sensei_Data_Port_Manager::SAMPLE_COURSE_SLUG );
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Safe-ish and rare query.
			$post_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_type='course' AND post_status IN ('publish', 'draft') AND post_name LIKE %s ORDER BY post_status='published' DESC, ID ASC LIMIT 1", "{$prefix}%" ) );
			if ( null === $post_id ) {
				$result = null;
			} else {
				$result = $post_id;
				wp_cache_set( $cache_key, $result, $cache_group, 60 );
			}
		}
		if ( null !== $result ) {
			return $this->create_item( __( 'Edit demo course', 'sensei-lms' ), get_edit_post_link( $result, '&' ) );
		}
		return $this->create_item( __( 'Install a demo course', 'sensei-lms' ), self::ACTION_INSTALL_DEMO_COURSE );
	}

	/**
	 * Create the structure for a Quick Links category.
	 *
	 * @param string $title The category title.
	 * @param array  $items The category items.
	 *
	 * @return array
	 */
	private function create_category( string $title, array $items ): array {
		return [
			'title' => $title,
			'items' => $items,
		];
	}

	/**
	 * Create the structure for a Quick Links item.
	 *
	 * @param string      $title The item title.
	 * @param string|null $url Optional. The item action URL.
	 *
	 * @return array
	 */
	private function create_item( string $title, ?string $url ): array {
		return [
			'title' => $title,
			'url'   => $url,
		];
	}
}

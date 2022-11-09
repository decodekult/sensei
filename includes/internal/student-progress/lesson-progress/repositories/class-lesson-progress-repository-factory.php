<?php
/**
 * File containing the Lesson_Progress_Repository_Factory class.
 *
 * @package sensei
 */

namespace Sensei\Internal\Student_Progress\Lesson_Progress\Repositories;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Lesson_Progress_Repository_Factory.
 *
 * @internal
 *
 * @since $$next-version$$
 */
class Lesson_Progress_Repository_Factory {
	/**
	 * Creates a new lesson progress repository.
	 *
	 * @internal
	 *
	 * @return Lesson_Progress_Repository_Interface The repository.
	 */
	public function create(): Lesson_Progress_Repository_Interface {
		global $wpdb;

		return new Aggregate_Lesson_Progress_Repository(
			new Comments_Based_Lesson_Progress_Repository(),
			new Tables_Based_Lesson_Progress_Repository( $wpdb ),
			true
		);
	}
}

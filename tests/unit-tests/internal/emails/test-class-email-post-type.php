<?php

namespace SenseiTest\Internal\Emails;

use Sensei\Internal\Emails\Email_Post_Type;

/**
 * Tests for the Email_Post_Type class.
 *
 * @covers \Sensei\Internal\Emails\Email_Post_Type
 */
class Email_Post_Type_Test extends \WP_UnitTestCase {

	use \Sensei_Test_Redirect_Helpers;

	public function testRegisterPostType_WhenCalled_RegistersEmailPostType() {
		/* Arrange. */
		$email_post_type = new Email_Post_Type();

		/* Act. */
		$email_post_type->register_post_type();

		/* Assert. */
		$this->assertTrue( post_type_exists( 'sensei_email' ) );
	}

	public function testInit_WhenCalled_AddsInitAction() {
		/* Arrange. */
		$email_post_type = new Email_Post_Type();

		/* Act. */
		$email_post_type->init();

		/* Assert. */
		$priority = has_action( 'init', [ $email_post_type, 'register_post_type' ] );
		$this->assertSame( 10, $priority );
	}

	public function testInit_WhenCalled_AddsLoadEditAction() {
		/* Arrange. */
		$email_post_type = new Email_Post_Type();

		/* Act. */
		$email_post_type->init();

		/* Assert. */
		$priority = has_action( 'load-edit.php', [ $email_post_type, 'maybe_redirect_to_listing' ] );
		$this->assertSame( 10, $priority );
	}

	public function testMaybeRedirectToListing_WhenCalledWithEmailPostType_RedirectsToEmailsPage() {
		/* Arrange. */
		$email_post_type   = new Email_Post_Type();
		$_GET['post_type'] = 'sensei_email';
		$this->prevent_wp_redirect();

		/* Act. */
		$redirect_status   = 0;
		$redirect_location = '';
		try {
			$email_post_type->maybe_redirect_to_listing();
		} catch ( \Sensei_WP_Redirect_Exception $e ) {
			$redirect_status   = $e->getCode();
			$redirect_location = $e->getMessage();
		}

		/* Assert. */
		$this->assertSame( 301, $redirect_status );
		$this->assertSame( admin_url( 'admin.php?page=sensei-settings&tab=email-notification-settings' ), $redirect_location );
	}

	public function testMaybeRedirectToListing_WhenCalledWithNonEmailPostType_RedirectsToEmailsPage() {
		/* Arrange. */
		$email_post_type   = new Email_Post_Type();
		$_GET['post_type'] = 'non_sensei_email';
		$this->prevent_wp_redirect();

		/* Act. */
		$redirect_status   = 0;
		$redirect_location = '';
		try {
			$email_post_type->maybe_redirect_to_listing();
		} catch ( \Sensei_WP_Redirect_Exception $e ) {
			$redirect_status   = $e->getCode();
			$redirect_location = $e->getMessage();
		}

		/* Assert. */
		$this->assertSame( 0, $redirect_status );
		$this->assertSame( '', $redirect_location );
	}
}

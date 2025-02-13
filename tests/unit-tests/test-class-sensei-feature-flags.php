<?php

class Sensei_Class_Feature_Flags_Test extends WP_UnitTestCase {

	/**
	 * Constructor function
	 */
	public function __construct() {
		parent::__construct();
	}

	public function add_mock_flags( $arr ) {
		return array(
			'foo_feature' => false,
		);
	}

	/**
	 * Test functionality
	 */
	public function testFlags() {
		add_filter( 'sensei_default_feature_flag_settings', array( $this, 'add_mock_flags' ) );
		$flags = new Sensei_Feature_Flags();

		$this->assertFalse( $flags->is_enabled( 'foo_feature' ) );

		define( 'SENSEI_FEATURE_FLAG_FOO_FEATURE', true );

		$flags = new Sensei_Feature_Flags();

		$this->assertTrue( $flags->is_enabled( 'foo_feature' ), 'overriden by define' );

		add_filter( 'sensei_feature_flag_foo_feature', '__return_false' );

		$this->assertFalse( $flags->is_enabled( 'foo_feature' ), 'overriden by filter' );
	}

	public function testIsEnabled_WhenEmailFeatureGivenAndFlagEnabled_ReturnsTrue(): void {
		/* Arrange. */
		$flags = new Sensei_Feature_Flags();

		add_filter( 'sensei_feature_flag_email_customization', '__return_true' );

		/* Act. */
		$actual = $flags->is_enabled( 'email_customization' );

		/* Assert. */
		$this->assertTrue( $actual );
	}

	public function testIsEnabled_WhenEmailFeatureGivenAndFlagDisabled_ReturnsFalse(): void {
		/* Arrange. */
		$flags = new Sensei_Feature_Flags();

		add_filter( 'sensei_feature_flag_email_customization', '__return_false' );

		/* Act. */
		$actual = $flags->is_enabled( 'email_customization' );

		/* Assert. */
		$this->assertFalse( $actual );
	}
}

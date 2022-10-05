<?php
/**
 * Sensei REST API: Sensei_REST_API_Home_Controller tests
 *
 * @package sensei-lms
 * @since   $$next-version$$
 */

/**
 * Class Sensei_REST_API_Home_Controller tests.
 *
 * @covers Sensei_REST_API_Home_Controller
 */
class Sensei_REST_API_Home_Controller_Test extends WP_Test_REST_TestCase {
	use Sensei_Test_Login_Helpers;

	/**
	 * A server instance that we use in tests to dispatch requests.
	 *
	 * @var WP_REST_Server $server
	 */
	protected $server;

	const REST_ROUTE = '/sensei-internal/v1/home';

	/**
	 * Test specific setup.
	 */
	public function setUp() {
		parent::setUp();

		global $wp_rest_server;
		$wp_rest_server = new WP_REST_Server();
		$this->server   = $wp_rest_server;

		do_action( 'rest_api_init' );
	}

	/**
	 * Test specific teardown.
	 */
	public function tearDown() {
		parent::tearDown();

		global $wp_rest_server;
		$wp_rest_server = null;
	}

	/**
	 * Asserts that guests cannot access Home Data.
	 */
	public function testRESTRequestReturns401ForGuests() {
		$this->login_as( null );

		$request  = new WP_REST_Request( 'GET', self::REST_ROUTE );
		$response = $this->server->dispatch( $request );

		$this->assertEquals( 401, $response->get_status() );
	}

	/**
	 * Asserts that admins can access Home Data.
	 */
	public function testRESTRequestReturns200ForAdmins() {
		$this->login_as_admin();

		$request  = new WP_REST_Request( 'GET', self::REST_ROUTE );
		$response = $this->server->dispatch( $request );

		$this->assertEquals( 200, $response->get_status() );
	}

	/**
	 * Check that Home Data contains Quick Links section as generated by the provider and mapped by the mapper.
	 */
	public function testHomeDataReturnsQuickLinksGeneratedByProviderAndMappedByMapper() {
		// Stubs
		$help_provider_stub  = $this->createMock( Sensei_Home_Help_Provider::class );
		$promo_provider_stub = $this->createMock( Sensei_Home_Promo_Banner_Provider::class );
		$tasks_provider_stub = $this->createMock( Sensei_Home_Tasks_Provider::class );
		// Mock provider and mapper interaction.
		$mocked_quick_links        = [ $this->createMock( Sensei_Home_Quick_Links_Category::class ) ];
		$quick_links_provider_mock = $this->createMock( Sensei_Home_Quick_Links_Provider::class );
		$quick_links_provider_mock->expects( $this->once() )
			->method( 'get' )
			->willReturn( $mocked_quick_links );
		// Mock mapper call and response.
		$mapper_mock            = $this->createMock( Sensei_REST_API_Home_Controller_Mapper::class );
		$mocked_mapped_response = [ 'mocked_response' ];
		$mapper_mock->expects( $this->once() )
			->method( 'map_quick_links' )
			->with( $mocked_quick_links )
			->willReturn( $mocked_mapped_response );

		// Do the actual call.
		$controller = new Sensei_REST_API_Home_Controller(
			'namespace',
			$mapper_mock,
			$quick_links_provider_mock,
			$help_provider_stub,
			$promo_provider_stub,
			$tasks_provider_stub
		);
		$result     = $controller->get_data();

		// Assert 'quick_links' are returned as received from the mapper.
		$this->assertArrayHasKey( 'quick_links', $result );
		$this->assertEquals( $mocked_mapped_response, $result['quick_links'] );
	}

	/**
	 * Check that Home Data contains Help section as generated by the provider and mapped by the mapper.
	 */
	public function testHomeDataReturnsHelpGeneratedByProviderAndMappedByMapper() {
		// Stubs
		$quick_links_provider_stub = $this->createMock( Sensei_Home_Quick_Links_Provider::class );
		$promo_provider_stub       = $this->createMock( Sensei_Home_Promo_Banner_Provider::class );
		$tasks_provider_stub       = $this->createMock( Sensei_Home_Tasks_Provider::class );
		$mapper_stub               = $this->createMock( Sensei_REST_API_Home_Controller_Mapper::class );
		// Mock provider and mapper interaction.
		$mocked_help_categories = [ 'category' ];
		$help_provider_mock     = $this->createMock( Sensei_Home_Help_Provider::class );
		$help_provider_mock->expects( $this->once() )
			->method( 'get' )
			->willReturn( $mocked_help_categories );

		// Do the actual call.
		$controller = new Sensei_REST_API_Home_Controller(
			'namespace',
			$mapper_stub,
			$quick_links_provider_stub,
			$help_provider_mock,
			$promo_provider_stub,
			$tasks_provider_stub
		);
		$result     = $controller->get_data();

		// Assert 'help' is returned as received from the mapper.
		$this->assertArrayHasKey( 'help', $result );
		$this->assertEquals( $mocked_help_categories, $result['help'] );
	}

	/**
	 * Check that Home Data contains Promotional Banner section as generated by the provider and mapped by the mapper.
	 */
	public function testHomeDataReturnsPromoBannerGeneratedByProviderAndMappedByMapper() {
		// Stubs
		$quick_links_provider_stub = $this->createMock( Sensei_Home_Quick_Links_Provider::class );
		$help_provider_stub        = $this->createMock( Sensei_Home_Help_Provider::class );
		$tasks_provider_stub       = $this->createMock( Sensei_Home_Tasks_Provider::class );
		$mapper_stub               = $this->createMock( Sensei_REST_API_Home_Controller_Mapper::class );
		// Mock provider
		$mocked_banner       = [ 'banner' ];
		$promo_provider_mock = $this->createMock( Sensei_Home_Promo_Banner_Provider::class );
		$promo_provider_mock->expects( $this->once() )
			->method( 'get' )
			->willReturn( $mocked_banner );

		// Do the actual call.
		$controller = new Sensei_REST_API_Home_Controller(
			'namespace',
			$mapper_stub,
			$quick_links_provider_stub,
			$help_provider_stub,
			$promo_provider_mock,
			$tasks_provider_stub
		);
		$result     = $controller->get_data();

		// Assert 'help' is returned as received from the mapper.
		$this->assertArrayHasKey( 'promo_banner', $result );
		$this->assertSame( $mocked_banner, $result['promo_banner'] );
	}

	/**
	 * Check that Home Data contains Tasks section as generated by the provider and mapped by the mapper.
	 */
	public function testHomeDataReturnsTasksGeneratedByProviderAndMappedByMapper() {
		// Stubs
		$quick_links_provider_stub = $this->createMock( Sensei_Home_Quick_Links_Provider::class );
		$help_provider_stub        = $this->createMock( Sensei_Home_Help_Provider::class );
		$promo_provider_stub       = $this->createMock( Sensei_Home_Promo_Banner_Provider::class );
		// Mock provider
		$mocked_tasks        = $this->createMock( Sensei_Home_Tasks::class );
		$tasks_provider_mock = $this->createMock( Sensei_Home_Tasks_Provider::class );
		$tasks_provider_mock->expects( $this->once() )
			->method( 'get' )
			->willReturn( $mocked_tasks );
		// Mock mapper
		$mocked_mapped_response = [ 'mocked_response' ];
		$mapper_mock            = $this->createMock( Sensei_REST_API_Home_Controller_Mapper::class );
		$mapper_mock->expects( $this->once() )
			->method( 'map_tasks' )
			->with( $mocked_tasks )
			->willReturn( $mocked_mapped_response );

		// Do the actual call.
		$controller = new Sensei_REST_API_Home_Controller(
			'namespace',
			$mapper_mock,
			$quick_links_provider_stub,
			$help_provider_stub,
			$promo_provider_stub,
			$tasks_provider_mock
		);
		$result     = $controller->get_data();

		// Assert 'help' is returned as received from the mapper.
		$this->assertArrayHasKey( 'tasks', $result );
		$this->assertSame( $mocked_mapped_response, $result['tasks'] );
	}
}

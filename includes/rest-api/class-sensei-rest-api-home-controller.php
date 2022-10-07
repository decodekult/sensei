<?php
/**
 * Sensei Home REST API.
 *
 * @package Sensei\Admin
 * @since   $$next-version$$
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Sensei Home REST API endpoints.
 *
 * @since $$next-version$$
 */
class Sensei_REST_API_Home_Controller extends \WP_REST_Controller {

	/**
	 * Routes namespace.
	 *
	 * @var string
	 */
	protected $namespace;

	/**
	 * Routes prefix.
	 *
	 * @var string
	 */
	protected $rest_base = 'home';

	/**
	 * Quick Links provider.
	 *
	 * @var Sensei_Home_Quick_Links_Provider
	 */
	private $quick_links_provider;

	/**
	 * Help provider.
	 *
	 * @var Sensei_Home_Help_Provider
	 */
	private $help_provider;

	/**
	 * Promo banner provider.
	 *
	 * @var Sensei_Home_Promo_Banner_Provider
	 */
	private $promo_banner_provider;

	/**
	 * Tasks provider.
	 *
	 * @var Sensei_Home_Tasks_Provider
	 */
	private $tasks_provider;

	/**
	 * News provider.
	 *
	 * @var Sensei_Home_News_Provider
	 */
	private $news_provider;

	/**
	 * Guides provider.
	 *
	 * @var Sensei_Home_Guides_Provider
	 */
	private $guides_provider;

	/**
	 * Sensei_REST_API_Home_Controller constructor.
	 *
	 * @param string                            $namespace             Routes namespace.
	 * @param Sensei_Home_Quick_Links_Provider  $quick_links_provider  Quick Links provider.
	 * @param Sensei_Home_Help_Provider         $help_provider         Help provider.
	 * @param Sensei_Home_Promo_Banner_Provider $promo_banner_provider Promo banner provider.
	 * @param Sensei_Home_Tasks_Provider        $tasks_provider        Tasks provider.
	 * @param Sensei_Home_News_Provider         $news_provider         News provider.
	 * @param Sensei_Home_Guides_Provider       $guides_provider       Guides provider.
	 */
	public function __construct(
		$namespace,
		Sensei_Home_Quick_Links_Provider $quick_links_provider,
		Sensei_Home_Help_Provider $help_provider,
		Sensei_Home_Promo_Banner_Provider $promo_banner_provider,
		Sensei_Home_Tasks_Provider $tasks_provider,
		Sensei_Home_News_Provider $news_provider,
		Sensei_Home_Guides_Provider $guides_provider
	) {
		$this->namespace             = $namespace;
		$this->quick_links_provider  = $quick_links_provider;
		$this->help_provider         = $help_provider;
		$this->promo_banner_provider = $promo_banner_provider;
		$this->tasks_provider        = $tasks_provider;
		$this->news_provider         = $news_provider;
		$this->guides_provider       = $guides_provider;
	}

	/**
	 * Register the REST API endpoints for Home.
	 */
	public function register_routes() {
		$this->register_get_data_route();
	}

	/**
	 * Check user permission for REST API access.
	 *
	 * @return bool Whether the user can access the Sensei Home REST API.
	 */
	public function can_user_access_rest_api() {
		return current_user_can( 'manage_sensei' );
	}

	/**
	 * Register GET / endpoint.
	 */
	public function register_get_data_route() {
		register_rest_route(
			$this->namespace,
			$this->rest_base,
			[
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_data' ],
					'permission_callback' => [ $this, 'can_user_access_rest_api' ],
				],
			]
		);
	}

	/**
	 * Get data for Sensei Home frontend.
	 *
	 * @return array Setup Wizard data
	 */
	public function get_data() {
		return [
			'tasks'         => $this->tasks_provider->get(),
			'quick_links'   => $this->quick_links_provider->get(),
			'help'          => $this->help_provider->get(),
			'guides'        => $this->guides_provider->get(),
			'news'          => $this->news_provider->get(),
			'extensions'    => [
				// TODO: Load from https://senseilms.com/wp-json/senseilms-home/1.0/{sensei-lms|sensei-pro}.json.
				[
					'title'        => 'Sensei LMS Post to Course Creator',
					'image'        => 'http://senseilms.com/wp-content/uploads/2022/02/sensei-post-to-course-80x80.png',
					'description'  => 'Turn your blog posts into online courses.',
					'price'        => 0,
					'product_slug' => 'sensei-post-to-course', // To be used with the installation function `Sensei_Setup_Wizard::install_extensions`.
					'more_url'     => 'http://senseilms.com/product/sensei-lms-post-to-course-creator/',
				],
			],
			'promo_banner'  => $this->promo_banner_provider->get(),
			'notifications' => [
				[
					'heading'     => null, // Not needed for the moment.
					'message'     => 'Your Sensei Pro license expires on 12.09.2022.',
					'actions'     => [
						[
							'label' => 'Update now',
							'url'   => 'https://...',
						],
					],
					'info_link'   => [
						'label' => 'What\'s new',
						'url'   => 'https://...',
					],
					'level'       => 'error', // One of: info, warning, error.
					'dismissible' => false, // The default value is true.
				],
				[
					'heading'     => null, // Not needed for the moment.
					'message'     => 'Good news, reminder to update to latest version',
					'actions'     => [
						[
							'label' => 'Update now',
							'url'   => 'https://...',
						],
					],
					'info_link'   => [
						'label' => 'Link for more information',
						'url'   => 'https://...',
					],
					'level'       => 'info', // One of: info, warning, error.
					'dismissible' => true, // The default value is true.
				],
			],
		];
	}
}

<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin menu, dashboard widget and stats queries.
 */
class RVS_Admin {

	/** @var string */
	private $table_name;

	/** @var string */
	private $page_hook = '';

	public function __construct( $table_name ) {
		$this->table_name = $table_name;

		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widget' ) );
	}

	public function add_admin_menu() {
		$this->page_hook = add_menu_page(
			__( 'Real Visitor Stats', 'real-visitor-stats' ),
			__( 'Visitor Stats', 'real-visitor-stats' ),
			'manage_options',
			'real-visitor-stats',
			array( $this, 'render_admin_page' ),
			'dashicons-chart-area',
			26
		);
	}

	public function enqueue_admin_assets( $hook_suffix ) {
		if ( $hook_suffix !== $this->page_hook ) {
			return;
		}

		wp_enqueue_style(
			'rvs-admin',
			RVS_PLUGIN_URL . 'assets/css/admin.css',
			array(),
			RVS_VERSION
		);

		wp_enqueue_script(
			'rvs-chartjs',
			RVS_PLUGIN_URL . 'assets/js/vendor/chart.umd.min.js',
			array(),
			'4.4.1',
			true
		);

		wp_enqueue_script(
			'rvs-admin',
			RVS_PLUGIN_URL . 'assets/js/admin.js',
			array( 'rvs-chartjs' ),
			RVS_VERSION,
			true
		);

		wp_localize_script( 'rvs-admin', 'rvsData', $this->get_chart_data() );
	}

	/**
	 * Data prepared for Chart.js, passed via wp_localize_script.
	 */
	private function get_chart_data() {
		global $wpdb;
		$table = $this->table_name;

		$daily = $wpdb->get_results(
			"SELECT visit_date, COUNT(DISTINCT ip_hash) AS uniques, COUNT(*) AS pageviews
			 FROM {$table}
			 WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL 14 DAY)
			 GROUP BY visit_date
			 ORDER BY visit_date ASC"
		);

		$browsers = $wpdb->get_results(
			"SELECT browser, COUNT(*) AS count FROM {$table} GROUP BY browser ORDER BY count DESC LIMIT 5"
		);

		$devices = $wpdb->get_results(
			"SELECT device, COUNT(*) AS count FROM {$table} GROUP BY device ORDER BY count DESC"
		);

		return array(
			'daily'    => array(
				'labels'     => wp_list_pluck( $daily, 'visit_date' ),
				'uniques'    => array_map( 'intval', wp_list_pluck( $daily, 'uniques' ) ),
				'pageviews'  => array_map( 'intval', wp_list_pluck( $daily, 'pageviews' ) ),
			),
			'browsers' => array(
				'labels' => wp_list_pluck( $browsers, 'browser' ),
				'counts' => array_map( 'intval', wp_list_pluck( $browsers, 'count' ) ),
			),
			'devices'  => array(
				'labels' => wp_list_pluck( $devices, 'device' ),
				'counts' => array_map( 'intval', wp_list_pluck( $devices, 'count' ) ),
			),
			'i18n'     => array(
				'uniqueVisitors' => __( 'Unique Visitors', 'real-visitor-stats' ),
				'pageViews'      => __( 'Page Views', 'real-visitor-stats' ),
			),
		);
	}

	/**
	 * Summary cards + top pages/referrers used by the admin page template.
	 */
	private function get_summary() {
		global $wpdb;
		$table = $this->table_name;
		$today = current_time( 'Y-m-d' );

		return array(
			'unique_today'     => (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(DISTINCT ip_hash) FROM {$table} WHERE visit_date = %s", $today ) ),
			'page_views_today' => (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE visit_date = %s", $today ) ),
			'unique_all'       => (int) $wpdb->get_var( "SELECT COUNT(DISTINCT ip_hash) FROM {$table}" ),
			'page_views_all'   => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" ),
			'top_pages'        => $wpdb->get_results(
				"SELECT page_url, COUNT(*) AS views FROM {$table}
				 WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
				 GROUP BY page_url ORDER BY views DESC LIMIT 10"
			),
			'top_referrers'    => $wpdb->get_results(
				"SELECT referrer, COUNT(*) AS count FROM {$table}
				 WHERE referrer != '' AND visit_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
				 GROUP BY referrer ORDER BY count DESC LIMIT 10"
			),
		);
	}

	public function render_admin_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$summary = $this->get_summary();
		include RVS_PLUGIN_DIR . 'includes/views/admin-page.php';
	}

	public function add_dashboard_widget() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		wp_add_dashboard_widget(
			'rvs_dashboard_widget',
			__( 'Visitor Stats (Today)', 'real-visitor-stats' ),
			array( $this, 'render_dashboard_widget' )
		);
	}

	public function render_dashboard_widget() {
		global $wpdb;
		$today = current_time( 'Y-m-d' );

		$unique_today = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(DISTINCT ip_hash) FROM {$this->table_name} WHERE visit_date = %s", $today ) );
		$views_today  = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$this->table_name} WHERE visit_date = %s", $today ) );

		printf(
			'<p><strong>%1$s:</strong> %2$d &nbsp; <strong>%3$s:</strong> %4$d</p>',
			esc_html__( 'Unique visitors', 'real-visitor-stats' ),
			$unique_today,
			esc_html__( 'Page views', 'real-visitor-stats' ),
			$views_today
		);
		printf(
			'<p><a href="%s">%s &rarr;</a></p>',
			esc_url( admin_url( 'admin.php?page=real-visitor-stats' ) ),
			esc_html__( 'View full stats', 'real-visitor-stats' )
		);
	}
}

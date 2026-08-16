<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin bootstrap: activation, cron, hooks, shortcode.
 */
final class RVS_Plugin {

	/** @var RVS_Plugin|null */
	private static $instance = null;

	/** @var RVS_Tracker */
	private $tracker;

	/** @var RVS_Admin */
	private $admin;

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		$this->tracker = new RVS_Tracker();
		$this->admin   = new RVS_Admin( $this->tracker->get_table_name() );

		add_action( 'template_redirect', array( $this, 'maybe_track_visit' ) );
		add_shortcode( 'real_visitor_stats', array( $this, 'render_shortcode' ) );

		add_action( 'rvs_daily_cleanup', array( $this->tracker, 'cleanup_old_data' ) );
		if ( ! wp_next_scheduled( 'rvs_daily_cleanup' ) ) {
			wp_schedule_event( time(), 'daily', 'rvs_daily_cleanup' );
		}
	}

	public function maybe_track_visit() {
		if ( $this->tracker->should_track() ) {
			$this->tracker->track_visit();
		}
	}

	/**
	 * [real_visitor_stats] — simple front-end summary of today's traffic.
	 */
	public function render_shortcode() {
		global $wpdb;
		$table = $wpdb->prefix . 'rvs_stats';
		$today = current_time( 'Y-m-d' );

		$unique_today = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(DISTINCT ip_hash) FROM {$table} WHERE visit_date = %s", $today ) );
		$views_today  = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE visit_date = %s", $today ) );

		return sprintf(
			'<div class="rvs-shortcode"><span>%1$s: <strong>%2$d</strong></span> <span>%3$s: <strong>%4$d</strong></span></div>',
			esc_html__( 'Unique visitors today', 'real-visitor-stats' ),
			$unique_today,
			esc_html__( 'Page views today', 'real-visitor-stats' ),
			$views_today
		);
	}

	public static function activate() {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$table_name      = $wpdb->prefix . 'rvs_stats';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$table_name} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			visit_date DATE NOT NULL,
			ip_hash VARCHAR(64) NOT NULL,
			ua_hash VARCHAR(64) NOT NULL,
			page_url VARCHAR(255) NOT NULL DEFAULT '',
			referrer VARCHAR(255) NOT NULL DEFAULT '',
			browser VARCHAR(100) NOT NULL DEFAULT '',
			os VARCHAR(100) NOT NULL DEFAULT '',
			device VARCHAR(50) NOT NULL DEFAULT '',
			created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY visit_date (visit_date),
			KEY ip_hash (ip_hash),
			KEY ua_hash (ua_hash),
			KEY created_at (created_at)
		) $charset_collate;";

		dbDelta( $sql );

		add_option( 'rvs_db_version', RVS_VERSION );

		if ( ! wp_next_scheduled( 'rvs_daily_cleanup' ) ) {
			wp_schedule_event( time(), 'daily', 'rvs_daily_cleanup' );
		}
	}

	public static function deactivate() {
		wp_clear_scheduled_hook( 'rvs_daily_cleanup' );
	}

	public static function uninstall() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'rvs_stats';
		$wpdb->query( "DROP TABLE IF EXISTS {$table_name}" );
		delete_option( 'rvs_db_version' );
	}
}

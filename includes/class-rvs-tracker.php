<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles visit tracking and storage.
 */
class RVS_Tracker {

	/** @var string */
	private $table_name;

	public function __construct() {
		global $wpdb;
		$this->table_name = $wpdb->prefix . 'rvs_stats';
	}

	public function get_table_name() {
		return $this->table_name;
	}

	/**
	 * Decide whether the current request should be tracked.
	 */
	public function should_track() {
		if ( is_admin() || is_feed() || is_robots() || is_trackback() || wp_doing_ajax() || wp_doing_cron() ) {
			return false;
		}
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return false;
		}
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			return false;
		}
		if ( is_404() ) {
			return false;
		}
		return true;
	}

	/**
	 * Record the current visit.
	 */
	public function track_visit() {
		global $wpdb;

		$ip         = $this->get_client_ip();
		$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
		$referrer   = isset( $_SERVER['HTTP_REFERER'] ) ? esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : '';
		$page_url   = $this->get_current_url();

		$ip_hash = hash( 'sha256', $ip . wp_salt( 'auth' ) );
		$ua_hash = hash( 'sha256', $user_agent . wp_salt( 'auth' ) );

		$ua_data = $this->parse_user_agent( $user_agent );

		$wpdb->insert(
			$this->table_name,
			array(
				'visit_date' => current_time( 'Y-m-d' ),
				'ip_hash'    => $ip_hash,
				'ua_hash'    => $ua_hash,
				'page_url'   => $page_url,
				'referrer'   => $referrer,
				'browser'    => $ua_data['browser'],
				'os'         => $ua_data['os'],
				'device'     => $ua_data['device'],
				'created_at' => current_time( 'mysql' ),
			),
			array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
		);
	}

	/**
	 * Resolve the client IP, honoring common proxy headers.
	 */
	private function get_client_ip() {
		$ip = '';

		if ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$forwarded = explode( ',', sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) );
			$ip        = trim( $forwarded[0] );
		} elseif ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ip = sanitize_text_field( wp_unslash( $_SERVER['HTTP_CLIENT_IP'] ) );
		} elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
			$ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
		}

		if ( ! filter_var( $ip, FILTER_VALIDATE_IP ) ) {
			$ip = '0.0.0.0';
		}

		return $ip;
	}

	/**
	 * Current URL without query string, for cleaner grouping in stats.
	 */
	private function get_current_url() {
		global $wp;
		$current_url = home_url( add_query_arg( array(), $wp->request ) );
		return untrailingslashit( $current_url );
	}

	/**
	 * Lightweight user agent parser (browser / OS / device).
	 */
	private function parse_user_agent( $ua ) {
		$browser = 'Unknown';
		$os      = 'Unknown';

		if ( strpos( $ua, 'Edg/' ) !== false ) {
			$browser = 'Edge';
		} elseif ( strpos( $ua, 'OPR/' ) !== false || strpos( $ua, 'Opera' ) !== false ) {
			$browser = 'Opera';
		} elseif ( strpos( $ua, 'Chrome/' ) !== false ) {
			$browser = 'Chrome';
		} elseif ( strpos( $ua, 'Firefox/' ) !== false ) {
			$browser = 'Firefox';
		} elseif ( strpos( $ua, 'Safari/' ) !== false ) {
			$browser = 'Safari';
		} elseif ( strpos( $ua, 'MSIE' ) !== false || strpos( $ua, 'Trident/' ) !== false ) {
			$browser = 'Internet Explorer';
		}

		if ( strpos( $ua, 'Windows NT' ) !== false ) {
			$os = 'Windows';
		} elseif ( strpos( $ua, 'Mac OS X' ) !== false || strpos( $ua, 'Macintosh' ) !== false ) {
			$os = 'macOS';
		} elseif ( strpos( $ua, 'Android' ) !== false ) {
			$os = 'Android';
		} elseif ( strpos( $ua, 'iPhone' ) !== false || strpos( $ua, 'iPad' ) !== false ) {
			$os = 'iOS';
		} elseif ( strpos( $ua, 'Linux' ) !== false ) {
			$os = 'Linux';
		}

		if ( preg_match( '/iPad|Tablet/i', $ua ) ) {
			$device = 'Tablet';
		} elseif ( preg_match( '/Mobile|Android|iPhone|iPod|BlackBerry|IEMobile|Opera Mini/i', $ua ) ) {
			$device = 'Mobile';
		} else {
			$device = 'Desktop';
		}

		return array(
			'browser' => sanitize_text_field( $browser ),
			'os'      => sanitize_text_field( $os ),
			'device'  => sanitize_text_field( $device ),
		);
	}

	/**
	 * Delete rows older than the retention window (default 90 days).
	 */
	public function cleanup_old_data( $days = 90 ) {
		global $wpdb;
		$days = (int) apply_filters( 'rvs_retention_days', $days );

		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$this->table_name} WHERE visit_date < DATE_SUB(CURDATE(), INTERVAL %d DAY)",
				$days
			)
		);
	}
}

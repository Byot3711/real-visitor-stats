<?php
/**
 * Admin page template.
 *
 * @var array $summary
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap rvs-wrap">
	<h1><?php esc_html_e( 'Real Visitor Stats', 'real-visitor-stats' ); ?></h1>

	<div class="rvs-cards">
		<div class="rvs-card">
			<h3><?php esc_html_e( 'Unique Today', 'real-visitor-stats' ); ?></h3>
			<div class="rvs-number"><?php echo esc_html( $summary['unique_today'] ); ?></div>
		</div>
		<div class="rvs-card">
			<h3><?php esc_html_e( 'Page Views Today', 'real-visitor-stats' ); ?></h3>
			<div class="rvs-number"><?php echo esc_html( $summary['page_views_today'] ); ?></div>
		</div>
		<div class="rvs-card">
			<h3><?php esc_html_e( 'Total Uniques', 'real-visitor-stats' ); ?></h3>
			<div class="rvs-number"><?php echo esc_html( $summary['unique_all'] ); ?></div>
		</div>
		<div class="rvs-card">
			<h3><?php esc_html_e( 'Total Page Views', 'real-visitor-stats' ); ?></h3>
			<div class="rvs-number"><?php echo esc_html( $summary['page_views_all'] ); ?></div>
		</div>
	</div>

	<div class="rvs-section">
		<h2><?php esc_html_e( 'Last 14 Days', 'real-visitor-stats' ); ?></h2>
		<div class="rvs-chart-container">
			<canvas id="rvsDailyChart"></canvas>
		</div>
	</div>

	<div class="rvs-section">
		<h2><?php esc_html_e( 'Browser Distribution', 'real-visitor-stats' ); ?></h2>
		<div class="rvs-chart-container rvs-chart-container--small">
			<canvas id="rvsBrowserChart"></canvas>
		</div>
	</div>

	<div class="rvs-section">
		<h2><?php esc_html_e( 'Device Type', 'real-visitor-stats' ); ?></h2>
		<div class="rvs-chart-container rvs-chart-container--tiny">
			<canvas id="rvsDeviceChart"></canvas>
		</div>
	</div>

	<div class="rvs-section">
		<h2><?php esc_html_e( 'Top Pages (Last 7 Days)', 'real-visitor-stats' ); ?></h2>
		<table class="rvs-table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'URL', 'real-visitor-stats' ); ?></th>
					<th><?php esc_html_e( 'Views', 'real-visitor-stats' ); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ( $summary['top_pages'] as $page ) : ?>
				<tr>
					<td><?php echo esc_url( $page->page_url ); ?></td>
					<td><?php echo esc_html( $page->views ); ?></td>
				</tr>
			<?php endforeach; ?>
			<?php if ( empty( $summary['top_pages'] ) ) : ?>
				<tr><td colspan="2"><?php esc_html_e( 'No data yet.', 'real-visitor-stats' ); ?></td></tr>
			<?php endif; ?>
			</tbody>
		</table>
	</div>

	<div class="rvs-section">
		<h2><?php esc_html_e( 'Top Referrers (Last 30 Days)', 'real-visitor-stats' ); ?></h2>
		<table class="rvs-table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Referrer', 'real-visitor-stats' ); ?></th>
					<th><?php esc_html_e( 'Count', 'real-visitor-stats' ); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ( $summary['top_referrers'] as $ref ) : ?>
				<tr>
					<td><?php echo esc_html( $ref->referrer ); ?></td>
					<td><?php echo esc_html( $ref->count ); ?></td>
				</tr>
			<?php endforeach; ?>
			<?php if ( empty( $summary['top_referrers'] ) ) : ?>
				<tr><td colspan="2"><?php esc_html_e( 'No data yet.', 'real-visitor-stats' ); ?></td></tr>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

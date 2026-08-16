/* global Chart, rvsData */
( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		if ( typeof rvsData === 'undefined' ) {
			return;
		}

		var dailyEl = document.getElementById( 'rvsDailyChart' );
		if ( dailyEl ) {
			new Chart( dailyEl.getContext( '2d' ), {
				type: 'line',
				data: {
					labels: rvsData.daily.labels,
					datasets: [
						{
							label: rvsData.i18n.uniqueVisitors,
							data: rvsData.daily.uniques,
							borderColor: '#2271b1',
							backgroundColor: 'rgba(34,113,177,0.1)',
							fill: true,
							tension: 0.1,
						},
						{
							label: rvsData.i18n.pageViews,
							data: rvsData.daily.pageviews,
							borderColor: '#d63638',
							backgroundColor: 'rgba(214,54,56,0.1)',
							fill: true,
							tension: 0.1,
						},
					],
				},
				options: {
					responsive: true,
					scales: { y: { beginAtZero: true } },
				},
			} );
		}

		var browserEl = document.getElementById( 'rvsBrowserChart' );
		if ( browserEl ) {
			new Chart( browserEl.getContext( '2d' ), {
				type: 'doughnut',
				data: {
					labels: rvsData.browsers.labels,
					datasets: [
						{
							data: rvsData.browsers.counts,
							backgroundColor: [ '#2271b1', '#d63638', '#00a32a', '#996800', '#dba617' ],
						},
					],
				},
				options: {
					responsive: true,
					plugins: { legend: { position: 'bottom' } },
				},
			} );
		}

		var deviceEl = document.getElementById( 'rvsDeviceChart' );
		if ( deviceEl ) {
			new Chart( deviceEl.getContext( '2d' ), {
				type: 'pie',
				data: {
					labels: rvsData.devices.labels,
					datasets: [
						{
							data: rvsData.devices.counts,
							backgroundColor: [ '#2271b1', '#d63638', '#00a32a' ],
						},
					],
				},
				options: {
					responsive: true,
					plugins: { legend: { position: 'bottom' } },
				},
			} );
		}
	} );
} )();

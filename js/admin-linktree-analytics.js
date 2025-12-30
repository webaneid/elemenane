/**
 * Linktree Analytics Dashboard
 *
 * @package elemenane
 * @since 1.0.6
 */

(function($) {
	'use strict';

	$(document).ready(function() {
		if (typeof Chart === 'undefined' || typeof aneLinktreeData === 'undefined') {
			return;
		}

		// Clicks Over Time Chart
		var clicksCtx = document.getElementById('ane-linktree-clicks-chart');
		if (clicksCtx) {
			new Chart(clicksCtx, {
				type: 'line',
				data: {
					labels: aneLinktreeData.clicksData.labels,
					datasets: [{
						label: 'Clicks',
						data: aneLinktreeData.clicksData.clicks,
						borderColor: aneLinktreeData.colors.primary,
						backgroundColor: aneLinktreeData.colors.primary + '20',
						tension: 0.4,
						fill: true,
						pointBackgroundColor: aneLinktreeData.colors.primary,
						pointBorderColor: '#fff',
						pointBorderWidth: 2,
						pointRadius: 4,
						pointHoverRadius: 6
					}]
				},
				options: {
					responsive: true,
					maintainAspectRatio: false,
					plugins: {
						legend: {
							display: false
						},
						tooltip: {
							backgroundColor: aneLinktreeData.colors.body,
							titleColor: '#fff',
							bodyColor: '#fff',
							borderColor: aneLinktreeData.colors.accent,
							borderWidth: 1,
							padding: 12,
							displayColors: false
						}
					},
					scales: {
						y: {
							beginAtZero: true,
							ticks: {
								stepSize: 1,
								color: aneLinktreeData.colors.body
							},
							grid: {
								color: aneLinktreeData.colors.accent + '40'
							}
						},
						x: {
							ticks: {
								color: aneLinktreeData.colors.body
							},
							grid: {
								display: false
							}
						}
					}
				}
			});
		}

		// Device Breakdown Chart
		var deviceCtx = document.getElementById('ane-device-chart');
		if (deviceCtx && typeof aneLinktreeData.deviceData !== 'undefined') {
			new Chart(deviceCtx, {
				type: 'doughnut',
				data: {
					labels: aneLinktreeData.deviceData.labels,
					datasets: [{
						data: aneLinktreeData.deviceData.values,
						backgroundColor: [
							aneLinktreeData.colors.primary,
							aneLinktreeData.colors.secondary,
							aneLinktreeData.colors.accent
						],
						borderWidth: 0
					}]
				},
				options: {
					responsive: true,
					maintainAspectRatio: false,
					plugins: {
						legend: {
							position: 'bottom',
							labels: {
								padding: 15,
								color: aneLinktreeData.colors.body,
								font: {
									size: 13
								},
								usePointStyle: true,
								pointStyle: 'circle'
							}
						},
						tooltip: {
							backgroundColor: aneLinktreeData.colors.body,
							titleColor: '#fff',
							bodyColor: '#fff',
							borderColor: aneLinktreeData.colors.accent,
							borderWidth: 1,
							padding: 12,
							callbacks: {
								label: function(context) {
									var label = context.label || '';
									var value = context.parsed || 0;
									var total = context.dataset.data.reduce((a, b) => a + b, 0);
									var percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
									return label + ': ' + value + ' (' + percentage + '%)';
								}
							}
						}
					}
				}
			});
		}
	});
})(jQuery);

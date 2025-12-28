/**
 * User profile page chart.
 *
 * @package ane
 */

(function() {
	'use strict';

	// Wait for DOM and Chart.js to load.
	document.addEventListener('DOMContentLoaded', function() {
		const chartCanvas = document.getElementById('ane-user-posts-chart');

		if (!chartCanvas || typeof Chart === 'undefined') {
			return;
		}

		// Get data from localized script.
		const postsData = aneUserProfile.postsData || {};
		const colors = aneUserProfile.colors || {};

		// Create chart.
		new Chart(chartCanvas, {
			type: 'line',
			data: {
				labels: postsData.labels || [],
				datasets: [
					{
						label: 'Posts',
						data: postsData.posts || [],
						borderColor: colors.primary || '#2d232e',
						backgroundColor: 'rgba(45, 35, 46, 0.1)',
						tension: 0.4,
						fill: true,
						pointRadius: 4,
						pointHoverRadius: 6,
					},
					{
						label: 'Views',
						data: postsData.views || [],
						borderColor: colors.accent || '#73ab01',
						backgroundColor: 'rgba(115, 171, 1, 0.1)',
						tension: 0.4,
						fill: true,
						pointRadius: 4,
						pointHoverRadius: 6,
						yAxisID: 'y1',
					}
				]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				interaction: {
					mode: 'index',
					intersect: false,
				},
				plugins: {
					legend: {
						display: true,
						position: 'top',
						labels: {
							usePointStyle: true,
							padding: 15,
							font: {
								size: 13,
								family: "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
							}
						}
					},
					tooltip: {
						backgroundColor: 'rgba(45, 35, 46, 0.95)',
						padding: 12,
						titleFont: {
							size: 14,
							weight: '600',
						},
						bodyFont: {
							size: 13,
						},
						borderColor: 'rgba(255, 255, 255, 0.1)',
						borderWidth: 1,
					}
				},
				scales: {
					y: {
						type: 'linear',
						display: true,
						position: 'left',
						title: {
							display: true,
							text: 'Posts',
							font: {
								size: 12,
								weight: '600',
							}
						},
						grid: {
							color: 'rgba(45, 35, 46, 0.08)',
						},
						ticks: {
							font: {
								size: 11,
							}
						}
					},
					y1: {
						type: 'linear',
						display: true,
						position: 'right',
						title: {
							display: true,
							text: 'Views',
							font: {
								size: 12,
								weight: '600',
							}
						},
						grid: {
							drawOnChartArea: false,
						},
						ticks: {
							font: {
								size: 11,
							}
						}
					},
					x: {
						grid: {
							display: false,
						},
						ticks: {
							font: {
								size: 11,
							}
						}
					}
				}
			}
		});
	});
})();

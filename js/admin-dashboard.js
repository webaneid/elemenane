/**
 * Ane Dashboard Charts
 *
 * @package elemenane
 */

(function() {
	'use strict';

	// Wait for DOM and Chart.js to load.
	document.addEventListener('DOMContentLoaded', function() {
		if (typeof Chart === 'undefined' || typeof aneDashboard === 'undefined') {
			return;
		}

		const chartCanvas = document.getElementById('ane-posts-chart');
		if (!chartCanvas) {
			return;
		}

		const data = aneDashboard.postsData;
		const colors = aneDashboard.colors;

		// Create gradient for posts line.
		const ctx = chartCanvas.getContext('2d');
		const postsGradient = ctx.createLinearGradient(0, 0, 0, 400);
		postsGradient.addColorStop(0, colors.primary + '40'); // 40 = 25% opacity in hex
		postsGradient.addColorStop(1, colors.primary + '00'); // 00 = 0% opacity

		// Create gradient for views line.
		const viewsGradient = ctx.createLinearGradient(0, 0, 0, 400);
		viewsGradient.addColorStop(0, colors.secondary + '40');
		viewsGradient.addColorStop(1, colors.secondary + '00');

		// Create chart.
		new Chart(ctx, {
			type: 'line',
			data: {
				labels: data.labels,
				datasets: [
					{
						label: 'Published Posts',
						data: data.posts,
						borderColor: colors.primary,
						backgroundColor: postsGradient,
						borderWidth: 2,
						fill: true,
						tension: 0.4,
						pointRadius: 4,
						pointHoverRadius: 6,
						pointBackgroundColor: '#ffffff',
						pointBorderColor: colors.primary,
						pointBorderWidth: 2,
						pointHoverBackgroundColor: colors.primary,
						pointHoverBorderColor: '#ffffff',
					},
					{
						label: 'Total Views',
						data: data.views,
						borderColor: colors.secondary,
						backgroundColor: viewsGradient,
						borderWidth: 2,
						fill: true,
						tension: 0.4,
						pointRadius: 4,
						pointHoverRadius: 6,
						pointBackgroundColor: '#ffffff',
						pointBorderColor: colors.secondary,
						pointBorderWidth: 2,
						pointHoverBackgroundColor: colors.secondary,
						pointHoverBorderColor: '#ffffff',
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
						display: false
					},
					tooltip: {
						backgroundColor: 'rgba(255, 255, 255, 0.95)',
						titleColor: colors.body,
						bodyColor: colors.body,
						borderColor: colors.accent,
						borderWidth: 1,
						padding: 12,
						displayColors: true,
						boxWidth: 12,
						boxHeight: 12,
						usePointStyle: true,
						callbacks: {
							label: function(context) {
								let label = context.dataset.label || '';
								if (label) {
									label += ': ';
								}
								label += new Intl.NumberFormat().format(context.parsed.y);
								return label;
							}
						}
					}
				},
				scales: {
					x: {
						grid: {
							display: false,
							drawBorder: false
						},
						ticks: {
							color: colors.body,
							font: {
								size: 11
							}
						}
					},
					y: {
						beginAtZero: true,
						grid: {
							color: colors.accent,
							drawBorder: false
						},
						ticks: {
							color: colors.body,
							font: {
								size: 11
							},
							callback: function(value) {
								return new Intl.NumberFormat().format(value);
							}
						}
					}
				}
			}
		});
	});
})();

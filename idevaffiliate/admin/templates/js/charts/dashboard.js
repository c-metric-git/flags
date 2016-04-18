

"use strict";

$(document).ready(function(){

	// Sample Data
	var hits = [[1262304000000, 1000], [1264982400000, 1150], [1267401600000, 1200], [1270080000000, 980], [1272672000000, 900], [1275350400000, 1200], [1277942400000, 1250], [1280620800000, 1400], [1283299200000, 1000], [1285891200000, 890], [1288569600000, 1120], [1291161600000, 1025]];
	var comms = [[1262304000000, 5], [1264982400000, 9], [1267401600000, 7], [1270080000000, 3], [1272672000000, 4], [1275350400000, 8], [1277942400000, 3], [1280620800000, 9], [1283299200000, 34], [1285891200000, 5], [1288569600000, 34], [1291161600000, 4]];
	
	var dash = [
		{ label: "Total Unique Hits", data: hits, color: App.getLayoutColorCode('blue') },
		{ label: "Total Commissions", data: comms, color: App.getLayoutColorCode('black') }
	];
	
	$.plot("#chart_dashboard", dash, $.extend(true, {}, Plugins.getFlotDefaults(), {
		xaxis: {
			min: (new Date(2009, 12, 1)).getTime(),
			max: (new Date(2010, 11, 2)).getTime(),
			mode: "time",
			tickSize: [1, "month"],
			monthNames: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
			tickLength: 0
		},
		series: {
			lines: {
				fill: true,
				lineWidth: 1.5
			},
			points: {
				show: true,
				radius: 2.5,
				lineWidth: 1.1
			},
			grow: { active: true, growings:[ { stepMode: "maximum" } ] }
		},
		grid: {
			hoverable: true,
			clickable: true
		},
		tooltip: true,
		tooltipOpts: {
			content: '%s: %y'
		}
	}));


});
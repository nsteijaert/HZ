/**
 * @author Pieter
 */
$(function() {

	var width = $('.visualisation').width(), height = 500;

	var svg = d3.select('.visualisation').append("svg:svg").attr("width", width).attr("height", height).call(d3.behavior.zoom().on("zoom", zoom));

	var rect = svg.append("rect").attr("x", 20).attr("y", 20).attr("width", width - 60).attr("height", height - 40).attr("rx", 10).attr("ry", 10).style("stroke", "steelblue").style("stroke-width", 4).style("stroke-opacity", 0.9).style("fill", "transparent");

	var zoom = function() {
		console.log("Zoom...");
		rect.attr("transform", "translate(" + d3.event.translate + ")" + " scale(" + d3.event.scale + ")");
	};
	
	// Events
	d3.select(".evenHandlers button.zoomIn").on("click", zoom);
	d3.select(".evenHandlers button.zoomOut").on("click", zoom);
});

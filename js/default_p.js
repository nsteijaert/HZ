/**
 * @author Pieter
 */
$(document).ready(function() {
	var zoom = function() {
		svg.transition().attr("transform", "translate(" + d3.event.translate[0] + ", " + d3.event.translate[1] + ")" + " scale(" + d3.event.scale + ")");
	};

	var width = $('.visualisation').get(0).clientWidth, height = 500;

console.log($('.visualisation').get(0).clientWidth);

	var svg = d3.select('.visualisation').append("svg:svg").attr("width", width).attr("height", height).call(d3.behavior.zoom().on("zoom", zoom));

	var rect = svg.append("rect").attr("x", 20).attr("y", 20).attr("width", width - 60).attr("height", height - 40).attr("rx", 10).attr("ry", 10).style("stroke", "steelblue").style("stroke-width", 4).style("stroke-opacity", 0.9).style("fill", "transparent");

	
	// Events
	d3.select(".evenHandlers button.zoomIn").on("click", zoom);
	d3.select(".evenHandlers button.zoomOut").on("click", zoom);
});
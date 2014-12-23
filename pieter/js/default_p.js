/**
 * @author Pieter
 */
$(document).ready(function() {
	var timeOut;

	function load() {
		$('.visualisationResult').append('<div class="bar"></div>');
		$bar = $('.bar');
		$bar.animate({
			width : '100%'
		}, 1000, "swing", function() {

		});
	}

	var width = $('.visualisation').width(), height = 500;

	var links = [{
		source : "Microsoft",
		target : "Amazon",
		type : "licensing"
	}, {
		source : "Microsoft",
		target : "HTC",
		type : "licensing"
	}, {
		source : "Samsung",
		target : "Apple",
		type : "suit"
	}, {
		source : "Motorola",
		target : "Apple",
		type : "suit"
	}, {
		source : "Nokia",
		target : "Apple",
		type : "resolved"
	}, {
		source : "HTC",
		target : "Apple",
		type : "suit"
	}, {
		source : "Kodak",
		target : "Apple",
		type : "suit"
	}, {
		source : "Microsoft",
		target : "Barnes & Noble",
		type : "suit"
	}, {
		source : "Microsoft",
		target : "Foxconn",
		type : "suit"
	}, {
		source : "Oracle",
		target : "Google",
		type : "suit"
	}, {
		source : "Apple",
		target : "HTC",
		type : "suit"
	}, {
		source : "Microsoft",
		target : "Inventec",
		type : "suit"
	}, {
		source : "Samsung",
		target : "Kodak",
		type : "resolved"
	}, {
		source : "LG",
		target : "Kodak",
		type : "resolved"
	}, {
		source : "RIM",
		target : "Kodak",
		type : "suit"
	}, {
		source : "Sony",
		target : "LG",
		type : "suit"
	}, {
		source : "Kodak",
		target : "LG",
		type : "resolved"
	}, {
		source : "Apple",
		target : "Nokia",
		type : "resolved"
	}, {
		source : "Qualcomm",
		target : "Nokia",
		type : "resolved"
	}, {
		source : "Apple",
		target : "Motorola",
		type : "suit"
	}, {
		source : "Microsoft",
		target : "Motorola",
		type : "suit"
	}, {
		source : "Motorola",
		target : "Microsoft",
		type : "suit"
	}, {
		source : "Huawei",
		target : "ZTE",
		type : "suit"
	}, {
		source : "Ericsson",
		target : "ZTE",
		type : "suit"
	}, {
		source : "Kodak",
		target : "Samsung",
		type : "resolved"
	}, {
		source : "Apple",
		target : "Samsung",
		type : "suit"
	}, {
		source : "Kodak",
		target : "RIM",
		type : "suit"
	}, {
		source : "Nokia",
		target : "Qualcomm",
		type : "suit"
	}];

	var nodes = {};

	// Compute the distinct nodes from the links.
	links.forEach(function(link) {
		link.source = nodes[link.source] || (nodes[link.source] = {
			name : link.source
		});
		link.target = nodes[link.target] || (nodes[link.target] = {
			name : link.target
		});
	});

	var width = 960, height = 500;

	var force = d3.layout.force().nodes(d3.values(nodes)).links(links).size([width, height]).linkDistance(100).charge(-600).on("tick", tick).start();

	var svg = d3.select(".visualisation").append("svg").attr("width", "100%").attr("height", height).call(d3.behavior.zoom().scaleExtent([1, 8]).on("zoom", zoom));

	var link = svg.selectAll(".link").data(force.links()).enter().append("line").attr("class", "link");

	var node = svg.selectAll(".node").data(force.nodes()).enter().append("g").attr("class", "node").on("mouseover", mouseover).on("mouseout", mouseout);

	var nodePath = d3.superformula().type("circle").size(125).segments(360);
	node.append("a").attr("xlink:href", "http://www.w3schools.com/svg/").append("path").attr("class", "path").attr("d", nodePath);

	node.append("a").attr("xlink:href", "http://www.w3schools.com/svg/").append("text").attr("x", 13).attr("dy", ".35em").text(function(d) {
		return d.name;
	});

	function tick() {
		link.attr("x1", function(d) {
			return d.source.x;
		}).attr("y1", function(d) {
			return d.source.y;
		}).attr("x2", function(d) {
			return d.target.x;
		}).attr("y2", function(d) {
			return d.target.y;
		});

		node.attr("transform", function(d) {
			return "translate(" + d.x + "," + d.y + ")";
		});
	}

	function mouseover() {
		var obj = this;
		d3.select(obj).select(".path").transition().duration(1500).style("fill", "#fff").style("stroke", "#555");
		timeOut = setTimeout(function() {
			d3.select(obj).select(".path").transition().duration(1500).attr("d", nodePath.type("rectangle").size(80000)).style("fill", "#fff").style("stroke", "#555");
		}, 1000);
	}

	function mouseout() {
		clearTimeout(timeOut);
		d3.select(this).select(".path").transition().duration(500).attr("d", nodePath.type("circle").size(125)).style("fill", "#555").style("stroke", "#fff");
	}

	// Events
	d3.select(".evenHandlers button.zoomIn").on("click", zoom);
	d3.select(".evenHandlers button.zoomOut").on("click", zoom);

	function zoom() {
		svg.transition().attr("transform", "translate(" + d3.event.translate + ")" + " scale(" + (d3.event.scale) + ")");
		//svg.transition().attr("-webkit-transform", "-webkit-translate(" + d3.event.translate + ")" + " scale(" + (d3.event.scale) + ")");
		//svg.transition().attr("transform", "translate(" + d3.event.translate + ")" + " scale(" + (d3.event.scale) + ")");
	}

});

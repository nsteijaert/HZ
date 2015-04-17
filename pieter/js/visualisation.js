/**
 * @author Pieter
 */
$(document).ready(function() {

	function load() {
		$('.visualisationResult').append('<div class="bar"></div>');
		$bar = $('.bar');
		$bar.animate({
			width : '100%'
		}, 1000, "swing", function() {

		});
	}

	if ($.cookie("depth") != "" && $.cookie("relations") != "")
		visualize("TZW:gezicht", $.cookie("depth"), $.cookie("relations"));
	else
		visualize("TZW:gezicht");

	function visualize(concept, depth, relations) {
		if ( typeof concept === 'undefined' || concept === '') {
			throw "Concept is undefined";
		}
		var depth = typeof depth !== 'undefined' ? depth : 1;
		var relations = typeof relations !== 'undefined' ? relations : "true,true";
		var timeOut;
		var mouseTimeOut;

		// Clear div
		d3.select(".visualisation").html("");

		var width = $('.visualisation').width(), height = 500;

		$.ajax({
			type : "POST",
			cache : false,
			url : "php/VisualisationScript.php",
			async : true,
			data : {
				concept : concept,
				depth : depth.toString(),
				relations : relationsx
			}
		}).done(function(data) {
			loadOptions(depth, relations);

			var links = JSON.parse(data);

			var nodes = {};
			
			console.log(nodes);
			
			$.each(nodes, function(key, value) {
				alert(key + ' ' + value);
			});

			// Compute the distinct nodes from the links.
			links.forEach(function(link) {
				link.source = nodes[link.source] || (nodes[link.source] = {
					name : link.source,
					url : link.urlsource
				});
				link.target = nodes[link.target] || (nodes[link.target] = {
					name : link.target,
					url : link.urltarget
				});
			});
			
			//console.log(links);

			//var width = 960, height = 500;
			center = [width / 2, height / 2];

			// Zoom functionality
			var zoom = d3.behavior.zoom().scaleExtent([1, 8]).on("zoom", zoomed);

			var force = d3.layout.force().nodes(d3.values(nodes)).links(links).size([width, height]).linkDistance(100).charge(-600).on("tick", tick).start();

			var svg = d3.select(".visualisation").append("svg").attr("width", "100%").attr("height", height).call(zoom).append("g");
			// Arrow
			svg.append("marker").attr("id", "arrow").attr("viewBox", "0 -5 10 10").attr("refX", "19").attr("refY", "0").attr("markerWidth", "6").attr("markerHeight", "6").attr("orient", "auto").append("path").attr("d", "M0,-5L10,0L0,5");

			var link = svg.selectAll(".link").data(force.links()).enter().append("line").attr("class", "link").attr("marker-end", "url(#arrow)");

			var node = svg.selectAll(".node").data(force.nodes()).enter().append("g").attr("class", "node").on("mouseover", mouseover).on("mouseleave", mouseleave);

			var nodePath = d3.superformula().type("circle").size(125).segments(360);
			node.append("a").attr("xlink:href", function(d) {
				return d.url;
			}).append("path").attr("class", "path").attr("d", nodePath);

			node.append("a").attr("xlink:href", function(d) {
				return d.url;
			}).append("text").attr("x", 13).attr("dy", ".35em").text(function(d) {
				return d.name;
			});

			// Events
			var pressed = false;
			d3.selectAll(".zoomHandlers button").on('mousedown', function() {
				pressed = true;
				zoomButton(d3.select(this).attr("class") === "zoomIn");
			}).on('mouseup', function() {
				pressed = false;
			}).on('mouseout', function() {
				pressed = false;
			});

			$(".optionsHandlers button.options").click(function() {
				$(".optionsHandlers #options").fadeIn('fast');
			});
			$(".optionsHandlers").mouseleave(function() {
				clearTimeout(mouseTimeOut);
			});
			$(".optionsHandlers").mouseleave(function() {
				mouseTimeOut = setTimeout(function() {
					$(".optionsHandlers #options").slideUp('fast');
					saveOptions(concept);
				}, 500);
			});

			// Functions
			//
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

			var isOpen = false;
			function mouseover() {
				if (!isOpen) {
					isOpen = true;
					var obj = this;
					d3.select(obj).select(".path").transition().duration(1000).style("fill", "#fff").style("stroke", "#555");
					d3.select(obj).moveToFront();
					timeOut = setTimeout(function() {
						d3.select(obj).select(".path").transition().duration(1000).attr("d", nodePath.type("rectangle").size(80000)).style("fill", "#fff").style("stroke", "#555").each("end", function() {
							d3.select(obj).append("foreignObject").attr("x", "-181px").attr("y", "-92px").attr("width", 363).attr("height", 183).append("xhtml:div").attr("class", "objcontext").html(function() {
								return '<h4><a href="#">Title</a></h4><table><tr><td>Beschrijving:</td><td>Beschrijving</td></tr></table><div class="links"><img src="http://www.jolwin.nl/wp-content/uploads/2013/02/logo-bibliotheek-150x150.png"/></div>';
							});
						});
						d3.select(obj).select("text").transition().duration(500).style("opacity", 0);
					}, 800);
				}
			}

			function mouseleave() {
				if (isOpen) {
					isOpen = false;
					clearTimeout(timeOut);

					d3.select(this).selectAll(function() {
						return this.getElementsByTagName("foreignObject");
					}).remove();
					d3.select(this).select(".path").transition().duration(500).attr("d", nodePath.type("circle").size(125)).style("fill", "#555").style("stroke", "#fff");
					d3.select(this).select("a > text").transition().duration(1000).style("opacity", 100);
				}
			}

			function zoomed() {
				//svg.attr("transform", "translate(" + d3.event.translate + ")" + " scale(" + (d3.event.scale) + ")");
				svg.attr("transform", "translate(" + zoom.translate() + ")scale(" + zoom.scale() + ")");
			}

			function loadOptions(depth, relations) {
				var currentDepth = depth;
				var currentRelations = relations.split(",");

				$("#options .depth").val(currentDepth);
				$("#options .broader").prop("checked", (currentRelations[0] === "true"));
				$("#options .narrower").prop("checked", (currentRelations[1] === "true"));
			}

			function saveOptions(concept) {
				var currentDepth = $.cookie("depth");
				var currentRelations = $.cookie("relations");

				var newDepth = $("#options .depth").val();
				var newRelations = $("#options .broader").is(":checked") + "," + $("#options .narrower").is(":checked");

				if (newDepth != currentDepth || newRelations != currentRelations) {
					$.cookie("depth", newDepth, {
						expires : 365
					});
					$.cookie("relations", newRelations, {
						expires : 365
					});
					visualize(concept, newDepth, newRelations);
				}
			}

			function zoomButton(zoom_in) {
				var scale = zoom.scale(), extent = zoom.scaleExtent(), translate = zoom.translate(), x = translate[0], y = translate[1], factor = zoom_in ? 1.3 : 1 / 1.3, target_scale = scale * factor;

				// If we're already at an extent, done
				if (target_scale === extent[0] || target_scale === extent[1]) {
					return false;
				}
				// If the factor is too much, scale it down to reach the extent exactly
				var clamped_target_scale = Math.max(extent[0], Math.min(extent[1], target_scale));
				if (clamped_target_scale != target_scale) {
					target_scale = clamped_target_scale;
					factor = target_scale / scale;
				}

				// Center each vector, stretch, then put back
				x = (x - center[0]) * factor + center[0];
				y = (y - center[1]) * factor + center[1];

				// Transition to the new view over 100ms
				d3.transition().duration(100).tween("zoom", function() {
					var interpolate_scale = d3.interpolate(scale, target_scale), interpolate_trans = d3.interpolate(translate, [x, y]);
					return function(t) {
						zoom.scale(interpolate_scale(t)).translate(interpolate_trans(t));
						zoomed();
					};
				}).each("end", function() {
					if (pressed)
						zoomButton(zoom_in);
				});
			}


			d3.selection.prototype.moveToFront = function() {
				return this.each(function() {
					this.parentNode.appendChild(this);
				});
			};

			d3.selection.prototype.first = function() {
				return d3.select(this[0][0]);
			};
		});
	}

	// Uncomment this to run the tests. It is unable to run the tests in a separate file.
	// QUnit.test("Visualisation", function(assert) {
	// assert.ok(function() {
	// visualize("TZW:hoofd");
	// }, "Passed!");
	// assert.ok(function() {
	// visualize("TZW:hoofd", "1");
	// }, "Passed!");
	// assert.ok(function() {
	// visualize("TZW:hoofd", "3", "true,false");
	// }, "Passed!");
	//
	// // Check if returns an exception.
	// assert.raises(function() {
	// visualize();
	// }, "Passed!");
	// });
});

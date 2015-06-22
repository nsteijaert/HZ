<?php
require_once(__DIR__.'/php-emont/Model.class.php');
require_once(__DIR__.'/Uri.class.php');

if($_GET['echo']==TRUE)
{
	$visualisatie=new Visualisatie($_GET['model_uri']);
	echo '<html><head></head><body>';
	echo '<script type="text/javascript" src="/mediawiki/resources/lib/jquery/jquery.js"></script>';
	echo $visualisatie->geefInhoud();
	echo '</body></html>';
}

Class Visualisatie
{
	private $inhoud='';

	public function __construct($model_uri)
	{
		//TODO NA: als ResourceLoader-modules toevoegen
		$this->inhoud.='<script type="text/javascript" src="/mediawiki/extensions/EMontVisualisator/includes/js/d3.v3.js"></script>';
		$this->inhoud.='<script type="text/javascript" src="/mediawiki/extensions/EMontVisualisator/includes/js/cola.v3.min.js"></script>';
		$this->inhoud.='<link rel="stylesheet" type="text/css" href="/mediawiki/extensions/EMontVisualisator/includes/css/visualisatie.css"></style>';

		//$domeinprefix='http://195.93.238.49/wiki/deltaexpertise/wiki/index.php/';
		$domeinprefix='http://127.0.0.1/mediawiki/index.php/';

		$context_uri=Model::geefContextVanModel($model_uri);

		$svgheight=2280;
		$nodeheight=30;
		$nodewidth=100;

		$visualisatie_id='visualisatie-'.Uri::stripSMWuriPadEnPrefixes($model_uri);

		$this->inhoud.='
		<svg id="'.$visualisatie_id.'" width="100%" height="'.$svgheight.'">
			<defs>
				<marker id="standaard" viewBox="0 -5 10 10" refX="10" refY="0" markerWidth="5" markerHeight="5" orient="auto">
		        	<path d="M0,-5L10,0L0,5"></path>
		    	</marker>
			</defs>
		</svg>
		<script type="text/javascript">

		function openInNewTab(url) {
			var win = window.open(url, \'_blank\');
			win.focus();
		}

		var graph;
		var width = $("#'.$visualisatie_id.'").width();
		var	height = $("#'.$visualisatie_id.'").height();

		// Haal de gegevens op
		$.ajax({
			type : "POST",
			cache : false,
			url : "/mediawiki/extensions/EMontVisualisator/includes/php/php-emont/VisualisationJSON.php",
			async : true,
			dataType: \'json\',
			data:{ context_uri: "'.$context_uri.'"},
			success: function(result) {
				graph=result;
				tekenDiagram();
			}
		});

		function tekenDiagram()
		{
			// Selecteer de visualisatie-container
		    var svg = d3.select(\'#'.$visualisatie_id.'\');

			console.log(width);
			console.log(height);

			var force = cola.d3adaptor()
		    	.linkDistance(120)
		    	.avoidOverlaps(true)
				.size([width, height])
		        .handleDisconnected(false)
		        .symmetricDiffLinkLengths(30)
		    	.nodes(graph.nodes)
		    	.links(graph.links)
		    	.constraints(graph.constraints)
		    	.groups(graph.groups);
		    var margin = 5, pad = 10;

			// Teken de pijlen
			force.on("tick", function () {
				node.each(function (d) {
		                d.innerBounds = d.bounds.inflate(- margin);
		            });
		        link.each(function (d) {
		                cola.vpsc.makeEdgeBetween(d, d.source.innerBounds, d.target.innerBounds, 0);
		            });

		        link.attr("x1", function (d) { return d.sourceIntersection.x; })
		            .attr("y1", function (d) { return d.sourceIntersection.y; })
		            .attr("x2", function (d) { return d.arrowStart.x; })
		            .attr("y2", function (d) { return d.arrowStart.y; });

		        linktooltip.each(function (d) {
		                cola.vpsc.makeEdgeBetween(d, d.source.innerBounds, d.target.innerBounds, 0);
		            });

		        linktooltip.attr("x1", function (d) { return d.sourceIntersection.x; })
		            .attr("y1", function (d) { return d.sourceIntersection.y; })
		            .attr("x2", function (d) { return d.arrowStart.x; })
		            .attr("y2", function (d) { return d.arrowStart.y; });

		        label.each(function (d) {
		            var b = this.getBBox();
		            d.width = b.width + 2 * pad;
		            d.height = b.height + 2 * pad;
		        });

				group.each(function (d) {
		            d.padding=25;
		        });

		        node.attr("x", function (d) { return d.innerBounds.x; })
		            .attr("y", function (d) { return d.innerBounds.y; })
		            .attr("width", function (d) { return d.innerBounds.width(); })
		            .attr("height", function (d) { return d.innerBounds.height(); });

		        group.attr("x", function (d) { return d.bounds.x+margin; })
		             .attr("y", function (d) { return d.bounds.y+margin; })
		            .attr("width", function (d) { return d.bounds.width()-pad;})
		            .attr("height", function (d) { return d.bounds.height()-pad;});

		        grouplabelrect.attr("x", function (d) { return d.bounds.x+margin; })
		             .attr("y", function (d) { return d.bounds.y+margin; })
		            .attr("width", function (d) { return d.bounds.width()-(2*margin);})
		            .attr("height", 50);

		        label.attr("transform", function (d) {
		            return "translate(" + (d.x - pad/2) + "," + (d.y + pad/1.5 - d.height/2) + ")";
		        });

		        grouplabel.attr("transform", function (d) {
		            return "translate(" + (d.bounds.x+pad) + "," + (d.bounds.y+(margin*4)) + ")";
		        });

		        grouplabelcliprect.attr("x",function (d) {return d.bounds.x+margin;})
		        				.attr("y",function (d) {return d.bounds.y;})
		        				.attr("width",function (d) { return d.bounds.width()-(2*margin);})
		        				.attr("height", 25);
			});

			// De force layout zet alles automatisch op zijn plek

			// Deze manier van aanroepen zorgt voor een oneindige lus bij kleine modellen (van bijv. 1 of 2 IE\'s), vandaar deze if-constructie.
			if(graph.nodes.length>10)
				force.start(80,160,100000);
			else
				force.start();

		    var group = svg.selectAll(".group")
		        .data(graph.groups)
		       .enter().append("rect")
		        .attr("rx", 10).attr("ry", 10)
		        .attr("class", "group")
		         .attr(\'width\','.($nodewidth+20).')
			    .attr(\'height\','.($nodeheight+20).');

		    var link = svg.selectAll(".link")
		        .data(graph.links)
		       .enter().append("line")
		        .attr("class", "link")
		        .attr("marker-end", "url(#standaard)");

		    var linktooltip = svg.selectAll(".linktooltip")
		        .data(graph.links)
		       .enter().append("line")
		        .attr("class","linktooltip")
		        .attr("title", function (d) { var title=d.type; if(d.extraInfo!=null){title=title+d.extraInfo}if(d.note!=null){title=title+"\n"+d.note}return title;});

		    var node = svg.selectAll(".node")
		         .data(graph.nodes)
		       .enter().append("rect")
		         .attr("class", function (d) {return "node node"+d.type})
		           .attr("rx", function (d) { if (d.type=="Condition" || d.type=="Goal" || d.type=="Belief") {return 40;}else{return 10;} })
		           .attr("ry", 10)
		           .attr(\'width\','.$nodewidth.')
			       .attr(\'height\','.$nodeheight.')
		         .call(force.drag);

			// Titels
		    var label = svg.selectAll(".label")
		        .data(graph.nodes)
		        .enter().append("text")
		         .attr("class", function (d) {return "label label"+d.type})
		         .text(function (d) { return d.name; })
		         .attr("title", function (d) { return d.heading;})
		         .on(\'dblclick\', function (d) { openInNewTab(\''.$domeinprefix.'\'+d.name+\' VN\');})
			     .call(force.drag);

			// Groeptitels
		    var grouplabel = svg.selectAll(".grouplabel")
		        .data(graph.groups)
		        .enter().append("g")
		        .attr("class", function (d) {return "grouplabel";})
		        .attr("style",function (d,i){return "clip-path: url(#clip"+i+");"})
		        .call(force.drag)
		        .append("text")
		         .attr("class", function (d) {return "grouplabeltext";})
		         .text(function (d) { return d.bijschrift; })
				 .attr("title", function (d) {return d.langbijschrift;});

		    var grouplabelrect = svg.selectAll(\'grouplabelrect\')
		    	.data(graph.groups)
		    	.enter().append("rect")
		         .attr("class", function (d) {return "grouplabelrect";})
		         .attr("style",function (d,i){return "clip-path: url(#clip"+i+");"})
		         .attr("rx", 10).attr("ry", 10)
				 .attr("title", function (d) {return d.langbijschrift;})
		         .call(force.drag);

			var grouplabelclip = svg.selectAll(\'.grouplabelclip\')
				.data(graph.groups)
				.enter().append("clipPath")
				 .attr("class", "grouplabelclip")
				 .attr("id",function (d,i) {return "clip"+i})
				 .attr("title", function (d) {return d.langbijschrift;})
				 .call(force.drag);

			var grouplabelcliprect = grouplabelclip.append("rect")
				 .attr("class","grouplabelcliprect")
				 .attr("title", function (d) {return d.langbijschrift;})
		         .attr("rx", 10).attr("ry", 10);

		    node.append("title")
		        .text(function (d) { return d.heading; });

		    var insertLinebreaks = function (d) {
		        var el = d3.select(this);
		        var words = d.name.split(\' \');
		        el.text(\'\');

				var rows=[\'\'];
				var row_number = 0;
		        for (var i = 0; i < words.length; i++) {
		        	if (rows[row_number].length>20)
		        	{
		        		rows.push(\'\');
		        		row_number++;
		        	}
		        	rows[row_number]=rows[row_number]+\' \'+words[i];
				}

				for (var i = 0; i < rows.length; i++) {
		            var tspan = el.append(\'tspan\').text(rows[i]);
		            tspan.attr(\'x\', margin).attr(\'dy\', 15)
		                 .attr("font-size", "12")
		                 .attr("style","fill:inherit;");
		        }
		    };
			label.each(insertLinebreaks);
		}
		</script>';
	}

	public function geefInhoud()
	{
		return $this->inhoud;
	}
}

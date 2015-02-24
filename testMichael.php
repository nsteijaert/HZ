<?php
/**
 * Testpagina voor EMont-parser.
 * @author: Michael Steenbeek
 */
require_once(__DIR__.'/php/php-emont/JSON_EMontParser.class.php');
require_once(__DIR__.'/php/SPARQLConnection.class.php');
?>
<!DOCTYPE html>
<html>
<head>
	<title>Testpagina voor EMont-parser</title>
	<meta charset='utf-8'>
</head>
<body>
<script src="js/jquery-2.1.3.js"></script>
<script src="js/d3.v3.js"></script>
<script src="js/cola.v3.min.js"></script>
<?php
$connectie=new SPARQLConnection();

//$context='Menselijk-2D_en_ecosysteem';
$context="Building_with_Nature-2Dinterventies_op_het_systeem";
//$context="B_en_O_Kust";
$context_uri='http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/'.$context;

echo 'Lijstje van IEs in context "'.JSON_EMontParser::decodeerSMWNaam($context);?>
<br /><a href="#geparset">Naar de geparsete gegevens</a><br />
<?php
$situatieparser=new JSON_EMontParser($context_uri);
$parse=$situatieparser->geefElementenInSituatie();
?>
<a name="geparset">Geparset:</a><br />
<pre>
<?php var_dump($parse); ?>
</pre>
<?php /*
Test van isSituatie: (moet 1.true en 2.false opleveren)
<pre>
<?php
var_dump(JSON_EMontParser::isSituatie("http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/B_en_O_Kust"));
var_dump(JSON_EMontParser::isSituatie("http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/B_en_O_Kus"));
?>
</pre>
Test van zoeken naar subrollen:
<pre>
<?php
var_dump(JSON_EMontParser::zoekSubrollen("http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Building_with_Nature-2Dinterventies_op_het_systeem"));
?>
</pre>
*/?>
<style>

.node {
    fill: #eee;
    stroke: #000;
    stroke-width: 1px;
    cursor: move;
}

.link {
    stroke: #777;
    stroke-width: 2px;
    marker-end:#standaard;
}

.group {
  stroke: #fff;
  stroke-width: 1.5px;
  cursor: move;
  opacity: 0.7;
  color:#00FF00;
}

.label {
  	font-size:10px;
	font-family:Arial,Helvetica,sans-serif;
	color:#000;
    /*text-anchor: middle;*/
    cursor: move;
}
</style>
<?php $svgheight=1280;$svgwidth=1600;$nodeheight=30;$nodewidth=100;?>

<svg id="visualisatie" width="<?php echo $svgwidth;?>" height="<?php echo $svgheight;?>">
	<defs>
		<marker id="standaard" viewBox="0 -5 10 10" refX="10" refY="0" markerWidth="6" markerHeight="6" orient="auto">
        	<path d="M0,-5L10,0L0,5"></path>
    	</marker>
	</defs>
</svg>
<div id="dump"></div>
<script type="text/javascript">
	var graph;

    var color = d3.scale.category20();
	console.log(1);
	
	//TODO: partOf-relaties niet links-rechts, maar boven/beneden

	// Haal de gegevens op
	$.ajax({
		type : "POST",
		cache : false,
		url : "php/php-emont/VisualisationJSON.php",
		async : false,
		dataType: 'json',
		data:{ context_uri: "<?php echo $context_uri;?>"},
		success: function(result) {
			console.log('1,5');
			graph=result;
			console.log(result);
			tekenDiagram();
		}
	});

	function tekenDiagram()
	{

		console.log(2);
		var width = <?php echo $svgwidth;?>,
	    	height = <?php echo $svgheight;?>;

		// Selecteer de visualisatie-container
	    var svg = d3.select('#visualisatie');
		console.log(3);

		console.trace();	
		var force = cola.d3adaptor()
	    	.linkDistance(120)
	    	.avoidOverlaps(true)
			.size([width, height])
			//.flowLayout('x', 150)
			//.jaccardLinkLengths(150)
	        .handleDisconnected(false)
	    	.nodes(graph.nodes)
	    	.links(graph.links)
	    	.constraints(graph.constraints)
	    	.groups(graph.groups);
		console.log(4);
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

	            label.each(function (d) {
	                var b = this.getBBox();
	                d.width = b.width + 2 * margin + 8;
	                d.height = b.height + 2 * margin + 8;
	            });

	            node.attr("x", function (d) { return d.innerBounds.x; })
	                .attr("y", function (d) { return d.innerBounds.y; })
	                .attr("width", function (d) { return d.innerBounds.width(); })
	                .attr("height", function (d) { return d.innerBounds.height(); });

	            group.attr("x", function (d) { return d.bounds.x; })
	                 .attr("y", function (d) { return d.bounds.y; })
	                .attr("width", function (d) { return d.bounds.width(); })
	                .attr("height", function (d) { return d.bounds.height(); });

	            label.attr("transform", function (d) {
	                return "translate(" + (d.x + margin - d.width/2) + "," + (d.y + margin - d.height/2) + ")";
	            });
		});

		// De force layout zet alles automatisch op zijn plek
		force.start();

	    var group = svg.selectAll(".group")
	        .data(graph.groups)
	       .enter().append("rect")
	        .attr("rx", 10).attr("ry", 10)
	        .attr("class", "group")
	        .attr('width',<?php echo $nodewidth;?>)
		    .attr('height',<?php echo $nodeheight;?>)
	        .style("fill", function (d, i) { return color(i); });

	    var link = svg.selectAll(".link")
	        .data(graph.links)
	       .enter().append("line")
	        .attr("class", "link")
	        .attr("marker-end", "url(#standaard)");

	    var node = svg.selectAll(".node")
	         .data(graph.nodes)
	       .enter().append("rect")
	         .attr("class", "node")
	           .attr("rx", 5).attr("ry", 5)
	           .attr('width',<?php echo $nodewidth;?>)
		       .attr('height',<?php echo $nodeheight;?>)
	         .call(force.drag);

		// Titels
	    var label = svg.selectAll(".label")
	        .data(graph.nodes)
	        .enter().append("text")
	         .attr("class", "label")
	         .text(function (d) { return d.heading; })
	         //.attr("x",0)
	    	 //.attr("y",0)
	    	 //.attr("dy", "1.0em")
		     .call(force.drag);

	    node.append("title")
	        .text(function (d) { return d.heading; });

	    var insertLinebreaks = function (d) {
	        var el = d3.select(this);
	        var words = d.heading.split(' ');
	        el.text('');

	        for (var i = 0; i < words.length; i++) {
	            var tspan = el.append('tspan').text(words[i]);
	            tspan.attr('x', margin).attr('dy', 15)
	                 .attr("font-size", "12");
	        }
	    };

	    label.each(insertLinebreaks);
	}
</script>
</body>
</html>
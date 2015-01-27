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
<script src="js/d3.min.js"></script>
<script src="js/jquery-2.1.1.min.js"></script>
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
    fill: #ccc;
    stroke: #fff;
    stroke-width: 1px;
}

.link {
    stroke: #777;
    stroke-width: 2px;
    marker-end:#standaard;
}

    </style>
<?php $svgheight=480; $svgwidth=640;?>

<svg id="visualisatie" width="<?php echo $svgwidth;?>" height="<?php echo $svgheight;?>">
	<defs>
		<marker id="standaard" viewBox="0 -5 10 10" refX="10" refY="0" markerWidth="6" markerHeight="6" orient="auto">
        	<path d="M0,-5L10,0L0,5"></path>
    	</marker>
	</defs>
</svg>
<script type="text/javascript">
	var theData;
	
	$.ajax({
		type : "POST",
		cache : true,
		url : "php/php-emont/JSON_EMontParser.php",
		async : true,
/*		data : {
			concept : concept,
			depth : depth.toString(),
			relations : relations
		}*/
		}).done(function(data) {
			thedata=data;
		});

	var width = <?php echo $svgwidth;?>,
    	height = <?php echo $svgheight;?>;

	// Here's were the code begins. We start off by creating an SVG
	// container to hold the visualization. We only need to specify
	// the dimensions for this container.

    var svg = d3.select('#visualisatie');

	// Before we do anything else, let's define the data for the visualization.

	var graph = {
    	"nodes": [  { "name": "A" },
                	{ "name": "B" },
            	    { "name": "C" }
        	    ],
    	"links": [  { "target":  1, "source":  0 },
                	{ "target":  2, "source":  0 }
            	]
    };
	var nodes = graph.nodes,
    	links = graph.links;

	var force = d3.layout.force()
		.size([width, height])
    	.nodes(nodes)
    	.links(links)
    	.linkDistance(110)
    	.charge(-450)
    	.on("tick",tick);

	// Okay, everything is set up now so it's time to turn
	// things over to the force layout. Here we go.

	force.start();
	
	//link.attr("marker-end", function(d) { return "url(#standaard)"; });

	// add the links and the arrows
	var path = svg.append("svg:g").selectAll("line")
    	.data(links)
    	.enter().append("svg:line")
    	.attr("class", "link")
    	.attr("marker-end", "url(#standaard)");

	// define the nodes
	var node = svg.selectAll(".node")
    	.data(nodes)
  		.enter().append("g")
    	.attr("class", "node")
    	.call(force.drag);

	// add the nodes
	node.append('rect')
	    .attr('width',75)
	    .attr('height',20);

	// add the text 
	node.append("text")
    .attr("x",12)
    .attr("y",0)
    .attr("dy", "1.0em")
    .text(function(d) { return d.name; });

	// add the curvy lines
	function tick() {
	    path.attr("x1", function(d) {
	        return d.source.x+75})
	    path.attr("y1", function(d) {
	        return d.source.y+10})
	    path.attr("x2", function(d) {
	        return d.target.x})
	    path.attr("y2", function(d) {
	        return d.target.y+10})
	
	    node.attr("transform", function(d) { 
	        return "translate(" + d.x + "," + d.y + ")"; });
	}




</script>
<div class="visualisation">
</div>
</body>
</html>
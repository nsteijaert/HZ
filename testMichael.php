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
<script src="js/jquery-2.1.1.min.js"></script>
<script src="js/d3.min.js"></script>
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
}

.link {
    stroke: #777;
    stroke-width: 2px;
    marker-end:#standaard;
}
text
{
	font-size:10px;
	font-family:Arial,Helvetica,sans-serif;
	color:#000;
}

    </style>
<?php $svgheight=1280;$svgwidth=1024;$nodeheight=30;$nodewidth=100;?>

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
	var nodes;
	var links;

	// Haal de gegevens op
	// Kan nog wat netter: asynchroon, waarbij gewacht wordt met tekenen
	$.ajax({
		type : "POST",
		cache : true,
		url : "php/php-emont/VisualisationJSON.php",
		async : false,
		dataType: 'json',
		success: function(data) {
			graph=data;
			console.log(data);
		}
	});

	var width = <?php echo $svgwidth;?>,
    	height = <?php echo $svgheight;?>;

	// Selecteer de visualisatie-container
    var svg = d3.select('#visualisatie');

	var nodes = graph.nodes,
    	links = graph.links;

	var force = cola.d3adaptor()
		.size([width, height])
    	.nodes(nodes)
    	.links(links)
    	.linkDistance(110)
    	.avoidOverlaps(true);

	// Teken de pijlen
	force.on("tick", function () {
	    path.attr("x1", function(d) {
	        return d.source.x+<?php echo $nodewidth;?>})
	    path.attr("y1", function(d) {
	        return d.source.y+<?php echo $nodeheight/2;?>})
	    path.attr("x2", function(d) {
	        return d.target.x})
	    path.attr("y2", function(d) {
	        return d.target.y+<?php echo $nodeheight/2;?>})
	
	    node.attr("transform", function(d) { 
	        return "translate(" + d.x + "," + d.y + ")"; });
	});

	// De force layout zet alles automatisch op zijn plek
	force.start();

	// Pijlen (lijnen + pijlpunten) definiëren
	var path = svg.append("svg:g").selectAll("line")
    	.data(links)
    	.enter().append("svg:line")
    	.attr("class", "link")
    	.attr("marker-end", "url(#standaard)");

	// Nodes definiëren
	var node = svg.selectAll(".node")
    	.data(nodes)
  		.enter().append("g")
    	.attr("class", "node")
    	.call(force.drag);

	// Nodes (IE's) tekenen
	node.append('rect')
	    .attr('width',<?php echo $nodewidth;?>)
	    .attr('height',<?php echo $nodeheight;?>);

	// Titels 
	node.append("text")
    .attr("x",0)
    .attr("y",0)
    .attr("dy", "1.0em")
    .text(function(d) { return d.heading; });


</script>
</div>
</body>
</html>
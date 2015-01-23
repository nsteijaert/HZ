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
	var theData = [ 1, 2, 3 ];
	var elementen = ['a','b','c'];
	//var links = [['a','b'],['b','c']];

	var width = <?php echo $svgwidth;?>,
    	height = <?php echo $svgheight;?>;

	// Here's were the code begins. We start off by creating an SVG
	// container to hold the visualization. We only need to specify
	// the dimensions for this container.

	/*var svg = d3.select('body').append('svg')
    	.attr('width', width)
    	.attr('height', height);*/
    var svg = d3.select('#visualisatie');

	// Before we do anything else, let's define the data for the visualization.

	var graph = {
    	"nodes": [  { "x": 100.0, "y": 200.0, "text": "A" },
                	{ "x": 200.0, "y": 100.0, "text": "B" },
            	    { "x": 200.0, "y": 300.0, "text": "C" }
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
    	.links(links);

	force.linkDistance(width/3.05);

	// Next we'll add the nodes and links to the visualization.
	// Note that we're just sticking them into the SVG container
	// at this point. We start with the links. The order here is
	// important because we want the nodes to appear "on top of"
	// the links. SVG doesn't really have a convenient equivalent
	// to HTML's `z-index`; instead it relies on the order of the
	// elements in the markup. By adding the nodes _after_ the
	// links we ensure that nodes appear on top of links.

	// Links are pretty simple. They're just SVG lines, and
	// we're not even going to specify their coordinates. (We'll
	// let the force layout take care of that.) Without any
	// coordinates, the lines won't even be visible, but the
	// markup will be sitting inside the SVG container ready
	// and waiting for the force layout.

	var link = svg.selectAll('.link')
	    .data(links)
	    .enter().append('line')
	    .attr('class', 'link');

	// Now it's the nodes turn. Each node is drawn as a rectangle.

	var node = svg.selectAll('.node')
	    .data(nodes)
	    .enter().append('rect')
	    .attr('class', 'node');

	// We're about to tell the force layout to start its
	// calculations. We do, however, want to know when those
	// calculations are complete, so before we kick things off
	// we'll define a function that we want the layout to call
	// once the calculations are done.

	force.on('end', function() {

		// When this function executes, the force layout
	    // calculations have concluded. The layout will
	    // have set various properties in our nodes and
	    // links objects that we can use to position them
	    // within the SVG container.

	    // First let's reposition the nodes. As the force
	    // layout runs it updates the `x` and `y` properties
	    // that define where the node should be centered.
	    // To move the node, we set the appropriate SVG
	    // attributes to their new values. Also give the
	    // nodes a non-zero radius so they're visible.

	    // Per-type markers, as they don't inherit styles.
	    /*svg.append("defs").selectAll("marker")
	    .data(["standaard"])
	    .enter().append("marker")
	    .attr("id", function(d) { return d; })
	    .attr("viewBox", "0 -5 10 10")
	    .attr("refX", "10")
	    .attr("refY", "0")
	    .attr("markerWidth", "6")
	    .attr("markerHeight", "6")
	    .attr("orient", "auto")
	    .append("path")
	    .attr("d", "M0,-5L10,0L0,5");*/

	    node.attr('x', function(d) { return d.x; })
	        .attr('y', function(d) { return d.y; })
	        .attr('width',75)
	        .attr('height',20);

	    // We also need to update positions of the links.
	    // For those elements, the force layout sets the
	    // `source` and `target` properties, specifying
	    // `x` and `y` values in each case.

	    link.attr('x1', function(d) { return d.source.x+(75); })
	        .attr('y1', function(d) { return d.source.y+(20/2); })
	        .attr('x2', function(d) { return d.target.x+(0); })
	        .attr('y2', function(d) { return d.target.y+(20/2); })
	        .attr("marker-end", function(d) { return "url(#standaard)"; });

	});

	// Okay, everything is set up now so it's time to turn
	// things over to the force layout. Here we go.

	force.start();

</script>
<div class="visualisation">
</div>
</body>
</html>
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
	<!-- Stylesheets van DeltaExpertise-->
	<!-- Aangepaste kopie van <link rel="stylesheet" href="http://195.93.238.49/wiki/deltaexpertise/wiki/load.php?debug=false&amp;lang=nl&amp;modules=mediawiki.legacy.commonPrint%2Cshared%7Cskins.deltaskin&amp;only=styles&amp;skin=deltaskin&amp;*" />
		Vervangen bij integreren in DeltaExpertise-->
	<link rel="stylesheet" href="css/dex1.css"/>
	<link rel="stylesheet" href="http://195.93.238.49/wiki/deltaexpertise/wiki/extensions/HeaderTabs/skins/ext.headertabs.large.css" />
	
	<style>
		body, svg {
			background-color:#eae4d7;
		}
		/* Standaard */
		.node {
		    fill: #eee;
		    stroke-width: 1px;
		    cursor: move;
		}
		.nodeActivity {
		    fill: #eae4d7;
		    stroke:#bfac88;
		}
		
		.nodeCondition {
			fill: #4c97d6;
		}
		
		.nodeBelief {
			fill: #4c97d6;
		}
		
		.nodeGoal {
			fill: #ffffff;
		}
		.nodeOutcome {
			fill: #ffffff;
		    stroke:#bfac88;
		}
		
		.link {
		    stroke:#bfac88;
		    stroke-width: 1px;
		    marker-end:#standaard;
		}

		.linktooltip
		{
			stroke:#222;
			stroke-opacity:0;
			stroke-width:7px;
		    cursor: help;
		}
		.linktooltip:hover {
			stroke-opacity:0.3;
		}
		
		.group {
		  stroke: #bfac88;
		  stroke-width: 1px;
		  fill: #eae4d7;
		  fill-opacity:0;
		  stroke-opacity:1;
		}
		
		.label {
		  	font-size:17px; /* 17px is 13 pt.*/
			font-family:Open Sans,Arial,Helvetica,sans-serif;
			fill:#222;
		    cursor: move;
		    text-anchor:middle;
		}
		.labelBelief {
			fill: #fff;
		}
		.labelGoal {
			fill:#4c97d6;
		}
		.labelOutcome {
			fill:#4c97d6;
		}
		
		marker#standaard {
			fill:#bfac88;
			stroke: #bfac88;
		}
	</style>
</head>
<!-- Start van gekopieerde DeltaExpertise-code. Verwijderen bij integratie. -->
<body class="mediawiki ltr sitedir-ltr ns-0 ns-subject page-Waterkering_VN skin-deltaskin action-view">
<div id="canvas">
<script src="js/jquery-2.1.3.js"></script>
<script src="js/d3.v3.js"></script>
<script src="js/cola.v3.min.js"></script>
	<div class="banner" style="background-image: url('http://195.93.238.49/wiki/deltaexpertise/wiki/skins/deltaskin/img/banners/banner-small-waterveiligheid.jpg');"><span></span></div>
	<header id="mainHeader" >
				
		<a href="#" id="toggleMenu" class="icon-menu"><span>Toon menu</span></a>
		
		<a href="#" id="toggleSearch" class="icon-menu"><span>Zoeken</span></a>
			
		<h1 id="idTag">
			<span title="DeltaExpertise - voor een leefbare delta">
		<a href='http://195.93.238.49/wiki/deltaexpertise/wiki/index.php/Home'>DeltaExpertise - voor een leefbare delta</a>					</span>
		</h1>
		
						
		<nav>
			<ul>
				<li ><a href="http://195.93.238.49/wiki/deltaexpertise/wiki/index.php/Home">Home</a></li>
				<li ><a href="http://195.93.238.49/wiki/deltaexpertise/wiki/index.php/Over%20ons">Over ons</a></li>
				<li ><a href="http://195.93.238.49/wiki/deltaexpertise/wiki/index.php/Help">Help</a></li>
				<li ><a href="http://195.93.238.49/wiki/deltaexpertise/wiki/index.php/Contact">Contact</a></li>
				<li ><a href="http://195.93.238.49/wiki/deltaexpertise/wiki/index.php/Sitemap">Sitemap</a></li>
				<li class="login"><a href="http://195.93.238.49/wiki/deltaexpertise/wiki/index.php/Speciaal:Aanmelden" data-icon="o" title="Aanmelden"><span>Aanmelden</span></a></li>
			</ul>
		</nav>
		<div id="searchBoxFront">
			<form action="http://195.93.238.49/wiki/deltaexpertise/wiki/index.php" id="searchform">
				<fieldset>
					<input type='hidden' name="title" value="Speciaal:Zoeken" />
					<input type="search" name="search" placeholder="Zoeken" title="Zoeken in DeltaExpertise - voor een leefbare delta [f]" accesskey="f" id="searchInput" autocomplete="off" />							<button data-icon="q"><span></span></button>
				</fieldset>
				<ul class="suggestions">
		        </ul>
			</form>
		</div>
	</header>
	<!--  //////////  BEGIN SECTION NAVIGATIE  ///////////  -->
	
	<div id="sectionNav">
		<nav>
			<a href="#" id="sectionNavButton">Secties</a>
			<ul>
				<li class="quadrant">
					<a href="#" class="icon-right-open-big"><span>Begrippen</span></a>
					<div class="navPanel">
					</div>
				</li>
				<li class="quadrant">
					<a href="#" class="icon-right-open-big"><span>Processen</span></a>
					<div class="navPanel">
					</div>
				</li>
				<li class="quadrant">
					<a href="#" class="icon-right-open-big"><span>Praktijk</span></a>
					<div class="navPanel">
					</div>
				</li>
			</ul>
		</nav>
	</div>
	<!--  //////////  END SECTION NAVIGATIE  ///////////  -->						

<div id="body">
<!-- Einde van de gekopieerde DeltaExpertise-code -->
<?php
$standaard_context_uri='emmwiki:Building_with_Nature-2Dinterventies_op_het_systeem';

$l1modellen=JSON_EMontParser::geefL1modellen();
$l2cases=JSON_EMontParser::geefL2cases();

echo '<form method="post" action="testMichael.php">L1: <select name="context">';

foreach ($l1modellen as $l1model)
{
	echo '<option ';
	if ($l1model->getUri()==$standaard_context_uri)
		echo 'selected="selected" ';
	echo 'value="'.$l1model->getUri().'">'.Uri::SMWuriNaarLeesbareTitel($l1model->getUri()).'</option>';
}
?>
</select><input type="submit" value="Opvragen" /></form>
<form method="post" action="testMichael.php">
L2: <select name="context">
<?php
foreach($l2cases as $l2case)
{
	echo '<option value="'.$l2case->getUri().'">'.Uri::SMWuriNaarLeesbareTitel($l2case->getUri()).'</option>';
}
?>
</select>
<input type="submit" value="Opvragen" />
</form>
<?php
if(!empty($_POST))
{
	$context_uri=$_POST['context'];
}
else
{
	$context_uri=$standaard_context_uri;
}

$connectie=new SPARQLConnection();

echo '<h1>Elementen uit de context: "'.Uri::SMWuriNaarLeesbareTitel($context_uri).'"</h1>';

$situatieparser=new JSON_EMontParser($context_uri);
$parse=$situatieparser->geefElementenInSituatie();
?>
<a href="#visualisatiekop">Spring naar de visualisatie</a><br />
<button onclick="document.getElementById('dump').style.display='block'">Dump tonen</button>
<div id="dump" style="display:none;">
<h2>Dump</h2>
<pre>
<?php var_dump($parse); ?>
</pre>
</div>

<?php $svgheight=1280;$svgwidth=1600;$nodeheight=30;$nodewidth=100;?>

<h2 id="visualisatiekop">Visualisatie</h2>
<svg id="visualisatie" width="100%" height="<?php echo $svgheight;?>">
	<defs>
		<marker id="standaard" viewBox="0 -5 10 10" refX="10" refY="0" markerWidth="5" markerHeight="5" orient="auto">
        	<path d="M0,-5L10,0L0,5"></path>
    	</marker>
	</defs>
</svg>
<script type="text/javascript">
/*function prefixReplace(url) {
}

function OpenInNewTab(url) {
	url = prefixReplace(url)
	var win = window.open(url, '_blank');
	win.focus();
}
*/

	var graph;
	
	// Haal de gegevens op
	$.ajax({
		type : "POST",
		cache : false,
		url : "php/php-emont/VisualisationJSON.php",
		async : false,
		dataType: 'json',
		data:{ context_uri: "<?php echo $context_uri;?>"},
		success: function(result) {
			graph=result;
			tekenDiagram();
		}
	});

	function tekenDiagram()
	{
		// Selecteer de visualisatie-container
	    var svg = d3.select('#visualisatie');

		var width = $("#visualisatie").width();
	    	height = $("#visualisatie").height();

		console.trace();	
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

            node.attr("x", function (d) { return d.innerBounds.x; })
                .attr("y", function (d) { return d.innerBounds.y; })
                .attr("width", function (d) { return d.innerBounds.width(); })
                .attr("height", function (d) { return d.innerBounds.height(); });

            group.attr("x", function (d) { return d.bounds.x+margin; })
                 .attr("y", function (d) { return d.bounds.y+margin; })
                .attr("width", function (d) { return d.bounds.width()-pad;})
                .attr("height", function (d) { return d.bounds.height()-pad;});

            //groupTitles.attr("x",function(d,i) {return group(i).bounds.x+10;});

            label.attr("transform", function (d) {
                return "translate(" + (d.x - pad/2) + "," + (d.y + pad/1.5 - d.height/2) + ")";
            });
		});

		// De force layout zet alles automatisch op zijn plek
		force.start(80,160,100000);
		//force.start();

	    var group = svg.selectAll(".group")
	        .data(graph.groups)
	       .enter().append("rect")
	        .attr("rx", 10).attr("ry", 10)
	        .attr("class", "group")
	        .attr('width',<?php echo $nodewidth;?>)
		    .attr('height',<?php echo $nodeheight;?>);

	    /*var groupTitles = svg.selectAll(".groupTitles")
	    	 .data(graph.groups)
	        .enter().append("rect")
     	     .attr("class", "groupTitles")
	         .attr('width',100)
	         .attr('height',15)
	         .style("fill","#ff0000");*/

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
	           .attr('width',<?php echo $nodewidth;?>)
		       .attr('height',<?php echo $nodeheight;?>)
	         .call(force.drag);

		// Titels
	    var label = svg.selectAll(".label")
	        .data(graph.nodes)
	        .enter().append("text")
	         .attr("class", function (d) {return "label label"+d.type})
	         .text(function (d) { return d.name; })
	         .attr("title", function (d) { return d.heading;})
		     .call(force.drag);

	    node.append("title")
	        .text(function (d) { return d.heading; });

	    var insertLinebreaks = function (d) {
	        var el = d3.select(this);
	        var words = d.name.split(' ');
	        el.text('');

			var rows=[''];
			var row_number = 0;
	        for (var i = 0; i < words.length; i++) {
	        	if (rows[row_number].length>20)
	        	{
	        		rows.push('');
	        		row_number++;
	        	}
	        	rows[row_number]=rows[row_number]+' '+words[i];
			}

			for (var i = 0; i < rows.length; i++) {      	 
	            var tspan = el.append('tspan').text(rows[i]);
	            tspan.attr('x', margin).attr('dy', 15)
	                 .attr("font-size", "12")
	                 .attr("style","fill:inherit;");
	        }
	    };
		label.each(insertLinebreaks);
	}
</script>
</div>
</div>
</body>
</html>
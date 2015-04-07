<?php
/**
 * Testpagina voor EMont-parser.
 * @author: Michael Steenbeek
 */
require_once(__DIR__.'/php/php-emont/JSON_EMontParser.class.php');
require_once(__DIR__.'/php/php-emont/Model.class.php');
require_once(__DIR__.'/php/SPARQLConnection.class.php');
require_once(__DIR__.'/php/Uri.class.php');

$standaard_model_uri='wiki:Building_with_Nature-2Dinterventies_op_het_systeem_practice';

if($par) {
	$model_uri='wiki:'.$pars[1];
}
elseif(!empty($_GET['model'])) {
	$model_uri=urldecode($_GET['model']);
}
elseif(!empty($_POST['model'])) {
	$model_uri=$_POST['model'];
}
else {
	$model_uri=$standaard_model_uri;
}

if($_POST['titel'])
{
	var_dump(Uri::geefIEtype('wiki:'.$_POST['ie']));
}

$domeinprefix='http://195.93.238.49/wiki/deltaexpertise/wiki/index.php/';
?>

	<style>
		/* Beter dan onzichtbaar */
		svg{
			overflow:visible;
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
<?php
//TODO NA: als ResourceLoader-modules toevoegen
echo '<script type="text/javascript" src="/mediawiki/extensions/EMontVisualisator/includes/js/d3.v3.js"></script>';
echo '<script type="text/javascript" src="/mediawiki/extensions/EMontVisualisator/includes/js/cola.v3.min.js"></script>';

$context_uri=Model::geefContextVanModel($model_uri);

$connectie=new SPARQLConnection();

$situatieparser=new JSON_EMontParser($context_uri);
$parse=$situatieparser->geefElementenInSituatie();

$svgheight=1280;
$svgwidth=1600;
$nodeheight=30;
$nodewidth=100;
?>

<h2 id="visualisatiekop">Visualisatie</h2>
<p>U kunt elementen verslepen om het overzicht te verbeteren. Dubbelklik op een element om de wikipagina ervan weer te geven.</p>
<svg id="visualisatie" width="100%" height="<?php echo $svgheight;?>">
	<defs>
		<marker id="standaard" viewBox="0 -5 10 10" refX="10" refY="0" markerWidth="5" markerHeight="5" orient="auto">
        	<path d="M0,-5L10,0L0,5"></path>
    	</marker>
	</defs>
</svg>
<script type="text/javascript">

function openInNewTab(url) {
	var win = window.open(url, '_blank');
	win.focus();
}

var graph;

// Haal de gegevens op
$.ajax({
	type : "POST",
	cache : false,
	url : "/mediawiki/extensions/EMontVisualisator/includes/php/php-emont/VisualisationJSON.php",
	async : true,
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
         .on('dblclick', function (d) { openInNewTab('<?php echo $domeinprefix.' ';?>'+d.name);})
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
<?php
if(Model::modelIsExperience($model_uri))
{
	$l1model=Model::geefL1modelVanCase($model_uri);
	$l1hoofdcontext=Model::geefContextVanModel($l1model);
	$subrollen=Model::zoekSubrollen($l1hoofdcontext);
	
	foreach(array_merge(array($l1hoofdcontext),$subrollen) as $te_doorzoeken_uri)
	{
		$alle_te_doorzoeken_uris[]=Uri::escape_uri($te_doorzoeken_uri);
	}
	
	$zoekstring=implode(' } UNION { ?ie property:Context ',$alle_te_doorzoeken_uris);
	
	$query_inhoud_situatie='DESCRIBE ?ie WHERE {{ ?ie property:Context '.$zoekstring.' }.{?ie rdf:type <http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Categorie-3AIntentional_Element>} UNION {?ie rdf:type <http://127.0.0.1/mediawiki/mediawiki/index.php/Speciaal:URIResolver/Categorie-3AActivity>}}';
	
	$data=$connectie->JSONQueryAsPHPArray($query_inhoud_situatie);
	echo '<form method="post">Beschikbare IE\'s (afkomstig van L1-model "'.Uri::SMWuriNaarLeesbareTitel($l1model).'"):<br /><select name="ie">';
	
	foreach($data['@graph'] as $item)
	{
		echo '<option value="'.Uri::stripSMWuriPadEnPrefixes($item['@id']).'">'.$item['label'].'</option>';
	}
	
	echo '</select><input type="text" name="titel"/><input type="submit" value="Aanmaken"/></form>';
}
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

$context_uri=Model::geefContextVanModel($model_uri);

if($_POST['titel'])
{
	Model::nieuwIE($_POST['ie'],$context_uri,$_POST['titel']);
}

if($_POST['van'])
{
	$eigenschappen=array();

	if($_POST['notitie'])
		$eigenschappen['Element link note']=$_POST['notitie'];
	if($_POST['type']=='Contributes')
		$eigenschappen['Element contribution value']=$_POST['subtype'];
	if($_POST['type']=='Connects')
		$eigenschappen['Element connection type']=$_POST['subtype'];

	Model::maakVerband($_POST['van'],$_POST['naar'],$_POST['type'],$eigenschappen);
}

if($_POST['verwijder-verband'])
{
	$waardes=explode('|',$_POST['verwijder-verband']);
	Model::verwijderVerband($waardes[0],$waardes[2],$waardes[1]);
}

//$domeinprefix='http://195.93.238.49/wiki/deltaexpertise/wiki/index.php/';
$domeinprefix='http://127.0.0.1/mediawiki/index.php/';
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

		.grouplabelrect
		{
			fill:#fff;
			fill-opacity:0.5;
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
            .attr("width", function (d) { return d.bounds.width()-margin;})
            .attr("height", 50);

        label.attr("transform", function (d) {
            return "translate(" + (d.x - pad/2) + "," + (d.y + pad/1.5 - d.height/2) + ")";
        });

        grouplabel.attr("transform", function (d) {
            return "translate(" + (d.bounds.x+margin+pad) + "," + (d.bounds.y+(margin*4)) + ")";
        });

        grouplabelcliprect.attr("x",function (d) {return d.bounds.x;})
        				.attr("y",function (d) {return d.bounds.y;})
        				.attr("width",function (d) { return d.bounds.width()-margin;})
        				.attr("height", 25);
	});

	// De force layout zet alles automatisch op zijn plek
	
	// Deze manier van aanroepen zorgt voor een oneindige lus bij kleine modellen (van bijv. 1 of 2 IE's), vandaar deze if-constructie.
	if(graph.nodes.length>10)
		force.start(80,160,100000);
	else
		force.start();

    var group = svg.selectAll(".group")
        .data(graph.groups)
       .enter().append("rect")
        .attr("rx", 10).attr("ry", 10)
        .attr("class", "group")
         .attr('width',<?php echo $nodewidth+20;?>)
	    .attr('height',<?php echo $nodeheight+20;?>);

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

	// Groeptitels
    var grouplabel = svg.selectAll(".grouplabel")
        .data(graph.groups)
        .enter().append("g")
        .attr("class", function (d) {return "grouplabel";})
        .attr("style",function (d,i){return "clip-path: url(#clip"+i+");"})
        .call(force.drag)
        .append("text")
         .attr("class", function (d) {return "grouplabeltext";})
         .text(function (d) { return d.bijschrift; });

    var grouplabelrect = svg.selectAll('grouplabelrect')
    	.data(graph.groups)
    	.enter().append("rect")
         .attr("class", function (d) {return "grouplabelrect";})
         .attr("style",function (d,i){return "clip-path: url(#clip"+i+");"})
         .attr("rx", 10).attr("ry", 10)
         .call(force.drag);

	var grouplabelclip = svg.selectAll('.grouplabelclip')
		.data(graph.groups)
		.enter().append("clipPath")
		 .attr("class", "grouplabelclip")
		 .attr("id",function (d,i) {return "clip"+i})
		 .call(force.drag)

	var grouplabelcliprect = grouplabelclip.append("rect")
		 .attr("class","grouplabelcliprect")
		 .attr("style",function (d,i){return "clip-path: url(#clip"+i+");"})
         .attr("rx", 10).attr("ry", 10);

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

	$data=Model::geefElementenUitContextEnSubcontexten($l1hoofdcontext);

	echo '<h2>Nieuw element</h2>';
	echo '<form method="post">Beschikbare IE\'s (afkomstig van L1-model "'.Uri::SMWuriNaarLeesbareTitel($l1model).'"):<br /><select name="ie">';

	foreach($data['@graph'] as $item)
	{
		echo '<option value="'.$item['@id'].'">'.$item['label'].'</option>';
	}

	echo '</select><br />Naam: <input type="text" style="width: 300px;" name="titel"/><input type="submit" value="Aanmaken"/></form>';

	$data=Model::geefElementenUitContextEnSubcontexten($context_uri);
	$ie_lijst='';
	$verbandenlijst=array();

	foreach($data['@graph'] as $item)
	{
		$ie_lijst.='<option value="'.$item['@id'].'">'.$item['label'].'</option>';
		$elementen=Model::elementenNaarArrays(Model::geefArtikelTekst($item['@id']));

		foreach($elementen as $element)
		{
			if($element['Element link'])
			{
				$verbandenlijst[]=array('van'=>$item['label'],'type'=>$element['type'],'naar'=>$element['Element link']);
			}
		}
	}

	echo '<h2>Nieuw verband aanbrengen</h2>';
	echo '<form method="post">';
	echo 'Van: <select name="van">'.$ie_lijst.'</select><br />';
	echo 'Naar: <select name="naar">'.$ie_lijst.'</select><br />';
	echo 'Type: <select name="type"><option value="Contributes">Contributes</option><option value="Depends">Depends</option><option value="Connects">Connects</option><option value="Produces">Produces</option><option value="Consumes">Consumes</option></select><br />';
	echo 'Notitie: <input name="notitie" type="text" style="width:300px;"><br />';
	echo 'CV/CT: <input name="subtype" type="text" style="width:300px;"/><br />';
	echo '<input type="submit" value="Aanmaken" /></form>';

	echo '<h2>Verband verwijderen</h2>';
	echo '<form method="post">';
	foreach($verbandenlijst as $verband)
	{
		echo '<input type="radio" name="verwijder-verband" value="'.$verband['van'].'|'.$verband['type'].'|'.$verband['naar'].'"/>'.$verband['van'].' '.$verband['type'].' '.$verband['naar'].'<br />';
	}
	echo '<input type="submit" value="Verwijderen"></form>';
}
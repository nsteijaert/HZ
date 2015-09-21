var nodewidth = 75;
var nodeheight = 20;
var margin = 5;
var pad = 10;
var graphs = [];

function startVisualisatie(visualisatieId, opTeVragenContextUri)
{
	var graph;

	// Haal de gegevens op
	$.ajax({
		type : "POST",
		cache : false,
		url : mw.config.get('wgExtensionAssetsPath')+"/EMontVisualisator/includes/php/php-emont/VisualisationJSON.php",
		async : true,
		dataType: 'json',
		data:{ context_uri: opTeVragenContextUri},
		success: function(result) {
			graph=result;
			tekenDiagram(visualisatieId, graph);
		}
	});
}

function maakTooltipZichtbaar()
{
	document.getElementById('elementTooltip').style.visibility='visible';
}

function maakTooltipOnzichtbaar()
{
	document.getElementById('elementTooltip').style.visibility='hidden';
}

function tekenTooltip(tekst,x,y)
{
	var tooltip=document.getElementById('elementTooltip');

	tooltip.innerHTML=tekst;
	tooltip.style.left=x+"px";
	tooltip.style.top=y+"px";
}

function tekenVerbandTooltip(d,x,y)
{
	var tekst=d.type;

	if(d.extraInfo!=null)
	{
		tekst+=d.extraInfo;
	}
	if(d.note!=null)
	{
		tekst+="<br />"+d.note;
	}

	tekenTooltip(tekst,x,y);
}

function tekenDiagram(visualisatieId, graph)
{
	var force = cola.d3adaptor().convergenceThreshold(0.1);

	var div = d3.select("#div-"+visualisatieId);
	var width = 3000;
	var height = 3000;

	var outer = div.append("svg")
		.attr({	id: visualisatieId,
			width: width,
			height: height
		});

	outer.append('svg:defs')
		.append('svg:marker')
        	.attr({
            	id: "standaard",
            	viewBox: "0 -5 10 10",
            	refX: 10,
            	refY: 0,
            	markerWidth: 5,
            	markerHeight: 5,
            	orient: 'auto'
        	})
      		.append('svg:path')
        		.attr({
            		d: "M0,-5L10,0L0,5"
				});

	// Selecteer de visualisatie-container
	var visualisatieIdMetHash = "#"+visualisatieId;
	var svg = d3.select(visualisatieIdMetHash);
	graphs[visualisatieId]=graph;

    force
    	.avoidOverlaps(true)
    	.flowLayout('x', 150)
		.jaccardLinkLengths(150)
		.size([width, height])
    	.nodes(graph.nodes)
    	.links(graph.links)
    	.constraints(graph.constraints)
    	.groups(graphs[visualisatieId].groups);



	// Teken de pijlen
	force.on("tick", function () {
		node.each(function (d) {
                d.innerBounds = d.bounds.inflate(- margin);
            });
        link.each(function (d) {
                cola.vpsc.makeEdgeBetween(d, d.source.innerBounds.inflate(-margin), d.target.innerBounds, 0);
            });

        link.attr("x1", function (d) { return d.sourceIntersection.x; })
            .attr("y1", function (d) { return d.sourceIntersection.y; })
            .attr("x2", function (d) { return d.arrowStart.x; })
            .attr("y2", function (d) { return d.arrowStart.y; });

      	link.attr("d", function (d) {
            cola.vpsc.makeEdgeBetween(d, d.source.innerBounds, d.target.innerBounds, 5);
        });

        linktooltip.each(function (d) {
                cola.vpsc.makeEdgeBetween(d, d.source.innerBounds.inflate(-margin), d.target.innerBounds, 0);
		});

        linktooltip.attr("x1", function (d) { return d.sourceIntersection.x; })
            .attr("y1", function (d) { return d.sourceIntersection.y; })
            .attr("x2", function (d) { return d.arrowStart.x; })
            .attr("y2", function (d) { return d.arrowStart.y; });

        label.each(function (d) {
            var b = this.getBBox();
            d.width = Math.max(nodewidth,b.width + 2 * pad);
            d.height = Math.max(nodeheight,b.height + 2 * pad);
        });

		group.each(function (d) {
            d.padding=25;
            d.bounds.width(Math.max(nodewidth+20,d.bounds.width()));
            d.bounds.height(Math.max(nodeheight+20,d.bounds.height()));
        });

        node.attr("x", function (d) { return d.innerBounds.x; })
            .attr("y", function (d) { return d.innerBounds.y; })
            .attr("width", function (d) { return Math.max(nodewidth,d.innerBounds.width()); })
            .attr("height", function (d) { return Math.max(nodeheight,d.innerBounds.height()); });

        group.attr("x", function (d) { return d.bounds.x+margin; })
            .attr("y", function (d) { return d.bounds.y+margin; })
            .attr("width", function (d) { return Math.max(nodewidth+20,d.bounds.width()-pad);})
            .attr("height", function (d) { return Math.max(nodeheight+20,d.bounds.height()-pad);});

        grouplabelrect.attr("x", function (d) { return d.bounds.x+margin; })
            .attr("y", function (d) { return d.bounds.y+margin; })
            .attr("width", function (d) { return Math.max(0,d.bounds.width()-(2*margin));})
            .attr("height", 50);

        label.attr("transform", function (d) {
            return "translate(" + (d.x - pad/2) + "," + (d.y + pad/1.5 - d.height/2) + ")";
        });

        grouplabel.attr("transform", function (d) {
            return "translate(" + (d.bounds.x+pad) + "," + (d.bounds.y+(margin*4)) + ")";
        });

        grouplabelcliprect.attr("x",function (d) {return d.bounds.x+margin;})
        	.attr("y",function (d) {return d.bounds.y;})
        	.attr("width",function (d) { return Math.max(nodewidth+20,d.bounds.width()-(2*margin));})
        	.attr("height", 25);

		adjustScrollbarsIfNecessary();

	});


	// De force layout zet alles automatisch op zijn plek

	// Deze manier van aanroepen zorgt voor een oneindige lus bij kleine modellen (van bijv. 1 of 2 IE's), vandaar deze if-constructie.
	if(graph.nodes.length>10)
		force.start(0,300,100000);
	else
		force.start();

    var group = svg.selectAll(".group")
        .data(graphs[visualisatieId].groups)
       .enter().append("rect")
        .attr("rx", 10).attr("ry", 10)
        .attr("class", "group")
        .attr('width',nodewidth+20)
	    .attr('height',nodeheight+20);

    var link = svg.selectAll(".link")
        .data(graph.links)
       .enter().append("line")
        .attr("class", "link")
        .attr("marker-end", "url(#standaard)");

	if(document.getElementById('elementTooltip')==null)
	{
		d3.select("body").append("div").attr("id", "elementTooltip");
	}

	// Mouseover en mouseout stellen de zichtbaarheid in van de tooltip,
	// Movemove zorgt ervoor dat de juiste informatie en positie in de tooltip terechtkomt,
	// en zorgt er tevens voor dat de tooltip meeverschuift met de muis.
    var linktooltip = svg.selectAll(".linktooltip")
        .data(graph.links)
       .enter().append("line")
        .attr("class","linktooltip")
        .on("mouseover", function (d) { maakTooltipZichtbaar(); })
		.on("mousemove", function (d) { tekenVerbandTooltip(d, d3.event.pageX+5, d3.event.pageY+5); })
		.on("mouseout", function (d) { maakTooltipOnzichtbaar(); });

    var node = svg.selectAll(".node")
         .data(graph.nodes)
       .enter().append("rect")
         .attr("class", function (d) {return "node node"+d.type;})
           .attr("rx", function (d) { if (d.type=="Condition" || d.type=="Goal" || d.type=="Belief") {return 40;}else{return 10;} })
           .attr("ry", 10)
           .attr('width',nodewidth)
	       .attr('height',nodeheight)
         .call(force.drag);

	// Titels
    var label = svg.selectAll(".label")
        .data(graph.nodes)
        .enter().append("text")
         .attr("class", function (d) {return "label label"+d.type;})
         .on('click', function (d) { setSelectedIE(d.uri, d.heading);})
         .on('dblclick', function (d) { openInNewTab(domeinprefix+d.name);})
	     .call(force.drag);

	// Groeptitels
    var grouplabel = svg.selectAll(".grouplabel")
        .data(graphs[visualisatieId].groups)
        .enter().append("g")
        .attr("class", function (d) {return "grouplabel";})
        .attr("style",function (d,i){return "clip-path: url(#"+visualisatieId+"-clip"+i+");";})
        .call(force.drag)
        .append("text")
         .attr("class", function (d) {return "grouplabeltext";})
         .text(function (d) { return d.bijschrift; });

    var grouplabelrect = svg.selectAll('grouplabelrect')
    	.data(graphs[visualisatieId].groups)
    	.enter().append("rect")
         .attr("class", function (d) {return "grouplabelrect";})
         .attr("style",function (d,i){return "clip-path: url(#"+visualisatieId+"-clip"+i+");";})
         .attr("rx", 10).attr("ry", 10)
         .call(force.drag)
   		  .on("mouseover", function (d) { maakTooltipZichtbaar(); })
		  .on("mousemove", function (d) { tekenTooltip(d.tooltip, d3.event.pageX+5, d3.event.pageY+5); })
		  .on("mouseout", function (d) { maakTooltipOnzichtbaar(); });


	var grouplabelclip = svg.selectAll('.grouplabelclip')
		.data(graphs[visualisatieId].groups)
		.enter().append("clipPath")
		 .attr("class", "grouplabelclip")
		 .attr("id",function (d,i) {return visualisatieId+"-clip"+i;})
		 .call(force.drag);

	var grouplabelcliprect = grouplabelclip.append("rect")
		 .attr("class","grouplabelcliprect")
         .attr("rx", 10).attr("ry", 10);

    var insertLinebreaks = function (d) {
        var el = d3.select(this);
        var words = d.heading.split(' ');
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

	force.on('end', adjustScrollbarsIfNecessary());


	function adjustScrollbarsIfNecessary()
	{
		adjustScrollbars(visualisatieId,false,2000);
	}
}

function verhelpOverlappendeNodes()
{
	var alleNodes = d3.select(visualisatieIdMetHash).selectAll(".node").selectAll(".rect");
	// nodeLocaties is een array met de y-coördinaat als index, met daarin een array met de x-coördinaat als index die
	// de nodes met dezelfde coördinaten bevat.
	var nodeLocaties = [];

	for(var i=0; i<alleNodes.length;i++)
	{
		var yValue = Math.floor(alleNodes[i].parentNode.y.animVal.value / 20 ) * 20;
		var xValue = Math.floor(alleNodes[i].parentNode.x.animVal.value / 100 ) * 100;

		if(!nodeLocaties[yValue])
			nodeLocaties[yValue]=[];

		if(!nodeLocaties[yValue][xValue])
			nodeLocaties[yValue][xValue]=[];

		nodeLocaties[yValue][xValue].push(i);
	}

	nodeLocaties.forEach(verwerkYwaarden);

	function verwerkYwaarden(xArray, y, array)
	{
		xArray.forEach(verwerkXwaarden);
	}

	function verwerkXwaarden(nodes, x, array)
	{
		if(nodes.length < 2)
		{
			return;
		}

		// Sla het eerste element over, aangezien dat geen tik hoeft te krijgen
		for(var i=1;i<nodes.length;i++)
		{
			console.log(nodes[i]);
		}
	}
}

function openInNewTab(url) {
	var win = window.open(url, '_blank');
	win.focus();
}
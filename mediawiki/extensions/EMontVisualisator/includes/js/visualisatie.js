var nodewidth = 30;
var nodeheight = 100;

function startVisualisatie(visualisatieId, opTeVragenContextUri)
{
	var graph;

	// Haal de gegevens op
	$.ajax({
		type : "POST",
		cache : false,
		url : "/mediawiki/extensions/EMontVisualisator/includes/php/php-emont/VisualisationJSON.php",
		async : true,
		dataType: 'json',
		data:{ context_uri: opTeVragenContextUri},
		success: function(result) {
			graph=result;
			tekenDiagram(visualisatieId, graph);
		}
	});
}

function tekenTooltipTitel(d)
{
	var title=d.type;
	if(d.extraInfo!=null)
	{
		title+=d.extraInfo;
	}
	if(d.note!=null)
	{
		title+="<br />"+d.note;
	}
	document.getElementById('elementTooltip').innerHTML=title;
}

function tekenDiagram(visualisatieId, graph)
{
	// Selecteer de visualisatie-container
    var svg = d3.select(visualisatieId);
	var width = $(visualisatieId).width();
	var	height = $(visualisatieId).height();

	console.log(width);
	console.log(height);

	var force = cola.d3adaptor()
    	.linkDistance(120)
    	.avoidOverlaps(true)
		.size([width, height])
        .handleDisconnected(false)
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
                cola.vpsc.makeEdgeBetween(d, d.source.innerBounds.inflate(-margin), d.target.innerBounds, 0);
            });

        link.attr("x1", function (d) { return d.sourceIntersection.x; })
            .attr("y1", function (d) { return d.sourceIntersection.y; })
            .attr("x2", function (d) { return d.arrowStart.x; })
            .attr("y2", function (d) { return d.arrowStart.y; });

      	link.attr("d", function (d) {
            cola.vpsc.makeEdgeBetween(d, d.source.innerBounds, d.target.innerBounds, 5);
            var lineData = [{ x: d.sourceIntersection.x, y: d.sourceIntersection.y }, { x: d.arrowStart.x, y: d.arrowStart.y }];
            return lineFunction(lineData);
        });
        if (isIE())
        {
        	link.each(function (d) { this.parentNode.insertBefore(this, this);});
        }

        linktooltip.each(function (d) {
                cola.vpsc.makeEdgeBetween(d, d.source.innerBounds.inflate(-margin), d.target.innerBounds, 0);
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
         .attr('width',nodewidth+20)
	    .attr('height',nodeheight+20);

    var link = svg.selectAll(".link")
        .data(graph.links)
       .enter().append("line")
        .attr("class", "link")
        .attr("marker-end", "url(#standaard)");

	var tooltip = d3.select("body")
	.append("div")
	.attr("class", "elementTooltip")
	.attr("id", "elementTooltip");

	// Mouseover en mouseout stellen de zichtbaarheid in van de tooltip,
	// Movemove zorgt ervoor dat de juiste informatie en positie in de tooltip terechtkomt,
	// en zorgt er tevens voor dat de tooltip meeverschuift met de muis.
    var linktooltip = svg.selectAll(".linktooltip")
        .data(graph.links)
       .enter().append("line")
        .attr("class","linktooltip")
        .on("mouseover", function() {return tooltip.style("visibility", "visible");})
		.on("mousemove", function (d) {tekenTooltipTitel(d);
			return tooltip.style("top", (d3.event.pageY+5)+"px").style("left",(d3.event.pageX+5)+"px");})
		.on("mouseout", function() {return tooltip.style("visibility", "hidden");});

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
         .text(function (d) { return d.name; })
         .attr("title", function (d) { return d.heading;})
         .on('dblclick', function (d) { openInNewTab(domeinprefix+d.name+' VN');})
	     .call(force.drag);

	// Groeptitels
    var grouplabel = svg.selectAll(".grouplabel")
        .data(graph.groups)
        .enter().append("g")
        .attr("class", function (d) {return "grouplabel";})
        .attr("style",function (d,i){return "clip-path: url(#clip"+i+");";})
        .call(force.drag)
        .append("text")
         .attr("class", function (d) {return "grouplabeltext";})
         .text(function (d) { return d.bijschrift; })
		 .attr("title", function (d) {return d.langbijschrift;});

    var grouplabelrect = svg.selectAll('grouplabelrect')
    	.data(graph.groups)
    	.enter().append("rect")
         .attr("class", function (d) {return "grouplabelrect";})
         .attr("style",function (d,i){return "clip-path: url(#clip"+i+");";})
         .attr("rx", 10).attr("ry", 10)
		 .attr("title", function (d) {return d.langbijschrift;})
         .call(force.drag);

	var grouplabelclip = svg.selectAll('.grouplabelclip')
		.data(graph.groups)
		.enter().append("clipPath")
		 .attr("class", "grouplabelclip")
		 .attr("id",function (d,i) {return "clip"+i;})
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

	var lineFunction = d3.svg.line()
        .x(function (d) { return d.x; })
        .y(function (d) { return d.y; })
        .interpolate("linear");

	var routeEdges = function () {
	    force.prepareEdgeRouting(margin / 3);
	    link.attr("d", function (d) { return lineFunction(force.routeEdge(d)); });
	    if (isIE()) link.each(function (d) { this.parentNode.insertBefore(this, this); });
	};

	//force.on("end", routeEdges);
}

function isIE()
{
	return navigator.appName == "Microsoft Internet Explorer";
}

function verhelpOverlappendeNodes()
{
	var alleNodes = d3.select(visualisatieId).selectAll(".node").selectAll(".rect");
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
var volgendeActie = null;
var popupVars = {};

function toggleL1modelDiv(zichtbaar)
{
	var l1modelDiv = document.getElementById('l1hover');

	if (zichtbaar == false)
	{
		l1modelDiv.style.visibility='hidden';
	}
	else
	{
		l1modelDiv.style.visibility='visible';
	}
}

function setSelectedIE(ie_uri, ie_heading)
{
	popupVars.instanceOf=ie_uri;
	var instanceBalk = document.getElementById('instanceBalk');
	instanceBalk.innerHTML = "<b>Geselecteerd IE: </b>"+ie_heading;
}

function createPopup(id)
{
	if(document.getElementById(id)==null)
	{
		d3.select("body").append("div").attr({id: id, class: "popup"});
	}

	var popup = d3.select('#'+id);
	popup.attr({class: "popup"});

	popup.append("div").attr({id: id+"-werkbalk", style: "display:flex; justify-content: space-between; align-items: center;"});
	var werkbalk=d3.select("#"+id+"-werkbalk");

	var linkerknoppen = werkbalk.append("div").attr({id: id+"-linkerknoppen"});
	var middenknoppen = werkbalk.append("div").attr({id: id+"-middenknoppen"});
	var rechterknoppen= werkbalk.append("div").attr({id: id+"-rechterknoppen"});

	linkerknoppen.append("button")
		.attr({id: "popup-sluitknop", title: "Sluiten", onclick: "verbergOfVerwijderPopup('"+id+"');"})
		.text("❌ Annuleer");
}

function createL1hoverPopup(secVisualisatieId)
{
	createPopup('l1hover');

	var popup = d3.select('#l1hover');
	popup.attr({class: "popup l1hover"});

	var middenknoppen = d3.select("#l1hover-middenknoppen");
	var rechterknoppen= d3.select("#l1hover-rechterknoppen");

	middenknoppen.append("input")
		.attr({id: "titel-nieuw-ie", type: "text", placeholder: "Naam nieuw Intentional Element", style: "width: 250px;"});

	rechterknoppen.append("button")
		.attr({id: "l1hover-opslagknop"})
		.text("➔ Volgende")
		.on("click", function() { nieuwIE_naarStap2();});

	popup.append("div").attr({id: "instanceBalk"});
	popup.append("div").attr({id: "div-"+secVisualisatieId, class: "modelembed"});
}

function verbergOfVerwijderPopup(id)
{
	if(id =='l1hover')
	{
		document.getElementById('l1hover').style.visibility="hidden";
	}
	else
	{
		d3.select('#'+id).remove();
	}
}

function nieuwIE_naarStap2()
{
	popupVars.titelNieuwIE = document.getElementById('titel-nieuw-ie').value;

	toggleL1modelDiv(false);
	createPopup('nieuwie-stap2');

	var popup = d3.select('#nieuwie-stap2');
	d3.select("#nieuwie-stap2-rechterknoppen").append("button")
		.attr({id: "l1hover-opslagknop"})
		.text("✓ Voeg toe")
		.on("click", function() { nieuwIE_finish();});

	popup.append('div').attr({'style': "text-align:center; margin-top:10px;"}).html('<h4>Kies in welke context u dit intentional element wilt plaatsen:</h4>');

	contextKeuzelijst('nieuwie-stap2');
}

function nieuweContextPopup()
{
	createPopup('nieuweContext');

	var popup = d3.select('#nieuweContext');
	var middenknoppen = d3.select("#nieuweContext-middenknoppen");
	var rechterknoppen= d3.select("#nieuweContext-rechterknoppen");

	middenknoppen.append("input")
		.attr({id: "titel-nieuwe-context", type: "text", placeholder: "Naam nieuwe context", style: "width: 250px;"});

	rechterknoppen.append("button")
		.attr({id: "nieuweContext-opslagknop"})
		.text("✓ Voeg toe")
		.on("click", function() { nieuweContext_finish();});

	popup.append('div').attr({'style': "text-align:center; margin-top:10px;"}).html('<h4>Kies een supercontext:</h4>');

	contextKeuzelijst('nieuweContext');
}

function contextKeuzelijst(div_id)
{
	var popup = d3.select('#'+div_id);
	var groepen=popup.append('div').attr({id: '#'+div_id+'-contextkeuze', class: "keuzelijstFrame"});

	gGraphs[visualisatieId].allgroups.forEach(
		function(d) {
			groepen.append('div')
				.attr({class: "keuzelijst"})
				.html(d.langbijschrift)
				.on("click", function () { popupVars.contextkeuze=d.uri; });
		}
	);

	$('.keuzelijst').hover(
    	function(){ $(this).addClass('keuzelijstHover'); },
    	function(){ $(this).removeClass('keuzelijstHover'); }
	);

	$('.keuzelijst').click(
		function(){ $('.keuzelijst').removeClass('keuzelijstSelected'); $(this).addClass('keuzelijstSelected');}
	);
}

function nieuwIE_finish()
{
	console.log(popupVars);
	var newnode={};
	newnode.name=popupVars.titelNieuwIE;
	newnode.heading=popupVars.titelNieuwIE;
	newnode.instanceof=popupVars.instanceOf;

	gGraphs[visualisatieId].nodes.push(newnode);
	gGraphs[visualisatieId].groups[0].leaves.push((gGraphs[visualisatieId].nodes.length)-1);
	setNodes(visualisatieId);

	verbergOfVerwijderPopup('nieuwie-stap2');

	mw.loader.using( 'mediawiki.api', function () {
		( new mw.Api() ).get( {
			action: 'EMVAI',
			actie: 'nieuw',
		 	type: 'ie',
		 	hoofdcontextUri: contextUri,
		 	context: popupVars.contextkeuze,
		 	titel: popupVars.titelNieuwIE,
		 	instanceOf: popupVars.instanceOf,
		} ).done( function() {

		} );
	} );
}

function nieuweContext_finish()
{
	var titel=document.getElementById('titel-nieuwe-context').value;
	var newgroup={};
	newgroup.leaves=[];
	newgroup.bijschrift=titel;
	newgroup.titel=titel;
	newgroup.tooltip=titel;

	gGraphs[visualisatieId].groups.push(newgroup);
	setGroups(visualisatieId);

	verbergOfVerwijderPopup('nieuweContext');

	mw.loader.using( 'mediawiki.api', function () {
		( new mw.Api() ).get( {
			action: 'EMVAI',
			actie: 'nieuw',
		 	type: 'context',
		 	hoofdcontextUri: contextUri,
		 	supercontext: popupVars.contextkeuze,
		 	titel: titel,
		} ).done( function() {

		} );
	} );
}

function adjustScrollbars(visualisatieId,force,timeout)
{
	setTimeout(function (data) {
		var d = gGraphs[visualisatieId].groups[0];
		var containerElement;
		var container = document.getElementById('div-'+visualisatieId);

		if(container.parentElement.id == "visualisatiepaginacontainer")
			containerElement=container.parentElement.id;
		else
			containerElement=container.id;

		if ((d.bounds.y>0 && 	(d.adjustedScrollbars == undefined && container.style.visibility!='hidden' ||
								$("#"+containerElement).scrollTop < 5 && $("#"+containerElement).scrollLeft < 5 ))
			|| force) {
			$("#"+containerElement).scrollTop(Math.max(0,d.bounds.y-25));
			$("#"+containerElement).scrollLeft(Math.max(0,d.bounds.x-25));
			gGraphs[visualisatieId].groups[0].adjustedScrollbars=true;
		}
	}, timeout);
}

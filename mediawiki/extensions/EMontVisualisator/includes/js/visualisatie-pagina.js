/**
 * Functies die met de pagina waarop de visualisatie draait te maken hebben.
 * @author Michael Steenbeek
 */

var volgendeActie = null;
var popupVars = {};

////////////
// Popups //
////////////

// Gedeelde functies
function setSelectedIE(ie_uri, ie_heading)
{
	popupVars.selectedIE=ie_uri;
	var instanceBalk = document.getElementById('instanceBalk');
	instanceBalk.innerHTML = "<b>Geselecteerd IE: </b>"+ie_heading;
}

function setLinkToAdd(linkNumber, uri)
{
	popupVars.linkNumber=linkNumber;
	popupVars.targetUri=uri;
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

function contextKeuzelijst(div_id)
{
	var popup = d3.select('#'+div_id);
	var groepen=popup.append('div').attr({id: '#'+div_id+'-contextkeuze', class: "keuzelijstFrame"});

	gGraphs[visualisatieId].groups.forEach(
		function(d) {
			groepen.append('div')
				.attr({class: "keuzelijst"})
				.html(d.langbijschrift)
				.on("click", function () { popupVars.contextkeuze=d.uri; });
		}
	);

	keuzelijstEffecten();
}

function keuzelijstEffecten()
{
	$('.keuzelijst').hover(
    	function(){ $(this).addClass('keuzelijstHover'); },
    	function(){ $(this).removeClass('keuzelijstHover'); }
	);

	$('.keuzelijst').click(
		function(){ $('.keuzelijst').removeClass('keuzelijstSelected'); $(this).addClass('keuzelijstSelected');}
	);
}

// Nieuw Intentional Element
function createL1hoverPopup(secVisualisatieId)
{
	createPopup('l1hover');

	var popup = d3.select('#l1hover');
	popup.attr({class: "popup l1hover"});

	var linkerknoppen = d3.select("#l1hover-linkerknoppen");
	var middenknoppen = d3.select("#l1hover-middenknoppen");
	var rechterknoppen= d3.select("#l1hover-rechterknoppen");

	linkerknoppen.append("button")
		.attr({title: "Naar hoofdcontext scrollen"})
		.html("⯐")
		.on("click", function() {adjustScrollbars(secVisualisatieId,true,0);});

	middenknoppen.append("input")
		.attr({id: "titel-nieuw-ie", type: "text", placeholder: "Naam nieuw Intentional Element", style: "width: 250px;"});

	rechterknoppen.append("button")
		.attr({id: "l1hover-opslagknop"})
		.text("➔ Volgende")
		.on("click", function() { nieuwIE_naarStap2();});

	popup.append("div").attr({style: "text-align:center;"}).html("<h3>Selecteer het te instantiëren Intentional Element:</h3>");
	popup.append("div").attr({id: "instanceBalk"});
	popup.append("div").attr({id: "div-"+secVisualisatieId, class: "modelembed"});
}

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

function nieuwIE_finish()
{
	instanceOfId=findNodeByUri(secVisualisatieId,popupVars.selectedIE);
	instanceOfNode=gGraphs[secVisualisatieId].nodes[instanceOfId];
	groupId=findGroupByUri(visualisatieId,popupVars.contextkeuze);

	var newnode={};
	newnode.name=popupVars.titelNieuwIE;
	newnode.heading=popupVars.titelNieuwIE;
	newnode.instanceof=popupVars.selectedIE;
	newnode.type=instanceOfNode.type;
	newnode.decompositionType=instanceOfNode.decompositionType;

	gGraphs[visualisatieId].nodes.push(newnode);
	gGraphs[visualisatieId].groups[groupId].leaves.push(newnode); //(gGraphs[visualisatieId].nodes.length)-1);

	redrawAfterChange(visualisatieId);
	verbergOfVerwijderPopup('nieuwie-stap2');

	mw.loader.using( 'mediawiki.api', function () {
		( new mw.Api() ).get( {
			action: 'EMVAI',
			actie: 'nieuw',
		 	type: 'ie',
		 	hoofdcontextUri: contextUri,
		 	context: popupVars.contextkeuze,
		 	titel: popupVars.titelNieuwIE,
		 	instanceOf: popupVars.selectedIE,
		 	ie_type: instanceOfNode.type,
		 	ie_decomposition_type: instanceOfNode.decompositionType
		} ).done( function() {

		} );
	} );
}

// Nieuwe Context
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

function nieuweContext_finish()
{
	var titel=document.getElementById('titel-nieuwe-context').value;
	var newgroup={};
	newgroup.leaves=[];
	newgroup.bijschrift=titel;
	newgroup.titel=titel;
	newgroup.tooltip=titel;

	gGraphs[visualisatieId].groups.push(newgroup);

	redrawAfterChange(visualisatieId);
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

// Nieuw verband
function nieuwVerbandPopup()
{
	createPopup('nieuwVerband');

	var popup = d3.select('#nieuwVerband');
	var middenknoppen= d3.select("#nieuwVerband-middenknoppen");
	var rechterknoppen= d3.select("#nieuwVerband-rechterknoppen");

	middenknoppen.append("input")
		.attr({id: "nieuwVerband-notitie", type: "text", placeholder: "Notitie (optioneel)", style: "width: 250px;"});

	rechterknoppen.append("button")
		.attr({id: "nieuwVerband-opslagknop"})
		.text("✓ Voeg toe")
		.on("click", function () { nieuwVerband_finish(); });

	// In het corresponderende L1-model is vastgelegd welke geldige verbindingsmogelijkheden er zijn.
	// Hiervoor zoeken we dus het corresponderde L1-IE op, en vragen daarvan een lijst op van mogelijke
	// verbindingen op L1-niveau. Hierna bepalen welke L2-elementen deze L1-elementen implementeren.
	var selectedIEId=findNodeByUri(visualisatieId, popupVars.selectedIE);
	var instanceOf=gGraphs[visualisatieId].nodes[selectedIEId].instanceOf;
	var instanceOfId=findNodeByUri(secVisualisatieId,instanceOf);

	popupVars.links=findLinksFromNodeId(secVisualisatieId, instanceOfId);

	eligibleL2nodes=[];
	gGraphs[visualisatieId].nodes.forEach(function(nodeElement, nodeIndex, nodeArray) {
		popupVars.links.forEach(function(linkElement, linkIndex, linkArray) {
			if(nodeElement.instanceOf==gGraphs[secVisualisatieId].nodes[linkElement.target].uri)
			{
				var nodeEntry={};
				nodeEntry.uri=nodeElement.uri;
				nodeEntry.type=linkElement.type;
				nodeEntry.heading=nodeElement.heading;
				nodeEntry.linkNumber=linkIndex;

				if(linkElement.type=='Connects')
					nodeEntry.extraInfolinkElement.connectionType+", "+linkElement.linkCondition;
				else if(linkElement.type=='Contributes')
					nodeEntry.extraInfo=linkElement.contributionValue;
				else
					nodeEntry.extraInfo="";

				eligibleL2nodes.push(nodeEntry);
			}
		});
	});

	popup.append('div').attr({style: "text-align:center; margin-top:10px;"}).html('<h4>Mogelijke verbanden voor '+gGraphs[visualisatieId].nodes[selectedIEId].heading+':</h4>');

	var verbindingen=popup.append('div').attr({class: "keuzelijstFrame"});

	eligibleL2nodes.forEach(function(element, index, array) {
		verbindingen.append('div')
			.attr({class: "keuzelijst", onclick: "setLinkToAdd("+element.linkNumber+",'"+element.uri+"');"})
			.html(getFriendlyDescriptionForLink(element.type,element.extraInfo)+" "+element.heading);
	});
	keuzelijstEffecten();
}

function nieuwVerband_finish()
{
	var sourceId=findNodeByUri(visualisatieId,popupVars.selectedIE);
	var targetId=findNodeByUri(visualisatieId,popupVars.targetUri);
	var sourceNode=gGraphs[visualisatieId].nodes[sourceId];
	var targetNode=gGraphs[visualisatieId].nodes[targetId];
	var notitie=$("#nieuwVerband-notitie").val();

	var newlink={};
	newlink.source=sourceNode;
	newlink.target=targetNode;
	newlink.type=popupVars.links[popupVars.linkNumber].type;
	newlink.note=notitie;
	newlink.extraInfo="";
	newlink.contributionValue="";
	newlink.connectionType="";
	newlink.linkCondition="";

	if(newlink.type=='Contributes') {
		newlink.contributionValue=popupVars.links[popupVars.linkNumber].contributionValue;
		newlink.extraInfo=': '+newlink.contributionValue;
	}
	else if(newlink.type=='Connects')
	{
		newlink.connectionType = popupVars.links[popupVars.linkNumber].connectionType;
		newlink.linkCondition = opupVars.links[popupVars.linkNumber].linkCondition;
		newlink.extraInfo=': '+newlink.connectionType+' '+newlink.linkCondition;
	}

	gGraphs[visualisatieId].links.push(newlink);

	redrawAfterChange(visualisatieId);
	verbergOfVerwijderPopup('nieuwVerband');

	mw.loader.using( 'mediawiki.api', function () {
	( new mw.Api() ).get( {
		action: 'EMVAI',
		actie: 'maakverband',
	 	type: 'ie',
	 	hoofdcontextUri: contextUri,
	 	van: popupVars.selectedIE,
	 	naar: popupVars.targetUri,
	 	linkType: newlink.type,
	 	notitie: notitie,
	 	contributionValue: newlink.contributionValue,
	 	connectionType: newlink.connectionType,
	 	linkCondition: newlink.linkCondition
	} ).done( function() {

	} );
} );
}

//////////////////////////
// Overige knopfuncties //
//////////////////////////
function openInNewTab(url) {
	var win = window.open(url, '_blank');
	win.focus();
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

//////////////
// Tooltips //
//////////////
function maakTooltipZichtbaar()
{
	if(document.getElementById('elementTooltip')==null)
	{
		d3.select("body").append("div").attr("id", "elementTooltip");
	}

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

function getFriendlyDescriptionForLink(linkType,extraInfo)
{
	switch(linkType)
	{
		case 'Depends':
			return 'Afhankelijk van';
		case 'Contributes':
			return 'Draagt bij ('+extraInfo+') aan';
		case 'Connects':
			return 'Verbindt ('+extraInfo+') met';
		case 'Produces':
			return 'Produceert';
		case 'Consumes':
			return 'Consumeert';
		case 'Part of':
			return 'Onderdeel van';
		default:
			return linkType;
	}
}

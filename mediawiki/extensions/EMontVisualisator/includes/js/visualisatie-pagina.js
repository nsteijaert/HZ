var standaardSVGhoogte=1280;

function toggleL1modelDiv()
{
	var l1modelDiv=document.getElementById('div-'+secVisualisatieId);
	if (l1modelDiv.style.visibility=='visible')
	{
		l1modelDiv.style.visibility='hidden';
	}
	else
	{
		l1modelDiv.style.visibility='visible';
	}
}
<?php
/**
 * Dit bestand bevat PHP-code om de visualisatie te laten werken binnen een gesimuleerde DeltaExpertise.nl-omgeving.
 * Bij integratie met DeltaExpertise.nl moet deze code worden verwijderd.
 * @author: Michael Steenbeek
 */

function toonDexPrePagina()
{
	?>
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

	<?php
}

function toonDexPostPagina()
{
	// Twee divjes die in de prepagina geopend werden.
	?>
	</div>
</div>
	<?php
}

function toonBroodkruimels($kruimels)
{
	if(!empty($kruimels))
	{
		echo '<div id="breadcrumb"><ul>';
		foreach ($kruimels as $kruimel)
		{
			echo '<li><a href="'.$kruimel['url'].'" title="'.$kruimel['titel'].'">'.$kruimel['titel'].'</a></li>';
		}
		// Afsluitende guillemets
		echo '<li></li>';
		echo '</ul></div>';
	}
}

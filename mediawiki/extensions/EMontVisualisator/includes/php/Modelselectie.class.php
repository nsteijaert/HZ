<?php
require_once(__DIR__.'/php-emont/Model.class.php');
require_once(__DIR__.'/Uri.class.php');

/* Manipulatiecode */
if($_POST['titel']!=null)
{
	$practice_uri=$_POST['l1model'];
	$titel=$_POST['titel'];
	Model::nieuweL2case($titel,$practice_uri);
}
/* Einde manipulatiecode */

class Modelselectie
{
	private $inhoud='';

	public function __construct()
	{
		$l1modellen=Model::geefL1modellen();
		$l2cases=Model::geefL2cases();
		$urlpad=rtrim($_SERVER['REQUEST_URI'],'/');

		$this->inhoud.='<h2 id="practices">Practices (L1)</h2><ul>';

		foreach ($l1modellen as $l1uri => $l1beschrijving)
		{
			$this->inhoud.='<li><a href="'.$urlpad.'/toon/'.Uri::stripSMWuriPadEnPrefixes($l1uri).'">'.$l1beschrijving.'</a></li>';
		}

		$this->inhoud.='</ul><h2 id="experiences">Experiences (L2, cases)</h2><ul>';

		foreach($l2cases as $l2uri => $l2beschrijving)
		{
			$this->inhoud.='<li><a href="'.$urlpad.'/toon/'.Uri::stripSMWuriPadEnPrefixes($l2uri).'">'.$l2beschrijving.'</a></li>';
		}

		$this->inhoud.='
		</ul>
		<h2>Nieuwe experience aanmaken</h2>
		<form method="post">
			<table>
				<tr><td style="width: 150px;">Gebaseerd op:</td><td>
			<select style="width:350px;" name="l1model">';

			foreach ($l1modellen as $l1uri => $l1beschrijving)
			{
				$this->inhoud.='<option value="'.$l1uri.'">'.$l1beschrijving.'</option>';
			}

		$this->inhoud.='
			</select></td></tr>
			<tr><td>Naam:</td><td><input style="width:342px;" type="text" name="titel" /></td></tr>
			<tr><td colspan="100%"><input type="submit" value="Aanmaken" /></td></tr>
			</table>
		</form>';
	}

	public function geefInhoud()
	{
		return $this->inhoud;
	}
}

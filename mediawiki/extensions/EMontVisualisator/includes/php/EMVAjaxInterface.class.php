<?php
/**
 * Lijm tussen visualisatie en back-end.
 * @author: Michael Steenbeek
 */
require_once(__DIR__.'/php-emont/Model.class.php');
require_once(__DIR__.'/Uri.class.php');

class EMVAjaxInterface extends ApiBase
{
	public function execute()
	{
		$type=$_POST['type'];
		$actie=$_POST['actie'];
		$naamprefix=Uri::SMWuriNaarLeesbareTitel($_POST['hoofdcontextUri']);

		if($type=='context')
		{
			if($actie=='nieuw')
			{
				$naam=$naamprefix.' '.$_POST['naam-nieuwe-context'];
				$supercontext_uri=$_POST['supercontext'];

				Model::nieuweContext($naam);
				Model::nieuweVN($naam.' VN','Context',$naam);
				Model::extraSupercontext($naam,$supercontext_uri);
			}
			elseif($actie=='extrasupercontext')
			{
				$context=$_POST['context'];
				$supercontext=$_POST['supercontext'];

				if($context!=$supercontext)
				{
					Model::extraSupercontext($context,$supercontext);
				}
			}
			elseif($actie=='supercontextverwijderen')
			{
				list($context,$supercontext)=explode('|',$_POST['verwijder-supercontexten']);

				Model::supercontextVerwijderen($context,$supercontext);
			}
		}
		elseif($type=='ie')
		{
			if($actie=='contexttoevoegen')
			{
				$ie=$_POST['ie'];
				$context=$_POST['context'];

				Model::contextToevoegenAanIE($ie,$context);
			}
			elseif($actie=='nieuw')
			{
				$naam=$_POST['titel'];
				Model::nieuwIE($_POST['instanceOf'],$_POST['context'],$naam,$naamprefix);
				Model::nieuweVN($naam.' VN','Intentional Element',$naamprefix.' '.$naam);
			}
			elseif($actie=='maakverband')
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
			elseif($actie=='verwijderverband')
			{
				$waardes=explode('|',$_POST['verwijder-verband']);
				Model::verwijderVerband($waardes[0],$waardes[2],$waardes[1]);
			}
		}

		$this->getResult()->addValue(null, $this->getModuleName(), "OK");
		return true;
	}
}
?>
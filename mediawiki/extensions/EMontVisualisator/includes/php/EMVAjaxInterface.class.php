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
		$type = $this->getParameter('type');
		$actie = $this->getParameter('actie');
		$naamprefix = Uri::SMWuriNaarLeesbareTitel($this->getParameter('hoofdcontextUri'));
		$return="";

		if ($type == 'context')
		{
			if ($actie == 'nieuw')
			{
				$naam = $naamprefix . ' ' . $this->getParameter('titel');
				$supercontext_uri = $this->getParameter('supercontext');

				Model::nieuweContext($naam);
				Model::nieuweVN($naam . ' VN', 'Context', $naam);
				Model::extraSupercontext($naam, $supercontext_uri);
			}
			elseif ($actie == 'extrasupercontext')
			{
				$context = $params['context'];
				$supercontext = $params['supercontext'];

				if ($context != $supercontext)
				{
					Model::extraSupercontext($context, $supercontext);
				}
			}
			elseif ($actie == 'supercontextverwijderen')
			{
				list($context, $supercontext) = explode('|', $params['verwijder-supercontexten']);

				Model::supercontextVerwijderen($context, $supercontext);
			}
		}
		elseif ($type == 'ie')
		{
			if ($actie == 'contexttoevoegen')
			{
				$ie = $params['ie'];
				$context = $params['context'];

				Model::contextToevoegenAanIE($ie, $context);
			}
			elseif ($actie == 'nieuw')
			{
				$naam = $this->getParameter('titel');
				Model::nieuwIE($this->getParameter('instanceOf'), $this->getParameter('context'), $naam, $naamprefix);
				Model::nieuweVN($naam .' VN', 'Intentional Element', $naamprefix.' '.$naam);
			}
			elseif ($actie == 'maakverband')
			{
				$eigenschappen = array();
				$linkType=ucfirst($this->getParameter('linkType'));

				if ($this->getParameter('notitie'))
					$eigenschappen['Element link note'] = $this->getParameter('notitie');
				if ($linkType == 'Contributes')
					$eigenschappen['Element contribution value'] = $this->getParameter('contributionValue');
				if ($linkType == 'Connects') {
					$eigenschappen['Element connection type'] = $this->getParameter('connectionType');
					$eigenschappen['Element link condition'] = $this->getParameter('linkCondition');
				}

				Model::maakVerband($this->getParameter('van'), $this->getParameter('naar'), $linkType, $eigenschappen);
			}
			elseif ($actie == 'verwijderverband')
			{
				$waardes = explode('|', $params['verwijder-verband']);
				Model::verwijderVerband($waardes[0], $waardes[2], $waardes[1]);
			}
		}
		elseif ($actie=='naamNaarUri') {
			$naam=$this->getParameter('naam');
			$return='wiki:'.Uri::codeerSMWNaam($naamprefix.' '.$naam);
		}

		/*$formattedData = array();
		$result = $this->getResult();
		$result->setIndexedTagName($formattedData, 'p');
		$result->addValue(null, $this->getModuleName(), $formattedData);*/
		$this->getResult()->addValue(null, $this->getModuleName(), $return);
	}
}
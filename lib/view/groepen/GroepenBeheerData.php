<?php

namespace CsrDelft\view\groepen;

use CsrDelft\model\entity\groepen\GroepStatus;
use CsrDelft\view\formulier\datatable\DataTableResponse;

class GroepenBeheerData extends DataTableResponse {

	public function getJson($groep) {
		$array = $groep->jsonSerialize();

		$array['detailSource'] = $groep->getUrl() . 'leden';

		$title = $groep->naam;
		if (!empty($groep->samenvatting)) {
			$title .= '&#13;&#13;' . mb_substr($groep->samenvatting, 0, 100);
			if (strlen($groep->samenvatting) > 100) {
				$title .= '...';
			}
		}
		$array['naam'] = '<span title="' . $title . '">' . $groep->naam . '</span>';
		$array['status'] = GroepStatus::getChar($groep->status);
		$array['samenvatting'] = null;
		$array['omschrijving'] = null;
		$array['website'] = null;
		$array['maker_uid'] = null;

		if (property_exists($groep, 'in_agenda')) {
			$array['in_agenda'] = $groep->in_agenda ? 'ja' : 'nee';
		}

		return parent::getJson($array);
	}

}

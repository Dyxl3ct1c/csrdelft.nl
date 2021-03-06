<?php

namespace CsrDelft\model\groepen;

use CsrDelft\model\AbstractGroepenModel;
use CsrDelft\model\entity\groepen\Commissie;
use CsrDelft\model\entity\groepen\CommissieSoort;

class CommissiesModel extends AbstractGroepenModel {

	const ORM = Commissie::class;

	public function nieuw($soort = null) {
		if (!in_array($soort, CommissieSoort::getTypeOptions())) {
			$soort = CommissieSoort::Commissie;
		}
		$commissie = parent::nieuw();
		$commissie->soort = $soort;
		return $commissie;
	}

}

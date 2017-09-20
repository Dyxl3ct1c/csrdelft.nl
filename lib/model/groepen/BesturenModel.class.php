<?php
namespace CsrDelft\model\groepen;

use CsrDelft\model\AbstractGroepenModel;
use CsrDelft\model\entity\groepen\Bestuur;

class BesturenModel extends AbstractGroepenModel {

	const ORM = Bestuur::class;

	protected static $instance;

	public function nieuw() {
		$bestuur = parent::nieuw();
		$bestuur->bijbeltekst = '';
		return $bestuur;
	}

}

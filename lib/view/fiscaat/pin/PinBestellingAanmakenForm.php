<?php

namespace CsrDelft\view\fiscaat\pin;

use CsrDelft\common\CsrGebruikerException;
use CsrDelft\model\entity\fiscaat\pin\PinTransactieMatch;
use CsrDelft\view\formulier\getalvelden\RequiredIntField;
use CsrDelft\view\formulier\invoervelden\RequiredLidField;
use CsrDelft\view\formulier\knoppen\FormDefaultKnoppen;
use CsrDelft\view\formulier\ModalForm;

/**
 * @author G.J.W. Oolbekkink <g.j.w.oolbekkink@gmail.com>
 * @since 24/02/2018
 */
class PinBestellingAanmakenForm extends ModalForm {
	/**
	 * @param PinTransactieMatch|null $pinTransactieMatch
	 * @throws CsrGebruikerException
	 */
	public function __construct($pinTransactieMatch = null) {
		parent::__construct([], '/fiscaat/pin/aanmaken', 'Voeg een bestelling toe.', true);

		$fields[] = new RequiredLidField('uid', null, 'Lid');
		$fields['pinTransactieId'] = new RequiredIntField('pinTransactieId', $pinTransactieMatch ? $pinTransactieMatch->id : null, 'Pin Transactie Id');
		$fields['pinTransactieId']->hidden = true;

		$this->addFields($fields);

		$this->formKnoppen = new FormDefaultKnoppen();
	}
}

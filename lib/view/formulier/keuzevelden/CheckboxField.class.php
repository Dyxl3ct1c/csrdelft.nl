<?php

namespace CsrDelft\view\formulier\keuzevelden;

use CsrDelft\model\security\LoginModel;
use CsrDelft\view\formulier\invoervelden\InputField;

/**
 * CheckboxField.class.php
 *
 * @author Jan Pieter Waagmeester <jieter@jpwaag.com>
 * @author P.W.G. Brussee <brussee@live.nl>
 * @author G.J.W. Oolbekkink <g.j.w.oolbekkink@gmail.com>
 * @date 30/03/2017
 *
 * @Warning: NEVER use for persistence!
 */
class CheckboxField extends InputField {

	public $type = 'checkbox';
	public $label;

	public function __construct($name, $value, $description, $label = null, $model = null) {
		parent::__construct($name, $value, $description, $model);
		$this->label = $label;
	}

	/**
	 * Speciaal geval:
	 * Veld is gepost = dit veld zit in POST
	 *                OF: iets is gepost maar niet dit veld.
	 *
	 * Uitzondering voor DataTable id & selection.
	 *
	 * @return boolean
	 */
	public function isPosted() {
		if (parent::isPosted()) {
			return true;
		}
		return !empty($_POST);
	}

	/**
	 * Speciaal geval:
	 * Uitgevinkt = niet gepost.
	 *
	 * @return boolean
	 */
	public function getValue() {
	    if ($this->isPosted()) {
            $this->value = parent::isPosted();
		}
		return $this->value;
	}

	public function validate() {
		if (!$this->value AND $this->required) {
			if ($this->leden_mod AND LoginModel::mag('P_LEDEN_MOD')) {
				// exception for leden mod
			} else {
				$this->error = 'Dit is een verplicht veld';
			}
		}
		return $this->error === '';
	}

	public function getHtml() {
		$html = '<input ' . $this->getInputAttribute(array('type', 'id', 'name', 'value', 'origvalue', 'class', 'disabled', 'readonly'));
		if ($this->value) {
			$html .= ' checked="checked" ';
		}
		$html .= '/>';

		if (!empty($this->label)) {
			$html .= '<label for="' . $this->getId() . '" class="CheckboxFieldLabel">' . $this->label . '</label>';
		}
		return $html;
	}

}

<?php

namespace CsrDelft\view\formulier\invoervelden;
/**
 * WachtwoordField.class.php
 *
 * @author P.W.G. Brussee <brussee@live.nl>
 * @author G.J.W. Oolbekkink <g.j.w.oolbekkink@gmail.com>
 * @date 30/03/2017
 */
class WachtwoordField extends TextField {

	public $type = 'password';
	public $enter_submit = true;

	// Override TextField getValue as passwords do not need to be sanitised here
	public function getValue() {
		if ($this->isPosted()) {
			$this->value = $_POST[$this->name];
		} else {
			$this->value = false;
		}
		if ($this->empty_null AND $this->value == '') {
			return null;
		}
		return $this->value;
	}

}

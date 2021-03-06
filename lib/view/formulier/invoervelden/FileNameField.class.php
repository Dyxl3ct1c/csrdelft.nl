<?php

namespace CsrDelft\view\formulier\invoervelden;

/**
 * FileNameField.class.php
 *
 * @author P.W.G. Brussee <brussee@live.nl>
 * @author G.J.W. Oolbekkink <g.j.w.oolbekkink@gmail.com>
 * @date 30/03/2017
 */
class FileNameField extends TextField {

	public function validate() {
		if (!parent::validate()) {
			return false;
		}
		if ($this->value !== '' AND !valid_filename($this->value)) {
			$this->error = 'Ongeldige bestandsnaam';
		}
		return $this->error === '';
	}

}

<?php

/**
 * MaaltijdForm.class.php
 * 
 * @author P.W.G. Brussee <brussee@live.nl>
 *
 * Formulier voor een nieuwe of te bewerken maaltijd.
 * 
 */
class MaaltijdForm extends ModalForm {

	public function __construct(Maaltijd $maaltijd, $action) {
		parent::__construct($maaltijd, '/maaltijden/beheer/'. $action, false, true);

		$product = CiviProductModel::instance()->findSparse(array('beschrijving'), 'id = ?', array($maaltijd->product_id))->fetch();
		if ($product == false) {
			$product = new CiviProduct();
		}

		if ($maaltijd->maaltijd_id < 0) {
			throw new Exception('invalid mid');
		}
		if ($maaltijd->maaltijd_id == 0) {
			$this->titel = 'Maaltijd aanmaken';
		} else {
			$this->titel = 'Maaltijd wijzigen';
			$this->css_classes[] = 'PreventUnchanged';
		}

		$fields['mrid'] = new IntField('mlt_repetitie_id', $maaltijd->mlt_repetitie_id, null);
		$fields['mrid']->readonly = true;
		$fields['mrid']->hidden = true;
		$fields[] = new RequiredTextField('titel', $maaltijd->titel, 'Titel', 255, 5);
		$fields[] = new RequiredDateField('datum', $maaltijd->datum, 'Datum', date('Y') + 2, date('Y') - 2);
		$fields[] = new RequiredTimeField('tijd', $maaltijd->tijd, 'Tijd', 15);
		$fields[] = new RequiredEntityField('product', 'beschrijving', 'Product', CiviProductModel::instance(), '/fiscaat/producten/suggesties?q=', $product);
		$fields[] = new FormulierKnop('/fiscaat/producten', 'redirect', 'Nieuw product', 'Nieuw product aanmaken', '');
		$fields[] = new RequiredIntField('aanmeld_limiet', $maaltijd->aanmeld_limiet, 'Aanmeldlimiet', 0, 200);
		$fields[] = new RechtenField('aanmeld_filter', $maaltijd->aanmeld_filter, 'Aanmeldrestrictie');
		$fields[] = new BBCodeField('omschrijving', $maaltijd->omschrijving, 'Omschrijving');
		$fields[] = new FormDefaultKnoppen();

		$this->addFields($fields);
	}

}

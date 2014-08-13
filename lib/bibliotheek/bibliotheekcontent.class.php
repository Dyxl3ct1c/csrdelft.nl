<?php

require_once 'catalogus.class.php';

/**
 * bibliotheekcontent.class.php
 * 
 * @author Gerrit Uitslag <klapinklapin@gmail.com>
 *
 */
class BibliotheekCatalogusContent extends TemplateView {

	public function __construct() {
		parent::__construct(null, 'Bibliotheek | Catalogus');
	}

	public function view() {
		$this->smarty->display('bibliotheek/catalogus.tpl');
	}

}

class BibliotheekCatalogusDatatableContent extends TemplateView {

	public function __construct(Catalogus $catalogus) {
		parent::__construct($catalogus);
	}

	public function view() {
		/*
		 * Output
		 */
		$output = array(
			"sEcho"					 => intval(filter_input(INPUT_GET, 'sEcho', FILTER_SANITIZE_NUMBER_INT)),
			"iTotalRecords"			 => $this->model->getTotaal(),
			"iTotalDisplayRecords"	 => $this->model->getGefilterdTotaal(),
			"aaData"				 => array()
		);

		//kolommen van de dataTable
		$aKolommen = $this->model->getKolommen();
		//Vult de array aaData met htmlcontent. Entries van aaData corresponderen met tabelcellen.
		foreach ($this->model->getBoeken() as $aBoek) {
			$boek = array();
			//loopt over de zichtbare kolommen
			for ($i = 0; $i < $this->model->getKolommenZichtbaar(); $i++) {
				//van sommige kolommen wordt de inhoud verfraaid
				switch ($aKolommen[$i]) {
					case 'titel':
						$boek[] = $this->render_titel($aBoek);
						break;
					case 'eigenaar':
					case 'lener':
						$boek[] = $this->render_lidlink($aBoek, $aKolommen[$i]);
						break;
					case 'leningen':
						$boek[] = str_replace(', ', '<br />', $aBoek['leningen']);
						break;
					case 'uitleendatum':
						$boek[] = $this->render_uitleendatum($aBoek);
						break;
					default:
						$boek[] = htmlspecialchars($aBoek[$aKolommen[$i]]);
				}
			}
			$output['aaData'][] = $boek;
		}

		echo json_encode($output);
	}

	/*
	 * methodes om htmlinhoud van cellen te maken
	 */

	// Geeft html voor titel-celinhoud
	protected function render_titel($aBoek) {
		//urltitle
		$urltitle = 'title="Boek: ' . $aBoek['titel'] . '
Auteur: ' . $aBoek['auteur'] . ' 
Rubriek: ' . $aBoek['categorie'] . '"';
		//url
		if (LoginModel::mag('P_BIEB_READ')) {
			$titel = '<a href="/communicatie/bibliotheek/boek/' . $aBoek['id'] . '" ' . $urltitle . '>'
					. htmlspecialchars($aBoek['titel'])
					. '</a>';
		} else {
			$titel = htmlspecialchars($aBoek['titel']);
		}
		return $titel;
	}

	//Geeft html voor lener- of eigenaar-celinhoud
	protected function render_lidlink($aBoek, $key) {
		$aUid = explode(', ', $aBoek[$key]);
		$sNaamlijst = '';
		foreach ($aUid as $uid) {
			if ($uid == 'x222') {
				$sNaamlijst .= 'C.S.R.-bibliotheek';
			} else {
				$naam = Lid::naamLink($uid, 'civitas', 'visitekaartje');
				if ($naam) {
					$sNaamlijst .= $naam;
				} else {
					$sNaamlijst .= '-';
				}
			}
			$sNaamlijst .= '<br />';
		}
		return $sNaamlijst;
	}

	//Geeft html voor status-celinhoud
	protected function render_uitleendatum($aBoek) {
		$aStatus = explode(', ', $aBoek['status']);
		$aUitleendatum = explode(', ', $aBoek['uitleendatum']);
		$sUitleendatalijst = '';
		$j = 0;
		foreach ($aUitleendatum as $uitleendatum) {
			//title met omschrijvingstatus
			switch ($aStatus[$j]) {
				case 'uitgeleend':
					$sUitleendatalijst .= '<span title="Uitgeleend sinds ' . strip_tags(reldate($uitleendatum)) . '">';
					break;
				case 'teruggegeven':
					$sUitleendatalijst .= '<span title="Teruggegeven door lener. Uitgeleend sinds ' . strip_tags(reldate($uitleendatum)) . '">';
					break;
				case 'vermist':
					$sUitleendatalijst .= '<span title="Vermist sinds ' . strip_tags(reldate($uitleendatum)) . '">';
					break;
				default:
					$sUitleendatalijst .= '<span title="Exemplaar is beschikbaar">';
			}
			//indicator
			$sUitleendatalijst .= '<span class="biebindicator ' . $aStatus[$j] . '">• </span>';
			//datum
			if ($aStatus[$j] == 'uitgeleend' OR $aStatus[$j] == 'teruggegeven' OR $aStatus[$j] == 'vermist') {
				$sUitleendatalijst .= strftime("%d %b %Y", strtotime($uitleendatum));
			}
			$sUitleendatalijst .= '</span><br />';
			$j++;
		}
		return $sUitleendatalijst;
	}

}

/**
 * Boek weergeven
 */
class BibliotheekBoekContent extends TemplateView {

	public function __construct(Boek $boek) {
		parent::__construct($boek, 'Bibliotheek | Boek: ' . $boek->getTitel());
		$this->smarty->assign('boek', $this->model);
	}

	public function view() {
		$this->smarty->display('bibliotheek/boek.tpl');
	}

}

/**
 * Contentclasse voor de boek-ubb-tag
 */
class BoekUbbContent extends TemplateView {

	public function __construct(Boek $boek) {
		parent::__construct($boek);
		$this->smarty->assign('boek', $this->model);
	}

	public function view() {
		return $this->smarty->fetch('bibliotheek/boek.ubb.tpl');
	}

}

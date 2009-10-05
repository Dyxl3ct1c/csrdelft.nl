<?php

# C.S.R. Delft
# -------------------------------------------------------------------
# maaltijden/class.corveebeheercontent.php
# -------------------------------------------------------------------
# Toevoegen en bewerken van maaltijden
# -------------------------------------------------------------------


require_once ('maaltijden/class.maaltrack.php');

class CorveebeheerContent extends SimpleHTML {

	private $_maaltrack;
	private $_maaltijd=null;
	private $_actie=null;
	private $_filter=1;
	
	private $_error='';

	function __construct($maaltrack) {
		$this->_maaltrack=$maaltrack;
	}
	function getTitel(){ return 'Maaltijdketzer - Corveebeheer'; }

	//functie om een maaltijd in het formulier te laden, normaal gewoon een formulier voor nieuwe maaltijden.
	function load($iMaalID, $actie = null, $filter = 1){
		$iMaalID=(int)$iMaalID;
		$this->_actie=$actie;
		$this->_maaltijd=$this->_maaltrack->getMaaltijd($iMaalID);
		$this->_filter=$filter;
	}
	function addError($error){ $this->_error=$error; }

	function view(){
		$loginlid=LoginLid::instance();
		//de html template in elkaar draaien en weergeven
		$corveebeheer=new Smarty_csr();
		$corveebeheer->caching=false;

		//Dingen ophalen voor het overzicht van maaltijden...
		$aMaal['error']=$this->_maaltrack->getError();
		$aMaal['maaltijden']=$this->_maaltrack->getMaaltijden(time()-3600*24*28, time()+3600*24*100, false, false);

		// bewerken
		$aForm=$this->_maaltijd;
		
		if ($this->_actie == 'bewerk') {
			$aForm['actie']='bewerk';
			
			# als er een error gegeven wordt, is er hoogstwaarschijnlijk wat mis gegaan bij het bewerken of toevoegen
			# van een nieuwe maaltijd. Daarom kijken we hier of er nog zinnige invoer uit de post te halen valt.
			if($this->_error!=''){
				if(isset($_POST['koks'])){  $aForm['koks']=(int)$_POST['koks']; }
				if(isset($_POST['afwassers'])){  $aForm['afwassers']=(int)$_POST['afwassers']; }
				if(isset($_POST['theedoeken'])){  $aForm['theedoeken']=(int)$_POST['theedoeken']; }
				if(isset($_POST['punten_kok'])){  $aForm['punten_kok']=(int)$_POST['punten_kok']; }
				if(isset($_POST['punten_afwas'])){  $aForm['punten_afwas']=(int)$_POST['punten_afwas']; }
				if(isset($_POST['punten_theedoek'])){  $aForm['punten_theedoek']=(int)$_POST['punten_theedoek']; }
			}
		} elseif ($this->_actie == 'takenbewerk') {
			$aForm['actie']='takenbewerk';			
			$dag = date('D',$aForm['datum']);
			$aForm['filter']=$this->_filter;
			if($this->_filter == 0 || $aForm['datum'] <= time()){
				//Oude maaltijden
				$aForm['kwalikoks']=$this->_maaltrack->getTaakLeden();
				$aForm['kokleden']=$this->_maaltrack->getTaakLeden();
				$aForm['afwasleden']=$this->_maaltrack->getTaakLeden();
				$aForm['theedoekleden']=$this->_maaltrack->getTaakLeden();
				$aForm['pt_opties']=array(
					'onbekend' => 'Onbekend',
					'ja' => 'Ja',
					'nee' => 'Nee'
				);
				print_r($aForm['toegekend']);				
			} else {
				//Toekomstige maaltijden
				$aForm['kwalikoks']=$this->_maaltrack->getTaakLedenGefilterd('kwalikok',$dag,0);
				$aForm['kokleden']=$this->_maaltrack->getTaakLedenGefilterd('kok',$dag,0);
				$aForm['afwasleden']=$this->_maaltrack->getTaakLedenGefilterd('afwas',$dag,0);
				$aForm['theedoekleden']=$this->_maaltrack->getTaakLedenGefilterd('theedoek',$dag,0);
			}
						
		}
		$aMaal['formulier']=$aForm;

		//arrays toewijzen en weergeven
		$corveebeheer->assign('maal', $aMaal);
		$corveebeheer->assign('toonLijsten', $loginlid->hasPermission('P_MAAL_MOD') or opConfide());
		$corveebeheer->assign('datumFormaat', '%a %e %b %H:%M');
		$corveebeheer->assign('datumFormaatInvoer', '%Y-%m-%d %H:%M');
		if($this->_error!=''){ $corveebeheer->assign('error', $this->_error); }
		$corveebeheer->display('maaltijdketzer/corveebeheer.tpl');
	}
}

?>

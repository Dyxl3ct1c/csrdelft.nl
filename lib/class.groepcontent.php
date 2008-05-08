<?php
/*
 * class.groepcontent.php	| 	Jan Pieter Waagmeester (jieter@jpwaag.com)
 * 
 * 
 * Een verzameling contentclassen voor de groepenketzer.
 * 
 * Groepcontent					Weergeven van een groep & bewerken en etc.
 * Groepencontent				Weergeven van een groepenoverzicht
 * Groepengeschiedeniscontent	Weergeven van een mooie patchwork van groepjes.
 * 
 */


class Groepcontent extends SimpleHTML{
	
	private $groep;
	private $action='view';
	
	public function __construct($groep){
		$this->groep=$groep;
	}
	public function setAction($action){
		$this->action=$action;
	}
	public function getTitel(){
		return $_GET['gtype'].' - '.$this->groep->getNaam();
	}
	
	/*
	 * Deze functie geeft een formulierding voor het eenvoudig toevoegen van leden
	 * aan een bepaalde groep.
	 */
	private function getLidAdder(){
		if(isset($_POST['rawNamen']) AND trim($_POST['rawNamen'])!=''){
			$return='';
			
			//uitmaken waarin we allemaal zoeken, standaard in de normale leden, wellicht
			//ook in oudleden en nobodies
			$zoekin=array('S_LID', 'S_NOVIET', 'S_GASTLID', 'S_KRINGEL');
			if(isset($_POST['filterOud'])){
				$zoekin[]='S_OUDLID';
			}
			if(isset($_POST['filterNobody']) AND $this->groep->isAdmin()){
				$zoekin[]='S_NOBODY';
			}

			$leden=namen2uid($_POST['rawNamen'], $zoekin);
			
			if(is_array($leden) AND count($leden)!=0){
				$return.='<table border="0">';
			
				foreach($leden as $aGroepUid){
					if(isset($aGroepUid['uid'])){
						//naam is gevonden en uniek, dus direct goed.
						$return.='<tr>';
						$return.='<td><input type="hidden" name="naam[]" value="'.$aGroepUid['uid'].'" />'.$aGroepUid['naam'].'</td>';
					}else{
						//naam is niet duidelijk, geef ook een selectievakje met de mogelijke opties
						if(count($aGroepUid['naamOpties'])>0){
							$return.='<tr><td><select name="naam[]" class="tekst">';
							foreach($aGroepUid['naamOpties'] as $aNaamOptie){
								$return.='<option value="'.$aNaamOptie['uid'].'">'.$aNaamOptie['naam'].'</option>';
							}
							$return.='</select></td>';
						}//dingen die niets opleveren wordt niets voor weergegeven.
					}
					if($this->groep->isAdmin()){
						$return.='<td><input type="text" name="functie[]" /></td></tr>';
					}else{
						$return.='<td>'.$this->getFunctieSelector().'</td></tr>';
					}
				}
				$return.='</table>';
				return $return;
			}
		}
		return false;
	}
	/*
	 * Niet-admins kunnen kiezen uit een van te voren vastgesteld lijstje met functies, zodat 
	 * we  niet allerlei onzinnamen krijgen zoals Kücherführer enzo.
	 */
	private function getFunctieSelector(){
		$return='';
		$aFuncties=array('Q.Q.', 'Praeses', 'Fiscus', 'Redacteur', 'Computeur', 'Archivaris', 
			'Bibliothecaris', 'Statisticus', 'Fotocommissaris','', 'Koemissaris', 'Regisseur', 
			'Lichttechnicus', 'Geluidstechnicus', 'Adviseur', 'Internetman', 'Posterman', 
			'Corveemanager', 'Provisor', 'HO', 'HJ', 'Onderhuurder');
		sort($aFuncties);
		$return.='<select name="functie[]" class="tekst">';
		foreach($aFuncties as $sFunctie){
			$return.='<option value="'.$sFunctie.'">'.$sFunctie.'</option>';
		}
		$return.='</select>';
		return $return;
	}
	public function view(){
		$content=new Smarty_csr();
		
		$content->assign('groep', $this->groep);
		$content->assign('opvolgerVoorganger', $this->groep->getOpvolgerVoorganger());
		
		$content->assign('action', $this->action);
		$content->assign('gtype', $_GET['gtype']);
		$content->assign('groeptypes', Groepen::getGroeptypes());
		
		if($this->action=='addLid'){
			$content->assign('lidAdder', $this->getLidAdder());
		}
		
		$content->assign('melding', $this->getMelding());
		$content->display('groepen/groep.tpl');		
	}
}
class Groepencontent extends SimpleHTML{
	
	private $groepen;
	private $action='view';
	
	public function __construct($groepen){
		$this->groepen=$groepen;
	}
	public function setAction($action){
		$this->action=$action;
	}
	public function getTitel(){
		return 'Groepen - '.$this->groepen->getNaam();
	}
	
	public function view(){
		$content=new Smarty_csr();
		
		$content->assign('groepen', $this->groepen);
		
		$content->assign('action', $this->action);
		$content->assign('gtype', $this->groepen->getNaam());
		$content->assign('groeptypes', Groepen::getGroeptypes());
		
		$content->assign('melding', $this->getMelding());
		$content->display('groepen/groepen.tpl');		
		
	}
}
class Groepgeschiedeniscontent extends SimpleHTML{
	
	private $groepen;
	
	public function __construct($groepen){
		$this->groepen=$groepen;
	}
		public function getTitel(){
		return 'Groepen - '.$this->groepen->getNaam();
	}
	
	public function view(){
		$maanden=5*12;
		echo '<table style="border-collapse: collapse;">';

		foreach($this->groepen->getGroepen() as $groep){
			echo '<tr>';
			$startspacer=12-substr($groep->getInstallatie(), 5,2);
			if($startspacer!=0){
				echo '<td colspan="'.$startspacer.'" style="background-color: lightgray;">&nbsp;</td>';
			}
			$oudeGr=Groep::getGroepgeschiedenis($groep->getSnaam(), 5);
			foreach($oudeGr as $grp){
				echo '<td colspan="12" style="border: 1px solid black; padding: 2px; width: 150px; text-align: left;">';
				echo '<a href="/actueel/groepen/'.$this->groepen->getNaam().'/'.$grp['id'].'">'.$grp['naam'].'</a>';

				echo '</td>';
			}
			if(count($oudeGr)<$maanden){
				$spacer=$maanden-count($oudeGr);
				echo '<td colspan="'.$spacer.'" style="background-color: lightgray;">&nbsp;</td>';
			}
			echo '</tr>';
		}
		echo '</table>';
			
	}
}
?>

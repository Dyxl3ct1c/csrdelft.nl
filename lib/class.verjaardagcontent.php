<?php
# C.S.R. Delft | pubcie@csrdelft.nl
# -------------------------------------------------------------------
# class.verjaardagcontent.php
# -------------------------------------------------------------------
# Beeldt informatie af over Verjaardagen
# -------------------------------------------------------------------

require_once 'lid/class.mootverjaardag.php';

class VerjaardagContent extends SimpleHTML {

	### private ###

	# de objecten die data leveren
	var $_lid;
	var $_actie;

	### public ###

	function VerjaardagContent ($actie) {
		$this->_lid =LoginLid::instance();
		$this->_actie = $actie;

	}
	function getTitel(){
		return 'Verjaardagen';
	}
	function view() {
		if($this->_actie=='komende' AND $this->_lid->hasPermission('P_ADMIN')){
			$this->_actie='komende_pasfotos';
		}
		switch ($this->_actie) {
			case 'alleverjaardagen':
				echo '<ul class="horizontal nobullets">
						<li>
							<a href="/communicatie/ledenlijst/">Ledenlijst</a>
						</li>
						<li class="active">
							<a href="/communicatie/verjaardagen" title="Overzicht verjaardagen">Verjaardagen</a>
						</li>
						<li>
							<a href="/communicatie/moten/">Kringen</a>
						</li>
					</ul>
					<hr />';
				echo '<h1>Verjaardagen</h1>';
				# de verjaardagen die vandaag zijn krijgen een highlight
				$nu = time();
				$dezemaand = date('n', $nu);
				$dezedag = date('j', $nu);

				# afbeelden van alle verjaardagen in 3 rijen en 4 kolommen
				$rijen = 3; $kolommen = 4;

				$maanden = array (
					1 => "Januari",
					2 => "Februari",
					3 => "Maart",
					4 => "April",
					5 => "Mei",
					6 => "Juni",
					7 => "Juli",
					8 => "Augustus",
					9 => "September",
					10 => "Oktober",
					11 => "November",
					12 => "December",
				);

				echo '<table style="width: 100%;">';
				for ($r=0; $r<$rijen; $r++) {
					echo '<tr>';
					for ($k=1; $k<=$kolommen; $k++) {
						$maand = ($r*$kolommen+$k+$dezemaand-2)%12+1;
						$tekst = ($maand <= 12) ? $maanden[$maand] : '&nbsp;';
						echo '<th>'.$tekst.'</th>'."\n";
					}
					echo "</tr><tr>\n";
					for ($k=1; $k<=$kolommen; $k++) {
						$maand = ($r*$kolommen+$k+$dezemaand-2)%12+1;
						if ($maand <= 12) {
							echo '<td>'."\n";
							$verjaardagen = Verjaardag::getVerjaardagen($maand);
							foreach ($verjaardagen as $verjaardag){
								$lid=LidCache::getLid($verjaardag['uid']);
								if ($verjaardag['gebdag'] == $dezedag and $maand == $dezemaand) echo '<em>';
								echo $verjaardag['gebdag'] . " ";
								echo $lid->getNaamLink('civitas', 'link')."<br />\n";
								if ($verjaardag['gebdag'] == $dezedag and $maand == $dezemaand) echo "</em>";
							}
							echo "<br /></td>\n";
						} else {
							echo "<td><&nbsp;</td>\n";
						}
					}
					echo "</tr>\n";
				}
				echo '</table><br>'."\n";
				break;
			case 'komende_pasfotos':
				$aantal=Instelling::get('zijbalk_verjaardagen');
				//veelvouden van 3 overhouden
				$aantal=$aantal-($aantal%3);
				$aVerjaardagen=Verjaardag::getKomendeVerjaardagen($aantal);
				echo '<h1>';
				if($this->_lid->hasPermission('P_LEDEN_READ')){
					echo '<a href="/communicatie/verjaardagen">Verjaardagen</a>';
				}else{
					echo 'Verjaardagen';
				}
				echo '</h1>';
				echo '<div class="item" id="komende_pasfotos">';
				foreach($aVerjaardagen as $verjaardag){
					$lid=LidCache::getLid($verjaardag['uid']);
					echo '<div class="verjaardag';
					if($verjaardag['jarig_over']==0){ echo ' opvallend'; }
					echo '">';
					echo $lid->getNaamLink('pasfoto', 'link').'<br />'.date('d-m', strtotime($verjaardag['gebdatum']));
					echo '</div>';
				}
				echo '<div class="clear">&nbsp;</div></div>';
			break;
			case 'komende':
				$aVerjaardagen=Verjaardag::getKomendeVerjaardagen(Instelling::get('zijbalk_verjaardagen'));
				echo '<h1>';
				if($this->_lid->hasPermission('P_LEDEN_READ')){
					echo '<a href="/communicatie/verjaardagen">Verjaardagen</a>';
				}else{
					echo 'Verjaardagen';
				}
				echo '</h1>';
				foreach($aVerjaardagen as $verjaardag) {
					$lid=LidCache::getLid($verjaardag['uid']);
					echo '<div class="item">'.date('d-m', strtotime($verjaardag['gebdatum'])).' ';
					if($verjaardag['jarig_over']==0){echo '<span class="opvallend">';}
					echo $lid->getNaamLink('civitas', 'link');
					if($verjaardag['jarig_over']==0){echo '</span>';}
					echo '</div>';
				}
			break;
		}
	}
}

?>

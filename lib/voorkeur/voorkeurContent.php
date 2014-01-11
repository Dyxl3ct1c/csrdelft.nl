<?php

class LidPaginaVoorkeurContent extends SimpleHTML{
	private $profiel;
	private $actie;

	public function __construct($lid, $actie){
		$this->lid=$lid;
		$this->actie=$actie;
	}
	public function getTitel(){
		return 'voorkeur van '.$this->lid->getNaam().' bekijken.';
	}
	public function view(){


		require_once 'formulier.class.php';
		$profiel=new TemplateEngine();
		$profiel->assign('profiel', $this->profiel);

		$profiel->assign('melding', $this->getMelding());
		$profiel->assign('actie', $this->actie);
		$profiel->display('profiel/wijzigvoorkeur.tpl');
	}
}

?>
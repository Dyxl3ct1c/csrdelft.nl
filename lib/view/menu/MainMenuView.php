<?php
/**
 * MainMenuView.php
 *
 * @author G.J.W. Oolbekkink <g.j.w.oolbekkink@gmail.com>
 * @date 06/05/2017
 */

namespace CsrDelft\view\menu;

use CsrDelft\model\gesprekken\GesprekDeelnemersModel;
use CsrDelft\model\MenuModel;
use CsrDelft\model\security\LoginModel;
use CsrDelft\SavedQuery;
use CsrDelft\view\formulier\InstantSearchForm;
use CsrDelft\view\MenuView;

class MainMenuView extends MenuView {

	public function __construct() {
		parent::__construct(MenuModel::instance()->getMenu('main'));
	}

	public function view() {
		parent::view();
		$mcount = new SavedQuery(62);
		$this->smarty->assign('mcount', $mcount->count());
		$this->smarty->assign('gesprekOngelezen', GesprekDeelnemersModel::instance()->getAantalNieuweBerichtenVoorLid(LoginModel::getUid()));
		$this->smarty->assign('favorieten', MenuModel::instance()->getMenu(LoginModel::getUid()));
		$this->smarty->assign('zoekbalk', new InstantSearchForm());
		$this->smarty->display('menu/main.tpl');
	}

}
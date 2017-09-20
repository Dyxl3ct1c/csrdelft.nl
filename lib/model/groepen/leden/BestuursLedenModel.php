<?php
/**
 * BestuursLedenModel.php
 *
 * @author G.J.W. Oolbekkink <g.j.w.oolbekkink@gmail.com>
 * @date 07/05/2017
 */

namespace CsrDelft\model\groepen\leden;

use CsrDelft\model\AbstractGroepLedenModel;
use CsrDelft\model\entity\groepen\BestuursLid;

class BestuursLedenModel extends AbstractGroepLedenModel {

	const ORM = BestuursLid::class;

	protected static $instance;

}
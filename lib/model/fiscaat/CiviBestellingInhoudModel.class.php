<?php

namespace CsrDelft\model\fiscaat;

use CsrDelft\model\entity\fiscaat\CiviBestellingInhoud;
use CsrDelft\Orm\PersistenceModel;

/**
 * @author Gerben Oolbekkink <g.j.w.oolbekkink@gmail.com>
 */
class CiviBestellingInhoudModel extends PersistenceModel {
	/**
	 * ORM class.
	 */
	const ORM = CiviBestellingInhoud::class;

	/**
	 * @var CiviProductModel
	 */
	private $civiProductModel;

	protected function __construct(CiviProductModel $civiProductModel) {
		parent::__construct();

		$this->civiProductModel = $civiProductModel;
	}

	/**
	 * @param CiviBestellingInhoud $inhoud
	 *
	 * @return int
	 */
	public function getPrijs(CiviBestellingInhoud $inhoud) {
		$product = $this->civiProductModel->getProduct($inhoud->product_id);

		return $product->prijs * $inhoud->aantal;
	}

	/**
	 * @param CiviBestellingInhoud $inhoud
	 *
	 * @return string
	 */
	public function getBeschrijving(CiviBestellingInhoud $inhoud) {
		$product = $this->civiProductModel->getProduct($inhoud->product_id);
		return sprintf("%d %s", $inhoud->aantal, $product->beschrijving);
	}

	/**
	 * @param int $bestelling_id
	 * @param int|null $product_id
	 * @return \PDOStatement|CiviBestellingInhoud[]
	 */
	public function getAll($bestelling_id, $product_id = null) {
		$query = 'bestelling_id = ?';
		$query_params = [$bestelling_id];

		if ($product_id !== null) {
			$query .= ' AND product_id = ?';
			$query_params[] = $product_id;
		}

		return $this->find($query, $query_params);
	}
}

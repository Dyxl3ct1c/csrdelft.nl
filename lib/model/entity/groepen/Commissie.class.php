<?php

/**
 * Commissie.class.php
 * 
 * @author P.W.G. Brussee <brussee@live.nl>
 * 
 * Een commissie is een groep waarvan de groepsleden een specifieke functie (kunnen) hebben.
 * 
 */
class Commissie extends OpvolgbareGroep {

	/**
	 * (Bestuurs-)Commissie / SjaarCie
	 * @var CommissieSoort
	 */
	public $soort;
	/**
	 * Database table columns
	 * @var array
	 */
	protected static $persistent_attributes = array(
		'soort' => array(T::Enumeration, false, 'CommissieSoort'),
	);
	/**
	 * Database table name
	 * @var string
	 */
	protected static $table_name = 'commissies';

	/**
	 * Extend the persistent attributes.
	 */
	public static function __constructStatic() {
		parent::__constructStatic();
		self::$persistent_attributes = parent::$persistent_attributes + self::$persistent_attributes;
	}

}

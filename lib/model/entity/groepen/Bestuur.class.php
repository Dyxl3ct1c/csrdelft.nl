<?php

require_once 'model/entity/groepen/Commissie.class.php';

/**
 * Bestuur.class.php
 * 
 * @author P.W.G. Brussee <brussee@live.nl>
 * 
 * Een bestuur is een speciaal type van een commissie.
 * 
 */
class Bestuur extends Commissie {

	/**
	 * Bestuurstekst
	 * @var string
	 */
	public $bijbeltekst;
	/**
	 * Database table columns
	 * @var array
	 */
	protected static $persistent_attributes = array(
		'bijbeltekst' => array(T::Text)
	);
	/**
	 * Database table name
	 * @var string
	 */
	protected static $table_name = 'besturen';

	/**
	 * Extend the persistent attributes.
	 */
	public static function __constructStatic() {
		parent::__constructStatic();
		self::$persistent_attributes = parent::$persistent_attributes + self::$persistent_attributes;
	}

}

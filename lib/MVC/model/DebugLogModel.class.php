<?php

/**
 * DebugLogModel.class.php
 * 
 * @author P.W.G. Brussee <brussee@live.nl>
 * 
 */
class DebugLogModel extends PersistenceModel {

	const orm = 'LogEntry';

	protected static $instance;

	protected function __construct() {
		parent::__construct();
		$entries = $this->find('moment < ?', array(strtotime('-2 month')));
		foreach ($entries as $entry) {
			$this->delete($entry);
		}
	}

	public function log($class, $function, array $args = array(), $dump = null) {
		$entry = new LogEntry();
		$entry->class_function = $class . '->' . $function . '(' . implode(', ', $args) . ')';
		$entry->dump = $dump;
		$e = new Exception();
		$entry->call_trace = $e->getTraceAsString();
		$entry->moment = getDateTime();
		$entry->lid_id = LoginLid::instance()->getUid();
		$entry->su_id = LoginLid::instance()->getSuedFrom();
		$entry->ip = $_SERVER['REMOTE_ADDR'];
		$entry->request = $_SERVER['REQUEST_URI'];
		$entry->referer = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null);
		$entry->user_agent = $_SERVER['HTTP_USER_AGENT'];
		$this->create($entry);
		return $entry;
	}

}

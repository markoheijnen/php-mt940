<?php

/**
 *
 * @package Kmt\Parser\Banking\Mt940\Engine
 * @author Kingsquare (source@kingsquare.nl)
 * @copyright Copyright (c) Kingsquare BV (http://www.kingsquare.nl)
 * @license http://opensource.org/licenses/gpl-2.0.php  Open Software License (GPLv2)
 */
class Rabo_engine_mt940_banking_parser extends Engine_mt940_banking_parser {
	/**
	 * returns the name of the bank
	 * @return string
	 */
	function _parseStatementBank() {
		return 'Rabo';
	}

	/**
	 * Overloaded: Rabo has different way of storing account info
	 * @return string
	 * @see Engine_mt940_banking_parser::_sanitizeAccount
	 */
	function _parseTransactionAccount() {
		$results = array();
		if (preg_match('/^:61:.{26}(.{16})/im', $this->getCurrentTransactionData(), $results) && !empty($results[1])) {
			return $this->_sanitizeAccount($results[1]);
		}
		return '';
	}

	/**
	 * Overloaded: Rabo has different way of storing account name
	 * @return string
	 * @see Rabo_engine_mt940_banking_parser::_parseTransactionAccount
	 */
	function _parseTransactionAccountName() {
		$results = array();
		if (preg_match('/^:61:.*? (.*)/m', $this->getCurrentTransactionData(), $results) && !empty($results[1])) {
			return $this->_sanitizeAccountName($results[1]);
		}
		return '';
	}

	/**
	 * Overloaded: Rabo has different way of storing transaction value timestamps (ymd)
	 * @return int
	 */
	function _parseTransactionEntryTimestamp() {
		$results = array();
		if (preg_match('/^:60F:[C|D]([\d]{6})/m', $this->getCurrentStatementData(), $results) && !empty($results[1])) {
			return $this->_sanitizeTimestamp($results[1], 'ymd');
		}
		return 0;
	}

	/**
	 * Overloaded: Rabo has different way of storing transaction value timestamps (ymd)
	 * @return int
	 */
	function _parseTransactionValueTimestamp() {
		$results = array();
		if (preg_match('/^:61:([\d]{6})[C|D]/', $this->getCurrentTransactionData(), $results) && !empty($results[1])) {
			return $this->_sanitizeTimestamp($results[1], 'ymd');
		}
		return 0;
	}

	/**
	 * Overloaded: Rabo uses longer strings for accountnumbers
	 * @param string $string
	 * @return string
	 */
	function _sanitizeAccount($string) {
		$account = parent::_sanitizeAccount($string);
		if (strlen($account)>20 && strpos($account, '80000') == 0) {
			$account = substr($account, 5);
		}
		return $account;
	}
}
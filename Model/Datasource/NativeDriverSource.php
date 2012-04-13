<?php
/**
 * Native Driver Source
 *
 * PHP versions 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Datasources
 * @subpackage    Datasources.Model.Datasource
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('DboSource', 'Model/Datasource');

/**
 * NativeDriverSource
 *
 * Creates DBO-descendant objects from a given db connection configuration
 *
 * @package       cake
 * @subpackage    cake.cake.libs.model.datasources
 */
class NativeDriverSource extends DboSource {

/**
 * Returns an array of all result rows for a given SQL query.
 * Returns false if no rows matched.
 *
 * @param string $sql SQL statement
 * @param boolean $cache Enables returning/storing cached query results
 * @return array Array of resultset rows, or false if no rows matched
 * @access public
 */
	public function fetchAll($sql, $cache = true, $modelName = null) {
		if ($cache && isset($this->_queryCache[$sql])) {
			if (preg_match('/^\s*select/i', $sql)) {
				return $this->_queryCache[$sql];
			}
		}

		if ($this->execute($sql)) {
			$out = array();

			$first = $this->fetchRow();
			if ($first != null) {
				$out[] = $first;
			}
			while ($this->hasResult() && $item = $this->fetchResult()) {
				$this->fetchVirtualField($item);
				$out[] = $item;
			}

			if ($cache) {
				if (strpos(trim(strtolower($sql)), 'select') !== false) {
					$this->_queryCache[$sql] = $out;
				}
			}
			if (empty($out) && is_bool($this->_result)) {
				return $this->_result;
			}
			return $out;
		} else {
			return false;
		}
	}

/**
 * Returns a row from current resultset as an array
 *
 * @return array The fetched row as an array
 * @access public
 */
	public function fetchRow($sql = null) {
		if (!empty($sql) && is_string($sql) && strlen($sql) > 5) {
			if (!$this->execute($sql)) {
				return null;
			}
		}

		if ($this->hasResult()) {
			$this->resultSet($this->_result);
			$resultRow = $this->fetchResult();
			if (!empty($resultRow)) {
				$this->fetchVirtualField($resultRow);
			}
			return $resultRow;
		} else {
			return null;
		}
	}

/**
 * Prepares a value, or an array of values for database queries by quoting and escaping them.
 *
 * @param mixed $data A value or an array of values to prepare.
 * @param string $column The column into which this data will be inserted
 * @param boolean $read Value to be used in READ or WRITE context
 * @return mixed Prepared value or array of values.
 * @access public
 */
	public function value($data, $column = null, $read = true) {
		if (is_array($data) && !empty($data)) {
			return array_map(
				array(&$this, 'value'),
				$data, array_fill(0, count($data), $column), array_fill(0, count($data), $read)
			);
		} elseif (is_object($data) && isset($data->type)) {
			if ($data->type == 'identifier') {
				return $this->name($data->value);
			} elseif ($data->type == 'expression') {
				return $data->value;
			}
		} elseif (in_array($data, array('{$__cakeID__$}', '{$__cakeForeignKey__$}'), true)) {
			return $data;
		} else {
			return null;
		}
	}

/**
 * Cache the DataSource description
 *
 * @param string $object The name of the object (model) to cache
 * @param mixed $data The description of the model, usually a string or array
 * @return mixed
 * @access private
 */
	protected function _cacheDescription($object, $data = null) {
		if ($this->cacheSources === false) {
			return null;
		}

		if ($data !== null) {
			$this->_descriptions[$object] =& $data;
		}

		$key = ConnectionManager::getSourceName($this) . '_' . $object;
		$cache = Cache::read($key, '_cake_model_');

		if (empty($cache)) {
			$cache = $data;
			Cache::write($key, $cache, '_cake_model_');
		}

		return $cache;
	}

/**
 * Checks if the result is valid
 *
 * @return boolean True if the result is valid else false
 * @access public
 */
	public function hasResult() {
		return is_resource($this->_result);
	}

}

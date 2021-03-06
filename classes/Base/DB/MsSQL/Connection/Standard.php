<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Copyright © 2011–2013 Spadefoot Team.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * This class handles a standard MS SQL connection.
 *
 * @package Leap
 * @category MS SQL
 * @version 2012-12-11
 *
 * @see http://www.php.net/manual/en/ref.mssql.php
 *
 * @abstract
 */
abstract class Base_DB_MsSQL_Connection_Standard extends DB_SQL_Connection_Standard {

	/**
	 * This function opens a connection using the data source provided.
	 *
	 * @access public
	 * @override
	 * @throws Throwable_Database_Exception     indicates that there is problem with
	 *                                          opening the connection
	 *
	 * @see http://stackoverflow.com/questions/1322421/php-sql-server-how-to-set-charset-for-connection
	 */
	public function open() {
		if ( ! $this->is_connected()) {
			try {
				$connection_string = $this->data_source->host;
				$port = $this->data_source->port;
				if ( ! empty($port)) {
					$connection_string .= ':' . $port;
				}
				$username = $this->data_source->username;
				$password = $this->data_source->password;
				$this->resource = ($this->data_source->is_persistent())
					? mssql_pconnect($connection_string, $username, $password)
					: mssql_connect($connection_string, $username, $password, TRUE);
			}
			catch (ErrorException $ex) {
				throw new Throwable_Database_Exception('Message: Failed to establish connection. Reason: :reason', array(':reason' => $ex->getMessage()));
			}
			$database = @mssql_select_db($this->data_source->database, $this->resource);
			if ($database === FALSE) {
				throw new Throwable_Database_Exception('Message: Failed to connect to database. Reason: :reason', array(':reason' => @mssql_get_last_message()));
			}
			if ( ! empty($this->data_source->charset)) {
				ini_set('mssql.charset', $this->data_source->charset);
			}
		}
	}

	/**
	 * This function begins a transaction.
	 *
	 * @access public
	 * @override
	 * @throws Throwable_SQL_Exception          indicates that the executed statement failed
	 *
	 * @see http://msdn.microsoft.com/en-us/library/ms188929.aspx
	 */
	public function begin_transaction() {
		$this->execute('BEGIN TRAN;');
	}

	/**
	 * This function processes an SQL statement that will NOT return data.
	 *
	 * @access public
	 * @override
	 * @param string $sql						the SQL statement
	 * @throws Throwable_SQL_Exception          indicates that the executed statement failed
	 */
	public function execute($sql) {
		if ( ! $this->is_connected()) {
			throw new Throwable_SQL_Exception('Message: Failed to execute SQL statement. Reason: Unable to find connection.');
		}
		$command = @mssql_query($sql, $this->resource);
		if ($command === FALSE) {
			throw new Throwable_SQL_Exception('Message: Failed to execute SQL statement. Reason: :reason', array(':reason' => @mssql_get_last_message()));
		}
		@mssql_free_result($command);
		$this->sql = $sql;
	}

	/**
	 * This function returns the last insert id.
	 *
	 * @access public
	 * @override
	 * @return integer                          the last insert id
	 * @throws Throwable_SQL_Exception          indicates that the query failed
	 */
	public function get_last_insert_id() {
		if ( ! $this->is_connected()) {
			throw new Throwable_SQL_Exception('Message: Failed to fetch the last insert id. Reason: Unable to find connection.');
		}
		try {
			$sql = $this->sql;
			if (preg_match('/^INSERT\s+(TOP.+\s+)?INTO\s+(.*?)\s+/i', $sql, $matches)) {
				$table = Arr::get($matches, 2);
				$query = ( ! empty($table)) ? "SELECT IDENT_CURRENT('{$table}') AS insert_id" : 'SELECT SCOPE_IDENTITY() AS insert_id';
				$result_set = $this->query($query);
				$insert_id = ($result_set->is_loaded()) ? ( (int)  Arr::get($result_set->fetch(0), 'insert_id')) : 0;
				$this->sql = $sql;
				return $insert_id;
			}
			return 0;
		}
		catch (Exception $ex) {
			throw new Throwable_SQL_Exception('Message: Failed to fetch the last insert id. Reason: :reason', array(':reason' => $ex->getMessage()));
		}
	}

	/**
	 * This function rollbacks a transaction.
	 *
	 * @access public
	 * @override
	 * @throws Throwable_SQL_Exception          indicates that the executed statement failed
	 */
	public function rollback() {
		$this->execute('ROLLBACK;');
	}

	/**
	 * This function commits a transaction.
	 *
	 * @access public
	 * @override
	 * @throws Throwable_SQL_Exception          indicates that the executed statement failed
	 *
	 * @see http://msdn.microsoft.com/en-us/library/ms190295.aspx
	 */
	public function commit() {
		$this->execute('COMMIT;');
	}

	/**
	 * This function closes an open connection.
	 *
	 * @access public
	 * @override
	 * @return boolean                          whether an open connection was closed
	 */
	public function close() {
		if ($this->is_connected()) {
			if ( ! @mssql_close($this->resource)) {
				return FALSE;
			}
			$this->resource = NULL;
		}
		return TRUE;
	}

	/**
	 * This destructor ensures that the connection is closed.
	 *
	 * @access public
	 * @override
	 */
	public function __destruct() {
		if (is_resource($this->resource)) {
			@mssql_close($this->resource);
		}
	}

}

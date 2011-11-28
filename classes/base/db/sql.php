<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Copyright 2011 Spadefoot
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
 * This class provides a shortcut way to get the various database builder class.
 *
 * @package Leap
 * @category SQL
 * @version 2011-11-27
 *
 * @abstract
 */
abstract class Base_DB_SQL extends Kohana_Object {

    /**
    * This function returns an instance of the DB_SQL_Delete_Proxy.
    *
    * @access public
    * @static
    * @param mixed $config                  the data source configurations
    * @return DB_SQL_Delete_Proxy           an instance of the class
    */
	public static function delete($config = 'default') {
		$proxy = new DB_SQL_Delete_Proxy($config);
	    return $proxy;
	}

    /**
    * This function returns an instance of the DB_SQL_Insert_Proxy.
    *
    * @access public
    * @static
    * @param mixed $config                  the data source configurations
    * @return DB_SQL_Insert_Proxy           an instance of the class
    */
	public static function insert($config = 'default') {
		$proxy = new DB_SQL_Insert_Proxy($config);
	    return $proxy;
	}

    /**
    * This function returns an instance of the DB_SQL_Select_Proxy.
    *
    * @access public
    * @static
    * @param mixed $config                  the data source configurations
    * @param array $columns                 the columns to be selected
    * @return DB_SQL_Select_Proxy           an instance of the class
    */
	public static function select($config = 'default', Array $columns = array()) {
		$proxy = new DB_SQL_Select_Proxy($config, $columns);
	    return $proxy;
	}

    /**
    * This function returns an instance of the DB_SQL_Update_Proxy.
    *
    * @access public
    * @static
    * @param mixed $config                  the data source configurations
    * @return DB_SQL_Update_Proxy           an instance of the class
    */
	public static function update($config = 'default') {
		$proxy = new DB_SQL_Update_Proxy($config);
	    return $proxy;
	}

}
?>
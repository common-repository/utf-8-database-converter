<pre>
<?php
/* This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://sam.zoy.org/wtfpl/COPYING for more details. */

/*
This was suppose to be the engine of the version 3.0, m leaving a copy here at the svn so any
developer can use it (or improve it, maybe even code a better replacement to my plugin).
ToDo:
	- Add the MySQL error handler
	- Solve the issues with UNIQUE Keys
*/

// Debug Mode...
error_reporting(E_ALL);

/** The name of the database */
define('DB_NAME', '');

/** MySQL database username */
define('DB_USER', '');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', '');

function UTF8_DB_Converter_core($tables, $collation, $complete_convert = true) {
	// Initialize vars.
	$string_querys = array();
	$binary_querys = array();
	$gen_index_querys = array();
	$drop_index_querys = array();
	$final_querys = array();

	// Since we cannot use the WordPress DB Class Object (wp-includes/wp-db.php),
	// we have to make a stand-alone connection to the database.
	$link_id = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die('<h3>Error establishing a database connection</h3>');
	mysql_select_db(DB_NAME, $link_id) or die('<h3>Can&#8217;t select database</h3>');

	// Begin Converter Core
	if ( !empty($tables) ) {
		foreach ( (array) $tables as $table ) {
			// Analyze tables for string types columns and generate his binary and string correctness sql sentences.
			$resource = mysql_query("DESCRIBE `{$table}`", $link_id);
			while ( $result = mysql_fetch_assoc($resource) ) {
				if ( preg_match('/(char)|(text)|(enum)|(set)/', $result['Type']) ) {
					// String Type SQL Sentence.
					$string_querys[] = "ALTER TABLE `{$table}` MODIFY " . $result['Field'] . ' ' . $result['Type'] . " CHARACTER SET utf8 COLLATE {$collation} " . ( ( NULL === $result['Default'] ) ? '' : "DEFAULT '". $result['Default'] ."' " ) . ( 'YES' == $result['Null'] ? '' : 'NOT ' ) . 'NULL';

					// Binary String Type SQL Sentence.
					if ( preg_match('/(enum)|(set)/', $result['Type']) ) {
						$binary_querys[] = "ALTER TABLE `{$table}` MODIFY " . $result['Field'] . ' ' . $result['Type'] . ' CHARACTER SET binary ' . ( ( NULL === $result['Default'] ) ? '' : "DEFAULT '". $result['Default'] ."' " ) . ( 'YES' == $result['Null'] ? '' : 'NOT ' ) . 'NULL';
					} else {
						$result['Type'] = preg_replace('/char/', 'binary', $result['Type']);
						$result['Type'] = preg_replace('/text/', 'blob', $result['Type']);
						$binary_querys[] = "ALTER TABLE `{$table}` MODIFY " . $result['Field'] . ' ' . $result['Type'] . ' ' . ( ( NULL === $result['Default'] ) ? '' : "DEFAULT '". $result['Default'] ."' " ) . ( 'YES' == $result['Null'] ? '' : 'NOT ' ) . 'NULL';
					}
				}
			}

			// Analyze table indexs for any FULLTEXT-Type of index in the table.
			$fulltext_indexes = array();
			$resource = mysql_query("SHOW INDEX FROM `{$table}`", $link_id);
			while ( $result = mysql_fetch_assoc($resource) ) {
				if ( preg_match('/FULLTEXT/', $result['Index_type']) )
					$fulltext_indexes[$result['Key_name']][$result['Column_name']] = 1;
			}

			// Generate the SQL Sentence for drop and add every FULLTEXT index we found previously.
			if ( !empty($fulltext_indexes) ) {
				foreach ( (array) $fulltext_indexes as $key_name => $column ) {
					$drop_index_querys[] = "ALTER TABLE `{$table}` DROP INDEX {$key_name}";
					$tmp_gen_index_query = "ALTER TABLE `{$table}` ADD FULLTEXT {$key_name}(";
					$fields_names = array_keys($column);
					for ($i = 1; $i <= count($column); $i++)
						$tmp_gen_index_query .= $fields_names[$i - 1] . (($i == count($column)) ? '' : ', ');
					$gen_index_querys[] = $tmp_gen_index_query . ')';
				}
			}

			// Generate the SQL Sentence for change default table character set.
			$tables_querys[] = "ALTER TABLE `{$table}` DEFAULT CHARACTER SET utf8 COLLATE {$collation}";

			// Generate the SQL Sentence for Optimize Table.
			$optimize_querys[] = "OPTIMIZE TABLE `{$table}`";
		}

		// SQL Sentence for change the default database character set.
		if ( $complete_convert )
			$db_query = "ALTER DATABASE " . DB_NAME . " DEFAULT CHARACTER SET utf8 COLLATE {$collation}";
	} else {
		die('<h3>There are no tables?</h3>');
	}
	// End Converter Core

	// Close MySQL Link.
	mysql_close($link_id);

	// Merge all SQL Sentences that we temporary store in arrays.
	$final_querys = array_merge( (array) $drop_index_querys, (array) $binary_querys, (array) $db_query, (array) $tables_querys, (array) $string_querys, (array) $gen_index_querys, (array) $optimize_querys );

	// Time to return.
	return $final_querys;
}

$link_id = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die('Error establishing a database connection');
mysql_select_db(DB_NAME, $link_id);

$resource = mysql_query('SHOW TABLES', $link_id);
while ( $result = mysql_fetch_row($resource) )
	$tables[] = $result[0];

$querys = UTF8_DB_Converter_core($tables, 'utf8_general_ci');
foreach ( $querys as $query )
	print $query . ";\n";
?>
</pre>
<?php

// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * BrookesEDGE - database upgrade
 *
 * @package    local_brookes_edge
 * @author     Peter Welham
 * @copyright  2020, Oxford Brookes University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

function xmldb_local_brookes_edge_upgrade($oldversion = 0) {
    global $DB;
    $dbman = $DB->get_manager();

    $result = true;

    if ($oldversion < 2020120200) {

		// Define table local_brookes_edge_awards
		$table = new xmldb_table('local_brookes_edge_awards');

		// Add fields
		$table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
		$table->add_field('recipient_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
		$table->add_field('award_time', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
		$table->add_field('issued', XMLDB_TYPE_INTEGER, '1', null, null, null, '0');
		$table->add_field('issue_time', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');

		// Add keys
		$table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

		// Add indexes
		$table->add_index('recipient_id', XMLDB_INDEX_UNIQUE, array('recipient_id'));

		// Conditionally create table
		if (!$dbman->table_exists($table)) {
			$dbman->create_table($table);
		}

        // brookes_edge savepoint reached
        upgrade_plugin_savepoint(true, 2020120200, 'local', 'brookes_edge');
    }

    return $result;
}

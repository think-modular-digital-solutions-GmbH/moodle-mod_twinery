<?php
// This file is part of Moodle - http://moodle.org/
//
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
 * Upgrade hooks for mod_twinery.
 *
 * @package    mod_twinery
 * @author     Stefan Weber (stefan.weber@think-modular.com)
 * @copyright  2025 think-modular
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade steps for mod_twinery.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_twinery_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    // Step: add maxattempts to main table and create attempts table.
    if ($oldversion < 2025081301) {

        // 1) Add field maxattempts to {twinery}.
        $table = new xmldb_table('twinery');
        $field = new xmldb_field('maxattempts', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0', 'timemodified');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // 2) Create {twinery_attempts} table.
        $table = new xmldb_table('twinery_attempts');

        $table->add_field('id',         XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('twineryid',  XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('userid',     XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('attempts',    XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('timecreated',XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified',XMLDB_TYPE_INTEGER,'10', null, XMLDB_NOTNULL, null, '0');

        // Keys & indexes.
        $table->add_key('primary',       XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('tw_fk_twinery', XMLDB_KEY_FOREIGN, ['twineryid'], 'twinery', ['id']);
        $table->add_key('tw_fk_user',    XMLDB_KEY_FOREIGN, ['userid'],    'user',    ['id']);
        $table->add_index('uniq_attempt_per_user', XMLDB_INDEX_UNIQUE, ['twineryid', 'userid', 'attempt']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Savepoint.
        upgrade_mod_savepoint(true, 2025081301, 'twinery');
    }

    return true;
}

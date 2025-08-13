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
 * Label module functions.
 *
 * @package    mod_twinery
 * @author     Stefan Weber (stefan.weber@think-modular.com)
 * @copyright  2025 think-modular
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require(__DIR__.'/../../config.php');

$id = required_param('id', PARAM_INT); // Course ID.

$course = get_course($id);
require_login($course);
$context = context_course::instance($course->id);

// Page setup.
$PAGE->set_url('/mod/twinery/index.php', ['id' => $id]);
$PAGE->set_title(get_string('modulenameplural', 'mod_twinery'));
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('modulenameplural', 'mod_twinery'));

// Fetch all instances of this module in the course.
if (!$twinerys = get_all_instances_in_course('twinery', $course)) {
    notice(get_string('nonewmodules', 'mod_twinery'), new moodle_url('/course/view.php', ['id' => $course->id]));
    exit;
}

// Simple table of instances.
$table = new html_table();
$table->head  = [get_string('name'), get_string('intro', 'mod_twinery')];
foreach ($twinerys as $t) {
    $link = html_writer::link(
        new moodle_url('/mod/twinery/view.php', ['id' => $t->coursemodule]),
        format_string($t->name)
    );
    $table->data[] = [$link, format_module_intro('twinery', $t, $t->coursemodule)];
}
echo html_writer::table($table);

echo $OUTPUT->footer();

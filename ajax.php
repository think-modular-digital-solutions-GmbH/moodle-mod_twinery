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
define('AJAX_SCRIPT', true);
require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

require_login();
require_sesskey();

$cmid = required_param('cmid', PARAM_INT);
$grade = required_param('grade', PARAM_FLOAT);
$feedback = required_param('feedback', PARAM_TEXT);

$cm = get_coursemodule_from_id('twinery', $cmid, 0, false, MUST_EXIST);
$context = context_module::instance($cm->id);
require_capability('mod/twinery:grade', $context);

// Get twinery instance
$twinery = $DB->get_record('twinery', ['id' => $cm->instance], '*', MUST_EXIST);

// Push the grade
$gradeitem = [
    'userid' => $USER->id,
    'rawgrade' => $grade,
    'feedback' => $feedback,
    'feedbackformat' => FORMAT_HTML,
];

twinery_grade_item_update($twinery, $gradeitem);

echo json_encode(['status' => 'success', 'message' => get_string('gradesubmitted', 'mod_twinery')]);
die();

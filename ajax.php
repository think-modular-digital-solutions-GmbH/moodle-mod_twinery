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

// Check user.
require_login();
require_sesskey();

// Only POST requests are allowed.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    throw new moodle_exception('postonly');
}


header('Content-Type: application/json; charset=utf-8');

// Parameters.
global $DB, $USER;
$cmid = required_param('cmid', PARAM_INT);
$grade = required_param('grade', PARAM_FLOAT);
$feedback = required_param('feedback', PARAM_TEXT);

// Check permissions.
$cm = get_coursemodule_from_id('twinery', $cmid, 0, false, MUST_EXIST);
$context = context_module::instance($cm->id);
require_capability('mod/twinery:grade', $context);

// Get twinery instance
$twinery = $DB->get_record('twinery', ['id' => $cm->instance], '*', MUST_EXIST);

// Get attempts.
if ($attemptrecord = $DB->get_record('twinery_attempts', ['userid' => $USER->id, 'twineryid' => $twinery->id])) {
    $attempts = $attemptrecord->attempts;
} else {
    $attempts = 0;
}

// Check if user has attempts left.
if ($attempts >= $twinery->maxattempts) {
    echo json_encode([
        'status' => 'nomoreattempts',
        'message' => get_string('nomoreattempts', 'mod_twinery')
    ]);
    die();
}

// Push the grade.
$gradeitem = [
    'userid' => $USER->id,
    'rawgrade' => $grade,
    'feedback' => $feedback,
    'feedbackformat' => FORMAT_HTML,
];

// Add attempt to the database.
$attempts++;
if ($attemptrecord) {
    $attemptrecord->timemodified = time();
    $attemptrecord->attempts = $attempts;
    $DB->update_record('twinery_attempts', $attemptrecord);
} else {
    $newattempt = new stdClass();
    $newattempt->twineryid = $twinery->id;
    $newattempt->userid = $USER->id;
    $newattempt->attempts = $attempts;
    $newattempt->timecreated = time();
    $newattempt->timemodified = time();
    $DB->insert_record('twinery_attempts', $newattempt);
}

// Do we have limited attempts?
if ($twinery->maxattempts > 0) {
    $string = 'gradesubmitted_attempts';

    // Is this the last attempt?
    if ($attempts >= $twinery->maxattempts) {
        $status = 'lastattempt';
    } else {
        $status = 'nextattempt';
    }
} else {
    $string = 'gradesubmitted';
    $status = 'success';
}

// Update the grade item for the Twinery module.
twinery_grade_item_update($twinery, $gradeitem);

// Much success. Very wow.
echo json_encode([
    'status' => $status,
    'message' => get_string($string, 'mod_twinery', [
        'attempts' => $attempts,
        'maxattempts' => $twinery->maxattempts
    ])
]);
die();

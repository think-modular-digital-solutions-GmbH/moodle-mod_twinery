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
 * Twinery module.
 *
 * @package    mod_twinery
 * @author     Stefan Weber (stefan.weber@think-modular.com)
 * @copyright  2025 think-modular
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');

// Get parameters.
$id = required_param('id', PARAM_INT); // Course module ID
$cm = get_coursemodule_from_id('twinery', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
$twinery = $DB->get_record('twinery', ['id' => $cm->instance], '*', MUST_EXIST);
$context = context_module::instance($cm->id);

// Check permissions.
require_login($course, true, $cm);
require_capability('mod/twinery:view', $context); // Optional: define in access.php

// Set up page.
$PAGE->set_url('/mod/twinery/view.php', ['id' => $id]);
$PAGE->set_title(format_string($twinery->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);
$PAGE->requires->data_for_js('M.cfg', [
    'cmid' => $cm->id,
    'sesskey' => sesskey(),
    'wwwroot' => $CFG->wwwroot,
]);
$PAGE->requires->js(new moodle_url('/mod/twinery/js/grading.js'));

// Start output.
echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($twinery->name));

// Check if we need to hide the Twinery activity based on max attempts.
if ($twinery->maxattempts > 0 && $twinery->maxattemptsaction == MOD_TWINERY_MAXATTEMPTS_HIDE) {
    $attemptrecord = $DB->get_record('twinery_attempts', ['userid' => $USER->id, 'twineryid' => $twinery->id]);
    if ($attemptrecord && $attemptrecord->attempts >= $twinery->maxattempts) {

        // Show nothing.
        echo $OUTPUT->notification(get_string('nomoreattempts', 'mod_twinery'), 'notifyproblem');
        echo $OUTPUT->footer();
        die();
    }
}

// Load the uploaded .html file
$fs = get_file_storage();
$files = $fs->get_area_files(
    $context->id,
    'mod_twinery',
    'twinery_file',
    $twinery->id,
    'timemodified DESC',
    false
);

$file = reset($files);

if ($file && pathinfo($file->get_filename(), PATHINFO_EXTENSION) === 'html') {
    $fileurl = moodle_url::make_pluginfile_url(
        $context->id,
        'mod_twinery',
        'twinery_file',
        $twinery->id,
        '/',
        $file->get_filename()
    );

    echo html_writer::tag('iframe', '', [
        'src' => $fileurl,
        'width' => '100%',
        'height' => '600px',
        'style' => 'border: 1px solid #ccc;'
    ]);
}

echo $OUTPUT->footer();

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

defined('MOODLE_INTERNAL') || die;

define('MOD_TWINERY_MAXATTEMPTS_SHOW', 0); // Show activity when max attempts is reached.
define('MOD_TWINERY_MAXATTEMPTS_HIDE', 1); // Hide activity when max attempts is reached.

/**
 * List of features supported in the Twinery module
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know or string for the module purpose.
 */
function twinery_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_ARCHETYPE:           return MOD_ARCHETYPE_ASSIGNMENT;
        case FEATURE_GROUPS:                  return false;
        case FEATURE_GROUPINGS:               return false;
        case FEATURE_MOD_INTRO:               return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_GRADE_HAS_GRADE:         return true;
        case FEATURE_GRADE_OUTCOMES:          return true;
        case FEATURE_BACKUP_MOODLE2:          return false;
        case FEATURE_SHOW_DESCRIPTION:        return true;
        case FEATURE_MOD_PURPOSE:             return MOD_PURPOSE_CONTENT;

        default: return null;
    }
}

/**
 * Add a new instance of the Twinery module.
 *
 * @param stdClass $data Data submitted from the form.
 * @param mod_twinery_mod_form $mform The form instance.
 * @return int The id of the newly created instance.
 */
function twinery_add_instance($data, $form) {
    global $DB;

    $data->timecreated = time();
    $data->id = $DB->insert_record('twinery', $data);

    // Save uploaded file.
    $cmid = $form->get_coursemodule()->id;
    $context = context_module::instance($cmid);
    file_save_draft_area_files(
        $data->twinery_file,  // draft item id
        $context->id,
        'mod_twinery',
        'twinery_file', // file area
        $data->id,
        ['subdirs' => 0]
    );

    twinery_grade_item_update($data); // ← this updates the gradebook

    return $data->id;
}

/**
 * Update an existing instance of the Twinery module.
 *
 * @param stdClass $data Data submitted from the form.
 * @param mod_twinery_mod_form $form The form instance.
 * @return bool True on success, false on failure.
 */
function twinery_update_instance($data, $form) {
    global $DB;

    $data->timemodified = time();
    $data->id = $data->instance;

    $DB->update_record('twinery', $data);

    // Save uploaded file.
    $cmid = $form->get_coursemodule()->id;
    $context = context_module::instance($cmid);
    file_save_draft_area_files(
        $data->twinery_file,
        $context->id,
        'mod_twinery',
        'twinery_file',
        $data->id,
        ['subdirs' => 0]
    );

    twinery_grade_item_update($data);

    return true;
}

/**
 * Update the grade item for the Twinery module.
 *
 * @param stdClass $twinery The Twinery instance.
 * @param array|null $grades Optional array of grades to update, if null all grades are updated.
 * @return bool True on success, false on failure.
 */
function twinery_grade_item_update($twinery, $grades = null) {

    global $CFG;

    require_once($CFG->libdir . '/gradelib.php');

    $item = [
        'itemname' => clean_param($twinery->name, PARAM_NOTAGS),
        'gradetype' => GRADE_TYPE_VALUE,
        'grademax' => $twinery->grade,
        'grademin' => 0
    ];

    return grade_update('mod/twinery', $twinery->course, 'mod', 'twinery', $twinery->id, 0, $grades, $item);
}

/**
 * Serve files from the Twinery module.
 *
 * @param stdClass $course The course object.
 * @param stdClass $cm The course module object.
 * @param context $context The context object.
 * @param string $filearea The file area.
 * @param array $args The file arguments.
 * @param bool $forcedownload Whether to force download.
 * @param array $options Additional options.
 * @return bool True if the file was served, false otherwise.
 */
function twinery_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    require_login($course, true, $cm);
    if ($filearea !== 'twinery_file') {
        return false;
    }

    $itemid = array_shift($args);
    $filename = array_pop($args);
    $filepath = $args ? '/' . implode('/', $args) . '/' : '/';

    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'mod_twinery', $filearea, $itemid, $filepath, $filename);
    if (!$file || $file->is_directory()) {
        return false;
    }

    send_stored_file($file, 0, 0, $forcedownload, $options);
}

/**
 * Get the file areas for the Twinery module.
 *
 * @param stdClass $course The course object.
 * @param stdClass $cm The course module object.
 * @param context $context The context object.
 * @return array An array of file areas.
 */
function twinery_get_file_areas($course, $cm, $context) {
    return ['twinery_file' => get_string('file', 'twinery')];
}



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
 * Add module form.
 *
 * @package    mod_twinery
 * @author     Stefan Weber (stefan.weber@think-modular.com)
 * @copyright  2025 think-modular
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once ($CFG->dirroot.'/course/moodleform_mod.php');

class mod_twinery_mod_form extends moodleform_mod {

    function definition() {
        global $PAGE;

        $PAGE->force_settings_menu();

        $mform = $this->_form;

        $mform->addElement('header', 'generalhdr', get_string('general'));

        // Name.
        $mform->addElement('text', 'name', get_string('name'), ['size' => '64', 'maxlength' => 255]);
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        $this->standard_intro_elements();

        // Add a file upload field
        $mform->addElement('filepicker', 'twinery_file', get_string('file', 'twinery'), null,
            ['maxbytes' => 0, 'accepted_types' => '.html']);
        $mform->addRule('twinery_file', get_string('required'), 'required', null, 'client');

        $this->standard_grading_coursemodule_elements();
        $this->standard_coursemodule_elements();
        $this->add_action_buttons(true, false, null);
    }

    /**
     * Handle draft files.
     *
     * @param array $data The data submitted in the form.
     * @param array $files The files submitted in the form.
     * @return array An array of errors, empty if no errors.
     */
    public function data_preprocessing(&$default_values) {
        if (!empty($this->current->instance)) {
            $context = context_module::instance($this->current->coursemodule);
            $draftid = 0;
            file_prepare_draft_area(
                $draftid,
                $context->id,
                'mod_twinery',
                'twinery_file',
                $this->current->instance,
                ['subdirs' => 0]
            );
            $default_values['twinery_file'] = $draftid;
        }
    }
}

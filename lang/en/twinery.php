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
 * Language strings for the Twinery module.
 *
 * @package    mod_twinery
 * @author     Stefan Weber (stefan.weber@think-modular.com)
 * @copyright  2025 think-modular
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// General strings.
$string['pluginname'] = 'Twinery';
$string['modulename'] = 'Twinery';
$string['modulenameplural'] = 'Twinery';
$string['modulename_help'] = 'The Twinery module allows you to create interactive stories using the Twine format. You can upload a Twine HTML file, which users can then play through, making choices that affect the outcome of the story.';
$string['privacy:metadata'] = 'The Twinery plugin does not store any personal data.';
$string['search:activity'] = 'Twinery';

// Permissions.
$string['twinery:addinstance'] = 'Add a new Twinery activity';
$string['twinery:grade'] = 'Grade Twinery activity';
$string['twinery:view'] = 'View Twinery activity';

// Form.
$string['file'] = 'Twinery html file';
$string['gradesubmitted'] = 'Grade submitted successfully.';
$string['gradesubmitted_attempts'] = 'Grade submitted successfully. Attempt {$a->attempts} of {$a->maxattempts}.';
$string['maxattempts'] = 'Maximum attempts';
$string['maxattempts_help'] = 'This setting defines how many attempts a user can make (ie how many times the Twinery can send back a grade). Leave at 0 for unlimited attempts.';
$string['maxattemptsaction'] = 'Action when max attempts reached';
$string['maxattemptsaction_show'] = 'Just stop Twinery from overwriting the grade';
$string['maxattemptsaction_hide'] = 'Hide Twinery completely';
$string['nomoreattempts'] = 'You have no more attempts left to get a grade for this Twinery activity.';
$string['pluginadministration'] = 'Edit';


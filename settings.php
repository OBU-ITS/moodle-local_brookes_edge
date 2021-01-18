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
 * BrookesEDGE - settings
 *
 * @package    local_brookes_edge
 * @author     Peter Welham
 * @copyright  2020, Oxford Brookes University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {
    $settings = new admin_settingpage(get_string('pluginname', 'local_brookes_edge'), get_string('title', 'local_brookes_edge'));
    $ADMIN->add('localplugins', $settings);

    $settings->add(new admin_setting_configtext('local_brookes_edge/minimum_words', get_string('minimum_words', 'local_brookes_edge'), get_string('minimum_words_desc', 'local_brookes_edge'), 500, PARAM_INT));
    $settings->add(new admin_setting_configtext('local_brookes_edge/minimum_entries', get_string('minimum_entries', 'local_brookes_edge'), get_string('minimum_entries_desc', 'local_brookes_edge'), 6, PARAM_INT));
    $settings->add(new admin_setting_configtext('local_brookes_edge/minimum_attributes', get_string('minimum_attributes', 'local_brookes_edge'), get_string('minimum_attributes_desc', 'local_brookes_edge'), 4, PARAM_INT));
}

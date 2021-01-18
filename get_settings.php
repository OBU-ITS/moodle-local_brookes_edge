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
 * BrookesEDGE - get settings
 *
 * @package    local_brookes_edge
 * @author     Peter Welham
 * @copyright  2020, Oxford Brookes University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

require_once("../../config.php");

header('Access-Control-Allow-Origin: *'); // Allow cross-origin resource sharing (by Google App Engine, for example)
header('Content-Type: application/json');

die(json_encode(array(
	'minimum_words' => get_config('local_brookes_edge', 'minimum_words'),
	'minimum_entries' => get_config('local_brookes_edge', 'minimum_entries'),
	'minimum_attributes' => get_config('local_brookes_edge', 'minimum_attributes')
)));

?>
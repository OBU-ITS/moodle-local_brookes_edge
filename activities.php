<?php

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
 * BrookesEDGE - Activities Report
 *
 * @package    local_brookes_edge
 * @author     Peter Welham
 * @copyright  2020, Oxford Brookes University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

require_once('../../config.php');
require_once('./locallib.php');
require_once('./activities_form.php');

require_login();

$home = new moodle_url('/');
if (!is_brookes_edge_manager()) {
	redirect($home);
}

$brookes_edge_course = get_brookes_edge_course();
require_login($brookes_edge_course);
$back = $home . 'course/view.php?id=' . $brookes_edge_course;

$context = context_system::instance();
$url = $home . 'local/brookes_edge/activities.php';

$PAGE->set_pagelayout('standard');
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_heading($SITE->fullname);
$PAGE->set_title(get_string('activities', 'local_brookes_edge'));
$PAGE->navbar->add(get_string('activities', 'local_brookes_edge'));

$message = '';

$mform = new activities_form(null, array());

if ($mform->is_cancelled()) {
    redirect($back);
} 
else if ($mform_data = $mform->get_data()) {
	$activities = get_activities(); // Get all selected activities
	if (empty($activities)) {
		$message = get_string('no_activities', 'local_brookes_edge');
	} else {
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename=activities.csv');
		$fp = fopen('php://output', 'w');
		fputcsv($fp, array('Shortname', 'Fullname', 'Visible', 'Faculty', 'Mnemonic', 'Attribute 1', 'Attribute 2', 'Attribute 3', 'Attribute 4', 'Attribute 5', 'Attribute 6'));

		foreach ($activities as $activity) {

			$codes = explode('~', $activity->codes); // We know that there's at least three elements
			$fields = array();
			$fields[0] = $activity->shortname;
			$fields[1] = $activity->fullname;
			$fields[2] = $activity->visible;
			$fields[3] = $codes[0];
			$fields[4] = $codes[1];
			$fields[5] = $codes[2]; // Attribute 1 (must be at least one)
			if (count($codes) > 3) {
				$fields[6] = $codes[3];
			} else {
				$fields[6] = '';
			}
			if (count($codes) > 4) {
				$fields[7] = $codes[4];
			} else {
				$fields[7] = '';
			}
			if (count($codes) > 5) {
				$fields[8] = $codes[5];
			} else {
				$fields[8] = '';
			}
			if (count($codes) > 6) {
				$fields[9] = $codes[6];
			} else {
				$fields[9] = '';
			}
			if (count($codes) > 7) {
				$fields[10] = $codes[7];
			} else {
				$fields[10] = '';
			}
			
			fputcsv($fp, $fields);
		}
		
		fclose($fp);
		
		exit();
	}
}	 

echo $OUTPUT->header();

if ($message) {
    notice($message, $url);    
} else {
    $mform->display();
}

echo $OUTPUT->footer();

exit();

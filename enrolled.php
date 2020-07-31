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
 * BrookesEDGE - Enrolment Report
 *
 * @package    local_brookes_edge
 * @author     Peter Welham
 * @copyright  2020, Oxford Brookes University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

require_once('../../config.php');
require_once('./locallib.php');
require_once('./enrolled_form.php');

require_login();

$home = new moodle_url('/');
if (!is_brookes_edge_manager()) {
	redirect($home);
}

$brookes_edge_course = get_brookes_edge_course();
require_login($brookes_edge_course);
$back = $home . 'course/view.php?id=' . $brookes_edge_course;

$context = context_system::instance();
$url = $home . 'local/brookes_edge/enrolled.php';

$PAGE->set_pagelayout('standard');
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_heading($SITE->fullname);
$PAGE->set_title(get_string('enrolled', 'local_brookes_edge'));
$PAGE->navbar->add(get_string('enrolled', 'local_brookes_edge'));

$message = '';

$mform = new enrolled_form(null, array());

if ($mform->is_cancelled()) {
    redirect($back);
} 
else if ($mform_data = $mform->get_data()) {
	$enrolments = get_enrolments($mform_data->date_from, $mform_data->date_to); // Get all selected enrolments
	if (empty($enrolments)) {
		$message = get_string('no_enrolments', 'local_brookes_edge');
	} else {
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename=enrolments_' . date('d-m-Y', $mform_data->date_from) . '_' . date('d-m-Y', $mform_data->date_to) . '.csv');
		$fp = fopen('php://output', 'w');
		fputcsv($fp, array('user id', 'firstname', 'lastname', 'student number', 'activity id', 'activity name', 'activity full name'));

		foreach ($enrolments as $enrolment) {
		
			$fields = array();
			$fields[0] = $enrolment->user_id;
			$fields[1] = $enrolment->firstname;
			$fields[2] = $enrolment->lastname;
			$fields[3] = $enrolment->student_number;
			$fields[4] = $enrolment->activity_id;
			$fields[5] = $enrolment->activity_name;
			$fields[6] = $enrolment->activity_full_name;
			
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

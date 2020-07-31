<?php

// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more settings.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * BrookesEDGE - db updates
 *
 * @package    local_brookes_edge
 * @author     Peter Welham
 * @copyright  2020, Oxford Brookes University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
 
function get_brookes_edge_course() {
	global $DB;
	
	$course = $DB->get_record('course', array('idnumber' => 'SUBS_BROOKES_EDGE'), 'id', MUST_EXIST);
	return $course->id;
}

// Check if the given user has the given role in the BrookesEDGE course
function has_brookes_edge_role($user_id = 0, $role_id_1 = 0, $role_id_2 = 0, $role_id_3 = 0) {
	global $DB;
	
	if (($user_id == 0) || ($role_id_1 == 0)) { // Both mandatory
		return false;
	}
	
	$sql = 'SELECT ue.id'
		. ' FROM {user_enrolments} ue'
		. ' JOIN {enrol} e ON e.id = ue.enrolid'
		. ' JOIN {context} ct ON ct.instanceid = e.courseid'
		. ' JOIN {role_assignments} ra ON ra.contextid = ct.id'
		. ' JOIN {course} c ON c.id = e.courseid'
		. ' WHERE ue.userid = ?'
			. ' AND e.enrol = "manual"'
			. ' AND ct.contextlevel = 50'
			. ' AND ra.userid = ue.userid'
			. ' AND (ra.roleid = ? OR ra.roleid = ? OR ra.roleid = ?)'
			. ' AND c.idnumber = "SUBS_BROOKES_EDGE"';
	$db_ret = $DB->get_records_sql($sql, array($user_id, $role_id_1, $role_id_2, $role_id_3));
	if (empty($db_ret)) {
		return false;
	} else {
		return true;
	}
}

function get_edge_attributes() {
	global $DB;

	// Get the tag ID of BrookesEDGE
	$criteria = "rawname = 'BrookesEDGE'";
    if (!($tag = $DB->get_record_select('tag', $criteria, null, 'id'))) {
		throw new invalid_parameter_exception('BrookesEDGE tag not found');
	}
		
	// Store the ID of each related tag (EDGE attribute) in an array
	$criteria = "tagid = '" . $tag->id . "' AND itemtype = 'tag'";
	$db_ret = $DB->get_records_select('tag_instance', $criteria, null, 'itemid');
	$ids = array();
	foreach ($db_ret as $row) {
		$ids[] = $row->itemid;
	}
		
	// Get the details of the related tags (EDGE attributes) and store them in an array sorted by name
	$db_ret = $DB->get_records_list('tag', 'id', $ids, 'name', 'id, rawname');
	$attributes = array();
	foreach ($db_ret as $row) {
		$pos_open = strpos($row->rawname, ' (');
		if ($pos_open !== false) {
			$pos_close = strpos($row->rawname, ')', $pos_open + 2);
			if ($pos_close !== false) {
				$attribute[substr($row->rawname, 0, $pos_open)] = substr($row->rawname, ($pos_open + 2), ($pos_close - $pos_open - 2));
			}
		}
	}

	return $attributes;
}

function get_activities() {
	global $DB;
			
	$sql = 'select c.shortname, c.fullname, c.visible, substr(c.idnumber, 6) as codes
		from {course} c
		where c.idnumber like "EDGE~%~%~%"
		order by c.shortname';

	return $DB->get_records_sql($sql);
}

function get_enrolments($date_from, $date_to) {
	global $DB;
	
	$time_to = $date_to + 86399; // 1 second before midnight
	
	$sql = 'SELECT u.id as user_id, u.firstname, u.lastname, u.username as student_number, c.idnumber AS activity_id, c.shortname AS activity_name, c.fullname AS activity_full_name
	FROM {user} u
	JOIN {user_enrolments} ue ON ue.userid = u.id
	JOIN {enrol} e ON e.id = ue.enrolid
	JOIN {role_assignments} ra ON ra.userid = u.id
	JOIN {context} ct ON ct.id = ra.contextid
	AND ct.contextlevel = 50
	JOIN {course} c ON c.id = ct.instanceid
	AND e.courseid = c.id
	JOIN {role} r ON r.id = ra.roleid
	AND r.shortname =  "student"
	WHERE c.idnumber LIKE "EDGE~%~%~%"
	AND ue.timecreated >= ?
	AND ue.timecreated <= ?
	order by u.lastname, u.firstname';

	return $DB->get_records_sql($sql, array($date_from, $time_to));
}

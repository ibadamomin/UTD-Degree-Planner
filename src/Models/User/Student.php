<?php

namespace DegreePlanner\Models\User;

use DegreePlanner\Database\Database;
use DegreePlanner\Models\Course\Course;
use DegreePlanner\Models\Major\Major;

class Student extends User {
    public $advisor;
    public array $majors;

    public function __construct($userDetails) {
        parent::__construct($userDetails);
        $this->role = 'student';
        $this->advisor = $userDetails['advisor_id'];
        $this->majors = array();
        $this->addMajor($userDetails);
    }

    public function addMajor($detailsArr): void {
        if (isset($detailsArr["major_id"], $detailsArr["name"], $detailsArr["degree_type"])) {
            $majorDetails = array(
                "major_id" => $detailsArr["major_id"],
                "name" => $detailsArr["name"],
                "degree_type" => $detailsArr["degree_type"]
            );
            $major = new Major($majorDetails);
            if (!$this->hasMajor($major)) {
                $this->majors[] = $major;
            }
        }
    }

    public function hasMajor($major): bool {
        return in_array($major, $this->majors);
    }

    public function getCompletedAndCurrentCourses() {
        $db = new Database();
        $db = $db->db();

        $q = <<<EOT
        SELECT course_id, instructor_id, course_prefix, course_no, course_section, course_name, semester, course_year 
        FROM student_course 
        NATURAL JOIN COURSES 
        WHERE student_id = ?
        EOT;
        $stmt = $db->prepare($q);

        if (!$stmt) {
            $db->close();
            return null;
        }

        $stmt->bind_param("s", $this->id);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            $db->close();
            $stmt->close();
            return null;
        }

        $courses = array();
        while ($row = $result->fetch_assoc()) {
            $course = new Course($row);
            $courses[] = $course;
        }

        $stmt->close();
        $db->close();
        return $courses;
    }

}
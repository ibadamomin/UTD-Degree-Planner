<?php

namespace DegreePlanner\Models\Course;

use DegreePlanner\Database\Database;

class Course {
    public $courseId;
    public $instructorId;
    public $coursePrefix;
    public $courseNo;
    public $courseSection;
    public $courseName;
    public $semester;
    public $courseYear;

    public function __construct($courseDetails) {
        $this->courseId = $courseDetails["course_id"];
        $this->instructorId = $courseDetails["instructor_id"];
        $this->coursePrefix = $courseDetails["course_prefix"];
        $this->courseNo = $courseDetails["course_no"];
        $this->courseSection = $courseDetails["course_section"];
        $this->courseName = $courseDetails["course_name"];
        $this->semester = $courseDetails["semester"];
        $this->courseYear = $courseDetails["course_year"];


    }
}
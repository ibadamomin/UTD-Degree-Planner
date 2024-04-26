<?php

namespace DegreePlanner\Models\User;

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

    public function addMajor($detailsArr) {
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

    public function hasMajor($major) {
        return in_array($major, $this->majors);
    }


}
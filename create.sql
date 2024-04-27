CREATE DATABASE IF NOT EXISTS degreeplanner;

USE degreeplanner;

-- Create users table. Students, faculty, and admins are users.
CREATE TABLE IF NOT EXISTS users (
    net_id        VARCHAR(50)  PRIMARY KEY,
    email         VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) NOT NULL, -- argon2id
    first_name    VARCHAR(50)  NOT NULL,
    middle_name   VARCHAR(50)  NULL,
    last_name     VARCHAR(50)  NOT NULL,

    CONSTRAINT uc_email UNIQUE (email) -- no duplicate emails
);

-- Create admins table. Admins may peform CRUD operations on the database using the admin portal.
CREATE TABLE IF NOT EXISTS admins (
    net_id VARCHAR(50) PRIMARY KEY,

    CONSTRAINT fk_admin_id
        FOREIGN KEY (net_id) REFERENCES users(net_id) ON UPDATE CASCADE ON DELETE CASCADE
);

-- Create faculty table. Faculty are instructors or advisors.
CREATE TABLE IF NOT EXISTS faculty (
    net_id VARCHAR(50) PRIMARY KEY,

    CONSTRAINT faculty_ibfk_1
        FOREIGN KEY (net_id) REFERENCES users (net_id) ON UPDATE CASCADE ON DELETE CASCADE
);

-- Create students table. Each student has an advisor.
CREATE TABLE IF NOT EXISTS students (
    net_id     VARCHAR(50) PRIMARY KEY,
    advisor_id VARCHAR(50) NULL,

    CONSTRAINT students_ibfk_1
        FOREIGN KEY (net_id) REFERENCES users (net_id) ON UPDATE CASCADE ON DELETE CASCADE,

    CONSTRAINT students_ibfk_2
        FOREIGN KEY (advisor_id) REFERENCES faculty (net_id) ON UPDATE CASCADE ON DELETE CASCADE
);

create index advisor_id on students (advisor_id);

-- Create majors table. Which represents a degree and major.
CREATE TABLE IF NOT EXISTS majors (
    major_id    int AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(50) NOT NULL, -- Ex. Computer Science
    degree_type VARCHAR(50) NOT NULL, -- Ex. Bachelor of Science

    -- Prevent duplicates due to surrogate key
    CONSTRAINT uc_degree UNIQUE (name, degree_type)
);

-- Create majors_in table. A student may major in many majors.
CREATE TABLE IF NOT EXISTS majors_in
(
    net_id   VARCHAR(50) NOT NULL,
    major_id int         NOT NULL,

    PRIMARY KEY (net_id, major_id),

    CONSTRAINT fk_major
        FOREIGN KEY (major_id) REFERENCES majors (major_id) ON UPDATE CASCADE, -- Deny any major deletion if in use.

    CONSTRAINT fk_net_id 
        FOREIGN KEY(net_id) REFERENCES students(net_id) ON UPDATE CASCADE ON DELETE CASCADE -- Delete if the user is deleted.
);

-- CREATE TABLE IF NOT EXISTS courses. Represents a specific course and section during a semester.
-- Course names may change from semester to semester so some redundancy is required for accuracy.
CREATE TABLE IF NOT EXISTS courses
(
    course_id      INT AUTO_INCREMENT                          PRIMARY KEY,
    instructor_id  VARCHAR(50),
    course_prefix  VARCHAR(5)                                  NOT NULL,
    course_no      INT                                         NOT NULL,
    course_section INT                                         NOT NULL,
    course_name    VARCHAR(100)                                NOT NULL,
    semester       enum ('Spring', 'Summer', 'Fall', 'Winter') NOT NULL,
    course_year    year                                        NOT NULL,

    -- Some professors may leave, we still need the course.
    CONSTRAINT fk_instructor
        FOREIGN KEY (instructor_id) REFERENCES faculty (net_id) ON UPDATE CASCADE ON DELETE SET NULL,

    -- A course section in a specific semester is unique.
    CONSTRAINT uc_course
        UNIQUE (course_prefix, course_no, course_section, semester, course_year)
);

-- Create student_course table representing the courses a student has or is taking (depending on the completed bool).
CREATE TABLE IF NOT EXISTS student_course
(
    student_id VARCHAR(50)           NOT NULL,
    course_id  INT                   NOT NULL,
    completed  BOOL DEFAULT false    NOT NULL,

    -- Repeats will just modify completed.
    PRIMARY KEY (student_id, course_id),

    CONSTRAINT fk_course
        FOREIGN KEY (course_id) REFERENCES courses(course_id),

    CONSTRAINT fk_student
        FOREIGN KEY (student_id) REFERENCES students (net_id)
);

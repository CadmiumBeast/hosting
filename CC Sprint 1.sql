DROP DATABASE IF EXISTS CCassignment1;


CREATE DATABASE CCassignment1;
USE CCassignment1;

-- Users Table
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, 
    name VARCHAR(50) NOT NULL,
    google_access_token TEXT,
    google_refresh_token TEXT,
    `role` VARCHAR(50) NOT NULL 
);

-- Discussion Room Table
CREATE TABLE discussionroom (
    discroom_id INT AUTO_INCREMENT PRIMARY KEY,
    discroom_name VARCHAR(50) NOT NULL,
    Location VARCHAR(50) NOT NULL
);

CREATE TABLE lecturer_timeslots (
  id INT AUTO_INCREMENT PRIMARY KEY,
  lecturer_id int(11) NOT NULL,
  dayofweek varchar(20) NOT NULL,
  start_time time NOT NULL,
  end_time time NOT NULL,
  active tinyint(1) DEFAULT 1
);

-- Discussion Room Booking Table
CREATE TABLE discroombooking (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    discroom_id INT NOT NULL,
    date DATE NOT NULL,
    timeslot VARCHAR(20) NOT NULL, 
    purpose TEXT NULL,
    numStudents INT NOT NULL DEFAULT 1, 
    status VARCHAR(20) DEFAULT 'Pending', 
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (discroom_id) REFERENCES discussionroom(discroom_id) ON DELETE CASCADE
);
CREATE TABLE appointbooking (
    appointment_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,   -- the one who books the appointment
    lecturer_id INT NOT NULL,   -- the one with whom the appointment is booked
    date DATE NOT NULL,
    timeslot_id INT NOT NULL, 
    purpose TEXT NULL, 
    status VARCHAR(20) DEFAULT 'Approved',
    google_event_id VARCHAR(255) NOT NULL, 
    FOREIGN KEY (student_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (lecturer_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (timeslot_id) REFERENCES lecturer_timeslots(id) ON DELETE CASCADE

);

CREATE TABLE appointmentnote (
    note_id INT AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT NOT NULL,
    note VARCHAR(255) NOT NULL,
    
    FOREIGN KEY (appointment_id) REFERENCES appointbooking(appointment_id) ON DELETE CASCADE
);


-- Set auto-increment values
ALTER TABLE users AUTO_INCREMENT = 5000;
ALTER TABLE discussionroom AUTO_INCREMENT = 300;
ALTER TABLE discroombooking AUTO_INCREMENT = 10000;
ALTER TABLE discroombooking AUTO_INCREMENT = 15000;



-- Insert Users
INSERT INTO users (email, password, name, `role`) VALUES
('cb000001@students.apiit.lk', 'password1', 'Sarith', 'Student'),
('cb000002@students.apiit.lk', 'password2', 'Steve', 'Student'),
('cb000003@students.apiit.lk', 'password3', 'Aleesha', 'Student'),
('cb000004@students.apiit.lk', 'password4', 'Vahini', 'Student'),
('cb000005@students.apiit.lk', 'password5', 'Amrah', 'Student'),
('cb000006@students.apiit.lk', 'password6', 'Senuka', 'Student'),
('cb000007@students.apiit.lk', 'password7', 'Hussein', 'Student'),
('cb000008@students.apiit.lk', 'password8', 'Abdullah', 'Student'),
('cb000009@students.apiit.lk', 'password9', 'Chenuli', 'Student'),
('cb000010@students.apiit.lk', 'password10', 'Amna', 'Student'),
('zeenath@apiit.lk', 'password11', 'Zeenath', 'Lecturer'),
('shani@apiit.lk', 'password12', 'Shani', 'Lecturer'),
('salinda@apiit.lk', 'password13', 'Salinda', 'Lecturer'),
('sajid@apiit.lk', 'password14', 'Sajid', 'Lecturer'),
('musharraf@apiit.lk', 'password15', 'Musharraf', 'Lecturer'),
('tharanga@apiit.lk', 'password16', 'Tharanga', 'Lecturer'),
('kavinkumar@apiit.lk', 'password17', 'Kavinkumar', 'Lecturer'),
('krishnadeva@apiit.lk', 'password18', 'Krishnadeva', 'Lecturer'),
('yovini@apiit.lk', 'password19', 'Yovini', 'Lecturer'),
('wathsala@apiit.lk', 'password20', 'Wathsala', 'Librarian');

-- Insert Discussion Rooms
INSERT INTO discussionroom (discroom_name, Location) VALUES
('Discussion Room 1','City'),
('Discussion Room 2','City'),
('Discussion Room 3','City');


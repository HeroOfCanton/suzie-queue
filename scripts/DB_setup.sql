drop database ta_queue;

create database ta_queue;

use ta_queue;

--User data;
create table users(
  username    VARCHAR(256),
  first_name  VARCHAR(32) NOT NULL,
  last_name   VARCHAR(32) NOT NULL,
  full_name   VARCHAR(64) NOT NULL,
  first_login TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  last_login  TIMESTAMP,
  primary key (username)
);

--Course data;
create table courses(
  course_id int NOT NULL AUTO_INCREMENT,
  depart_pref VARCHAR(16) NOT NULL,
  course_num  VARCHAR(16) NOT NULL,
  course_name VARCHAR(128) UNIQUE,
  professor   VARCHAR(128), 
  description TEXT,
  ldap_group  VARCHAR(256),
  primary key (course_id),
  foreign key (professor) references users(username) ON DELETE SET NULL
);

--Students enrolled in course;
create table enrolled(
  username    VARCHAR(256),
  course_id   int NOT NULL,
  primary key (username, course_id),
  foreign key (username) references users(username) ON DELETE CASCADE,
  foreign key (course_id) references courses(course_id) ON DELETE CASCADE
);


--State of each queue;
--Closed queues don't appear here
create table queue_state(
  course_id  int,
  state      ENUM('open','paused') NOT NULL,
  time_lim   int DEFAULT 0 NOT NULL,
  primary key (course_id),
  foreign key (course_id) references courses(course_id) ON DELETE CASCADE
);

--Master queue for all courses;
--foreign key contraints guarantee student is enrolled in course
--  and queue is open
create table queue(
  position   BIGINT AUTO_INCREMENT,
  username   VARCHAR(256) NOT NULL,
  course_id  int NOT NULL,
  question   TEXT,
  location   VARCHAR(256) NOT NULL,
  primary key (position),
  foreign key (username, course_id) references enrolled(username, course_id) ON DELETE CASCADE,
  foreign key (course_id) references queue_state(course_id) ON DELETE CASCADE,
  unique (username, course_id)
);

--State of each TA on duty--
create table ta_status(
  username   VARCHAR(256) NOT NULL,
  course_id  int NOT NULL,
  helping    BIGINT,
  primary key (username, course_id),
  foreign key (username) references users(username) ON DELETE CASCADE,
  foreign key (course_id) references queue_state(course_id) ON DELETE CASCADE,
  foreign key (helping) references queue(position) ON DELETE SET NULL
);

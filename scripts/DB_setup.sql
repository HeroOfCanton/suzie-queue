drop database ta_queue;

create database ta_queue;

use ta_queue;

--User data;
create table users(
  username    VARCHAR(256),
  first_name  VARCHAR(32),
  last_name   VARCHAR(32),
  full_name   VARCHAR(64),
  first_login TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  last_login  TIMESTAMP,
  primary key (username)
);

--Course data;
create table courses(
  course_id int NOT NULL AUTO_INCREMENT,
  depart_pref VARCHAR(16),
  course_num  VARCHAR(16),
  course_name VARCHAR(128) UNIQUE,
  description TEXT,
  ldap_group  VARCHAR(256),
  primary key (course_id)
);

--Students enrolled in course;
create table enrolled(
  username    VARCHAR(256),
  course_id   int,
  primary key (username, course_id),
  foreign key (username) references users(username),
  foreign key (course_id) references courses(course_id)
);



--State of each queue;
create table queue_state(
  course_id  int,
  state      ENUM('open','closed','paused'),
  time_lim   int
);

--Master queue for all courses;
create table queue(
  position   BIGINT NOT NULL AUTO_INCREMENT,
  username   VARCHAR(256),
  course_id  int,
  question   TEXT,
  location   VARCHAR(256),
  primary key (position),
  foreign key (username) references users(username),
  foreign key (course_id) references courses(course_id)
);

create table ta_status(
  username   VARCHAR(256),
  course_id  int,
  helping    BIGINT,
  primary key (username, course_id),
  foreign key (username) references users(username),
  foreign key (course_id) references courses(course_id),
  foreign key (helping) references queue(position)
);


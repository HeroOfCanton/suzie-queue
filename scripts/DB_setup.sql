drop database ta_queue;

create database ta_queue;

use ta_queue;
create table users(
  username    VARCHAR(256),
  first_name  VARCHAR(32),
  last_name   VARCHAR(32),
  full_name   VARCHAR(64),
  first_login TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  last_login  TIMESTAMP,
  primary key (username)
);

create table courses(
  id int NOT NULL AUTO_INCREMENT,
  course_name VARCHAR(128),
  description TEXT,
  ldap_group  VARCHAR(256),
  primary key (id)
); 

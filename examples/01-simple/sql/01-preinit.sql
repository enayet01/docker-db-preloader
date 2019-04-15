-- create user test01:test01pw
CREATE USER 'test01'@'%' IDENTIFIED BY 'test01pw';
GRANT USAGE ON *.* TO 'test01'@'%' IDENTIFIED BY 'test01pw' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;

-- create db test01
CREATE DATABASE IF NOT EXISTS `test01`;

-- set permissions
GRANT ALL PRIVILEGES ON `test01`.* TO 'test01'@'%';

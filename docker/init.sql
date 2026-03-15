-- Creates the development and test databases.
-- This file runs automatically when the MySQL container starts for the first time.
-- To reset: docker compose down -v && docker compose up -d

CREATE DATABASE IF NOT EXISTS mydb_development;
CREATE DATABASE IF NOT EXISTS mydb_test;

GRANT ALL PRIVILEGES ON mydb_development.* TO 'user'@'%';
GRANT ALL PRIVILEGES ON mydb_test.*        TO 'user'@'%';

FLUSH PRIVILEGES;

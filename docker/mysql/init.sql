-- Init script para criar database e usuário para o projeto
CREATE DATABASE IF NOT EXISTS `lop_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'lop_user'@'%' IDENTIFIED BY 'lop_pass';
GRANT ALL PRIVILEGES ON `lop_db`.* TO 'lop_user'@'%';
FLUSH PRIVILEGES;

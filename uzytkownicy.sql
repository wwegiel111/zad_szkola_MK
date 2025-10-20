CREATE USER 'user1'@'localhost' IDENTIFIED BY 'user1';
GRANT SELECT ON szkola.uczniowie TO 'uczen_czyta'@'localhost';

CREATE USER 'user2'@'localhost' IDENTIFIED BY 'user2';
GRANT SELECT, INSERT, UPDATE, DELETE ON szkola.uczniowie TO 'uczen_admin'@'localhost';

FLUSH PRIVILEGES;
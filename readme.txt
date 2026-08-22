C:\dev\RY\IT30a\backups

1. CREATE DATABASE <database_name>;
2. SHOW DATABASES;
3. CONNECT <database_name>;
4. CREATE TABLE <table_name_in_plural> ();
5. INSERT INTO <table_name_in_plural>
    (columns)
    VALUES(values);

Utility Commands
\! cls
mysqldump -u root -p --databases library_db > C:\dev\RY\IT30a\backups\08182026_library_db.sql
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

mysqldump -u root -p --databases library_db > C:\dev\RY\IT30a\backups\08182026_library_db2.sql

mysqldump -u root -p --databases library_db > "C:\dev\RY\IT30a\backups\%date:~-4%_%date:~4,2%_%date:~7,2%_%time:~0,2%_%time:~3,2%_%time:~6,2%_library_db2.sql"

%date:~-4%_
%date:~4,2%_
%date:~7,2%_
%time:~0,2%_
%time:~3,2%_
%time:~6,2%_
library_db2.sql"
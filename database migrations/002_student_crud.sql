--Student Query #1
SELECT * FROM students;

--Student Query #2 Select students order by id
Select *from students 
    ORDER BY student_id ASC;

--Student Query #3 Select students order by id DESC
Select *from students
    ORDER BY student_id DESC;


--Student Query #4 Select students order by last name ASC
Select
    student_first_name,
    student_last_name
FROM students
ORDER BY student_last_name ASC;

--Student Query #5 Select students order by last name DESC
Select
    student_first_name,
    student_last_name
FROM students
ORDER BY student_last_name DESC;

    --Student Query #6 Select students order by first name ASC
Select
    student_first_name,
    student_last_name
FROM students
ORDER BY student_first_name ASC;

    --Student Query #7 Select students order by first name DESC
Select
    student_first_name,
    student_last_name
FROM students
ORDER BY student_first_name DESC;

    --Student Query #8 Select students with specific id number
Select
    student_first_name,
    student_last_name
FROM students
WHERE student_id = 1
LIMIT 1;

    --Student Query #9 Update student first name, last name using specific id
UPDATE students

SET student_first_name = 'ry',
    student_last_name = 'chuu'

WHERE student_id = 1
LIMIT 1;

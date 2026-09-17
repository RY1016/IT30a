--Student Query #1

SELECT * FROM books;

--Student Query #2 - Select students order by id ASC

SELECT * FROM books
ORDER BY book_id ASC;

--Student Query #3 - Select students order by id DESC

SELECT * FROM books
ORDER BY book_id DESC;

--Student Query #4 - Select books order by book_title ASC

SELECT 
    book_title,
    book_author
 FROM books
 ORDER BY book_title ASC;

--Student Query #5 - Select books order by book_title DESC

SELECT 
     book_title,
     book_author
 FROM books
 ORDER BY book_title DESC;

 --Student Query #6 - Select books order by book_author ASC

SELECT 
     book_author,
     book_title
 FROM books
 ORDER BY book_author ASC;

  --Student Query #7 - Select books order by book_author DESC

SELECT 
     book_author,
     book_title
 FROM books
 ORDER BY book_author DESC;



 --Student Query #8 - Select student with specific id number

SELECT 
     book_author,
     book_title
 FROM books
 WHERE book_id = 1
 LIMIT 1;

--Student Query #9 - Update student firstname, lastname using specific id

UPDATE books
SET

    book_title = 'Rain in Espanya',
    book_author = '4Umirict'
WHERE book_id = 1;
CREATE TABLE books (
    book_id INT PRIMARY KEY AUTO_INCREMENT,
    book_title VARCHAR(50) NOT NULL,
    book_author VARCHAR(100) NOT NULL,
    book_category VARCHAR(50) NOT NULL,
    book_created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO books (book_title, book_author, book_category) VALUES
('Atomic Habits','James Clear','Self-Help'),
('The Psychology of Money','Morgan Housel','Finance'),
('Trading in the Zone','Mark Doughlas','Trading Psychology');

CREATE TABLE borrow(
    borrow_id INT PRIMARY KEY AUTO_INCREMENT,

    student_id INT NOT NULL,
    book_id INT NOT NULL,

    borrow_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    book_return_date TIMESTAMP NULL DEFAULT NULL,

    Constraint fk_borrow_student FOREIGN KEY (student_id) 
     REFERENCES students(student_id),

    Constraint fk_borrow_book FOREIGN KEY (book_id)
     REFERENCES books(book_id)
);

INSERT INTO borrow (student_id, book_id) VALUES
(1, 2),
(1, 2),
(3, 3);

SELECT
    br.borrow_id,
    s.student_id,
    CONCAT(s.student_first_name, ' ', s.student_last_name) AS student_name,
    s.student_course,
    b.book_title,
    b.book_author,
    b.book_category,
    br.borrow_date
FROM borrow br
JOIN students s ON br.student_id = s.student_id
JOIN books b ON br.book_id = b.book_id
WHERE br.book_return_date IS NULL
ORDER BY br.borrow_date DESC;

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

-- Table #1: students

CREATE TABLE IF NOT EXISTS students (
    -- Primary key
    student_id INT AUTO_INCREMENT PRIMARY KEY,

    -- Student name
    student_first_name VARCHAR(50) NOT NULL,
    student_last_name VARCHAR(50) NOT NULL,

    -- Student course
    student_course VARCHAR(50) NOT NULL,

    -- Student created timestamp
    student_created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;


-- Table #2: books

CREATE TABLE IF NOT EXISTS books (
    -- Primary key
    book_id INT AUTO_INCREMENT PRIMARY KEY,

    -- Book details
    book_title VARCHAR(50) NOT NULL,
    book_author VARCHAR(50) NOT NULL,
    book_category VARCHAR(50) NOT NULL,

    -- Book created timestamp
    book_created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;


-- Table #3: borrows

CREATE TABLE IF NOT EXISTS borrows (
    -- Primary key
    borrow_id INT AUTO_INCREMENT PRIMARY KEY,

    -- Foreign keys
    student_id INT NOT NULL,
    book_id INT NOT NULL,

    -- Borrow timestamp
    borrow_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    -- Return timestamp
    borrow_return_date TIMESTAMP NULL DEFAULT NULL,

    -- Foreign key constraints
    CONSTRAINT fk_borrow_student
        FOREIGN KEY (student_id)
        REFERENCES students(student_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_borrow_book
        FOREIGN KEY (book_id)
        REFERENCES books(book_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;



-- Insert Students 

INSERT INTO students (
    student_first_name,
    student_last_name,
    student_course
)
VALUES
('Rica Jane', 'Novo', 'BSIT'),
('XAVIER', 'ALIEN', 'BS-HUNTER'),
('ZAYNE', 'SNOWMAN', 'MEDICAL/Cardiologist'),
('RAFAYEL', 'FIRE', 'ARTIST');


-- Insert Books

INSERT INTO books (
    book_title,
    book_author,
    book_category
)
VALUES
('Atomic Habits', 'James Clear', 'Self-Help'),
('The Psychology of Money', 'Morgan Housel', 'Finance'),
('Trading in the Zone', 'Mark Douglas', 'Trading Psychology');


-- Insert Borrow Transactions

INSERT INTO borrows (
    student_id,
    book_id
)
VALUES
(1, 1),
(2, 1),
(3, 3);
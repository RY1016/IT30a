    <?php
    //Database Connection
    $host = 'localhost';
    $db = 'it30a_lab_db';
    $user = 'root';
    $pass = '';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        echo 'Database connection failed: ' . $e->getMessage();
    }


    // Session
    session_start();

    //Determine the current Section
    $section = $_GET['section'] ?? 'students';

    //CRUD operations
    $action = $_GET['action'] ?? '';

    //----------------------------------------------
    // Students
    //----------------------------------------------

    //Fetch students
    if ($section === 'students') {
        $stmt = $pdo->query("
            SELECT *
            FROM students
            ORDER BY student_id DESC
            ");
        $students = $stmt->fetchAll();
    }

    //Fetch Books
    if ($section === 'books') {
        $stmt = $pdo->query("
            SELECT *
            FROM books
            ORDER BY book_id DESC
            ");
        $books = $stmt->fetchAll();
    }
    //--------------------
    //STUDENTS
    //--------------------
    //Create a new student
    if ($section === 'students' && $action === 'create') {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $first_name = $_POST['first_name'] ?? '';
            $last_name = $_POST['last_name'] ?? '';
            $course = $_POST['course'] ?? '';

            if ($first_name && $last_name && $course !== '') {

                $sql="
                INSERT INTO students (
                student_first_name, 
                student_last_name, 
                student_course) 
                VALUES (?, ?, ?)";

                $stmt = $pdo->prepare($sql);

                $stmt->execute([
                    $first_name,
                    $last_name,
                    $course
                ]);

                $_SESSION['alert'] = 'Student saved successfully.';

                // Redirect to the students section after successful creation
                header('Location: index.php?section=students');
                exit;
            }
        }
    }
    //Update student
    if ($section === 'students' && $action === 'update') {
        $student_id = (int) ($_GET['id'] ?? 0);

        // Retrieve Student Information
        $stmt = $pdo->prepare("
            SELECT *
            FROM students
            WHERE student_id = ?
        ");
        
        $stmt->execute([$student_id]);
        $student = $stmt->fetch();
    
        //Update Student Info
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $first_name = $_POST['first_name'] ?? '';
            $last_name = $_POST['last_name'] ?? '';
            $course = $_POST['course'] ?? '';

            if ($first_name && $last_name && $course !== '') {

                $sql="
                UPDATE students
                SET student_first_name = ?, 
                    student_last_name = ?, 
                    student_course = ?
                WHERE student_id = ?";

                $stmt = $pdo->prepare($sql);

                $stmt->execute([
                    $first_name,
                    $last_name,
                    $course,
                    $student_id
                ]);

                $_SESSION['alert'] = 'Student updated successfully.';

                // Redirect to the students section after successful update
                header('Location: index.php?section=students');
                exit;
            }
        }
    }

    //--------------------
    //BOOKS
    //--------------------


    //Create a new Book
    if ($section === 'books' && $action === 'create') {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $bookTitle = $_POST['book_title'] ?? '';
            $bookAuthor = $_POST['book_author'] ?? '';
            $bookCategory = $_POST['book_category'] ?? '';

            if ($bookTitle && $bookAuthor && $bookCategory !== '') {

                $sql = "
                INSERT INTO books (
                    book_title,
                    book_author,
                    book_category
                )
                VALUES (?, ?, ?)
                ";

                $stmt = $pdo->prepare($sql);

                $stmt->execute([
                    $bookTitle,
                    $bookAuthor,
                    $bookCategory
                ]);

                // Redirect to the books section after successful creation
                header("Location: index.php?section=books");
                exit;
            }
        }
    }

    //Update Book
    if ($section === 'books' && $action === 'update') {
        $book_id = (int) ($_GET['id'] ?? 0);

        // Retrieve Book Information
        $stmt = $pdo->prepare("
            SELECT *
            FROM books
            WHERE book_id = ?
        ");

        $stmt->execute([$book_id]);
        $book = $stmt->fetch();

        //Update Book Info
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $bookTitle = $_POST['book_title'] ?? '';
            $bookAuthor = $_POST['book_author'] ?? '';
            $bookCategory = $_POST['book_category'] ?? '';

            if ($bookTitle && $bookAuthor && $bookCategory !== '') {

                $sql = "
                UPDATE books
                SET book_title = ?,
                    book_author = ?,
                    book_category = ?
                WHERE book_id = ?";

                $stmt = $pdo->prepare($sql);

                $stmt->execute([
                    $bookTitle,
                    $bookAuthor,
                    $bookCategory,
                    $book_id
                ]);

                $_SESSION['alert'] = 'Book updated successfully.';

                // Redirect to the Books section
                header('Location: index.php?section=books');
                exit;
            }
        }
 }


    //--------------------
    //BORROWS
    //--------------------

    //Fetch Borrow Records
     if ($section === 'borrows') {

                //Fetch Borrow Records
                if ($section === 'borrows') {

                    // Fetch Borrow Records
                    $stmt = $pdo->query("
                        SELECT
                            borrows.borrow_id,
                            borrows.student_id,
                            students.student_first_name,
                            students.student_last_name,
                            borrows.book_id,
                            books.book_title,
                            borrows.borrow_date,
                            borrows.borrow_return_date
                        FROM borrows
                        INNER JOIN students
                            ON borrows.student_id = students.student_id
                        INNER JOIN books
                            ON borrows.book_id = books.book_id
                        ORDER BY borrows.borrow_id DESC
                    ");

                    $borrows = $stmt->fetchAll();

                    // Fetch Books for borrow form
                    $stmt = $pdo->query("
                        SELECT
                            book_id,
                            book_title,
                            book_author
                        FROM books
                        ORDER BY book_title
                    ");

                    $books = $stmt->fetchAll();
                }
     }
    //Borrow A Book
    if ($section === 'borrows' && $action === 'create') {
     if ($_SERVER['REQUEST_METHOD'] === 'POST') {

                $studentId = (int) ($_POST['student_id'] ?? 0);
                $bookId = (int) ($_POST['book_id'] ?? 0);

                // Check if student has an unreturned book
                if ($studentId > 0 && $bookId > 0) {

                    $stmt = $pdo->prepare("
                        SELECT borrow_id
                        FROM borrows
                        WHERE student_id = ?
                        AND borrow_return_date IS NULL
                        LIMIT 1
                    ");

                    $stmt->execute([$studentId]);

                    $studentBorrow = $stmt->fetch();

                    if ($studentBorrow) {

                        $_SESSION['alert'] =
                            'This student cannot borrow another book because a previous book has not been returned.';

                    } else {

                        // Check if book is already borrowed
                        $stmt = $pdo->prepare("
                            SELECT borrow_id
                            FROM borrows
                            WHERE book_id = ?
                            AND borrow_return_date IS NULL
                            LIMIT 1
                        ");

                        $stmt->execute([$bookId]);

                        $bookBorrow = $stmt->fetch();

                        if ($bookBorrow) {

                            $_SESSION['alert'] =
                                'This book cannot be borrowed because it has not been returned.';

                        } else {

                            // Create borrow record
                            $stmt = $pdo->prepare("
                                INSERT INTO borrows (
                                    student_id,
                                    book_id,
                                    borrow_date
                                )
                                VALUES (?, ?, NOW())
                            ");

                            $stmt->execute([
                                $studentId,
                                $bookId
                            ]);

                            $_SESSION['alert'] = 'Book borrowed successfully';
                        }
                    }

                    header("Location: index.php?section=borrows");
                    exit;
                }
            }
    }
    //Return A Book
    if ($section === 'borrows' && $action === 'update') {

    $borrowId = (int) ($_GET['id'] ?? 0);

    if ($borrowId > 0) {

        $stmt = $pdo->prepare("
            UPDATE borrows
            SET borrow_return_date = NOW()
            WHERE borrow_id = ?
            AND borrow_return_date IS NULL
        ");

        $stmt->execute([$borrowId]);

        $_SESSION['alert'] = 'Book returned successfully';

        header("Location: index.php?section=borrows");
        exit;
    }
    }

    
?>
        <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Library Systems</title>
    </head>
<body>
        <h1>Simple Library Systems</h1>

        <nav>
            <a href="index.php?section=students">Students</a>
            <a href="index.php?section=books">Books</a>
            <a href="index.php?section=borrows">Borrows</a>
        </nav>
        <hr>
        <?php if ($section === 'students'): ?>
            <h1>Students</h1>
            <p>
                <a href="index.php?section=students&action=create">
                    Add New Student
                </a>
            </p>
            <?php if ($action === 'create'): ?>
                <h2>Add New Student</h2>
                <form method="POST">
                    <!-- Form fields for adding a new student -->
                     <p>
                        <label>First Name:</label>
                        <br>
                        <input type="text" 
                            id="first_name" 
                            name="first_name" 
                            required
                            />
                     </p>

                      <p>
                        <label>Last Name:</label>
                        <br>
                        <input type="text" 
                        id="last_name" 
                        name="last_name" 
                        required
                        />
                     </p>

                      <p>
                        <label>Course</label>
                        <br>
                        <input type="text" 
                        id="course" 
                        name="course" 
                        required
                        />
                     </p>

                    <button type="submit">
                        Save
                    </button>
                    <a href = "index.php?section=students">
                        Cancel
                    </a>
                </form>

            <?php elseif ($action === 'update'): ?>
                <h2>Update Student</h2>
                <form method="POST">
                    <!-- Form fields for adding a new student -->
                     <p>
                        <label>First Name:</label>
                        <br>
                        <input type="text" 
                            id="first_name" 
                            name="first_name" 
                            value="<?= htmlspecialchars($student['student_first_name']) ?>"
                            required
                            />
                     </p>

                      <p>
                        <label>Last Name:</label>
                        <br>
                        <input type="text" 
                        id="last_name" 
                        name="last_name" 
                        value="<?= htmlspecialchars($student['student_last_name']) ?>"
                        required
                        />
                     </p>

                      <p>
                        <label>Course</label>
                        <br>
                        <input type="text" 
                        id="course" 
                        name="course" 
                        value="<?= htmlspecialchars($student['student_course']) ?>"
                        required
                        />
                     </p>

                    <button type="submit">
                        Update
                    </button>
                    <a href = "index.php?section=students">
                        Cancel
                    </a>
                </form>

            <?php else: ?>
<table border = '1'>
        <thead>
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Course</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($students as $student): ?>
                <tr>
                    <td><?= htmlspecialchars($student['student_id']) ?></td>
                    <td><?= htmlspecialchars($student['student_first_name']) ?></td>
                    <td><?= htmlspecialchars($student['student_last_name']) ?></td>
                    <td><?= htmlspecialchars($student['student_course']) ?></td>
                    <td><?= htmlspecialchars($student['student_created_at']) ?></td>

                    <td>
                        <a href = "index.php?section=students&action=update&id=<?= $student['student_id'] ?>">Edit</a>
                        |
                        <a h>Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>

</table>
            <?php endif; ?>
           <?php endif; ?>

<?php if ($section === 'students'): ?>
    <?php endif; ?>

<?php if ($section === 'books'): ?>
    <h1>Books</h1>

    <p>
        <a href="index.php?section=books&action=create">
            Add New Book
        </a>
    </p>

    <?php if ($action === 'create'): ?>

        <h2>Add New Book</h2>

        <form method="POST">

            <p>
                <label>Book Title:</label>
                <br>
                <input
                    type="text"
                    name="book_title"
                    required
                />
            </p>

            <p>
                <label>Book Author:</label>
                <br>
                <input
                    type="text"
                    name="book_author"
                    required
                />
            </p>

            <p>
                <label>Book Category:</label>
                <br>
                <input
                    type="text"
                    name="book_category"
                    required
                />
            </p>

            <button type="submit">
                Save
            </button>

            <a href="index.php?section=books">
                Cancel
            </a>

        </form>

    <?php elseif ($action === 'update'): ?>

        <h2>Update Book</h2>

        <form method="POST">

            <p>
                <label>Book Title:</label>
                <br>
                <input
                    type="text"
                    name="book_title"
                    value="<?= htmlspecialchars($book['book_title']) ?>"
                    required
                />
            </p>

            <p>
                <label>Book Author:</label>
                <br>
                <input
                    type="text"
                    name="book_author"
                    value="<?= htmlspecialchars($book['book_author']) ?>"
                    required
                />
            </p>

            <p>
                <label>Book Category:</label>
                <br>
                <input
                    type="text"
                    name="book_category"
                    value="<?= htmlspecialchars($book['book_category']) ?>"
                    required
                />
            </p>

            <button type="submit">
                Update
            </button>

            <a href="index.php?section=books">
                Cancel
            </a>

        </form>

    <?php else: ?>

        <table border="1">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Book Title</th>
                    <th>Book Author</th>
                    <th>Book Category</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($books as $book): ?>

                    <tr>

                        <td>
                            <?= htmlspecialchars($book['book_id']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($book['book_title']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($book['book_author']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($book['book_category']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($book['book_created_at']) ?>
                        </td>

                        <td>

                            <a href="index.php?section=books&action=update&id=<?= $book['book_id'] ?>">
                                Edit
                            </a>

                            |

                            <a href="index.php?section=books&action=delete&id=<?= $book['book_id'] ?>" onclick="return confirm('Are you sure you want to delete this book?');">
                                Delete
                            </a>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    <?php endif; ?>
    <?php endif; ?>




<?php if ($section === 'borrows'): ?>

    <h1>Borrows</h1>

    <p>
        <a href="index.php?section=borrows&action=create">
            Borrow A Book
        </a>
    </p>

    <?php if ($action === 'create'): ?>

        <h2>Borrow A Book</h2>

        <form method="POST">

            <p>
                <label>Student:</label>
                <br>

                <select name="student_id" required>

                    <option value="">
                        --Select Student--
                    </option>

                    <?php foreach ($students as $student): ?>

                        <option value="<?= $student['student_id'] ?>">

                            <?= htmlspecialchars(
                                $student['student_first_name']
                                . ' ' .
                                $student['student_last_name']
                            ) ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </p>


            <p>

                <label>Book:</label>
                <br>

                <select name="book_id" required>

                    <option value="">
                        --Select Book--
                    </option>

                    <?php foreach ($books as $book): ?>

                        <option value="<?= $book['book_id'] ?>">

                            <?= htmlspecialchars(
                                $book['book_title']
                                . ' - ' .
                                $book['book_author']
                            ) ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </p>


            <button type="submit">
                Borrow
            </button>

            <a href="index.php?section=borrows">
                Cancel
            </a>

        </form>


    <?php else: ?>

        <table border="1">

            <thead>

                <tr>
                    <th>Borrow ID</th>
                    <th>Student Name</th>
                    <th>Book Title</th>
                    <th>Borrow Date</th>
                    <th>Book Return Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>

            </thead>


            <tbody>

                <?php foreach ($borrows as $borrow): ?>

                    <tr>

                        <td>
                            <?= htmlspecialchars($borrow['borrow_id']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($borrow['student_first_name'] . ' ' . $borrow['student_last_name']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($borrow['book_title']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($borrow['borrow_date']) ?>
                        </td>

                        <td>
                            <?php if ($borrow['borrow_return_date'] === null): ?>
                                not Returned
                            <?php else: ?>
                                <?= htmlspecialchars($borrow['borrow_return_date']) ?>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php if ($borrow['borrow_return_date'] === null): ?>
                                Borrowed
                            <?php else: ?>
                                Returned
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php if ($borrow['borrow_return_date'] === null): ?>
                                <a href="index.php?section=borrows&action=update&id=<?= $borrow['borrow_id'] ?>">
                                    Return
                                </a>
                            <?php endif; ?>
                        </td>
                        

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    <?php endif; ?>

<?php endif; ?>

</body>


<?php if (isset($_SESSION['alert'])): ?>
                    <script>
                        alert(<?=json_encode($_SESSION['alert'])?>);
                    </script>

                <?php unset($_SESSION['alert']); ?>

<?php endif; ?>

    </html> 
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


    // Check current section
echo "Current section: " . $section;

    //Fetch students
    if ($section === 'students') {
        $stmt = $pdo->query(
            "SELECT *
            FROM students
            ORDER BY student_id DESC
            ");
        $students = $stmt->fetchAll();
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
            <table border="1">
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
                        <a>Edit</a>
                        |
                        <a>Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
        <?php endif; ?>

            <?php if ($section === 'students'): ?>
                
            <?php endif; ?>

            <?php if ($section === 'books'):?>
                <h1>Books</h1>
            <?php endif; ?>
            <?php if ($section === 'borrows'): ?>
                <h1>Borrows</h1>
            <?php endif; ?>
        </body>
    </html> 
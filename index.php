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
<table>
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
                        <a href = "index.php?section=students&action=update&id=<?= $student['student_id'] ?>?>">Edit</a>
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

            <?php if ($section === 'books'):?>
                <h1>Books</h1>
            <?php endif; ?>
            <?php if ($section === 'borrows'): ?>
                <h1>Borrows</h1>
            <?php endif; ?>
        </body>
                <?php if (isset($_SESSION['alert'])): ?>
                    <script>
                        alert(<?=json_encode($_SESSION['alert'])?>);
                    </script>

                <?php unset($_SESSION['alert']); ?>

                <?php endif; ?>

    </html> 
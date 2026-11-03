<?php
$currentSection = $_GET['section'] ?? 'dashboard';

function navActive($section, $currentSection) {
    return $section === $currentSection ? 'active' : '';
}

?>

<!--Material Icons-->

 <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

  <style>
    .app-header{
        display:flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap:1rem;
        font-family: Roboto,system-ui,-apple-system, sans-serif;
    }

    .app-header h1{
        font-weight: 400;
        font-size: 1.8rem;
        display: flex;
        align-items: center;
        gap:0.5rem;
        color:#212121;
        margin: 0;
    }

    .app-header h1 material-icons{
        font-size:2.2rem;
        color: #1976d2; 
    }

    .nav-links{
        display:flex;
        gap:0.25rem;
        background: transparent;
    }

    .nav-links a{
        text-decoration:none;
        color:#616161;
        font-size:0.9rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: .6rem 1.2rem;
        border-radius: 4px;
        transition: background-color 0.2s, color 0.2s;
        display:flex;
        align-items: center;
        gap:.35rem;
    }

    .nav-links a .material-icons{
        font-size:1.2rem;
    }

    .nav-links a:hover{
        background:#e3f2fd;
        color:#1976d2;
    }

    .nav-links a.active{
        background:#1976d2;
        color:white;
        box-shadow: 0 2px 2px 0 rgba(0, 0, 0, .14), 0 3px 1px -2px  rgba(0, 0, 0, .12), 0 1px 5px 0  rgba(0, 0, 0, .2);
    }


    .nav-link a.active .material-icons{
        color:white;
    }

    @media(max-width:760px){
        .app-header{
            flex-direction: column;
            align-items: flex-start;
        }

        .nav-links{
            width: 100%;
            overflow-x: auto;
            padding-bottom:  0.5rem;
        }

        .nav-links a{
            padding: .5rem .8rem;
            font-size: .8rem;
            white-space: nowrap;
        }
    }

 </style>


<header class="app-header">

    <h1>
        <span class="material-icons">dashboard</span>
        Library Dashboard
    </h1>

    <nav class="nav-links">

        <a href="?section=dashboard" class="<?= navActive('dashboard', $currentSection) ?>">
        <span class="material-icons">dashboard</span>
        Dashboard
        </a>

        <a href="?section=students" class="<?= navActive('students', $currentSection) ?>">
        <span class="material-icons">people</span> 
        Students
        </a>

        <a href="?section=books" class="<?= navActive('books', $currentSection) ?>">
        <span class="material-icons">menu_book</span>
        Books
        </a>

        <a href="?section=borrow" class="<?= navActive('borrow', $currentSection) ?>">
        <span class="material-icons">swap_horiz</span>
        Borrow
        </a>
        
    </nav>

</header>

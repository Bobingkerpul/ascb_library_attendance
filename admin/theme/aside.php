<style>
    .managebooks {
        cursor: pointer;
    }

    .sub-menu-books {
        display: none;
    }

    .sub-menu-books a {
        margin-left: 8px;
    }

    .sub-menu-books.active {
        display: block;
    }

    .nav-link {

        position: relative;
        z-index: 3;
        padding: 8px;
    }

    a.nav-link::after {
        content: '';
        display: block;
        width: 10%;
        background: rgba(72, 202, 228, 0.7);
        height: 100%;
        position: absolute;
        left: 0;
        bottom: 0;
        transition: 0.3s linear;
        opacity: 0;
        z-index: -1;
        border-radius: 6px;
    }

    a.nav-link:hover::after {
        width: 100%;
        opacity: 1;
    }
</style>

<aside class="sidebar p-4">
    <nav class="d-flex flex-column">
        <div class="admin-name">
            <div class="admin-profile d-flex flex-row align-items-center" style="gap:12px">
                <div class="admin-image">
                    <img src="../img/ascblogo.jpg" alt="image">
                </div>
                <div class="name">
                    <h6 class="m-0" style="color: var(--white); font-size:14px">Gracito P. Pingos, Jr.</h6>
                    <small style="color: var(--white); font-weight: 100; font-size:12px"><em>Library Head</em></small>
                </div>
            </div>
            <div class="admin-logout mt-4">
                <a href="../logout.php">
                    <button class="form-control" style="background: rgba(72, 202, 228, 1);
    border: none;">Logout</button>
                </a>
            </div>
            <hr style="border: 1px solid var(--white);" class="mt-4">
        </div>
        <div class="navbar-menu mt-4">
            <div class="">
                <ul class="p-0 gap-2 d-flex flex-column">
                    <li class="">
                        <a class="nav-link" href="./index.php">
                            <img src="../img/icons/dashboard.svg">
                            Dashboard
                        </a>
                    </li>
                    <li class="">
                        <a class="nav-link" href="./course.php">
                            <img src="../img/icons/course.svg">
                            Course
                        </a>
                    </li>
                    <li class="">
                        <a class="nav-link" href="./class.php">
                            <img src="../img/icons/class.svg">
                            Section
                        </a>
                    </li>
                    <li id="managebooks" class="managebooks">
                        <a class="nav-link sub-menu">
                            <img src="../img/icons/managebooks.svg">
                            Manage Books
                        </a>
                        <ul class="sub-menu-books" id="sub-menu-books">
                            <li><a class="nav-link" href="./addbooks.php">Add Books</a></li>
                            <li><a class="nav-link" href="./viewbooks.php">View Books</a></li>
                            <li><a class="nav-link" href="./borrowedbooks.php">Borrow-Return</a></li>
                        </ul>
                    </li>
                    <!-- <li class="">
                        <a class="nav-link" href="./faculty.php">
                            <img src="../img/icons/faculty.svg">
                            Faculty
                        </a>
                    </li> -->
                    <li class="">
                        <a class="nav-link" href="./filterattendace.php">
                            <img src="../img/icons/class.svg">
                            Attendance Report
                        </a>
                    </li>
                    <li class="">
                        <a class="nav-link" href="./masterlist.php">
                            <img src="../img/icons/students.svg">
                            List of Students
                        </a>
                    </li>
                </ul>
            </div>
        </div>

    </nav>
</aside>

<script>
    $(document).ready(function() {
        $("#managebooks").click(function() {
            $("#sub-menu-books").toggleClass('active').stop(true, true).slideToggle(300);
        })
    })
</script>
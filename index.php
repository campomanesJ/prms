<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>St. Joseph Parish Records Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body,
        html {
            height: 100%;
            font-family: 'Poppins', sans-serif;
        }

        .hero {
            background: url('assets/img/church.jpg') center center / cover no-repeat;
            position: relative;
            height: 100vh;
            color: #fff;
        }

        .hero::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
        }

        .hero-content {
            position: relative;
            z-index: 1;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            text-align: center;
            padding: 0 15px;
        }

        h1 {
            font-size: 5rem;
            font-weight: 900;
            line-height: 1.2;
            display: inline-block;
        }

        h1 span {
            display: inline-block;
            margin-right: 0.5rem;
            transition: color 0.3s ease, text-shadow 0.3s ease;
            cursor: default;
        }

        h1 span.hovered {
            color: #00ff00;
            text-shadow: 0 0 15px rgba(0, 255, 0, 0.8);
        }

        h1 span.nearby {
            color: rgba(0, 255, 0, 0.5);
            text-shadow: 0 0 10px rgba(0, 255, 0, 0.3);
        }

        p {
            font-size: 1.3rem;
            font-weight: 500;
        }

        /* Bible verse bottom left */
        .bible-verse {
            position: absolute;
            bottom: 40px;
            left: 20px;
            font-style: italic;
            font-size: 1rem;
            color: #f8f9fa;
            max-width: 400px;
            z-index: 2;
        }

        .bible-source {
            position: absolute;
            bottom: 20px;
            left: 20px;
            font-size: 0.85rem;
            color: #dcdcdc;
            font-style: normal;
            z-index: 2;
        }

        @media (max-width:768px) {
            h1 {
                font-size: 2.5rem;
            }

            p {
                font-size: 0.9rem;
            }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center fs-4" href="index.php">
                <img src="assets/img/loginlogo.png" alt="Logo" height="40" class="me-2" />
                <div class="d-flex flex-column lh-1">
                    <span class="text-white fw-bold mb-1">St. Joseph Parish</span>
                    <small class="text-white-50" style="font-size: 0.85rem;">Matalom, Leyte</small>
                </div>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="ms-auto navbar-nav">
                    <a class="nav-link ms-2 active" href="#">Home</a>
                    <a class="nav-link ms-2" href="searchRecord.php">Search Record</a>
                    <a class="nav-link ms-2" href="announcement.php">Announcements & Events</a>
                    <a class="nav-link ms-2" href="about.php">About</a>
                    <a class="nav-link ms-2 btn btn-warning border border-white" href="login/login.php">Login</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero">
        <div class="hero-content">
            <h1 id="hoverTitle">Welcome to St. Joseph Parish Records Management System</h1>
            <p>
                Securely manage certificates like baptism, marriage, and other parish records with ease and reliability.
                The system is currently under development, with a target completion date of December 31, 2025
            </p>
        </div>
        <!-- Bible verse placed bottom-left -->
        <p id="bibleVerse" class="bible-verse">Loading verse...</p>
        <p class="bible-source" style="font-size: 0.6rem;">New American Bible, Revised Edition (NABRE)</p>


    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Split heading into words (hover effect)
        const title = document.getElementById("hoverTitle");
        const words = title.textContent.split(" ");
        title.textContent = "";
        words.forEach((word, index) => {
            const span = document.createElement("span");
            span.textContent = word;
            title.appendChild(span);
            if (index < words.length - 1) {
                title.appendChild(document.createTextNode(" "));
            }
        });

        const spans = title.querySelectorAll("span");
        spans.forEach((span, i) => {
            span.addEventListener("mouseenter", () => {
                spans.forEach(s => s.classList.remove("hovered", "nearby"));
                span.classList.add("hovered");
                if (spans[i - 1]) spans[i - 1].classList.add("nearby");
                if (spans[i + 1]) spans[i + 1].classList.add("nearby");
            });
            span.addEventListener("mouseleave", () => {
                spans.forEach(s => s.classList.remove("hovered", "nearby"));
            });
        });

        // Fetch random Bible verse (Catholic-friendly fallback)
        async function fetchRandomVerse() {
            try {
                const response = await fetch("https://bible-api.com/?random=verse");
                if (!response.ok) throw new Error("Failed to fetch verse");
                const data = await response.json();

                document.getElementById("bibleVerse").textContent =
                    `"${data.text.trim()}" — ${data.reference}`;
            } catch (error) {
                document.getElementById("bibleVerse").textContent =
                    "“The Lord is my shepherd; I shall not want.” — Psalm 23:1";
            }
        }

        fetchRandomVerse();
    </script>
</body>

</html>
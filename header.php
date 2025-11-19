<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TRIP_AGENCY</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.5.0/chart.min.js" integrity="sha512-n/G+dROKbKL3GVngGWmWfwK0yPctjZQM752diVYnXZtD/48agpUKLIn0xDQL9ydZ91x6BiOmTIFwWjjFi2kEFg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #0081a7">

    <div class="container-fluid">

        <!--Logo o Brand-->
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="logo.png" alt="" style="height: 50px; width: auto;">
            Trip-Agency
        </a>

        <!--Bottone Hamburgher-->
        <button 
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNav"
                aria-controls="navbarNav"
                aria-expanded="false"
                aria-label="Toggle navigation"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="navbar-nav ms-auto">
                <!--Link del menu-->
                <a href="clienti.php" class="nav-link">Clienti</a>
                <a href="destinazioni.php" class="nav-link">Destinazioni</a>
                <a href="prenotazioni.php" class="nav-link">Prenotazioni</a>
                <a href="ricerca.php" class="nav-link">Ricerca</a>
                <a href="statistiche.php" class="nav-link">Statistiche</a>

            </div>
        </div>
    </div>
</nav>

<main class="container mt-4">





<nav>
    <ul class="nav">
        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="users.php">Utenti</a></li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#">Carte</a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="cards.php">Visualizza Carte</a></li>
                <li><a class="dropdown-item" href="add_card.php">Aggiungi Carta</a></li>
                <li><a class="dropdown-item" href="manage_categories.php">Gestisci Categorie</a></li>
            </ul>
        </li>
        <li class="nav-item"><a class="nav-link" href="manage_levels.php">Gestisci Livelli Utente</a></li>
        <li class="nav-item"><a class="nav-link" href="manage_points.php">Gestione Punteggi</a></li>
    </ul>
</nav>

<style>
    .nav {
        list-style-type: none;
        padding: 0;
    }
    .nav-item {
        display: inline-block;
        position: relative;
    }
    .nav-link {
        display: block;
        padding: 10px;
        text-decoration: none;
        color: #000;
    }
    .nav-link:hover {
        background-color: #f8f9fa;
    }
    .dropdown-menu {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        background-color: #fff;
        box-shadow: 0 8px 16px rgba(0,0,0,.3);
        list-style-type: none;
        padding: 0;
        margin: 0;
    }
    .dropdown-item {
        padding: 10px;
        text-decoration: none;
        color: #000;
        display: block;
    }
    .dropdown-item:hover {
        background-color: #f8f9fa;
    }
    .dropdown:hover .dropdown-menu {
        display: block;
    }
</style>

<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Home</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            overflow: hidden;
            height: 100%;
            width: 100%;
        }
        #particles-js {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 1;
        }
        .background-image {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            background: url('img/ingresso.jpg') no-repeat center center fixed;
            background-size: cover;
            z-index: 0;
        }
        .content {
            position: relative;
            z-index: 2;
            text-align: center;
            margin-top: 20px;
        }
        #play-music-button {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 3;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: none;
        }
        #play-music-button:hover {
            background-color: #0056b3;
        }

        .content {
  position: fixed;
  bottom: 0;
  text-align: center;
  width: 100%;
  padding-bottom: 36px;
}
    </style>
</head>
<body>
    <div class="background-image"></div>
    <div id="particles-js"></div>
    <audio id="background-music" loop>
        <source src="music/Alba_di_Eroi.mp3" type="audio/mpeg">
        Il tuo browser non supporta l'audio HTML5.
    </audio>
    <button id="play-music-button">Play Music</button>
    <div class="content">
        <?php if (isset($_SESSION['user_id'])) { ?>
            <a href="dashboard.php" class="btn btn-primary">Profilo</a>
            <a href="logout.php" class="btn btn-secondary">Logout</a>
        <?php } else { ?>
            <a href="login.php" class="btn btn-primary">Accedi</a>
            <a href="register.php" class="btn btn-secondary">Registrati</a>
        <?php } ?>
    </div>
    <script src="js/particles.min.js"></script>
    <script src="js/mainhome.js"></script>
</body>
</html>
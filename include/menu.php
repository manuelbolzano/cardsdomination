<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'include/db_connect.php';

$user_id = $_SESSION['user_id'];
$sql = "SELECT username, punti, profile_image, monete FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $punti, $profile_image, $monete);
$stmt->fetch();
$stmt->close();

// Recupera il livello dell'utente in base ai punti
$level_sql = "SELECT level_number, image_url, points_required FROM levels WHERE points_required <= ? ORDER BY points_required DESC LIMIT 1";
$level_stmt = $conn->prepare($level_sql);
$level_stmt->bind_param("i", $punti);
$level_stmt->execute();
$level_stmt->bind_result($level_number, $level_image_url, $current_level_points_required);
$level_stmt->fetch();
$level_stmt->close();

// Recupera i punti necessari per il prossimo livello
$next_level_sql = "SELECT points_required FROM levels WHERE points_required > ? ORDER BY points_required ASC LIMIT 1";
$next_level_stmt = $conn->prepare($next_level_sql);
$next_level_stmt->bind_param("i", $punti);
$next_level_stmt->execute();
$next_level_stmt->bind_result($next_level_points_required);
$next_level_stmt->fetch();
$next_level_stmt->close();

$conn->close();

$progress_percentage = 0;
if (isset($next_level_points_required)) {
    $points_for_current_level = $punti - $current_level_points_required;
    $points_for_next_level = $next_level_points_required - $current_level_points_required;
    $progress_percentage = ($points_for_current_level / $points_for_next_level) * 100;
}
?>

<div class="container top-menu-full">
  <div class="row">
    <div class="menusub">
        <div class="top-menu firstmenu"> 
             <?php if ($profile_image): ?>
                <div>
                    <img class="menuavatar" src="<?php echo htmlspecialchars($profile_image); ?>" alt="Profile Image" style="max-width: 100px; margin-top: 10px;">
                </div>
            <?php endif; ?>
        </div>
        <div class="top-menu menu-center"> 
            <div class="username">
                <?php echo htmlspecialchars($username); ?>
                <img class="medaglie" src="<?php echo htmlspecialchars($level_image_url); ?>" alt="Level Image" style="max-width: 40px; margin-top: 2px;">
            </div>
        </div>
    </div>
    <div class="top-menu menu-right"> 
        <div class="starpunti">
            <div class="bloccopunti">
                <img src="img/UI/punti.png" alt="point" style="max-width: 32px; margin-top: 2px;"> 
            </div>
        </div>
             <div class="barralivelli">
                <div class="barralivelliblock">
                    <?php if (isset($next_level_points_required)): ?>
                        <div class="progress" style="height: 20px; margin-top: 10px; position: relative;">
                            <div class="progress-bar" role="progressbar" style="width: <?php echo $progress_percentage; ?>%;" aria-valuenow="<?php echo $progress_percentage; ?>" aria-valuemin="0" aria-valuemax="100">
                            </div>
                            <span class="progress-text">
                                <?php echo htmlspecialchars($punti); ?> / <?php echo htmlspecialchars($next_level_points_required); ?>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>


                Monete: <?php echo htmlspecialchars($monete); ?>

        <div class="numerolivelliblock">
            <span class="my-css-class"><?php echo htmlspecialchars($level_number); ?></span> 
        </div>
    </div>
  </div>
</div><?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'include/db_connect.php';

$user_id = $_SESSION['user_id'];
$sql = "SELECT username, punti, profile_image, monete FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $punti, $profile_image, $monete);
$stmt->fetch();
$stmt->close();

// Recupera il livello dell'utente in base ai punti
$level_sql = "SELECT level_number, image_url, points_required, win_coins_min, win_coins_max FROM levels WHERE points_required <= ? ORDER BY points_required DESC LIMIT 1";
$level_stmt = $conn->prepare($level_sql);
$level_stmt->bind_param("i", $punti);
$level_stmt->execute();
$level_stmt->bind_result($level_number, $level_image_url, $current_level_points_required, $win_coins_min, $win_coins_max);
$level_stmt->fetch();
$level_stmt->close();

// Recupera i punti necessari per il prossimo livello
$next_level_sql = "SELECT points_required FROM levels WHERE points_required > ? ORDER BY points_required ASC LIMIT 1";
$next_level_stmt = $conn->prepare($next_level_sql);
$next_level_stmt->bind_param("i", $punti);
$next_level_stmt->execute();
$next_level_stmt->bind_result($next_level_points_required);
$next_level_stmt->fetch();
$next_level_stmt->close();

$conn->close();

$progress_percentage = 0;
if (isset($next_level_points_required)) {
    $points_for_current_level = $punti - $current_level_points_required;
    $points_for_next_level = $next_level_points_required - $current_level_points_required;
    $progress_percentage = ($points_for_current_level / $points_for_next_level) * 100;
}
?>

<div class="container top-menu-full">
  <div class="row">
    <div class="menusub">
        <div class="top-menu firstmenu"> 
             <?php if ($profile_image): ?>
                <div>
                    <img class="medaglie" src="<?php echo htmlspecialchars($level_image_url); ?>" alt="Level Image" style="max-width: 40px; margin-top: 2px;">
                    <img class="menuavatar" src="<?php echo htmlspecialchars($profile_image); ?>" alt="Profile Image" style="max-width: 100px; margin-top: 10px;">
                </div>
            <?php endif; ?>
        </div>
        <div class="top-menu menu-center"> 
            <div class="username">
                <?php echo htmlspecialchars($username); ?>
            </div>
            <div class="moneteblock">
                <img src="img/UI/moneta.png" alt="point" style="max-width: 26px; margin-top: 2px;"> <section class="monetasection"> <?php echo htmlspecialchars($monete); ?></section>
            </div>
        </div>
    </div>
    <div class="top-menu menu-right"> 
        <div class="starpunti">
                <div class="bloccopunti"><img src="img/UI/punti.png" alt="point" style="max-width: 32px; margin-top: 2px;"> </div>
            </div>
            <div class="barralivelli">
    <div class="barralivelliblock">
        <?php if (isset($next_level_points_required)): ?>
            <div class="progress" style="height: 20px; margin-top: 10px; position: relative;">
                <div class="progress-bar" role="progressbar" style="width: <?php echo $progress_percentage; ?>%;" aria-valuenow="<?php echo $progress_percentage; ?>" aria-valuemin="0" aria-valuemax="100">
                </div>
                <span class="progress-bar-text"><?php echo htmlspecialchars($punti); ?> / <?php echo htmlspecialchars($next_level_points_required); ?></span>
            </div>
        <?php endif; ?>
    </div>
</div>
            <div class="numerolivelliblock">
                   <span class="my-css-class"><?php echo htmlspecialchars($level_number); ?></span> 
                </div>
            <?php if ($level_image_url): ?>
            <div class="pulsantiuser">
                <br>

                <a href="dashboard.php"><img src="img/UI/home.png" alt="home" style="max-width: 40px; margin-top: 26px;"></a>
                <a href="leaderboard.php"><img src="img/UI/classifica.png" alt="leaderboard" style="max-width: 40px; margin-top: 26px;"></a>
                <a href="profile.php"><img src="img/UI/impostazioni.png" alt="settings" style="max-width: 40px; margin-top: 26px;"></a>
            </div>
        <?php endif; ?>
    </div>
  </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Aggiungi i dati delle monete al DOM
        let winCoinsMin = <?php echo $win_coins_min; ?>;
        let winCoinsMax = <?php echo $win_coins_max; ?>;
        document.body.insertAdjacentHTML('beforeend', `<div id="winCoinsMin" style="display: none;">${winCoinsMin}</div>`);
        document.body.insertAdjacentHTML('beforeend', `<div id="winCoinsMax" style="display: none;">${winCoinsMax}</div>`);
    });
</script>
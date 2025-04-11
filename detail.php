<?php
ini_set('display_errors', 1);
ini_set('display_startup_erros', 1);
error_reporting(E_ALL);
include 'includes/travel-config.inc.php';

$conn = getConnection();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
   die("Invalid Image ID.");
}

$imageID = $_GET['id'];

$sql = "SELECT i.*, c.CountryName, ci.AsciiName as CityName FROM imagedetails i LEFT JOIN countries c ON i.CountryCodeISO = c.ISO LEFT JOIN cities ci ON i.CityCode = ci.CityCode WHERE i.ImageID = :id";

$stmt = $conn->prepare($sql);
$stmt->bindvalue(':id', $imageID);
$stmt->execute();
$image = $stmt->fetch();

if (!$image) {
   die("Image not found.");
}

$exif = !empty($image['Exif']) ? json_decode($image['Exif'], true) : [];
$colors = !empty($image['Colors']) ? json_decode($image['Colors'], true) : [];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($image['title']) ?>Chapter 14</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
   
    <link rel="stylesheet" href="css/styles.css" />

</head>

<body>                                    
   <main class="detail">
      <div>
         <img src="images/medium/<?= htmlspecialchars($image['Path']) ?>" alt="<?= htmlspecialchars($image['Title']) ?>">
      </div>
      <div>
         <h1><?= htmlspecialchars($image['Title']) ?> </h1>
         <h3><?= htmlspecialchars($image['CityName']) ?>, <?= htmlspecialchars($image['CountryName']) ?></h3>
         <p><?= htmlspecialchars($image['Description']) ?></p>
         
         <div class="box">
            <h3>Creator</h3>
            <p><?= htmlspecialchars($image['UserID']) ?></p>
          </div>
         
         <div class="box">
            <h3>Camera</h3>
            <?php if ($exif): ?>
               <ul>
                  <?php foreach ($exif as $key => $val): ?>
                     <li><strong><?= htmlspecialchars($key) ?>:</strong> <?= htmlspecialchars($val) ?></li>
                     <?php endforeach; ?>
                  </ul>
                  <?php else: ?>
                     <p> No Camera info available.</p>
                     <?php endif; ?>
         </div>
         
         <div class="box">
            <h3>Colors</h3>
            <div class="colorBoxes">
               <?php if ($colors): ?>
                  <?php foreach ($colors as $color): ?>
                     <span style="background-color: <?= htmlspecialchars($color) ?>;"></span>
                     <?php endforeach; ?>
                     <?php else: ?>
                        <p>No color info available.</p>
                        <?php endif; ?>

         </div>
      </div>
   </main>
</body>

</html>
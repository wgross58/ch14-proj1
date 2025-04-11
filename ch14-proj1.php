<?php
ini_set('display_errors', 1);
ini_set('display_startup_erros', 1);
error_reporting(E_ALL);
include 'includes/travel-config.inc.php';




$conn = getConnection();
//Continent List
$continents = $conn->query("SELECT ContinentCode, ContinentName FROM continents")->fetchALL();
// Country list(only countries used in imagedetails)
$countries = $conn->query("SELECT DISTINCT countries.CountryName, countries.ISO FROM countries INNER JOIN imagedetails ON countries.ISO = imagedetails.CountryCodeISO")->fetchALL();
//Handle filter logic
$whereClauses = [];
$params = [];

if (!empty($_GET['continent']) && $_GET['continent'] != "0"){
  $whereClauses[] = "ContinentCode = :continent";
  $params[':continent'] = $_GET['continent'];
}

if (!empty($_GET['country']) && $_GET['country'] != "0"){
  $whereClauses[] = "CountryCodeISO = :country";
  $params[':country'] = $_GET['country'];
}

if (!empty($_GET['title'])) {
  $whereClauses[] = "Title LIKE :title";
  $params[':title'] = '%' . $_GET['title'] . '%';
}

$sql = "SELECT * FROM imagedetails";

if (count($whereClauses)) {
    $sql .= " WHERE " . implode(" AND ", $whereClauses);
}

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$images = $stmt->fetchAll();


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Chapter 14</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
   
    <link rel="stylesheet" href="css/styles.css" />

</head>

<body>
    <header>
        <form action="ch14-proj1.php" method="get" >
          <div class="form-inline">
          
          <select name="continent" >
            <option value="0">Select Continent</option>
             <?php foreach ($continents as $continent): ?>
           <option value = "<?= $continent['ContinentCode'] ?>" <?= (isset($_GET['continent']) && $_GET['continent'] == $continent['ContinentCode']) ? 'selected' : ''?>>
            <?= $continent['ContinentName'] ?>
            </option>
        <?php endforeach; ?>
            </select>
          
            <select name="country">
            <option value="0">Select Country</option>
            <?php foreach ($countries as $country): ?>
              <option value = "<?= $country['ISO'] ?>" <?= (isset($_GET['country']) && $_GET['country'] == $country['ISO']) ? 'selected' : ''?>>
              <?= $country['CountryName'] ?>
            </option>
            <?php endforeach; ?>
          </select>    
          <input type="text"  placeholder="Search title" name=title>
          <button type="submit" class="btn-primary">Filter</button>
          <a href="ch14-proj1.php" class="btn-secondary">Reset</a>
          </div>
        </form>
    </header>   
                                    
    <main >
        <ul >
<?php foreach ($images as $image): ?>
            <li>
                <a href="detail.php?id=<?= $image['ImageID'] ?>">
                <img src="images/square-medium/<?= $image['Path'] ?> " alt="<?= htmlspecialchars($image['Title']) ?>" />
                 </a>
            </li>        
            <?php endforeach; ?>
          </ul>       
    </main>
</body>
</html>
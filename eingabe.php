<?php
$woche = $_GET['woche'];
$tag = $_GET['tag'];
$stunde = $_GET['stunde'];

$xmlPfad = "plan/$woche.xml";
$xml = simplexml_load_file($xmlPfad);
$eintrag = $xml->{strtolower($tag)}->xpath("stunde[@nr=$stunde]")[0];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eintrag->fach = $_POST['fach'];
    $eintrag->info = $_POST['info'];
    $xml->asXML($xmlPfad);
    header("Location: anzeige.php?woche=$woche");
    exit;
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Eintrag bearbeiten</title>
    <style>
        <?php include('data/style.css'); ?>
    </style>
</head>
<body>
    <h1><?= "$tag – Stunde $stunde – Woche $woche" ?></h1>
    <form method="post">
        Fach: <input name="fach" value="<?= htmlspecialchars($eintrag->fach) ?>"><br><br>
        Info:<br>
        <textarea name="info" rows="5" cols="40"><?= htmlspecialchars($eintrag->info) ?></textarea><br><br>
        <button type="submit">Speichern</button>
        <a href="anzeige.php?woche=<?= $woche ?>">Zurück</a>
    </form>
</body>
</html>

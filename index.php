<?php

$heute = new DateTime();
$woche = isset($_GET['woche']) ? $_GET['woche'] : $heute->format("Y") . "-W" . $heute->format("W");
$tage = ["Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag"];
$stunden = [1, 2, 3];

$xmlPfad = "plan/$woche.xml";
if (!file_exists($xmlPfad)) {
    // Leere XML erzeugen
    $xml = new SimpleXMLElement("<woche/>");
    foreach ($tage as $tag) {
        $tagEl = $xml->addChild(strtolower($tag));
        foreach ($stunden as $stunde) {
            $eintrag = $tagEl->addChild("stunde");
            $eintrag->addAttribute("nr", $stunde);
            $eintrag->addChild("fach", "");
            $eintrag->addChild("info", "");
        }
    }
    $xml->asXML($xmlPfad);
} else {
    $xml = simplexml_load_file($xmlPfad);
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Mein test text für eine PersonStundenplan – Woche <?= htmlspecialchars($woche) ?></title>
   <!-- <link rel="stylesheet" href="data/style.css"> <!-- Import der externen CSS-Datei -->
</head>
<body>
    <h1>Stundenplan</h1>
    <form>
        Woche wählen: 
        <input type="week" name="woche" value="<?= htmlspecialchars($woche) ?>">
        <button type="submit">Anzeigen</button>
        <a href="?woche=<?= $heute->format("Y") . "-W" . $heute->format("W") ?>">Heute</a>
    </form>

    <table>
        <tr>
            <th>Stunde / Tag</th>
            <?php foreach ($tage as $tag): ?>
                <th><?= $tag ?></th>
            <?php endforeach; ?>
        </tr>
        <?php foreach ($stunden as $stunde): ?>
            <tr>
                <th>Stunde <?= $stunde ?></th>
                <?php foreach ($tage as $tag): 
                    $eintrag = $xml->{strtolower($tag)}->xpath("stunde[@nr=$stunde]")[0];
                ?>
                    <td onclick="showPopup(event, '<?= htmlspecialchars($eintrag->info) ?>')">
                        <?= htmlspecialchars($eintrag->fach) ?>
                        <br>
                        <a href="eingabe.php?woche=<?= $woche ?>&tag=<?= $tag ?>&stunde=<?= $stunde ?>">📝</a>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </table>

    <div class="popup" id="popup"></div>

    <script>
        function showPopup(evt, text) {
            let popup = document.getElementById("popup");
            popup.style.display = "block";
            popup.style.left = evt.pageX + "px";
            popup.style.top = evt.pageY + "px";
            popup.innerText = text;
        }
        document.addEventListener("click", e => {
            if (!e.target.closest("td")) {
                document.getElementById("popup").style.display = "none";
            }
        });
    </script>
</body>
</html>

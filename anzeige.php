<?php

$heute = new DateTime();
$woche = isset($_GET['woche']) ? $_GET['woche'] : $heute->format("Y") . "-W" . $heute->format("W");
$tage = ["Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag"];
$stunden = [1, 2, 3];

$xmlPfad = "plan/$woche.xml";
if (!file_exists($xmlPfad)) {
    echo "Keine Daten für diese Woche vorhanden.";
    exit;
}
$xml = simplexml_load_file($xmlPfad);
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Stundenplan anzeigen – Woche <?= htmlspecialchars($woche) ?></title>
    <link rel="stylesheet" href="data/style.css"> <!-- Import der externen CSS-Datei -->
</head>
<body>
    <h1>Stundenplan – Woche <?= htmlspecialchars($woche) ?></h1>
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
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </table>

    <div class="popup" id="popup">
        <button class="close-btn" onclick="closePopup()">X</button>
        <div id="popup-content"></div>
    </div>

    <script>
        function showPopup(evt, text) {
            let popup = document.getElementById("popup");
            let content = document.getElementById("popup-content");
            content.innerText = text || "(Keine Info)";
            popup.style.display = "block";
        }

        function closePopup() {
            document.getElementById("popup").style.display = "none";
        }

        document.addEventListener("click", function(e) {
            if (!e.target.closest("td") && !e.target.closest(".popup")) {
                closePopup();
            }
        });
    </script>
</body>
</html>

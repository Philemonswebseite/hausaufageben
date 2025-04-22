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
    <style>
        td { border: 1px solid black; padding: 5px; cursor: pointer; }
        table { border-collapse: collapse; }
        .popup {
            display: none;
            position: absolute;
            background: #f4f4f4;
            padding: 10px;
            border: 1px solid #888;
            box-shadow: 2px 2px 8px rgba(0,0,0,0.3);
            z-index: 100;
        }
    </style>
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
            <th>Tag / Stunde</th>
            <?php foreach ($stunden as $stunde): ?>
                <th>Stunde <?= $stunde ?></th>
            <?php endforeach; ?>
        </tr>
        <?php foreach ($tage as $tag): ?>
            <tr>
                <th><?= $tag ?></th>
                <?php foreach ($stunden as $stunde): 
                    $eintrag = $xml->{strtolower($tag)}->xpath("stunde[@nr=$stunde]")[0];
                ?>
                    <td onclick="showPopup(event, '<?= htmlspecialchars($eintrag->info) ?>')">
                        <?= htmlspecialchars($eintrag->fach) ?>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </table>

    <div class="popup" id="popup"></div>

    <script>
        function showPopup(evt, text) {
            let popup = document.getElementById("popup");
            popup.innerText = text || "(Keine Info)";
            popup.style.display = "block";
            popup.style.left = evt.pageX + "px";
            popup.style.top = evt.pageY + "px";
        }

        document.addEventListener("click", function(e) {
            if (!e.target.closest("td")) {
                document.getElementById("popup").style.display = "none";
            }
        });
    </script>
</body>
</html>

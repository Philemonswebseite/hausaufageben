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
       <?php include('data/style.css'); ?>
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

    <table class="desktop-table">
        <tr>
            <th>Stunde / Tag</th>
            <?php foreach ($tage as $tag): ?>
                <th><?= $tag ?></th>
            <?php endforeach; ?>
        </tr>
        <?php foreach ($stunden as $stunde): ?>
            <tr>
                <th>
                    <?php 
                        switch ($stunde) {
                            case 1:
                                echo "1/2";
                                break;
                            case 2:
                                echo "3/4";
                                break;
                            case 3:
                                echo "5/6";
                                break;
                            case 4:
                                echo "7/8";
                                break;
                            default:
                                echo "Stunde $stunde";
                        }
                    ?>
                </th>
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

    <div class="mobile-table">
        <?php foreach ($tage as $tag): ?>
            <div class="mobile-row">
                <div class="mobile-header"><?= $tag ?></div>
                <?php foreach ($stunden as $stunde): 
                    $eintrag = $xml->{strtolower($tag)}->xpath("stunde[@nr=$stunde]")[0];
                ?>
                    <div class="mobile-cell" onclick="showPopup(event, '<?= htmlspecialchars($eintrag->info) ?>')">
                        <strong>Stunde <?= $stunde ?>:</strong> <?= htmlspecialchars($eintrag->fach) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="popup" id="popup">
        <button class="close-btn" onclick="closePopup()">X</button>
        <div id="popup-content"></div>
    </div>

    <script>
        // Funktion zum Anzeigen des Popups
        function showPopup(evt, text) {
            evt.stopPropagation(); // Verhindert, dass andere Klick-Events ausgelöst werden
            let popup = document.getElementById("popup");
            let content = document.getElementById("popup-content");
            content.innerText = text || "(Keine Info)";
            popup.style.display = "block";
        }

        // Funktion zum Schließen des Popups
        function closePopup() {
            document.getElementById("popup").style.display = "none";
        }

        // Event-Listener für die Escape-Taste
        document.addEventListener("keydown", function(e) {
            if (e.key === "Escape") {
                closePopup();
            }
        });

        // Event-Listener für Klicks außerhalb des Popups
        document.addEventListener("click", function(e) {
            if (!e.target.closest(".popup") && !e.target.closest("td") && !e.target.closest(".mobile-cell")) {
                closePopup();
            }
        });
    </script>
</body>
</html>

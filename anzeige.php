<?php

$heute = new DateTime();
$woche = isset($_GET['woche']) ? $_GET['woche'] : $heute->format("Y") . "-W" . $heute->format("W");
$tage = ["Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag"];
$stunden = [1, 2, 3, 4];

$xmlPfad = "plan/$woche.xml";
if (!file_exists($xmlPfad)) {
    // Leere XML-Datei erstellen
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

// Bearbeitungsmodus prüfen
$bearbeiten = isset($_GET['bearbeiten']) && $_GET['bearbeiten'] === 'true';
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Hausaufgaben</title>
    <style>
       <?php include('data/style.css'); ?>
    </style>
</head>
<body>
    <h1>Hausaufgaben – Woche <?= htmlspecialchars($woche) ?></h1>
    <form>
        Woche wählen: 
        <input type="week" name="woche" value="<?= htmlspecialchars($woche) ?>" onchange="this.form.submit()"><p>  </p>
        <a href="?woche=<?= $heute->format("Y") . "-W" . $heute->format("W") ?>" class="dark-button">Heute</a>
    </form>
        <?php if ($bearbeiten): ?>
        <a href="?woche=<?= htmlspecialchars($woche) ?>&bearbeiten=false" class="dark-button">zurück</a>
    <?php else: ?>
        <a href="?woche=<?= htmlspecialchars($woche) ?>&bearbeiten=true" class="dark-button">bearbeiten</a>
    <?php endif; ?>
<br><br>
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
                    <td>
                        <?= htmlspecialchars($eintrag->fach) ?>
                        <?php if ($bearbeiten): ?>
                            <a href="eingabe.php?woche=<?= $woche ?>&tag=<?= $tag ?>&stunde=<?= $stunde ?>">📝</a>
                        <?php endif; ?>
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
                        <strong>
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
                            ?>:
                        </strong> 
                        <?= htmlspecialchars($eintrag->fach) ?>
                        <?php if ($bearbeiten): ?>
                            <a href="eingabe.php?woche=<?= $woche ?>&tag=<?= $tag ?>&stunde=<?= $stunde ?>">📝</a>
                        <?php endif; ?>
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

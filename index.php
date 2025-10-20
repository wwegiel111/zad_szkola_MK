<?php
session_start();

$db_user = 'user1';
$db_pass = 'haslo_czyta';
$is_admin = false;

if (isset($_SESSION['user']) && $_SESSION['user'] === 'admin') {
    $db_user = 'user2';
    $db_pass = 'haslo_admin';
    $is_admin = true;
}

$conn = mysqli_connect('localhost', $db_user, $db_pass, 'szkola');

if (isset($_POST['akcja_loguj'])) {
    $_SESSION['user'] = 'admin';
    header('Location: index.php');
    exit;
}
if (isset($_POST['akcja_wyloguj'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

if ($is_admin) {
    if (isset($_POST['akcja_dodaj'])) {
        $sql = "INSERT INTO uczniowie (id_ucz, nazwisko, imie, pesel, adres_ul, adres_nr, miasto) 
                VALUES ('{$_POST['id_ucz']}', '{$_POST['nazwisko']}', '{$_POST['imie']}', '{$_POST['pesel']}', '{$_POST['adres_ul']}', '{$_POST['adres_nr']}', '{$_POST['miasto']}')";
        mysqli_query($conn, $sql);
    }
    if (isset($_POST['akcja_usun'])) {
        foreach ($_POST['do_usuniecia'] as $id) {
            $sql = "DELETE FROM uczniowie WHERE id_ucz = $id";
            mysqli_query($conn, $sql);
        }
    }
    if (isset($_POST['akcja_zapisz_edycje'])) {
        $sql = "UPDATE uczniowie SET 
                    nazwisko = '{$_POST['nazwisko']}', 
                    imie = '{$_POST['imie']}', 
                    pesel = '{$_POST['pesel']}', 
                    adres_ul = '{$_POST['adres_ul']}', 
                    adres_nr = '{$_POST['adres_nr']}', 
                    miasto = '{$_POST['miasto']}' 
                WHERE id_ucz = {$_POST['id_ucz']}";
        mysqli_query($conn, $sql);
        header('Location: index.php');
        exit;
    }
}

if ($is_admin && isset($_GET['akcja']) && $_GET['akcja'] == 'eksport') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=uczniowie.csv');
    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    fputcsv($output, ['id_ucz', 'nazwisko', 'imie', 'pesel', 'adres_ul', 'adres_nr', 'miasto'], ';');
    $result = mysqli_query($conn, "SELECT * FROM uczniowie");
    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, $row, ';');
    }
    fclose($output);
    exit;
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Baza uczniowie (wg Twojego SQL)</title>
</head>
<body>

    <div style="background: #eee; padding: 10px;">
        <?php if ($is_admin): ?>
            <b>Zalogowano jako User 2 (Admin)</b>
            <form action="index.php" method="POST" style="display: inline;">
                <button type="submit" name="akcja_wyloguj">Rozłącz</button>
            </form>
        <?php else: ?>
            <b>Widok User 1 (Tylko odczyt)</b>
            <form action="index.php" method="POST" style="display: inline;">
                <button type="submit" name="akcja_loguj">Połącz (Zaloguj jako User 2)</button>
            </form>
        <?php endif; ?>
    </div>
    
    <hr>
    
    <?php if ($is_admin && isset($_GET['akcja']) && $_GET['akcja'] == 'edytuj'): ?>
        <?php
            $id = $_GET['id'];
            $result = mysqli_query($conn, "SELECT * FROM uczniowie WHERE id_ucz = $id");
            $uczen = mysqli_fetch_assoc($result);
        ?>
        <h2>Edytuj ucznia</h2>
        <form action="index.php" method="POST">
            <input type="hidden" name="akcja_zapisz_edycje" value="1">
            <input type="hidden" name="id_ucz" value="<?php echo $uczen['id_ucz']; ?>">
            
            <p>ID Ucznia: <?php echo $uczen['id_ucz']; ?> (Nie można edytować)</p>
            <p>Nazwisko: <input type="text" name="nazwisko" value="<?php echo $uczen['nazwisko']; ?>"></p>
            <p>Imię: <input type="text" name="imie" value="<?php echo $uczen['imie']; ?>"></p>
            <p>PESEL: <input type="text" name="pesel" value="<?php echo $uczen['pesel']; ?>"></p>
            <p>Ulica: <input type="text" name="adres_ul" value="<?php echo $uczen['adres_ul']; ?>"></p>
            <p>Nr domu: <input type="text" name="adres_nr" value="<?php echo $uczen['adres_nr']; ?>"></p>
            <p>Miasto: <input type="text" name="miasto" value="<?php echo $uczen['miasto']; ?>"></p>
            
            <button type="submit">Zapisz zmiany</button>
            <a href="index.php">Anuluj</a>
        </form>

    <?php else: ?>
        <table width="100%">
            <tr>
                <td width="30%" valign="top">
                    <h3>Filtruj (Zad 2)</h3>
                    <form action="index.php" method="GET">
                        <label>Miasto: <input type="text" name="miasto" value="<?php echo $_GET['miasto'] ?? ''; ?>"></label><br>
                        <button type="submit">Filtruj</button>
                        <a href="index.php">Wyczyść</a>
                    </form>
                    
                    <?php if ($is_admin): ?>
                    <hr>
                    <h3>Dodaj ucznia (Zad 3)</h3>
                    <form action="index.php" method="POST">
                        <input type="hidden" name="akcja_dodaj" value="1">
                        <p>ID Ucznia: <input type="number" name="id_ucz" required></p>
                        <p>Nazwisko: <input type="text" name="nazwisko" required></p>
                        <p>Imię: <input type="text" name="imie" required></p>
                        <p>PESEL: <input type="text" name="pesel" required></p>
                        <p>Ulica: <input type="text" name="adres_ul" required></p>
                        <p>Nr domu: <input type="text" name="adres_nr" required></p>
                        <p>Miasto: <input type="text" name="miasto" required></p>
                        <button type="submit">Dodaj ucznia</button>
                    </form>
                    <?php endif; ?>
                </td>
                
                <td width="70%" valign="top">
                    <?php
                        $sql = "SELECT * FROM uczniowie";
                        if (!empty($_GET['miasto'])) {
                            $sql .= " WHERE miasto LIKE '%{$_GET['miasto']}%'";
                        }
                        $sql .= " ORDER BY nazwisko ASC";
                        $result_uczniowie = mysqli_query($conn, $sql);
                        $liczba_uczniow = mysqli_num_rows($result_uczniowie);
                    ?>
                    <h3>Lista uczniów (Znaleziono: <?php echo $liczba_uczniow; ?>)</h3>

                    <form action="index.php" method="POST" id="form-usun">
                        <input type="hidden" name="akcja_usun" value="1">
                        <table border="1" cellpadding="5" width="100%">
                            <thead>
                                <tr>
                                    <?php if ($is_admin): ?><th>Usuń</th><?php endif; ?>
                                    <th>Nazwisko</th>
                                    <th>Imię</th>
                                    <th>Adres</th>
                                    <th>Miasto</th>
                                    <?php if ($is_admin): ?><th>PESEL</th><th>Edytuj</th><?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result_uczniowie)): ?>
                                <tr>
                                    <?php if ($is_admin): ?>
                                        <td><input type="checkbox" name="do_usuniecia[]" value="<?php echo $row['id_ucz']; ?>"></td>
                                    <?php endif; ?>
                                    <td><?php echo $row['nazwisko']; ?></td>
                                    <td><?php echo $row['imie']; ?></td>
                                    <td><?php echo $row['adres_ul'] . ' ' . $row['adres_nr']; ?></td>
                                    <td><?php echo $row['miasto']; ?></td>
                                    <?php if ($is_admin): ?>
                                        <td><?php echo $row['pesel']; ?></td>
                                        <td><a href="index.php?akcja=edytuj&id=<?php echo $row['id_ucz']; ?>">Edytuj</a></td>
                                    <?php endif; ?>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                        <?php if ($is_admin): ?>
                            <p>
                                <button type="submit" form="form-usun">Usuń zaznaczone</button>
                                <a href="index.php?akcja=eksport">Eksportuj do CSV (uczniowie.csv)</a>
                            </p>
                        <?php endif; ?>
                    </form>
                </td>
            </tr>
        </table>
    <?php endif; ?>

<?php
mysqli_close($conn);
?>
</body>
</html>

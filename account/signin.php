<?php
//SUPER ŚMIESZNE SŁOWA HAHAHA
$badWords = [
    'kurwa', 'kurw', 'kura', 'kurde',   
    'chuj', 'huj', 'choj', 'czuj',
    'pierdol', 'pierdol', 'pierd', 'prdl',
    'jeba', 'jeb', 'jebac',
    'pizd', 'cipa', 'cip', 'ciota', 'cioto',
    'kutas', 'kutaf', 'kutwa',
    'suka', 'sukin', 'sukinsyn',
    'gown', 'gowno', 'gowien',
    'spierdalaj', 'wypierdalaj', 'spierdal',
    'ruchaj', 'ruchanie',
    'debil', 'idiot', 'kretyn', 'matoł',
    'frajer', 'noob', 'cwel', 'pedał', 'pedal', 'nigger', 'nigga'
];

error_reporting(E_ALL); ini_set('display_errors', 1);

$db_host = 'localhost';
$db_user = 'root';
$db_password = '';
$db_name = 'KrokDoPrzodu';


$conn = new mysqli($db_host, $db_user, $db_password, $db_name);

// connection
if ($conn->connect_error) {
    die("Błąd połączenia z bazą danych: " . $conn->connect_error);
}
echo "<script>console.log('checkpoint 0');</script>";//-------------------------------------------------------------------------------------------------

$conn->select_db($db_name);

$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];
$password_confirm = $_POST['password_confirm'];
$wiek = $_POST['wiek'];
$school = $_POST['szkoła'];

echo "<script>console.log('checkpoint 1');</script>";//-------------------------------------------------------------------------------------------------
$errors = [];
$is_error = false;

function addError(array &$errors, string $message): void
{
    $errors[] = $message;
    $is_error = true;  
}

function isValidUsername(string $username): bool
{
    return preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username) === 1;
}


if (empty($username)) {
    addError($errors, "Nazwa użytkownika jest wymagana");
} elseif (!isValidUsername($username)) {
    addError($errors, 'Nazwa użytkownika musi mieć od 3 do 20 znaków i może zawierać tylko litery, cyfry oraz podkreślenia "_"');
}

$usernameLower = mb_strtolower($username, 'UTF-8');  

$containsBadWord = false;
foreach ($badWords as $word) {
    if (mb_strpos($usernameLower, $word) !== false) {
        $containsBadWord = true;
        break;
    }
}

if ($containsBadWord) {
    $errors[] = "Nazwa użytkownika zawiera niedozwolone słowa – wybierz inną.";
}

if (empty($email)) {
    addError($errors, "Email jest wymagany");
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    addError($errors, "Podany email jest nieprawidłowy");
}

if (empty($password)) {
    addError($errors, "Hasło jest wymagane");
} elseif (strlen($password) < 6) {
    addError($errors, "Hasło musi mieć co najmniej 6 znaków");
} elseif ($password !== $password_confirm) {
    addError($errors, "Hasła nie są identyczne");
}
if ($wiek === '') {
    addError($errors, "Wybór wieku jest wymagany");
}

if (empty($school)) {
    addError($errors, "Wybór szkoły jest wymagany");
}
echo "<script>console.log('checkpoint 2');</script>"; //-------------------------------------------------------------------------------------------------





$stmt = $conn->prepare("SELECT player_id FROM players WHERE username = ? LIMIT 1");
$stmt->bind_param("s", $username);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    $errors[] = "Nazwa użytkownika jest już zajęta";
}
$stmt->close();
echo "<script>console.log('checkpoint 3');</script>";//-------------------------------------------------------------------------------------------------


$stmt = $conn->prepare("SELECT player_id FROM players WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    $errors[] = "Ten email jest już zarejestrowany";
}
$stmt->close();

echo "<script>console.log('checkpoint 4');</script>";//-------------------------------------------------------------------------------------------------

if (empty($errors)) {

    
    $hashed_password = password_hash($password,  PASSWORD_BCRYPT);  
    

    
    $sql = "INSERT INTO players (username, email, password_hash, team_id, age_group, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())";

    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        $errors[] = "Błąd przygotowania zapytania INSERT: " . $conn->error;
    } else {



        $stmt->bind_param("sssii", $username, $email, $hashed_password, $school,$wiek); //here might be error

        if ($stmt->execute()) {
            // Succes 
            $new_user_id = $conn->insert_id;

            echo '<div class="alert alert-success">Konto zostało pomyślnie utworzone! Możesz teraz <a href="login.html">zalogować się</a>.</div>';
            exit;
        } else {
            // db error
            $errors[] = "Błąd podczas zapisywania do bazy: " . $stmt->error;
        }

        $stmt->close();
    }

    if (!empty($errors)) {
        echo '<div class="alert alert-danger">';
        foreach ($errors as $msg) {
            echo '<p>' . htmlspecialchars($msg) . '</p>';
        }
        echo '</div>';
    }

    echo '</div>';
}


echo "<script>console.log('checkpoint 5');</script>";//-------------------------------------------------------------------------------------------------





?>
<!DOCTYPE html>
<html lang="pl">
<head>
      <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script> <!-- Biblioteka do wyszukiwania szkoły -->
    <link rel="icon" type="image/x-icon" href="image.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rejestracja - KDP</title>
</head>
<body>
    <div class="form-container">
        <h1>Stwórz Konto</h1>
        
        <?php if (isset($success_message)): ?>
            <div class="success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
            <ul class="errors">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        
        <form method="post" action="">

            <label for="username">Nazwa użytkownika:</label>
            <input type="username" name="username" id="username" value="<?php echo htmlspecialchars($username ?? ''); ?>" >

            <label for="email">email:</label>
            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" >
            
            <label for="password">Hasło:</label>
            <input type="password" name="password" id="password" >
            
            <label for="password_confirm">Powtórz Hasło:</label>
            <input type="password" name="password_confirm" id="password_confirm" >

            <label for="wiek">Wiek: </label>
            <select id="wiek" name="wiek">
                <option value="" selected disabled>--Wybierz wiek--</option>


                
                <option value="1" <?php echo ($wiek ?? '') === '1' ? 'selected' : ''; ?>>0-12</option>
                <option value="2" <?php echo ($wiek ?? '') === '2' ? 'selected' : ''; ?>>13-17</option>
                <option value="3" <?php echo ($wiek ?? '') === '3' ? 'selected' : ''; ?>>18-30</option>
                <option value="4" <?php echo ($wiek ?? '') === '4' ? 'selected' : ''; ?>>30+</option>
            </select>
            
            
            <label for="szkoła">Twoja szkoła:</label>
            <select id="szkoła" name="szkoła" required>
                <option value="001" selected="selected">Nie chodzę do szkoły / Mojej szkoły nie ma na liście</option>
                <option value="101" <?php echo ($school ?? '') === '101' ? 'selected' : ''; ?>>Szkoła Podstawowa 1</option>
                <option value="102" <?php echo ($school ?? '') === '102' ? 'selected' : ''; ?>>Szkoła Podstawowa 2</option>
                <option value="201" <?php echo ($school ?? '') === '201' ? 'selected' : ''; ?>>Szkoła Średnia 1</option>
                <option value="202" <?php echo ($school ?? '') === '202' ? 'selected' : ''; ?>>Szkoła Średnia 2</option>
            </select>
            
            <button type="submit">Stwórz Konto!</button>
        </form>
        
        <div class="login-link">
            <p>Masz już konto? <a href="sin.html">Zaloguj Się!</a></p>
        </div>
    </div>

    <script>
  new TomSelect('#szkoła', {
    maxOptions: 500,           
    searchField: ['text'],
    create: false,              
    sortField: { field: 'text', direction: 'asc' }
  });

</script>
</body>
</html>

<?php

$conn->close();
?>
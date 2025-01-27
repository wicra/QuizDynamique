<?php
// Charger les fichiers nécessaires
require_once 'php/StorageInterface.php';
require_once 'php/JsonStorage.php';
require_once 'php/MysqlStorage.php';

// Démarrer la session
session_start();

// Charger la configuration
$config = require 'php/config.php';

// Gestion du choix de stockage
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['storage_type'])) {
    $config['storage_type'] = $_POST['storage_type'];
    
    // Mettre à jour le fichier de configuration
    file_put_contents('php/config.php', '<?php' . PHP_EOL . 'return ' . var_export($config, true) . ';');
}

// Sélectionner le bon système de stockage
if ($config['storage_type'] === 'json') {
    $questionStorage = new JsonQuestionStorage('data/question.json');
    $resultStorage = new JsonResultStorage('data/results.json');
} else {
    $questionStorage = new MysqlQuestionStorage($config['mysql']);
    $resultStorage = new MysqlResultStorage($config['mysql']);
}

// Charger les questions et résultats
$questions = $questionStorage->getAllQuestions();
$results = $resultStorage->getAllResults();

// Variables globales pour gérer l'état du quiz
$playerName = '';
$currentQuestion = 0;
$score = 0;
$quizFinished = false;

// Logique de traitement du quiz
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Si aucun état de quiz n'existe, commencer par le nom
    if (!isset($_COOKIE['quiz_state'])) {
        if (isset($_POST['player_name'])) {
            $playerName = trim($_POST['player_name']);
            
            // Validation du nom
            if (empty($playerName)) {
                $error = "Veuillez entrer un nom de joueur valide.";
            } else {
                // Sauvegarder l'état du quiz dans un cookie
                $quizState = [
                    'player_name' => $playerName,
                    'current_question' => 0,
                    'score' => 0
                ];
                setcookie('quiz_state', json_encode($quizState), time() + 3600, '/');
                
                // Rediriger pour éviter les rechargements problématiques
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit();
            }
        }
    } else {
        // Récupérer l'état du quiz depuis le cookie
        $quizState = json_decode($_COOKIE['quiz_state'], true);
        $playerName = $quizState['player_name'];
        $currentQuestion = $quizState['current_question'];
        $score = $quizState['score'];

        // Vérification de la réponse
        if (isset($_POST['answer'])) {
            $question = $questions[$currentQuestion];
            
            // Vérifier si la réponse est correcte
            if ($_POST['answer'] == $question['correct']) {
                $score++;
            }
            
            // Passer à la question suivante
            $currentQuestion++;
            
            // Mettre à jour l'état du quiz
            $quizState['current_question'] = $currentQuestion;
            $quizState['score'] = $score;
            
            // Vérifier si le quiz est terminé
            if ($currentQuestion >= count($questions)) {
                // Sauvegarder le résultat
                $result = [
                    'name' => $playerName,
                    'score' => $score,
                    'total' => count($questions),
                    'date' => date('Y-m-d H:i:s')
                ];

                $resultStorage->saveResult($result);
                
                // Supprimer le cookie d'état du quiz
                setcookie('quiz_state', '', time() - 3600, '/');
                
                // Rediriger pour éviter les rechargements problématiques
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit();
            } else {
                // Mettre à jour le cookie
                setcookie('quiz_state', json_encode($quizState), time() + 3600, '/');
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Quiz Dynamique</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f4f8;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
        }
        .quiz-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 30px;
            width: 100%;
            max-width: 500px;
            text-align: center;
        }
        .choice, .error {
            display: block;
            width: 100%;
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .choice {
            background-color: #3498db;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .choice:hover {
            background-color: #2980b9;
        }
        .error {
            background-color: #e74c3c;
            color: white;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .results-table th, .results-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .storage-selection {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 20px;
        }
        .storage-btn {
            padding: 10px 20px;
            border: 2px solid #3498db;
            background-color: white;
            color: #3498db;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .storage-btn.selected {
            background-color: #3498db;
            color: white;
        }
        #timer {
            font-size: 18px;
            margin-bottom: 15px;
            color: #e74c3c;
        }
        .choices {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
    </style>
</head>
<body>
    <div class="quiz-container">
        <?php if (!isset($_COOKIE['quiz_state'])): ?>
            <h2>Quiz Dynamique</h2>
            
            <div class="storage-selection">
                <form method="POST">
                    <input type="hidden" name="storage_type" value="json">
                    <button type="submit" class="storage-btn <?= $config['storage_type'] == 'json' ? 'selected' : '' ?>">
                        Stockage JSON
                    </button>
                </form>
                <form method="POST">
                    <input type="hidden" name="storage_type" value="mysql">
                    <button type="submit" class="storage-btn <?= $config['storage_type'] == 'mysql' ? 'selected' : '' ?>">
                        Stockage MySQL
                    </button>
                </form>
            </div>
            
            <h3>Tableau des Scores (<?= $config['storage_type'] ?>)</h3>
            <table class="results-table">
                <tr>
                    <th>Nom</th>
                    <th>Score</th>
                    <th>Date</th>
                </tr>
                <?php 
                // Trier les résultats par score décroissant
                usort($results, function($a, $b) {
                    return $b['score'] - $a['score'];
                });
                
                foreach ($results as $scoreEntry): 
                    // Ne pas afficher les entrées avec un nom null
                    if (!is_null($scoreEntry['name'])): 
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($scoreEntry['name']); ?></td>
                        <td><?php echo $scoreEntry['score']; ?> / <?php echo $scoreEntry['total']; ?></td>
                        <td><?php echo $scoreEntry['date']; ?></td>
                    </tr>
                <?php 
                    endif;
                endforeach; 
                ?>
            </table>

            <form method="POST">
                <label for="player_name">Votre nom :</label>
                <input type="text" 
                       name="player_name" 
                       placeholder="Entrez votre nom" 
                       required>
                <button type="submit" class="choice">Commencer le Quiz</button>
            </form>

            <?php else: ?>
                <!-- Quiz en cours -->
                <?php 
                $quizState = json_decode($_COOKIE['quiz_state'], true);
                $question = $questions[$quizState['current_question']];
                ?>
                <div id="timer">Temps restant : <span id="time">15</span> secondes</div>
                
                <div class="question">
                    <h3><?php echo htmlspecialchars($question['question']); ?></h3>
                </div>
                
                <form method="POST" id="quiz-form">
                    <div class="choices">
                        <?php foreach ($question['choices'] as $choiceIndex => $choice): ?>
                            <button type="submit" 
                                    name="answer" 
                                    value="<?php echo $choiceIndex; ?>" 
                                    class="choice">
                                <?php echo htmlspecialchars($choice); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </form>
            <?php endif; ?>
    </div>

    <script>
        <?php 
        $quizState = isset($_COOKIE['quiz_state']) ? json_decode($_COOKIE['quiz_state'], true) : null;
        if ($quizState && $quizState['current_question'] < count($questions)): 
        ?>
            let timeLeft = 15;
            const timerDisplay = document.getElementById('time');
            const quizForm = document.getElementById('quiz-form');

            const timer = setInterval(() => {
                timeLeft--;
                timerDisplay.textContent = timeLeft;

                if (timeLeft <= 0) {
                    clearInterval(timer);
                    quizForm.submit(); // Soumettre automatiquement le formulaire
                }
            }, 1000);
        <?php endif; ?>
    </script>
</body>
</html>
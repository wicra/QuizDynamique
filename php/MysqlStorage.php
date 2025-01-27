<?php
class MysqlQuestionStorage implements QuestionStorageInterface {
    private $connection;

    public function __construct(array $config) {
        try {
            $this->connection = new PDO(
                "mysql:host={$config['host']};dbname={$config['database']}", 
                $config['username'], 
                $config['password']
            );
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $this->initializeTables();
        } catch(PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    }

    private function initializeTables() {
        $this->connection->exec("CREATE TABLE IF NOT EXISTS questions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            question TEXT NOT NULL,
            choices JSON NOT NULL,
            correct INT NOT NULL
        )");

        $checkQuestions = $this->connection->query("SELECT COUNT(*) FROM questions")->fetchColumn();
        if ($checkQuestions == 0) {
            $jsonQuestions = json_decode(file_get_contents('data/question.json'), true);
            $stmt = $this->connection->prepare("INSERT INTO questions (question, choices, correct) VALUES (:question, :choices, :correct)");
            
            foreach ($jsonQuestions as $q) {
                $stmt->execute([
                    ':question' => $q['question'],
                    ':choices' => json_encode($q['choices']),
                    ':correct' => $q['correct']
                ]);
            }
        }
    }

    public function getAllQuestions(): array {
        $stmt = $this->connection->query("SELECT * FROM questions");
        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return array_map(function($q) {
            return [
                'question' => $q['question'],
                'choices' => json_decode($q['choices'], true),
                'correct' => $q['correct']
            ];
        }, $questions);
    }
}

class MysqlResultStorage implements ResultStorageInterface {
    private $connection;

    public function __construct(array $config) {
        try {
            $this->connection = new PDO(
                "mysql:host={$config['host']};dbname={$config['database']}", 
                $config['username'], 
                $config['password']
            );
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $this->connection->exec("CREATE TABLE IF NOT EXISTS results (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                score INT NOT NULL,
                total INT NOT NULL,
                date DATETIME NOT NULL
            )");
        } catch(PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    }

    public function saveResult(array $result): bool {
        $stmt = $this->connection->prepare("INSERT INTO results (name, score, total, date) VALUES (:name, :score, :total, :date)");
        return $stmt->execute([
            ':name' => $result['name'],
            ':score' => $result['score'],
            ':total' => $result['total'],
            ':date' => $result['date']
        ]);
    }

    public function getAllResults(): array {
        $stmt = $this->connection->query("SELECT * FROM results ORDER BY score DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
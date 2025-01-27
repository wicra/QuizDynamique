<?php
class JsonQuestionStorage implements QuestionStorageInterface {
    private $questionsFile;

    public function __construct($questionsFile) {
        $this->questionsFile = $questionsFile;
    }

    public function getAllQuestions(): array {
        return json_decode(file_get_contents($this->questionsFile), true);
    }
}

class JsonResultStorage implements ResultStorageInterface {
    private $resultsFile;

    public function __construct($resultsFile) {
        $this->resultsFile = $resultsFile;
    }

    public function saveResult(array $result): bool {
        $results = file_exists($this->resultsFile) 
            ? json_decode(file_get_contents($this->resultsFile), true) 
            : [];
        
        $results[] = $result;
        return file_put_contents($this->resultsFile, json_encode($results, JSON_PRETTY_PRINT)) !== false;
    }

    public function getAllResults(): array {
        return file_exists($this->resultsFile) 
            ? json_decode(file_get_contents($this->resultsFile), true) 
            : [];
    }
}
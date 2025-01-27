<?php
interface QuestionStorageInterface {
    public function getAllQuestions(): array;
}

interface ResultStorageInterface {
    public function saveResult(array $result): bool;
    public function getAllResults(): array;
}
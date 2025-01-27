-- Créer la base de données
CREATE DATABASE IF NOT EXISTS quiz_dynamique;

-- Utiliser la base de données
USE quiz_dynamique;

-- Supprimer les tables existantes si nécessaire
DROP TABLE IF EXISTS results;
DROP TABLE IF EXISTS questions;

-- Créer la table des questions
CREATE TABLE questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question TEXT NOT NULL,
    choices JSON NOT NULL,
    correct INT NOT NULL
);

-- Créer la table des résultats
CREATE TABLE results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    score INT NOT NULL,
    total INT NOT NULL,
    date DATETIME NOT NULL
);

-- Insérer les questions PHP
INSERT INTO questions (question, choices, correct) VALUES 
(
    'Quelle est la syntaxe correcte pour déclarer une variable en PHP ?',
    JSON_ARRAY('$variable', 'variable', '@variable', 'var $variable'),
    0
),
(
    'Comment démarrer une session en PHP ?',
    JSON_ARRAY('start_session()', 'session_begin()', 'session_start()', 'begin_session()'),
    2
),
(
    'Quel opérateur est utilisé pour la concaténation de chaînes en PHP ?',
    JSON_ARRAY('+', '&', '.', '~'),
    2
),
(
    'Comment définir une constante en PHP ?',
    JSON_ARRAY('const CONSTANTE', 'define("CONSTANTE", valeur)', 'constant CONSTANTE', 'set CONSTANTE'),
    1
),
(
    'Quelle fonction PHP est utilisée pour compter le nombre d\'éléments dans un tableau ?',
    JSON_ARRAY('size()', 'count()', 'length()', 'sizeof()'),
    1
),
(
    'Comment inclure un fichier externe en PHP ?',
    JSON_ARRAY('include', 'require', 'import', 'load'),
    0
),
(
    'Quelle est la différence entre == et === en PHP ?',
    JSON_ARRAY('Aucune différence', '== compare la valeur, === compare la valeur et le type', '=== compare la valeur, == compare le type', 'Ils sont totalement différents'),
    1
),
(
    'Comment déclarer un tableau associatif en PHP ?',
    JSON_ARRAY('$tableau = array()', '$tableau = []', '$tableau = ["cle" => "valeur"]', 'new Array()'),
    2
),
(
    'Quelle méthode HTTP est utilisée pour transmettre des données sensibles ?',
    JSON_ARRAY('GET', 'POST', 'PUT', 'DELETE'),
    1
),
(
    'Comment échapper les caractères spéciaux dans une chaîne SQL pour prévenir l\'injection ?',
    JSON_ARRAY('addslashes()', 'escape_string()', 'prepare_statement()', 'sanitize()'),
    0
),
(
    'Quelle fonction PHP permet de convertir un tableau en chaîne JSON ?',
    JSON_ARRAY('to_json()', 'json_encode()', 'array_to_json()', 'convert_to_json()'),
    1
),
(
    'Comment déclarer une fonction avec un nombre variable d\'arguments en PHP ?',
    JSON_ARRAY('function maFonction(...$args)', 'function maFonction(array $args)', 'function maFonction($args)', 'function maFonction()'),
    0
),
(
    'Quelle est la portée par défaut d\'une méthode dans une classe PHP ?',
    JSON_ARRAY('private', 'protected', 'public', 'static'),
    2
),
(
    'Comment gérer les exceptions en PHP ?',
    JSON_ARRAY('try/catch', 'if/else', 'switch/case', 'while/do'),
    0
),
(
    'Quelle fonction PHP permet de vérifier si une clé existe dans un tableau ?',
    JSON_ARRAY('in_array()', 'array_key_exists()', 'isset()', 'contains()'),
    2
);
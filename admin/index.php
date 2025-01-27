<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Add Question</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h1>Add a New Question</h1>
    <form action="../php/add_question.php" method="POST">
        <label>Question: <input type="text" name="question" required></label><br>
        <label>Choice 1: <input type="text" name="choice1" required></label><br>
        <label>Choice 2: <input type="text" name="choice2" required></label><br>
        <label>Choice 3: <input type="text" name="choice3" required></label><br>
        <label>Choice 4: <input type="text" name="choice4" required></label><br>
        <label>Correct Answer (index 0-3): <input type="number" name="correct" min="0" max="3" required></label><br>
        <button type="submit">Add Question</button>
    </form>
</body>
<script>
    let timer;
let score = 0;

function fetchQuestion() {
    fetch('php/get_question.php')
        .then(response => response.json())
        .then(data => {
            document.getElementById('question').textContent = data.question;
            const choicesContainer = document.getElementById('choices');
            choicesContainer.innerHTML = '';
            data.choices.forEach((choice, index) => {
                const button = document.createElement('button');
                button.textContent = choice;
                button.onclick = () => checkAnswer(index, data.correct);
                choicesContainer.appendChild(button);
            });
            startTimer();
        });
}

function checkAnswer(selected, correct) {
    if (selected === correct) {
        score++;
    }
    clearInterval(timer);
    fetchQuestion();
}

function startTimer() {
    let timeLeft = 30;
    document.getElementById('timer').textContent = timeLeft;
    timer = setInterval(() => {
        timeLeft--;
        document.getElementById('timer').textContent = timeLeft;
        if (timeLeft <= 0) {
            clearInterval(timer);
            fetchQuestion();
        }
    }, 1000);
}

document.addEventListener('DOMContentLoaded', fetchQuestion);

</script>
</html>

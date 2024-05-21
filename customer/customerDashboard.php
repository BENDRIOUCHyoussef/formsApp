<?php
session_start();
require '../database.php'; // Ensure the path to your database.php is correct


$user_id = $_SESSION['user_id'];
$user_id = 1;

$survey_id = $_GET['survey_id'] ?? null;
if (!$survey_id) {
    echo "Invalid Survey ID.";
    exit();
}

// Fetch the survey details
$survey_query = $db->prepare("SELECT title, description FROM Surveys WHERE survey_id = ?");
$survey_query->bind_param("i", $survey_id);
$survey_query->execute();
$survey_result = $survey_query->get_result();
$survey = $survey_result->fetch_assoc();

if (!$survey) {
    echo "Survey not found.";
    exit();
}

echo $user_id;

$check_sql = "
    SELECT * 
    FROM Responses 
    INNER JOIN Questions ON Responses.question_id = Questions.question_id 
    WHERE Questions.survey_id = ? AND Responses.user_id = ?
    LIMIT 1";
$stmt = $db->prepare($check_sql);
$stmt->bind_param("ii", $survey_id, $user_id);
$stmt->execute();
$stmt->store_result();
$user_has_responded = $stmt->num_rows > 0;
$stmt->close();



// Fetch questions for the survey
$questions_sql = "SELECT * FROM Questions WHERE survey_id = ?";
$stmt = $db->prepare($questions_sql);
$stmt->bind_param("i", $survey_id);
$stmt->execute();
$questions_result = $stmt->get_result();
$questions = $questions_result->fetch_all(MYSQLI_ASSOC);

// Fetch answers for MCQs
$answers = [];
$question_ids = array_column($questions, 'question_id');
if (!empty($question_ids)) {
    $placeholders = implode(',', array_fill(0, count($question_ids), '?'));
    $answers_sql = "SELECT * FROM Answers WHERE question_id IN ($placeholders)";
    $stmt = $db->prepare($answers_sql);
    $stmt->bind_param(str_repeat('i', count($question_ids)), ...$question_ids);
    $stmt->execute();
    $answers_result = $stmt->get_result();
    while ($row = $answers_result->fetch_assoc()) {
        $answers[$row['question_id']][] = $row;
    }
}
// Fetch questions and their answers
// $questions_query = $db->prepare("
//     SELECT q.question_id, q.text AS question_text, a.text AS answer_text
//     FROM Questions q
//     LEFT JOIN Answers a ON q.question_id = a.question_id
//     WHERE q.survey_id = ?
//     ORDER BY q.question_id ASC, a.answer_id ASC
// ");
// $questions_query->bind_param("i", $survey_id);
// $questions_query->execute();
// $result = $questions_query->get_result();

// $questions = [];
// while ($row = $result->fetch_assoc()) {
//     $questions[$row['question_id']]['text'] = $row['question_text'];
//     $questions[$row['question_id']]['answers'][] = $row['answer_text'];
// }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $survey_id = $_POST['survey_id'];
    $user_id = $_SESSION['user_id']; // Replace with actual user ID from session/authentication
    $responses = $_POST['responses'];

    foreach ($responses as $question_id => $response) {
        if (is_array($response)) {
            // Multiple Choice responses
            foreach ($response as $answer_id) {
                $insert_sql = "INSERT INTO Responses (question_id, user_id, answer_id) VALUES (?, ?, ?, ?)";
                $stmt = $db->prepare($insert_sql);
                $stmt->bind_param("iii", $question_id, $user_id, $answer_id);
                $stmt->execute();
            }
        } else {
            // Text or Rating responses
            $insert_sql = "INSERT INTO Responses (question_id, user_id, text_response, survey_id) VALUES (?, ?, ?, ?)";
            $stmt = $db->prepare($insert_sql);
            $stmt->bind_param("iisi", $question_id, $user_id, $response, $survey_id);
            $stmt->execute();
        }
    }

    echo "Responses submitted successfully!";
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>View Survey</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body>
    <div class="dashboard-container">
        <main>
            <a href="../logout.php" class="logout-button">Logout</a>


            <h1><?= htmlspecialchars($survey['title']) ?></h1>
            <p><?= htmlspecialchars($survey['description']) ?></p>


            <?php if ($user_has_responded) : ?>
                <p>You have already completed this survey. Thank you for your participation!</p>
            <?php else : ?>
                <form action="" method="POST">
                    <input type="hidden" name="survey_id" value="<?php echo $survey_id; ?>">
                    <?php foreach ($questions as $question) : ?>
                        <div class="question">
                            <p><?php echo htmlspecialchars($question['text']); ?></p>
                            <?php if ($question['type'] == 'Text') : ?>
                                <input type="text" name="responses[<?php echo $question['question_id']; ?>]" required>
                            <?php elseif ($question['type'] == 'Multiple_Choice') : ?>
                                <?php foreach ($answers[$question['question_id']] as $answer) : ?>
                                    <label>
                                        <input type="radio" name="responses[<?php echo $question['question_id']; ?>]" value="<?php echo $answer['text']; ?>" required>
                                        <?php echo htmlspecialchars($answer['text']); ?>
                                    </label><br>
                                <?php endforeach; ?>
                            <?php elseif ($question['type'] == 'Rating') : ?>
                                <input type="number" name="responses[<?php echo $question['question_id']; ?>]" min="1" max="5" required>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                    <button type="submit">Submit</button>
                </form>
            <?php endif; ?>

        </main>
    </div>
</body>

</html>
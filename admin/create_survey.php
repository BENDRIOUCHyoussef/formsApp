<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Survey</title>
    <link rel="stylesheet" href="style.css">
    <script>
document.addEventListener('DOMContentLoaded', function () {
    let questionCount = 0; // Keep track of the number of questions

    const container = document.getElementById('questions-container');
    const addQuestionBtn = document.getElementById('add-question');

    addQuestionBtn.addEventListener('click', function() {
        addQuestion(container);
    });

    function addQuestion(container) {
        const div = document.createElement('div');
        div.className = 'question-block';
        div.id = 'question-block-' + questionCount;

        const questionTypeSelect = document.createElement('select');
        questionTypeSelect.innerHTML = `
            <option value="">Select Question Type</option>
            <option value="Multiple Choice">Multiple Choice</option>
            <option value="Text">Text</option>
            <option value="Rating">Rating</option>
        `;
        questionTypeSelect.onchange = function() {
            addQuestionFields(this.value, div);
        };

        const removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.textContent = 'Remove Question';
        removeButton.onclick = function() {
            div.remove();
        };

        div.appendChild(questionTypeSelect);
        div.appendChild(removeButton);
        container.appendChild(div);
        questionCount++;
    }

    function addQuestionFields(type, questionBlock) {
        const fieldsContainer = document.createElement('div');
        questionBlock.appendChild(fieldsContainer);

        if (type === 'Text') {
            fieldsContainer.innerHTML = `<label>Question: <input type="text" name="questions[${questionCount}][text]" required></label>
                                         <input type="hidden" name="questions[${questionCount}][type]" value="Text">`;
        } else if (type === 'Multiple Choice') {
            fieldsContainer.innerHTML = `<label>Question: <input type="text" name="questions[${questionCount}][text]" required></label>
                                         <div class="options-container" id="options-${questionCount}"></div>
                                         <button type="button" onclick="addOption(${questionCount})">Add Option</button>`;
            addOption(questionCount); // Add initial set of option fields
        } else if (type === 'Rating') {
            fieldsContainer.innerHTML = `<label>Question: <input type="text" name="questions[${questionCount}][text]" required></label>
                                         <label>Scale (1-5): <input type="number" name="questions[${questionCount}][scale]" min="1" max="5" required></label>
                                         <input type="hidden" name="questions[${questionCount}][type]" value="Rating">`;
        }
    }

    window.addOption = function(questionIndex) {
        const optionsContainer = document.getElementById(`options-${questionIndex}`);
        const optionDiv = document.createElement('div');

        const optionInput = document.createElement('input');
        optionInput.type = 'text';
        optionInput.name = `questions[${questionIndex}][options][]`;
        optionInput.required = true;

        const removeOptionButton = document.createElement('button');
        removeOptionButton.type = 'button';
        removeOptionButton.textContent = 'Remove Option';
        removeOptionButton.onclick = function() {
            optionDiv.remove();
        };

        optionDiv.appendChild(optionInput);
        optionDiv.appendChild(removeOptionButton);
        optionsContainer.appendChild(optionDiv);
    };
});
</script>



</head>
<body>
    <div class="dashboard-container">
    <?php include 'sidebar.php'; ?>  <!-- Including the sidebar -->
        <main>
            <h1>Create New Survey</h1>
            <form action="save_survey.php" method="post">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" required><br>
                <label for="description">Description:</label>
                <textarea id="description" name="description" required></textarea><br>

                <div id="questions-container"></div>
                <button type="button" id="add-question">Add Question</button>
                <br><br>
                <button type="submit">Submit Survey</button>
            </form>
        </main>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OpenAI Prompt</title>
</head>
<body>
    <h1>Generate Response</h1>
    <form id="openai-form">
        <label for="prompt">Enter your prompt:</label><br>
        <textarea id="prompt" name="prompt" rows="4" cols="50"></textarea><br><br>
        <button type="submit">Generate</button>
    </form>

    <div id="response-container">
        <h2>Response:</h2>
        <p id="response-text"></p>
    </div>

    <script>
        document.getElementById('openai-form').addEventListener('submit', function(event) {
            event.preventDefault();  // Prevent form submission

            const prompt = document.getElementById('prompt').value;

            fetch('http://127.0.0.1:5000/api', {
                method: 'POST',
                body: JSON.stringify({ message: prompt }),
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.response) {
                    document.getElementById('response-text').innerText = data.response;
                } else if (data.error) {
                    document.getElementById('response-text').innerText = 'Error: ' + data.error;
                }
            });
        });
    </script>
</body>
</html>

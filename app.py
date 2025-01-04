from flask import Flask, request, jsonify
from flask_cors import CORS
from groq import Groq

app = Flask(__name__)
CORS(app)  # Allow all origins

# Initialize the client with your API key
client = Groq(api_key="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")

@app.route('/api', methods=['POST'])
def generate_response():
    try:
        data = request.get_json()
        prompt = data.get("message", "")

        # Generate response from Groq
        completion = client.chat.completions.create(
            model="llama3-70b-8192",
            messages=[{"role": "user", "content": prompt}],
            temperature=1,
            max_tokens=1024,
            top_p=1,
            stream=False,
            stop=None,
        )

        # Extract response text
        response_text = completion.choices[0].message.content
        return jsonify({"response": response_text})
    except Exception as e:
        return jsonify({"error": str(e)}), 500

if __name__ == '__main__':  # Corrected here
    app.run(port=5000, debug=True)

    

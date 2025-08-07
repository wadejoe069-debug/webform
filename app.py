from flask import Flask, render_template_string, request

app = Flask(__name__)

FORM_HTML = '''
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Form</title>
    <style>
      body { font-family: Arial, sans-serif; margin: 2em; }
      form { max-width: 400px; margin: auto; }
      label { display: block; margin-top: 1em; }
      input, select { width: 100%; padding: 0.5em; margin-top: 0.5em; }
      button { margin-top: 1.5em; padding: 0.7em 1.5em; }
    </style>
  </head>
  <body>
    <h2>User Information Form</h2>
    <form method="post">
      <label for="username">User Name:</label>
      <input type="text" id="username" name="username" required>

      <label for="email">Email:</label>
      <input type="email" id="email" name="email" required>

      <label for="phone">Phone Number:</label>
      <input type="tel" id="phone" name="phone" required>

      <label for="document">Document Type:</label>
      <select id="document" name="document" required>
        <option value="Bank Statements">Bank Statements</option>
        <option value="Pay Stubs">Pay Stubs</option>
        <option value="Utility Bills">Utility Bills</option>
        <option value="Tax Returns">Tax Returns</option>
      </select>

      <button type="submit">Submit</button>
    </form>
    {% if submitted %}
      <div style="margin-top:2em; color: green;">
        <h3>Submission Received:</h3>
        <ul>
          <li><strong>User Name:</strong> {{ data.username }}</li>
          <li><strong>Email:</strong> {{ data.email }}</li>
          <li><strong>Phone Number:</strong> {{ data.phone }}</li>
          <li><strong>Document Type:</strong> {{ data.document }}</li>
        </ul>
      </div>
    {% endif %}
  </body>
</html>
'''

@app.route('/', methods=['GET', 'POST'])
def user_form():
    if request.method == 'POST':
        data = {
            'username': request.form['username'],
            'email': request.form['email'],
            'phone': request.form['phone'],
            'document': request.form['document']
        }
        return render_template_string(FORM_HTML, submitted=True, data=data)
    return render_template_string(FORM_HTML, submitted=False, data={})

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0')
from flask import Flask, render_template, request, flash, redirect, url_for
import os
from werkzeug.utils import secure_filename

app = Flask(__name__)
app.secret_key = 'your-secret-key-here'  # Change this to a secure secret key

# Configure upload folder
UPLOAD_FOLDER = 'uploads'
ALLOWED_EXTENSIONS = {'txt', 'pdf', 'png', 'jpg', 'jpeg', 'gif', 'doc', 'docx'}

app.config['UPLOAD_FOLDER'] = UPLOAD_FOLDER

# Create uploads directory if it doesn't exist
os.makedirs(UPLOAD_FOLDER, exist_ok=True)

def allowed_file(filename):
    return '.' in filename and \
           filename.rsplit('.', 1)[1].lower() in ALLOWED_EXTENSIONS

@app.route('/')
def index():
    return render_template('form.html')

@app.route('/submit', methods=['POST'])
def submit_form():
    if request.method == 'POST':
        # Get form data
        name = request.form.get('name')
        email = request.form.get('email')
        phone = request.form.get('phone')
        service = request.form.get('service')
        payment = request.form.get('payment')
        
        # Handle file uploads
        uploaded_files = []
        files = request.files.getlist('formFileMultiple')
        
        for file in files:
            if file and file.filename != '':
                if allowed_file(file.filename):
                    filename = secure_filename(file.filename)
                    file.save(os.path.join(app.config['UPLOAD_FOLDER'], filename))
                    uploaded_files.append(filename)
        
        # Validate form data
        if not name or not email or not phone or not service or payment == 'Select':
            flash('Please fill in all required fields.', 'error')
            return redirect(url_for('index'))
        
        # Process the form data (you can add your logic here)
        # For now, we'll just flash a success message
        flash(f'Form submitted successfully! Name: {name}, Email: {email}, Service: {service}', 'success')
        
        # You can add database operations, email sending, etc. here
        
        return redirect(url_for('index'))

if __name__ == '__main__':
    app.run(debug=True)
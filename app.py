from flask import Flask, render_template, request, flash, redirect, url_for
from flask_mail import Mail, Message
import os
from werkzeug.utils import secure_filename
import html

app = Flask(__name__)
app.secret_key = 'your-secret-key-here'  # Change this to a secure secret key

# Configure upload folder
UPLOAD_FOLDER = 'uploads'
ALLOWED_EXTENSIONS = {'txt', 'pdf', 'png', 'jpg', 'jpeg', 'gif', 'doc', 'docx'}

app.config['UPLOAD_FOLDER'] = UPLOAD_FOLDER

# Email configuration (similar to your PHP settings)
app.config['MAIL_SERVER'] = 'mail.bankstatementediting.com'
app.config['MAIL_PORT'] = 587
app.config['MAIL_USE_TLS'] = True
app.config['MAIL_USERNAME'] = 'admin@bankstatementediting.com'
app.config['MAIL_PASSWORD'] = 'K7u5U-(rb7T*~at?'
app.config['MAIL_DEFAULT_SENDER'] = 'admin@bankstatementediting.com'

mail = Mail(app)

# Create uploads directory if it doesn't exist
os.makedirs(UPLOAD_FOLDER, exist_ok=True)

def allowed_file(filename):
    return '.' in filename and \
           filename.rsplit('.', 1)[1].lower() in ALLOWED_EXTENSIONS

def send_email(name, email, phone, service, payment, additional_fields=""):
    """Send email using Flask-Mail (similar to PHPMailer)"""
    try:
        # Create HTML message (similar to your PHP message)
        html_message = f"""
        <html>
        <head>
          <title>Contact Form Submission</title>
        </head>
        <body>
          <h2>Contact Form Details</h2>
          <p><strong>Full Name:</strong> {html.escape(name)}</p>
          <p><strong>Email Address:</strong> {html.escape(email)}</p>
          <p><strong>Phone Number:</strong> {html.escape(phone)}</p>
          <p><strong>Service Needed:</strong> {html.escape(service)}</p>
          <p><strong>Payment Method:</strong> {html.escape(payment)}</p>
          {additional_fields}
        </body>
        </html>
        """
        
        # Create plain text version
        text_message = f"""
        Contact Form Details
        
        Full Name: {name}
        Email Address: {email}
        Phone Number: {phone}
        Service Needed: {service}
        Payment Method: {payment}
        """
        
        # Create and send message
        msg = Message(
            subject='New Contact Form Submission',
            recipients=['quickpapersfix@gmail.com'],
            body=text_message,
            html=html_message
        )
        
        mail.send(msg)
        return True, "Message sent successfully!"
        
    except Exception as e:
        return False, f"Mailer Error: {str(e)}"

@app.route('/')
def index():
    return render_template('form.html')

@app.route('/submit', methods=['POST'])
def submit_form():
    if request.method == 'POST':
        # Get form data (similar to your PHP validation)
        name = html.escape(request.form.get('name', ''))
        email = html.escape(request.form.get('email', ''))
        phone = html.escape(request.form.get('phone', ''))
        service = html.escape(request.form.get('service', ''))
        payment = html.escape(request.form.get('payment', ''))
        
        # Handle additional fields (similar to your PHP additionalFields logic)
        additional_fields = ""
        for key, value in request.form.items():
            if key not in ['name', 'email', 'phone', 'service', 'payment']:
                field_name = key.replace('_', ' ').title()
                additional_fields += f"<p><strong>{field_name}:</strong> {html.escape(value)}</p>"
        
        # Validate required fields (similar to your PHP validation)
        if not name or not email or not phone or not service or payment == 'Select':
            flash('Please fill in all required fields.', 'error')
            return redirect(url_for('index'))
        
        # Handle file uploads
        uploaded_files = []
        files = request.files.getlist('formFileMultiple')
        
        for file in files:
            if file and file.filename != '':
                if allowed_file(file.filename):
                    filename = secure_filename(file.filename)
                    file.save(os.path.join(app.config['UPLOAD_FOLDER'], filename))
                    uploaded_files.append(filename)
        
        # Add uploaded files to additional fields
        if uploaded_files:
            additional_fields += f"<p><strong>Uploaded Files:</strong> {', '.join(uploaded_files)}</p>"
        
        # Send email (similar to your PHP email sending)
        success, message = send_email(name, email, phone, service, payment, additional_fields)
        
        if success:
            flash(f'Form submitted successfully! {message}', 'success')
        else:
            flash(f'Error sending email: {message}', 'error')
        
        return redirect(url_for('index'))

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=8080)
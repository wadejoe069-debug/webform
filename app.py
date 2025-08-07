from flask import Flask, render_template, request, flash, redirect, url_for
import re

app = Flask(__name__)
app.secret_key = 'your-secret-key-change-this'

def validate_email(email):
    """Validate email format"""
    pattern = r'^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$'
    return re.match(pattern, email) is not None

def validate_phone(phone):
    """Validate phone number format (basic validation)"""
    # Remove all non-digit characters for validation
    digits_only = re.sub(r'\D', '', phone)
    # Check if it has 10-15 digits (common phone number lengths)
    return len(digits_only) >= 10 and len(digits_only) <= 15

@app.route('/', methods=['GET', 'POST'])
def index():
    if request.method == 'POST':
        username = request.form.get('username', '').strip()
        email = request.form.get('email', '').strip()
        phone = request.form.get('phone', '').strip()
        
        # Validation
        errors = []
        
        if not username:
            errors.append('Username is required')
        elif len(username) < 2:
            errors.append('Username must be at least 2 characters long')
            
        if not email:
            errors.append('Email is required')
        elif not validate_email(email):
            errors.append('Please enter a valid email address')
            
        if not phone:
            errors.append('Phone number is required')
        elif not validate_phone(phone):
            errors.append('Please enter a valid phone number (10-15 digits)')
        
        if errors:
            for error in errors:
                flash(error, 'error')
            return render_template('form.html', username=username, email=email, phone=phone)
        
        # If validation passes, show success message
        flash(f'Form submitted successfully! Welcome, {username}!', 'success')
        return render_template('success.html', username=username, email=email, phone=phone)
    
    return render_template('form.html')

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=5000)
# Python Web Form Application

A modern, responsive web form built with Flask that collects user information including username, email, and phone number.

## Features

- ✅ **Responsive Design**: Works perfectly on desktop, tablet, and mobile devices
- ✅ **Form Validation**: Server-side validation for all fields with user-friendly error messages
- ✅ **Modern UI**: Beautiful gradient background with clean, professional styling
- ✅ **Flash Messages**: Success and error messages with proper styling
- ✅ **Email Validation**: Proper email format validation using regex
- ✅ **Phone Validation**: Phone number validation (10-15 digits)
- ✅ **Success Page**: Displays submitted information after successful form submission

## Form Fields

1. **Username** (Required)
   - Minimum 2 characters
   - Text input with validation

2. **Email Address** (Required)
   - Valid email format required
   - Email input type with validation

3. **Phone Number** (Required)
   - 10-15 digits required
   - Accepts various phone number formats

## Installation

1. **Clone or download the project files**

2. **Install Python dependencies**:
   ```bash
   pip install -r requirements.txt
   ```

3. **Run the application**:
   ```bash
   python app.py
   ```

4. **Open your web browser** and navigate to:
   ```
   http://localhost:5000
   ```

## Project Structure

```
/workspace/
├── app.py                 # Main Flask application
├── requirements.txt       # Python dependencies
├── README.md             # This file
└── templates/
    ├── form.html         # Main form template
    └── success.html      # Success page template
```

## Usage

1. **Access the Form**: Open your browser to `http://localhost:5000`
2. **Fill Out the Form**: Enter your username, email, and phone number
3. **Submit**: Click the "Submit Form" button
4. **View Results**: If successful, you'll see a confirmation page with your submitted data

## Form Validation

The application includes comprehensive validation:

### Username Validation
- Cannot be empty
- Must be at least 2 characters long

### Email Validation
- Cannot be empty
- Must be a valid email format (user@domain.com)

### Phone Number Validation
- Cannot be empty
- Must contain 10-15 digits (formatting characters like spaces, dashes, parentheses are allowed)

## Error Handling

- **Validation Errors**: Displayed at the top of the form with red styling
- **Form Persistence**: Invalid form data is preserved so users don't have to re-enter everything
- **User-Friendly Messages**: Clear, actionable error messages

## Customization

### Styling
The CSS is embedded in the HTML templates. You can customize:
- Colors by modifying the gradient values
- Fonts by changing the font-family
- Layout by adjusting padding, margins, and dimensions

### Validation Rules
Modify the validation functions in `app.py`:
- `validate_email()` - Email format validation
- `validate_phone()` - Phone number validation
- Add custom validation in the main route handler

### Form Fields
To add new fields:
1. Add the field to `form.html`
2. Update the form processing logic in `app.py`
3. Add validation as needed
4. Update `success.html` to display the new field

## Security Notes

- The application uses Flask's built-in CSRF protection via sessions
- Change the `secret_key` in `app.py` for production use
- Input validation prevents basic injection attacks
- Consider adding HTTPS for production deployment

## Development

To run in development mode with debug enabled:
```bash
python app.py
```

The application will automatically reload when you make changes to the code.

## Production Deployment

For production deployment, consider:
1. Using a production WSGI server like Gunicorn
2. Setting up a reverse proxy with Nginx
3. Using environment variables for configuration
4. Implementing proper logging
5. Adding database storage for form submissions

## Browser Support

This application works on all modern browsers including:
- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+

## License

This project is open source and available under the MIT License.
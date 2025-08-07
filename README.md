# Order Form Application

A Flask web application that provides a form with the same fields as your original HTML form.

## Features

- Full Name field
- Email Address field
- Phone Number field
- Service selection dropdown (Bank Statement, Pay Stub, Tax Returns, Utility Bill)
- File upload (multiple files supported)
- Payment method selection (PayPal, Debit/Credit Card, Bitcoin)
- Form validation
- Flash messages for success/error feedback
- Bootstrap styling for modern UI

## Installation

1. Create a virtual environment (recommended):
```bash
python -m venv venv
source venv/bin/activate  # On Windows: venv\Scripts\activate
```

2. Install dependencies:
```bash
pip install -r requirements.txt
```

## Running the Application

1. Start the Flask application:
```bash
python app.py
```

2. Open your web browser and navigate to:
```
http://localhost:5000
```

## File Structure

```
├── app.py                 # Main Flask application
├── templates/
│   └── form.html         # HTML template with the form
├── uploads/              # Directory for uploaded files (created automatically)
├── requirements.txt      # Python dependencies
└── README.md            # This file
```

## Form Fields

The form includes all the same fields as your original HTML:

- **Full Name**: Text input (required)
- **Email Address**: Email input (required)
- **Phone Number**: Text input (required)
- **Service Needed**: Dropdown with options:
  - Bank Statement
  - Pay Stub
  - Tax Returns
  - Utility Bill
- **Upload File**: File input supporting multiple files
- **Payment Method**: Dropdown with options:
  - PayPal
  - Debit card/Credit card
  - Bitcoin

## Customization

You can modify the form processing logic in the `submit_form()` function in `app.py` to:
- Save data to a database
- Send email notifications
- Integrate with payment processors
- Add additional validation rules

## Security Notes

- Change the `app.secret_key` in `app.py` to a secure random string
- Consider adding CSRF protection for production use
- Implement proper file type validation for uploads
- Add rate limiting for form submissions
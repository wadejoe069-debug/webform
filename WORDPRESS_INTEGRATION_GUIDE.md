# WordPress Integration Guide

## How to Integrate the Form into Your WordPress Project

### Option 1: WordPress Plugin (Recommended)

#### Step 1: Create Plugin Directory
1. Go to your WordPress site's `wp-content/plugins/` directory
2. Create a new folder called `custom-order-form`
3. Upload these files to the folder:
   - `wordpress-form-plugin-smtp.php` (rename to `custom-order-form.php`)
   - `js/custom-form.js`

#### Step 2: Install PHPMailer (Optional but Recommended)
If you want to use SMTP email (same as your original PHP code):

1. Install PHPMailer via Composer:
```bash
composer require phpmailer/phpmailer
```

2. Or download PHPMailer files manually and include them in your plugin.

#### Step 3: Activate the Plugin
1. Go to WordPress Admin → Plugins
2. Find "Custom Order Form with SMTP"
3. Click "Activate"

#### Step 4: Use the Form
Add this shortcode to any page or post:
```
[custom_order_form]
```

### Option 2: Direct Integration in Theme

#### Step 1: Add to functions.php
Add this code to your theme's `functions.php`:

```php
// Enqueue scripts and styles
function enqueue_custom_form_assets() {
    wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css');
    wp_enqueue_script('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js', array(), null, true);
    wp_enqueue_script('custom-form', get_template_directory_uri() . '/js/custom-form.js', array('jquery'), '1.0', true);
    wp_localize_script('custom-form', 'ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('order_form_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_custom_form_assets');

// Add AJAX handlers
add_action('wp_ajax_submit_order_form', 'handle_form_submission');
add_action('wp_ajax_nopriv_submit_order_form', 'handle_form_submission');

function handle_form_submission() {
    // Copy the handle_form_submission function from the plugin
    // (Same code as in the plugin)
}
```

#### Step 2: Add Form to Template
Add this to your page template:

```php
<?php
// Include the form HTML (copy from the plugin's render_form function)
?>
```

### Option 3: Page Template

#### Step 1: Create Custom Page Template
Create a file called `page-order-form.php` in your theme:

```php
<?php
/*
Template Name: Order Form
*/

get_header(); ?>

<div class="container">
    <div class="form-container">
        <h2 class="text-center mb-4">Order Form</h2>
        
        <div id="form-messages"></div>
        
        <form id="orderForm" enctype="multipart/form-data" novalidate>
            <!-- Copy the form HTML from the plugin -->
        </form>
    </div>
</div>

<?php get_footer(); ?>
```

### Option 4: Gutenberg Block

#### Step 1: Create Custom Block
Create a custom Gutenberg block that includes the form.

### Email Configuration

The form uses the same email settings as your original PHP code:

- **SMTP Server:** `mail.bankstatementediting.com`
- **Port:** 587 with TLS
- **Username:** `admin@bankstatementediting.com`
- **Password:** `K7u5U-(rb7T*~at?`
- **Recipient:** `quickpapersfix@gmail.com`

### Features Included

✅ **Same Form Fields:**
- Full Name
- Email Address
- Phone Number
- Service selection
- File upload (multiple files)
- Payment method selection

✅ **Same Validation:**
- Required field validation
- Email format validation
- File upload validation
- HTML escaping for security

✅ **Same Email Functionality:**
- SMTP email sending
- HTML email format
- Same email server settings
- File attachment support

✅ **WordPress Integration:**
- AJAX form submission
- Nonce security
- WordPress file upload handling
- Flash messages
- Bootstrap styling

### Troubleshooting

#### Email Not Sending
1. Check if your hosting supports SMTP
2. Verify email credentials
3. Check server logs for errors
4. Try the fallback wp_mail() method

#### Form Not Displaying
1. Check if shortcode is correct: `[custom_order_form]`
2. Verify plugin is activated
3. Check browser console for JavaScript errors

#### File Upload Issues
1. Check file permissions in `wp-content/uploads/order-forms/`
2. Verify file size limits in WordPress settings
3. Check allowed file types

### Security Notes

1. **Change the nonce key** in production
2. **Use HTTPS** for secure form submission
3. **Add rate limiting** to prevent spam
4. **Validate file types** on server side
5. **Use reCAPTCHA** for additional protection

### Customization

You can easily customize:
- Form fields by editing the HTML
- Email template by modifying the message HTML
- Styling by updating the CSS
- Validation rules in the PHP code
- Email settings in the configuration
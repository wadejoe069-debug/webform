<?php
/**
 * Plugin Name: Custom Order Form
 * Description: A custom order form with email functionality
 * Version: 1.0
 * Author: Your Name
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class CustomOrderForm {
    
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_shortcode('custom_order_form', array($this, 'render_form'));
        add_action('wp_ajax_submit_order_form', array($this, 'handle_form_submission'));
        add_action('wp_ajax_nopriv_submit_order_form', array($this, 'handle_form_submission'));
    }
    
    public function enqueue_scripts() {
        wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css');
        wp_enqueue_script('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js', array(), null, true);
        
        wp_enqueue_script('custom-form', plugin_dir_url(__FILE__) . 'js/custom-form.js', array('jquery'), '1.0', true);
        wp_localize_script('custom-form', 'ajax_object', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('order_form_nonce')
        ));
    }
    
    public function render_form() {
        ob_start();
        ?>
        <div class="container">
            <div class="form-container">
                <h2 class="text-center mb-4">Order Form</h2>
                
                <div id="form-messages"></div>
                
                <form id="orderForm" enctype="multipart/form-data" novalidate>
                    <div class="form-group">
                        <label for="name">Full Name:</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address:</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number:</label>
                        <input type="text" class="form-control" id="phone" name="phone" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="service">Service Needed:</label>
                        <select class="form-control" id="service" name="service" required>
                            <option value="">Select a service</option>
                            <option value="bank_statement">Bank Statement</option>
                            <option value="pay_stub">Pay Stub</option>
                            <option value="tax_returns">Tax Returns</option>
                            <option value="utility_bill">Utility Bill</option>
                        </select>
                        <div class="invalid-feedback">Please select a service.</div>
                    </div>
                    
                    <div id="serviceFields"></div>
                    
                    <div class="form-group">
                        <label for="formFileMultiple" class="form-label">Upload File:</label>
                        <input class="form-control" type="file" id="formFileMultiple" name="formFileMultiple[]" multiple>
                    </div>
                    
                    <div class="form-group">
                        <label for="payment">Your Preferred Payment Method?</label>
                        <select class="form-control" name="payment" required>
                            <option value="Select">Select payment method</option>
                            <option value="Pay-Pal">Pay Pal</option>
                            <option value="Debit card/Credit card">Debit card/Credit card</option>
                            <option value="Bitcoin">Bitcoin</option>
                        </select>
                    </div>
                    
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
        
        <style>
        :root {
            --tp-gradient-primary: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        }
        
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-control {
            border-radius: 5px;
            border: 1px solid #ddd;
            padding: 10px 15px;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-primary {
            background: var(--tp-gradient-primary);
            border: none;
            padding: 10px 69px;
            border-radius: 5px;
            color: white;
            font-weight: bold;
        }
        
        .btn-primary:hover {
            opacity: 0.9;
        }
        
        .alert {
            border-radius: 5px;
            margin-bottom: 20px;
        }
        </style>
        <?php
        return ob_get_clean();
    }
    
    public function handle_form_submission() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'order_form_nonce')) {
            wp_die('Security check failed');
        }
        
        // Get form data
        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $phone = sanitize_text_field($_POST['phone']);
        $service = sanitize_text_field($_POST['service']);
        $payment = sanitize_text_field($_POST['payment']);
        
        // Validate required fields
        if (empty($name) || empty($email) || empty($phone) || empty($service) || $payment === 'Select') {
            wp_send_json_error('Please fill in all required fields.');
            return;
        }
        
        // Handle file uploads
        $uploaded_files = array();
        if (!empty($_FILES['formFileMultiple'])) {
            $upload_dir = wp_upload_dir();
            $custom_dir = $upload_dir['basedir'] . '/order-forms/';
            
            // Create directory if it doesn't exist
            if (!file_exists($custom_dir)) {
                wp_mkdir_p($custom_dir);
            }
            
            foreach ($_FILES['formFileMultiple']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['formFileMultiple']['error'][$key] === UPLOAD_ERR_OK) {
                    $filename = sanitize_file_name($_FILES['formFileMultiple']['name'][$key]);
                    $filepath = $custom_dir . $filename;
                    
                    if (move_uploaded_file($tmp_name, $filepath)) {
                        $uploaded_files[] = $filename;
                    }
                }
            }
        }
        
        // Send email
        $success = $this->send_email($name, $email, $phone, $service, $payment, $uploaded_files);
        
        if ($success) {
            wp_send_json_success('Form submitted successfully!');
        } else {
            wp_send_json_error('Error sending email. Please try again.');
        }
    }
    
    private function send_email($name, $email, $phone, $service, $payment, $uploaded_files) {
        // Email configuration (same as your PHP settings)
        $to = 'quickpapersfix@gmail.com';
        $subject = 'New Contact Form Submission';
        
        // Build HTML message
        $html_message = "
        <html>
        <head>
          <title>Contact Form Submission</title>
        </head>
        <body>
          <h2>Contact Form Details</h2>
          <p><strong>Full Name:</strong> " . esc_html($name) . "</p>
          <p><strong>Email Address:</strong> " . esc_html($email) . "</p>
          <p><strong>Phone Number:</strong> " . esc_html($phone) . "</p>
          <p><strong>Service Needed:</strong> " . esc_html($service) . "</p>
          <p><strong>Payment Method:</strong> " . esc_html($payment) . "</p>";
        
        if (!empty($uploaded_files)) {
            $html_message .= "<p><strong>Uploaded Files:</strong> " . esc_html(implode(', ', $uploaded_files)) . "</p>";
        }
        
        $html_message .= "</body></html>";
        
        // Email headers
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: admin@bankstatementediting.com',
            'Reply-To: ' . $email
        );
        
        // Send email using WordPress wp_mail() or custom SMTP
        return wp_mail($to, $subject, $html_message, $headers);
    }
}

// Initialize the plugin
new CustomOrderForm();
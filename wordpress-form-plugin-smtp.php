<?php
/**
 * Plugin Name: Custom Order Form with SMTP
 * Description: A custom order form with SMTP email functionality
 * Version: 1.0
 * Author: Your Name
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class CustomOrderFormSMTP {
    
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
        
        // Get form data (same validation as your PHP code)
        $name = htmlspecialchars($_POST['name']);
        $email = htmlspecialchars($_POST['email']);
        $phone = htmlspecialchars($_POST['phone']);
        $service = htmlspecialchars($_POST['service']);
        $payment = htmlspecialchars($_POST['payment']);
        
        // Validate required fields
        if (empty($name) || empty($email) || empty($phone) || empty($service) || $payment === 'Select') {
            wp_send_json_error('Please fill in all required fields.');
            return;
        }
        
        // Handle additional fields (same logic as your PHP code)
        $additional_fields = "";
        foreach ($_POST as $key => $value) {
            if (!in_array($key, ['name', 'email', 'phone', 'service', 'payment', 'action', 'nonce'])) {
                $additional_fields .= "<p><strong>" . ucfirst(str_replace('_', ' ', $key)) . ":</strong> " . htmlspecialchars($value) . "</p>";
            }
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
        
        // Add uploaded files to additional fields
        if (!empty($uploaded_files)) {
            $additional_fields .= "<p><strong>Uploaded Files:</strong> " . implode(', ', $uploaded_files) . "</p>";
        }
        
        // Send email using SMTP (same as your PHP code)
        $success = $this->send_email_smtp($name, $email, $phone, $service, $payment, $additional_fields);
        
        if ($success) {
            wp_send_json_success('Message sent successfully!');
        } else {
            wp_send_json_error('Error sending email. Please try again.');
        }
    }
    
    private function send_email_smtp($name, $email, $phone, $service, $payment, $additional_fields) {
        // Check if PHPMailer is available
        if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            // Fallback to WordPress wp_mail()
            return $this->send_email_wp_mail($name, $email, $phone, $service, $payment, $additional_fields);
        }
        
        try {
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            
            // Server settings (same as your PHP code)
            $mail->isSMTP();
            $mail->Host = 'mail.bankstatementediting.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'admin@bankstatementediting.com';
            $mail->Password = 'K7u5U-(rb7T*~at?';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            
            // Recipients (same as your PHP code)
            $mail->setFrom('admin@bankstatementediting.com', 'Website Contact Form');
            $mail->addAddress('quickpapersfix@gmail.com');
            
            // Content (same HTML structure as your PHP code)
            $message = "
            <html>
            <head>
              <title>Contact Form Submission</title>
            </head>
            <body>
              <h2>Contact Form Details</h2>
              <p><strong>Full Name:</strong> {$name}</p>
              <p><strong>Email Address:</strong> {$email}</p>
              <p><strong>Phone Number:</strong> {$phone}</p>
              <p><strong>Service Needed:</strong> {$service}</p>
              <p><strong>Payment Method:</strong> {$payment}</p>
              {$additional_fields}
            </body>
            </html>
            ";
            
            $mail->isHTML(true);
            $mail->Subject = 'New Contact Form Submission';
            $mail->Body = $message;
            $mail->AltBody = strip_tags($message);
            
            $mail->send();
            return true;
            
        } catch (Exception $e) {
            error_log("Mailer Error: " . $e->getMessage());
            return false;
        }
    }
    
    private function send_email_wp_mail($name, $email, $phone, $service, $payment, $additional_fields) {
        // Fallback email method using WordPress wp_mail()
        $to = 'quickpapersfix@gmail.com';
        $subject = 'New Contact Form Submission';
        
        $html_message = "
        <html>
        <head>
          <title>Contact Form Submission</title>
        </head>
        <body>
          <h2>Contact Form Details</h2>
          <p><strong>Full Name:</strong> {$name}</p>
          <p><strong>Email Address:</strong> {$email}</p>
          <p><strong>Phone Number:</strong> {$phone}</p>
          <p><strong>Service Needed:</strong> {$service}</p>
          <p><strong>Payment Method:</strong> {$payment}</p>
          {$additional_fields}
        </body>
        </html>
        ";
        
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: admin@bankstatementediting.com',
            'Reply-To: ' . $email
        );
        
        return wp_mail($to, $subject, $html_message, $headers);
    }
}

// Initialize the plugin
new CustomOrderFormSMTP();
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Owner Registration</title>
    <style>
        :root {
            --primary: #ff6b6b;
            --primary-dark: #ee5253;
            --secondary: #1DD1A1;
            --dark: #2F3640;
            --light: #F5F6FA;
            --gray: #DCDDE1;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.6;
            color: var(--dark);
            background-color: var(--light);
            margin: 0;
            padding: 0;
        }

        .registration-container {
            max-width: 800px;
            margin: 2rem auto;
            background: #fff;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: var(--shadow);
        }

        h1 {
            color: var(--primary);
            text-align: center;
            margin-bottom: 2rem;
        }

        fieldset {
            border: 1px solid var(--gray);
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        legend {
            font-weight: 600;
            color: var(--primary);
            padding: 0 10px;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        input, select, textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--gray);
            border-radius: 8px;
            font-size: 1rem;
            transition: var(--transition);
        }

        input:focus, select:focus, textarea:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 107, 107, 0.2);
        }

        .file-upload {
            border: 2px dashed var(--gray);
            padding: 1.5rem;
            text-align: center;
            border-radius: 8px;
        }

        .submit-btn {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 1rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            transition: var(--transition);
        }

        .submit-btn:hover {
            background-color: var(--primary-dark);
        }

        .business-hours {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        @media (max-width: 768px) {
            .registration-container {
                padding: 1.5rem;
                margin: 1rem;
            }
            
            .business-hours {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="registration-container">
        <h1>Register Your Restaurant</h1>
        <form id="restaurantForm" action="process_restaurant.php" method="POST" enctype="multipart/form-data">
            
            <!-- Owner Information -->
            <fieldset>
                <legend>Owner Information</legend>
                <div class="form-group">
                    <label for="owner_name">Full Name*</label>
                    <input type="text" id="owner_name" name="owner_name" required>
                </div>
                
                <div class="form-group">
                    <label for="owner_email">Email*</label>
                    <input type="email" id="owner_email" name="owner_email" required>
                </div>
                
                <div class="form-group">
                    <label for="owner_phone">Phone Number*</label>
                    <input type="tel" id="owner_phone" name="owner_phone" required>
                </div>
                
                <div class="form-group">
                    <label for="owner_password">Create Password*</label>
                    <input type="password" id="owner_password" name="owner_password" minlength="8" required>
                </div>
            </fieldset>
            
            <!-- Restaurant Information -->
            <fieldset>
                <legend>Restaurant Details</legend>
                <div class="form-group">
                    <label for="restaurant_name">Restaurant Name*</label>
                    <input type="text" id="restaurant_name" name="restaurant_name" required>
                </div>
                
                <div class="form-group">
                    <label for="cuisine_type">Cuisine Type*</label>
                    <select id="cuisine_type" name="cuisine_type" required>
                        <option value="">Select Cuisine</option>
                        <option value="italian">Italian</option>
                        <option value="indian">Indian</option>
                        <option value="chinese">Chinese</option>
                        <option value="mexican">Mexican</option>
                        <option value="american">American</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="restaurant_address">Full Address*</label>
                    <textarea id="restaurant_address" name="restaurant_address" rows="3" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="fssai_license">FSSAI License Number*</label>
                    <input type="text" id="fssai_license" name="fssai_license" required>
                </div>
            </fieldset>
            
            <!-- Business Hours -->
            <fieldset>
                <legend>Business Hours</legend>
                <div class="business-hours">
                    <div class="form-group">
                        <label>Opening Time*</label>
                        <input type="time" id="opening_time" name="opening_time" required>
                    </div>
                    <div class="form-group">
                        <label>Closing Time*</label>
                        <input type="time" id="closing_time" name="closing_time" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="working_days">Working Days*</label>
                    <select id="working_days" name="working_days" multiple required>
                        <option value="monday">Monday</option>
                        <option value="tuesday">Tuesday</option>
                        <option value="wednesday">Wednesday</option>
                        <option value="thursday">Thursday</option>
                        <option value="friday">Friday</option>
                        <option value="saturday">Saturday</option>
                        <option value="sunday">Sunday</option>
                    </select>
                </div>
            </fieldset>
            
            <!-- Documents Upload -->
            <fieldset>
                <legend>Required Documents</legend>
                <div class="form-group">
                    <label for="owner_id">Owner ID Proof*</label>
                    <div class="file-upload">
                        <input type="file" id="owner_id" name="owner_id" accept="image/*,.pdf" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="fssai_certificate">FSSAI Certificate*</label>
                    <div class="file-upload">
                        <input type="file" id="fssai_certificate" name="fssai_certificate" accept="image/*,.pdf" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="menu_file">Menu (PDF/Image)*</label>
                    <div class="file-upload">
                        <input type="file" id="menu_file" name="menu_file" accept="image/*,.pdf" required>
                    </div>
                </div>
            </fieldset>
            
            <!-- Bank Details -->
            <fieldset>
                <legend>Bank Information</legend>
                <div class="form-group">
                    <label for="bank_name">Bank Name*</label>
                    <input type="text" id="bank_name" name="bank_name" required>
                </div>
                
                <div class="form-group">
                    <label for="account_number">Account Number*</label>
                    <input type="text" id="account_number" name="account_number" required>
                </div>
                
                <div class="form-group">
                    <label for="ifsc_code">IFSC Code*</label>
                    <input type="text" id="ifsc_code" name="ifsc_code" required>
                </div>
            </fieldset>
            
            <!-- Terms and Conditions -->
            <div class="form-group">
                <input type="checkbox" id="terms" name="terms" required>
                <label for="terms">I agree to the Terms and Conditions and Privacy Policy*</label>
            </div>
            
            <button type="submit" class="submit-btn">Register Restaurant</button>
        </form>
    </div>

    <script>
        // Form validation would go here
        document.getElementById('restaurantForm').addEventListener('submit', function(e) {
            // Add validation logic
        });
    </script>
</body>
</html>
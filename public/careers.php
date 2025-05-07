<?php
session_start();
require_once '../config/database.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Careers - G-3 online food ordering system</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="careers.css">

</head>
<body>
    <header class="careers-header">
        <div class="container">
            <h1>Join Our Team</h1>
            <p>Be part of revolutionizing food delivery in Ethiopia. At FoodExpress, we're building more than an app - we're creating opportunities and connecting communities.</p>
            <a href="#open-positions" class="cta-button">View Open Positions</a>
        </div>
    </header>

    <div class="container">
        <section class="why-join">
            <div class="benefits-card">
                <h2><i class="fas fa-medal"></i> Why Work With Us?</h2>
                <ul>
                    <li>Competitive salaries and flexible schedules</li>
                    <li>Opportunities for growth and advancement</li>
                    <li>Positive impact on local businesses and communities</li>
                    <li>Fast-paced, dynamic work environment</li>
                    <li>Employee meal discounts and perks</li>
                </ul>
            </div>
            <div class="benefits-card">
                <h2><i class="fas fa-users"></i> Our Values</h2>
                <ul>
                    <li>Customer obsession - we start with the customer and work backwards</li>
                    <li>Ownership - we're all owners and act on behalf of the entire company</li>
                    <li>Innovation - we expect and require innovation from our team</li>
                    <li>High standards - we have relentlessly high standards</li>
                    <li>Teamwork - we value collaboration and diverse perspectives</li>
                </ul>
            </div>
        </section>

        <section id="open-positions" class="job-categories">
            <h2 style="text-align: center; margin-bottom: 20px;">Open Positions</h2>
            <p style="text-align: center; margin-bottom: 30px; color: #666;">Browse our current job openings by category</p>

            <div class="category-tabs">
                <div class="category-tab active" data-category="all">All Positions</div>
                <div class="category-tab delivery" data-category="delivery">Delivery</div>
                <div class="category-tab restaurant" data-category="restaurant">Restaurant Partners</div>
                <div class="category-tab tech" data-category="tech">Technology</div>
                <div class="category-tab support" data-category="support">Customer Support</div>
            </div>

            <div id="all-jobs" class="job-listings active">
                <!-- Delivery Driver -->
                <div class="job-card delivery">
                    <div class="job-header">
                        <h3 class="job-title">Delivery Driver - Addis Ababa</h3>
                        <div class="job-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Addis Ababa</span>
                            <span><i class="fas fa-clock"></i> Full-time/Part-time</span>
                            <span><i class="fas fa-money-bill-wave"></i> Competitive + Tips</span>
                        </div>
                    </div>
                    <div class="job-description">
                        <p>As a FoodExpress Delivery Driver, you'll be the face of our company, delivering delicious meals from local restaurants to happy customers. Enjoy flexible hours and great earnings potential.</p>
                    </div>
                    <div class="job-requirements">
                        <h4>Requirements:</h4>
                        <ul>
                            <li>Valid driver's license and clean driving record</li>
                            <li>Smartphone with GPS capability</li>
                            <li>Ability to lift 30+ pounds</li>
                            <li>Friendly and professional demeanor</li>
                            <li>Knowledge of local area preferred</li>
                        </ul>
                    </div>
                    <button class="apply-btn" onclick="showApplicationForm('Delivery Driver')">Apply Now</button>
                </div>

                <!-- Restaurant Account Manager -->
                <div class="job-card restaurant">
                    <div class="job-header">
                        <h3 class="job-title">Restaurant Account Manager</h3>
                        <div class="job-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Addis Ababa</span>
                            <span><i class="fas fa-clock"></i> Full-time</span>
                            <span><i class="fas fa-money-bill-wave"></i> ETB 15,000+</span>
                        </div>
                    </div>
                    <div class="job-description">
                        <p>Help local restaurants grow their business through our platform. You'll onboard new partners, optimize their menus, and ensure they have the tools to succeed with FoodExpress.</p>
                    </div>
                    <div class="job-requirements">
                        <h4>Requirements:</h4>
                        <ul>
                            <li>2+ years sales or account management experience</li>
                            <li>Excellent communication and negotiation skills</li>
                            <li>Knowledge of restaurant operations preferred</li>
                            <li>Tech-savvy with ability to learn new systems quickly</li>
                            <li>Passion for food and local businesses</li>
                        </ul>
                    </div>
                    <button class="apply-btn" onclick="showApplicationForm('Restaurant Account Manager')">Apply Now</button>
                </div>

                <!-- Software Engineer -->
                <div class="job-card tech">
                    <div class="job-header">
                        <h3 class="job-title">Software Engineer (PHP/JavaScript)</h3>
                        <div class="job-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Addis Ababa or Remote</span>
                            <span><i class="fas fa-clock"></i> Full-time</span>
                            <span><i class="fas fa-money-bill-wave"></i> ETB 25,000+</span>
                        </div>
                    </div>
                    <div class="job-description">
                        <p>Help build and scale the technology powering Ethiopia's fastest growing food delivery platform. We're looking for talented engineers to join our product development team.</p>
                    </div>
                    <div class="job-requirements">
                        <h4>Requirements:</h4>
                        <ul>
                            <li>3+ years experience with PHP and JavaScript</li>
                            <li>Experience with Laravel, MySQL, and Vue.js/React</li>
                            <li>Strong problem-solving skills</li>
                            <li>Experience with RESTful APIs and microservices</li>
                            <li>Bachelor's degree in Computer Science or related field</li>
                        </ul>
                    </div>
                    <button class="apply-btn" onclick="showApplicationForm('Software Engineer')">Apply Now</button>
                </div>

                <!-- Customer Support -->
                <div class="job-card support">
                    <div class="job-header">
                        <h3 class="job-title">Customer Support Specialist</h3>
                        <div class="job-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Addis Ababa</span>
                            <span><i class="fas fa-clock"></i> Full-time</span>
                            <span><i class="fas fa-money-bill-wave"></i> ETB 10,000+</span>
                        </div>
                    </div>
                    <div class="job-description">
                        <p>Provide exceptional support to our customers, restaurant partners, and delivery drivers. You'll be solving problems and ensuring everyone has a great FoodExpress experience.</p>
                    </div>
                    <div class="job-requirements">
                        <h4>Requirements:</h4>
                        <ul>
                            <li>Excellent communication skills in Amharic and English</li>
                            <li>1+ year customer service experience</li>
                            <li>Ability to multitask and problem-solve</li>
                            <li>Tech-savvy with ability to learn new systems</li>
                            <li>Positive attitude and patience</li>
                        </ul>
                    </div>
                    <button class="apply-btn" onclick="showApplicationForm('Customer Support Specialist')">Apply Now</button>
                </div>
            </div>

            <!-- More job listings would be displayed when categories are selected -->
            <div id="delivery-jobs" class="job-listings">
                <!-- Delivery jobs would appear here when filtered -->
            </div>
        </section>

        <section class="culture-section">
            <h2>Our Culture</h2>
            <div class="culture-grid">
                <div class="culture-card">
                    <i class="fas fa-heart"></i>
                    <h3>Passion for Food</h3>
                    <p>We're foodies at heart, committed to connecting people with great meals from local restaurants.</p>
                </div>
                <div class="culture-card">
                    <i class="fas fa-lightbulb"></i>
                    <h3>Innovation</h3>
                    <p>We're constantly improving our platform to better serve customers, restaurants, and delivery partners.</p>
                </div>
                <div class="culture-card">
                    <i class="fas fa-hands-helping"></i>
                    <h3>Community</h3>
                    <p>We support local businesses and create economic opportunities in the communities we serve.</p>
                </div>
                <div class="culture-card">
                    <i class="fas fa-chart-line"></i>
                    <h3>Growth</h3>
                    <p>We invest in our team's development and promote from within whenever possible.</p>
                </div>
            </div>
        </section>

        <section class="testimonials">
            <h2 style="text-align: center; margin-bottom: 30px;">What Our Team Says</h2>
            
            <div class="testimonial-card">
                <div class="testimonial-content">
                    "I love being a FoodExpress delivery driver because of the flexibility. I can choose my own hours and earn good money while getting to know my city better. The app makes everything so easy!"
                </div>
                <div class="testimonial-author">
                    <img src="../images/careers/driver.jpg" alt="Delivery Driver" class="author-avatar">
                    <div class="author-info">
                        <h4>Michael T.</h4>
                        <p>Delivery Driver, 2 years with FoodExpress</p>
                    </div>
                </div>
            </div>
            
            <div class="testimonial-card">
                <div class="testimonial-content">
                    "As a software engineer at FoodExpress, I get to work on challenging problems that directly impact thousands of users. The collaborative culture and focus on innovation make this a great place to grow as a developer."
                </div>
                <div class="testimonial-author">
                    <img src="../images/careers/engineer.jpg" alt="Software Engineer" class="author-avatar">
                    <div class="author-info">
                        <h4>Sarah K.</h4>
                        <p>Senior Software Engineer</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="application-form" class="application-form" style="display: none;">
            <h2>Apply for <span id="position-title"></span></h2>
            <form id="career-form" method="POST" action="../controllers/careers_controller.php">
                <input type="hidden" name="position" id="position-field">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="first-name">First Name</label>
                        <input type="text" id="first-name" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label for="last-name">Last Name</label>
                        <input type="text" id="last-name" name="last_name" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="location">Location (City)</label>
                    <input type="text" id="location" name="location" required>
                </div>
                
                <div class="form-group">
                    <label for="resume">Upload Resume/CV</label>
                    <input type="file" id="resume" name="resume" accept=".pdf,.doc,.docx" required>
                </div>
                
                <div class="form-group">
                    <label for="cover-letter">Cover Letter</label>
                    <textarea id="cover-letter" name="cover_letter" placeholder="Tell us why you'd be a great fit for this position..."></textarea>
                </div>
                
                <div class="form-group">
                    <label for="referral">How did you hear about us?</label>
                    <select id="referral" name="referral">
                        <option value="">Select an option</option>
                        <option value="job-board">Job Board (Ethiojobs, etc.)</option>
                        <option value="social-media">Social Media</option>
                        <option value="friend">Friend or Colleague</option>
                        <option value="website">Company Website</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                
                <button type="submit" class="submit-btn">Submit Application</button>
            </form>
        </section>
    </div>

    <script>
        // Category tab functionality
        document.querySelectorAll('.category-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                // Remove active class from all tabs
                document.querySelectorAll('.category-tab').forEach(t => {
                    t.classList.remove('active');
                });
                
                // Add active class to clicked tab
                tab.classList.add('active');
                
                // Hide all job listings
                document.querySelectorAll('.job-listings').forEach(listing => {
                    listing.classList.remove('active');
                });
                
                // Show selected job listings
                const category = tab.getAttribute('data-category');
                if (category === 'all') {
                    document.getElementById('all-jobs').classList.add('active');
                } else {
                    // In a real implementation, you would filter and show only jobs for this category
                    document.getElementById('all-jobs').classList.add('active');
                }
            });
        });

        // Show application form for specific position
        function showApplicationForm(position) {
            document.getElementById('position-title').textContent = position;
            document.getElementById('position-field').value = position;
            document.getElementById('application-form').style.display = 'block';
            document.getElementById('application-form').scrollIntoView({
                behavior: 'smooth'
            });
        }

        // Form submission handling
        document.getElementById('career-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Form validation would go here
            
            // Simulate form submission
            alert('Thank you for your application! We will review your information and contact you if there\'s a match.');
            this.reset();
            document.getElementById('application-form').style.display = 'none';
        });
    </script>
</body>
</html>
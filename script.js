// Handle Signup Form
document.getElementById('signupForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const email = document.getElementById('email').value;
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    // Send signup data to the backend
    alert(`Sign up with Email: ${email}, Username: ${username}`);
});

// Handle Login Form
document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const username = document.getElementById('loginUsername').value;
    const password = document.getElementById('loginPassword').value;

    // Send login data to the backend
    alert(`Login with Username: ${username}`);
});

// Comment Functionality
document.querySelectorAll('.comment-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const commentsDiv = btn.nextElementSibling;
        commentsDiv.style.display = commentsDiv.style.display === 'none' ? 'block' : 'none';
    });
});



document.getElementById('sendOtpButton').addEventListener('click', function() {
    const email = document.getElementById('email').value;

    if (email) {
        // Simulate sending OTP to email
        alert('OTP has been sent to ' + email);

        // Generate a random OTP for demonstration
        const generatedOtp = Math.floor(100000 + Math.random() * 900000);
        sessionStorage.setItem('generatedOtp', generatedOtp);

        console.log("Generated OTP (for testing):", generatedOtp); // For testing purposes only
    } else {
        alert('Please enter a valid email address.');
    }
});

document.getElementById('signupForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const enteredOtp = document.getElementById('otp').value;
    const generatedOtp = sessionStorage.getItem('generatedOtp');

    if (enteredOtp === generatedOtp) {
        alert('OTP verified successfully. Registration complete.');
        // Proceed with form submission
        // You can add actual form submission code here
    } else {
        alert('Invalid OTP. Please try again.');
    }
});

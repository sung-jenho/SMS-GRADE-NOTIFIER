document.addEventListener('DOMContentLoaded', function() {
  let loadingAnimation = null;
  
  try {
    if (window.lottie) {
      // Load the hey animation for the left side
      window.lottie.loadAnimation({
        container: document.getElementById('heyLottie'),
        renderer: 'svg',
        loop: true,
        autoplay: true,
        path: '../assets/hey.json'
      });

      // Preload the loading animation
      const loadingContainer = document.getElementById('loadingAnimation');
      if (loadingContainer) {
        loadingAnimation = window.lottie.loadAnimation({
          container: loadingContainer,
          renderer: 'svg',
          loop: true,
          autoplay: false,
          path: '../assets/loading.json'
        });
      }
    }
  } catch (e) {}

  const inputs = document.querySelectorAll('.form-control');
  inputs.forEach(input => {
    input.addEventListener('focus', function () {
      this.parentElement.style.transform = 'scale(1.02)';
    });
    input.addEventListener('blur', function () {
      this.parentElement.style.transform = 'scale(1)';
    });
  });

  const passwordToggle = document.getElementById('passwordToggle');
  const passwordInput = document.getElementById('password');
  const passwordIcon = document.getElementById('passwordIcon');
  passwordToggle.addEventListener('click', function () {
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);
    if (type === 'text') {
      passwordIcon.className = 'bi bi-eye-slash';
      this.classList.add('show-password');
    } else {
      passwordIcon.className = 'bi bi-eye';
      this.classList.remove('show-password');
    }
  });

  // Enhanced form submission with loading animation
  const form = document.querySelector('form');
  const submitBtn = document.querySelector('.btn-primary');
  const loadingOverlay = document.getElementById('loadingOverlay');
  let isSubmitting = false;
  
  form.addEventListener('submit', function (e) {
    if (isSubmitting) return;
    
    // Prevent the default form submission
    e.preventDefault();
    isSubmitting = true;
    
    // Show loading overlay
    loadingOverlay.classList.add('show');
    
    // Start loading animation
    if (loadingAnimation) {
      loadingAnimation.play();
    }
    
    // Update button state
    submitBtn.innerHTML = 'Authenticating...';
    submitBtn.disabled = true;
    
    // Force exactly 3 seconds delay before actual form submission
    setTimeout(() => {
      // Create a new FormData object with the form data
      const formData = new FormData(form);
      
      // Submit the form programmatically after 3 seconds
      const xhr = new XMLHttpRequest();
      xhr.open('POST', form.action || window.location.href, true);
      
      xhr.onload = function() {
        if (xhr.status === 200) {
          // Check if response contains redirect or success
          if (xhr.responseText.includes('Location:') || xhr.responseText.includes('index.php')) {
            // Successful login - redirect to dashboard
            window.location.href = 'index.php';
          } else {
            // Login failed - reload page to show error
            window.location.reload();
          }
        } else {
          // Error occurred - reload page
          window.location.reload();
        }
      };
      
      xhr.onerror = function() {
        // Network error - reload page
        window.location.reload();
      };
      
      xhr.send(formData);
    }, 3000);
  });
  
  function hideLoading() {
    loadingOverlay.classList.remove('show');
    if (loadingAnimation) {
      loadingAnimation.stop();
    }
    submitBtn.innerHTML = 'Sign in';
    submitBtn.disabled = false;
  }
  
  // Hide loading on page visibility change (when user comes back to tab)
  document.addEventListener('visibilitychange', function() {
    if (!document.hidden && window.location.pathname.includes('login.php')) {
      hideLoading();
    }
  });
});



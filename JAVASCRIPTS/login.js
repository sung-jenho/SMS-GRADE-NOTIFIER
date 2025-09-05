document.addEventListener('DOMContentLoaded', function() {
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

  const form = document.querySelector('form');
  const submitBtn = document.querySelector('.btn-primary');
  form.addEventListener('submit', function () {
    submitBtn.innerHTML = 'Signing in...';
    submitBtn.disabled = true;
  });
});



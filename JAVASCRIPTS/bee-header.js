document.addEventListener('DOMContentLoaded', function() {
  try {
    if (window.lottie) {
      // Load bee animation into existing beeLottie container
      var beeContainer = document.getElementById('beeLottie');
      if (beeContainer) {
        window.lottie.loadAnimation({
          container: beeContainer,
          renderer: 'svg',
          loop: true,
          autoplay: true,
          path: '../assets/bee.json'
        });
      }
    }
  } catch (e) {
    console.error('Error loading bee animation:', e);
  }
});

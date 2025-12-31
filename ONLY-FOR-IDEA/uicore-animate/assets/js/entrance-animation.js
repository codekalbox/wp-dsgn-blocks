document.addEventListener('DOMContentLoaded', () => {
  // Select all elements with the data-animation attribute
  const elementsToAnimate = document.querySelectorAll('[data-ui-animation]');

  // Create an Intersection Observer instance
  const observer = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        // Get the animation name from the data-animation attribute
        const animationName = entry.target.getAttribute('data-ui-animation');
        // Get the animation speed from the data-speed attribute
        const animationSpeed = entry.target.getAttribute('data-ui-duration');

        // Add the corresponding speed class based on the data-speed attribute
        if (animationSpeed === 'fast') {
          entry.target.classList.add('animated', 'animated-fast');
        } else if (animationSpeed === 'slow') {
          entry.target.classList.add('animated', 'animated-slow');
        } else {
          entry.target.classList.add('animated');
        }

        // Add the animation class and remove the class that hides the element
        entry.target.classList.add(...animationName.split(' '));
        entry.target.classList.remove('uicore-animate-hide');

        // Optionally, unobserve the element if you only want the animation to run once
        observer.unobserve(entry.target);
      }
    });
  }, {
    rootMargin: '10px',
    // Adjust the root margin as needed
    threshold: 0 // Adjust threshold as needed to control when animation is triggered
  });

  // Observe each element that should be animated
  elementsToAnimate.forEach(element => {
    observer.observe(element);
  });
});
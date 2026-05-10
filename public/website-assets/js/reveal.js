/**
 * Reveal Animation System
 * Lightweight IntersectionObserver-based replacement for AOS
 */

(function () {
  'use strict';

  // Respect prefers-reduced-motion
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  if (prefersReducedMotion) {
    // Immediately show all elements without animation
    document.querySelectorAll('.reveal').forEach(function (el) {
      el.classList.add('active');
    });
    return;
  }

  // IntersectionObserver configuration
  const observerOptions = {
    root: null,
    rootMargin: '0px 0px -50px 0px',
    threshold: 0.1,
  };

  const revealObserver = new IntersectionObserver(function (entries, observer) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        entry.target.classList.add('active');
        observer.unobserve(entry.target);
      }
    });
  }, observerOptions);

  // Observe all reveal elements
  function initReveal() {
    document.querySelectorAll('.reveal:not(.active)').forEach(function (el) {
      revealObserver.observe(el);
    });
  }

  // Initialize on DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initReveal);
  } else {
    initReveal();
  }

  // Re-init for dynamically added content (e.g., AJAX)
  window.initReveal = initReveal;
})();

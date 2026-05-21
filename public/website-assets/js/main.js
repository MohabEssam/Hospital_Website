/**
* Template Name: Medilab
* Template URL: https://bootstrapmade.com/medilab-free-medical-bootstrap-theme/
* Updated: Jun 29 2024 with Bootstrap v5.3.3
* Author: BootstrapMade.com
* License: https://bootstrapmade.com/license/
*/

(function() {
  "use strict";

  /**
   * Apply .scrolled class to the body as the page is scrolled down
   */
  function toggleScrolled() {
    const selectBody = document.querySelector('body');
    const selectHeader = document.querySelector('#header');
    if (!selectHeader.classList.contains('scroll-up-sticky') && !selectHeader.classList.contains('sticky-top') && !selectHeader.classList.contains('fixed-top')) return;
    window.scrollY > 40 ? selectBody.classList.add('scrolled') : selectBody.classList.remove('scrolled');
  }

  document.addEventListener('scroll', toggleScrolled);
  window.addEventListener('load', toggleScrolled);

  /**
   * Mobile nav toggle
   */
  const mobileNavToggleBtn = document.querySelector('.mobile-nav-toggle');
  const mobileNavBackdrop = document.querySelector('.mobile-nav-backdrop');

  function mobileNavToogle() {
    const isOpen = document.querySelector('body').classList.toggle('mobile-nav-active');
    mobileNavToggleBtn.classList.toggle('bi-list');
    mobileNavToggleBtn.classList.toggle('bi-x');
    mobileNavToggleBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    mobileNavToggleBtn.setAttribute('aria-label', isOpen ? 'Close navigation menu' : 'Open navigation menu');
  }

  if (mobileNavToggleBtn) {
    mobileNavToggleBtn.addEventListener('click', mobileNavToogle);
  }

  if (mobileNavBackdrop) {
    mobileNavBackdrop.addEventListener('click', () => {
      if (document.querySelector('.mobile-nav-active')) {
        mobileNavToogle();
      }
    });
  }

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && document.querySelector('.mobile-nav-active')) {
      mobileNavToogle();
    }
  });

  /**
   * Hide mobile nav on same-page/hash links
   */
  document.querySelectorAll('#navmenu a').forEach(navmenu => {
    navmenu.addEventListener('click', () => {
      if (document.querySelector('.mobile-nav-active')) {
        mobileNavToogle();
      }
    });

  });

  /**
   * Toggle mobile nav dropdowns
   */
  document.querySelectorAll('.navmenu .toggle-dropdown').forEach(navmenu => {
    navmenu.addEventListener('click', function(e) {
      e.preventDefault();
      this.parentNode.classList.toggle('active');
      this.parentNode.nextElementSibling.classList.toggle('dropdown-active');
      e.stopImmediatePropagation();
    });
  });

  /**
   * Preloader
   */
  const preloader = document.querySelector('#preloader');
  if (preloader) {
    window.addEventListener('load', () => {
      preloader.remove();
    });
  }

  /**
   * Scroll top button
   */
  let scrollTop = document.querySelector('.scroll-top');

  function toggleScrollTop() {
    if (scrollTop) {
      window.scrollY > 100 ? scrollTop.classList.add('active') : scrollTop.classList.remove('active');
    }
  }
  scrollTop.addEventListener('click', (e) => {
    e.preventDefault();
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  });

  window.addEventListener('load', toggleScrollTop);
  document.addEventListener('scroll', toggleScrollTop);

  /**
   * Animation on scroll function and init
   */
  function aosInit() {
    if (typeof AOS === 'undefined') {
      return;
    }

    AOS.init({
      duration: 600,
      easing: 'ease-in-out',
      once: true,
      mirror: false
    });
  }
  window.addEventListener('load', aosInit);

  /**
   * Initiate glightbox
   */
  const glightbox = GLightbox({
    selector: '.glightbox'
  });

  /**
   * Initiate Pure Counter
   */
  new PureCounter();

  /**
   * Frequently Asked Questions Toggle
   */
  document.querySelectorAll('.faq-item h3, .faq-item .faq-toggle').forEach((faqItem) => {
    faqItem.addEventListener('click', () => {
      faqItem.parentNode.classList.toggle('faq-active');
    });
  });

  /**
   * Init swiper sliders
   */
  function initSwiper() {
    document.querySelectorAll(".init-swiper").forEach(function(swiperElement) {
      let config = JSON.parse(
        swiperElement.querySelector(".swiper-config").innerHTML.trim()
      );

      if (swiperElement.classList.contains("swiper-tab")) {
        initSwiperWithCustomPagination(swiperElement, config);
      } else {
        new Swiper(swiperElement, config);
      }
    });
  }

  window.addEventListener("load", initSwiper);

  /**
   * Correct scrolling position upon page load for URLs containing hash links.
   */
  window.addEventListener('load', function(e) {
    if (window.location.hash) {
      if (document.querySelector(window.location.hash)) {
        setTimeout(() => {
          let section = document.querySelector(window.location.hash);
          let scrollMarginTop = getComputedStyle(section).scrollMarginTop;
          window.scrollTo({
            top: section.offsetTop - parseInt(scrollMarginTop),
            behavior: 'smooth'
          });
        }, 100);
      }
    }
  });

  /**
   * Navmenu Scrollspy
   */
  let navmenulinks = document.querySelectorAll('.navmenu a');

  function navmenuScrollspy() {
    let activeLink = null;

    navmenulinks.forEach(navmenulink => {
      if (!navmenulink.hash) return;
      let section = document.querySelector(navmenulink.hash);
      if (!section) return;
      let position = window.scrollY + 160;

      if (position >= section.offsetTop && position < (section.offsetTop + section.offsetHeight)) {
        activeLink = navmenulink;
      }
    })

    if (activeLink) {
      document.querySelectorAll('.navmenu a.active').forEach(link => link.classList.remove('active'));
      activeLink.classList.add('active');
    }
  }
  window.addEventListener('load', navmenuScrollspy);
  document.addEventListener('scroll', navmenuScrollspy);

  /**
   * Doctor cards filtering and lightweight loading polish
   */
  document.querySelectorAll('[data-doctor-grid]').forEach((grid) => {
    const section = grid.closest('.doctors');
    const cards = Array.from(grid.querySelectorAll('.doctor-card-col'));
    const search = section ? section.querySelector('[data-doctor-search]') : null;
    const department = section ? section.querySelector('[data-doctor-department]') : null;
    const sort = section ? section.querySelector('[data-doctor-sort]') : null;
    const empty = section ? section.querySelector('[data-doctor-empty]') : null;
    const skeletons = section ? section.querySelector('[data-doctor-skeletons]') : null;

    if (skeletons) {
      window.setTimeout(() => skeletons.classList.add('is-hidden'), 450);
    }

    function applyDoctorFilters() {
      const searchTerm = search ? search.value.trim().toLowerCase() : '';
      const departmentValue = department ? department.value : '';
      const hasActiveFilters = searchTerm.length > 0 || departmentValue.length > 0;
      let visibleCount = 0;

      cards.forEach((cardColumn) => {
        const card = cardColumn.querySelector('[data-doctor-card]');
        if (!card) return;

        const name = (card.dataset.name || '').toLowerCase();
        const specialty = (card.dataset.specialty || '').toLowerCase();
        const matchesSearch = !searchTerm || name.includes(searchTerm) || specialty.includes(searchTerm);
        const matchesDepartment = !departmentValue || card.dataset.department === departmentValue;

        let isVisible;
        if (hasActiveFilters) {
          // While searching/filtering, every card is a candidate (including
          // those that were hidden by default on the homepage).
          isVisible = matchesSearch && matchesDepartment;
        } else {
          // No active filter — restore the page's default visibility.
          // Cards explicitly marked as "extra" stay hidden so the homepage
          // keeps its original 6-card layout.
          isVisible = cardColumn.dataset.doctorDefault !== 'extra';
        }

        cardColumn.hidden = !isVisible;
        if (isVisible) visibleCount++;
      });

      if (empty) {
        empty.hidden = visibleCount > 0 || !hasActiveFilters;
      }
    }

    function sortDoctorCards() {
      if (!sort || !sort.value) return;

      const sortedCards = [...cards].sort((first, second) => {
        const firstCard = first.querySelector('[data-doctor-card]');
        const secondCard = second.querySelector('[data-doctor-card]');
        const key = sort.value;

        return Number(secondCard.dataset[key] || 0) - Number(firstCard.dataset[key] || 0);
      });

      sortedCards.forEach((cardColumn) => grid.appendChild(cardColumn));
    }

    if (search) {
      search.addEventListener('input', applyDoctorFilters);
    }

    if (department && !department.closest('form')) {
      department.addEventListener('change', applyDoctorFilters);
    }

    if (sort) {
      sort.addEventListener('change', () => {
        sortDoctorCards();
        applyDoctorFilters();
      });
    }

    applyDoctorFilters();
  });

  /**
   * Doctor profile interactions
   */
  const profilePage = document.querySelector('[data-doctor-profile]');
  if (profilePage) {
    const skeleton = document.querySelector('[data-profile-skeleton]');
    const profilePhoto = document.querySelector('[data-profile-parallax]');
    const profileNavLinks = document.querySelectorAll('.profile-section-nav a');
    const profileSlots = document.querySelectorAll('.profile-slot.is-open');
    const bookingForm = document.querySelector('[data-profile-booking-form]');
    const bookingDate = document.querySelector('[data-profile-date]');
    const bookingTime = document.querySelector('[data-profile-time]');
    const bookingSubmit = document.querySelector('[data-profile-booking-submit]');
    const bookingFeedback = document.querySelector('[data-profile-booking-feedback]');

    window.setTimeout(() => {
      if (skeleton) skeleton.classList.add('is-hidden');
    }, 520);

    profileSlots.forEach((slot) => {
      slot.addEventListener('click', () => {
        profileSlots.forEach((item) => {
          item.classList.remove('is-selected');
          item.setAttribute('aria-checked', 'false');
        });

        slot.classList.add('is-selected');
        slot.setAttribute('aria-checked', 'true');

        if (bookingDate && bookingTime) {
          bookingDate.value = slot.dataset.date;
          bookingTime.value = slot.dataset.time;
        }

        if (bookingSubmit) {
          bookingSubmit.disabled = false;
        }
      });

      slot.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' || event.key === ' ') {
          event.preventDefault();
          slot.click();
        }
      });
    });

    if (bookingForm) {
      bookingForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        if (!bookingDate.value || !bookingTime.value) {
          if (bookingFeedback) {
            bookingFeedback.className = 'profile-booking-feedback is-error';
            bookingFeedback.textContent = 'Please choose an available appointment slot.';
          }
          return;
        }

        if (bookingSubmit) {
          bookingSubmit.disabled = true;
          bookingSubmit.dataset.originalText = bookingSubmit.innerHTML;
          bookingSubmit.innerHTML = '<span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Booking...';
        }

        try {
          const response = await fetch(bookingForm.action, {
            method: 'POST',
            headers: {
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest',
            },
            body: new FormData(bookingForm),
          });

          const payload = await response.json();

          if (!response.ok) {
            const firstError = payload.errors ? Object.values(payload.errors)[0][0] : payload.message;
            throw new Error(firstError || 'Unable to book this appointment.');
          }

          if (bookingFeedback) {
            bookingFeedback.className = 'profile-booking-feedback is-success';
            bookingFeedback.textContent = payload.message;
          }

          document.querySelector('.profile-slot.is-selected')?.classList.add('is-closed');
          document.querySelector('.profile-slot.is-selected')?.setAttribute('disabled', 'disabled');
          document.querySelector('.profile-slot.is-selected')?.classList.remove('is-open', 'is-selected');
          bookingForm.reset();
          bookingDate.value = '';
          bookingTime.value = '';
        } catch (error) {
          if (bookingFeedback) {
            bookingFeedback.className = 'profile-booking-feedback is-error';
            bookingFeedback.textContent = error.message;
          }
        } finally {
          if (bookingSubmit) {
            bookingSubmit.innerHTML = bookingSubmit.dataset.originalText || bookingSubmit.innerHTML;
            bookingSubmit.disabled = true;
          }
        }
      });
    }

    function updateProfileNav() {
      let activeLink = null;
      profileNavLinks.forEach((link) => {
        const section = document.querySelector(link.hash);
        if (!section) return;

        const position = window.scrollY + 150;
        if (position >= section.offsetTop && position < section.offsetTop + section.offsetHeight) {
          activeLink = link;
        }
      });

      if (activeLink) {
        profileNavLinks.forEach((link) => link.classList.remove('active'));
        activeLink.classList.add('active');
      }
    }

    function updateProfileParallax() {
      if (!profilePhoto || window.innerWidth < 992) return;
      const offset = Math.min(26, window.scrollY * 0.035);
      profilePhoto.style.transform = `translateY(${offset}px)`;
    }

    window.addEventListener('scroll', () => {
      updateProfileNav();
      updateProfileParallax();
    });
    window.addEventListener('load', () => {
      updateProfileNav();
      updateProfileParallax();
    });
  }

  /**
   * Medical Center Services — carousel arrows
   */
  const mcsTrack = document.querySelector('.mcs-track');
  const mcsLeft = document.querySelector('.mcs-arrow-left');
  const mcsRight = document.querySelector('.mcs-arrow-right');

  if (mcsTrack && mcsLeft && mcsRight) {
    const getScrollAmount = () => {
      const card = mcsTrack.querySelector('.mcs-card');
      if (!card) return 300;
      return card.offsetWidth + 28;
    };

    mcsLeft.addEventListener('click', () => {
      mcsTrack.scrollBy({ left: -getScrollAmount(), behavior: 'smooth' });
    });

    mcsRight.addEventListener('click', () => {
      mcsTrack.scrollBy({ left: getScrollAmount(), behavior: 'smooth' });
    });
  }

})();

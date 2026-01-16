document.addEventListener("DOMContentLoaded", () => {
  if (!window.location.pathname.includes("/wp-admin")) {
    document.documentElement.classList.add("is-frontend");
  }

  const isMobile = window.matchMedia("(max-width: 819px)").matches;

  // --- Lenis (Desactivado en mobile para mejor performance) ---
  let lenis;
  if (typeof Lenis !== "undefined" && !isMobile) {
    lenis = new Lenis({
      smooth: true,
      lerp: 0.1, // Un poco más suave
      duration: 1.2,
    });

    const raf = (time) => {
      lenis.raf(time);
      requestAnimationFrame(raf);
    };
    requestAnimationFrame(raf);
    
    // Header sincronizado con Lenis
    const header = document.querySelector(".header");
    if (header) {
      lenis.on("scroll", ({ scroll, direction }) => {
        if (scroll > 80) header.classList.add("active");
        else header.classList.remove("active");

        if (direction === 1 && scroll > 140) header.classList.add("hide");
        else header.classList.remove("hide");
      });
    }
  } else if (isMobile) {
    // Header simple para mobile (sin Lenis)
    const header = document.querySelector(".header");
    if (header) {
      window.addEventListener("scroll", () => {
        const scroll = window.scrollY;
        if (scroll > 20) header.classList.add("active");
        else header.classList.remove("active");
      }, { passive: true });
    }
  }

  // --- Animaciones de entrada ---
  const sections = document.querySelectorAll(".anim");
  if (sections.length && "IntersectionObserver" in window) {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add("in-view");
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.1 }
    );

    sections.forEach((section) => {
      // Delay secuencial para hijos
      section.querySelectorAll(":scope > *").forEach((el, i) => {
        el.style.transitionDelay = `${i * 60}ms`;
      });
      observer.observe(section);
    });
  }

  // --- Buscador custom ---
  const toggle = document.getElementById("search-toggle");
  const overlay = document.getElementById("search-overlay");
  const searchInput = document.querySelector(".wp-block-search__input");

  if (toggle && overlay && searchInput) {
    toggle.addEventListener("click", () => {
      overlay.classList.add("active");
      searchInput.value = "";
      setTimeout(() => searchInput.focus(), 100);
    });

    overlay.addEventListener("click", (e) => {
      if (e.target === overlay) overlay.classList.remove("active");
    });
  }

  // --- Vista grid/list ---
  const wrapper = document.querySelector(".actividades-wrapper");
  const buttons = document.querySelectorAll(".view-btn");
  if (wrapper && buttons.length) {
    const savedView = localStorage.getItem("actividades-view") || "view-list";
    wrapper.classList.add(savedView);
    
    buttons.forEach((button) => {
      const view = "view-" + button.dataset.view;
      if (view === savedView) button.classList.add("active");

      button.addEventListener("click", () => {
        wrapper.classList.remove("view-grid", "view-list");
        wrapper.classList.add(view);
        buttons.forEach(b => b.classList.toggle("active", b === button));
        localStorage.setItem("actividades-view", view);
      });
    });
  }

  // --- Overlay link en li ---
  document.querySelectorAll(".post-overlay li").forEach((li) => {
    const href = li.querySelector(".wp-block-post-featured-image a")?.href;
    if (href && !li.querySelector(".full-link")) {
      const a = document.createElement("a");
      a.href = href;
      a.className = "full-link";
      a.setAttribute("aria-label", "Ver actividad");
      li.appendChild(a);
    }
  });

  // --- Swipers ---
  if (typeof Swiper !== "undefined") {
    if (document.querySelector(".hero-swiper")) {
      new Swiper(".hero-swiper", {
        slidesPerView: "auto",
        centeredSlides: true,
        spaceBetween: 24,
        pagination: { el: ".swiper-pagination", clickable: true },
        navigation: { nextEl: ".swiper-button-next", prevEl: ".swiper-button-prev" },
        autoplay: { delay: 5000, disableOnInteraction: false },
        speed: 800,
        loop: true,
      });
    }
    if (document.querySelector(".colaboradores-swiper")) {
      new Swiper(".colaboradores-swiper", {
        slidesPerView: 2,
        spaceBetween: 16,
        pagination: { el: ".swiper-pagination", dynamicBullets: true },
        freeMode: true,
        breakpoints: {
          640: { slidesPerView: 3, spaceBetween: 24 },
          1024: { slidesPerView: 5 },
          1200: { slidesPerView: 6 },
        },
      });
    }
  }

  // --- Fancybox ---
  if (typeof Fancybox !== "undefined") {
    const initFancy = (img) => {
      const caption = img.getAttribute("alt") || "";
      const link = img.closest("a");
      if (link) {
        link.setAttribute("data-fancybox", "gallery");
        link.setAttribute("data-caption", caption);
      } else {
        const wrapper = document.createElement("a");
        wrapper.href = img.src;
        wrapper.setAttribute("data-fancybox", "gallery");
        wrapper.setAttribute("data-caption", caption);
        img.parentNode.insertBefore(wrapper, img);
        wrapper.appendChild(img);
      }
    };

    document.querySelectorAll(".wp-block-image img, .wp-block-gallery a img").forEach(initFancy);
  }

  // --- Accordion ---
  document.querySelectorAll(".accordion-header").forEach((header) => {
    header.addEventListener("click", () => {
      const content = header.nextElementSibling;
      const isOpen = content.classList.contains("open");
      
      document.querySelectorAll(".accordion-content").forEach(c => {
        c.style.maxHeight = null;
        c.classList.remove("open");
        c.previousElementSibling.classList.remove("active");
      });

      if (!isOpen) {
        content.style.maxHeight = content.scrollHeight + "px";
        content.classList.add("open");
        header.classList.add("active");
      }
    });
  });

  // --- Tribe Events Layout Fix ---
  if (window.tribe && window.tribe.events) {
    // Escuchar una sola vez cuando la vista esté lista
    document.addEventListener('afterSetup.tribeEvents', () => {
       window.dispatchEvent(new Event("resize"));
    });
  }
});


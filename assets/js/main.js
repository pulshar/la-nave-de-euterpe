document.addEventListener("DOMContentLoaded", () => {
  if (!window.location.pathname.includes("/wp-admin")) {
    document.documentElement.classList.add("is-frontend");
  }

  if (typeof Lenis !== "undefined") {
    const lenis = new Lenis({
      smooth: true,
      lerp: 0.2,
      duration: 0.8,
    });

    // --- Animaciones de entrada ---
    const sections = document.querySelectorAll(".anim");
    if (sections.length) {
      sections.forEach((section) => {
        section.querySelectorAll(":scope > *").forEach((el, i) => {
          el.style.transitionDelay = `${i * 100}ms`;
        });
      });

      if ("IntersectionObserver" in window) {
        const observer = new IntersectionObserver(
          (entries) => {
            entries.forEach((entry) => {
              if (entry.isIntersecting) {
                entry.target.classList.add("in-view");
                observer.unobserve(entry.target);
              }
            });
          },
          { threshold: 0.2 }
        );

        sections.forEach((section) => observer.observe(section));
      }
    }

    // --- HEADER — sincronizado con Lenis ---
    const header = document.querySelector(".header");
    if (header) {
      const isMobile = window.matchMedia("(max-width: 819px)").matches;
      lenis.on("scroll", ({ scroll, direction }) => {
        // Activa fondo y blur al hacer scroll
        if (scroll > (isMobile ? 20 : 80)) {
          header.classList.add("active");
        } else {
          header.classList.remove("active");
        }

        // Oculta si baja, muestra si sube
        if (direction === 1 && scroll > (isMobile ? 60 : 140)) {
          header.classList.add("hide");
        } else {
          header.classList.remove("hide");
        }
      });
    }
    const raf = (time) => {
      lenis.raf(time);
      requestAnimationFrame(raf);
    };
    requestAnimationFrame(raf);
  } else {
    console.warn("Lenis no encontrado — scroll no inicializado.");
  }

  // --- Buscador custom ---
  const toggle = document.getElementById("search-toggle");
  const overlay = document.getElementById("search-overlay");
  const searchInput = document.querySelector(".wp-block-search__input");

  if (!toggle || !overlay || !searchInput) return;

  toggle.addEventListener("click", () => {
    overlay.classList.add("active");
    searchInput.value= ""
    searchInput.focus();
  });

  overlay.addEventListener("click", (e) => {
    if (e.target === overlay) overlay.classList.remove("active");
  });

  // --- Vista grid/list ---
  const wrapper = document.querySelector(".actividades-wrapper");
  const buttons = document.querySelectorAll(".view-btn");

  if (wrapper && buttons.length) {
    const savedView = localStorage.getItem("actividades-view") || "view-grid";
    wrapper.classList.remove("view-grid", "view-list");
    wrapper.classList.add(savedView);

    buttons.forEach((button) => {
      const view = "view-" + button.dataset.view;
      const isActive = view === savedView;
      button.classList.toggle("active", isActive);
      button.setAttribute("aria-pressed", isActive);

      button.addEventListener("click", () => {
        wrapper.classList.remove("view-grid", "view-list");
        wrapper.classList.add(view);

        buttons.forEach((b) => {
          const active = b === button;
          b.classList.toggle("active", active);
          b.setAttribute("aria-pressed", active);
        });

        localStorage.setItem("actividades-view", view);
      });
    });
  }

  // --- Overlay link en li ---
  const listItems = document.querySelectorAll(".post-overlay li");
  listItems.forEach((li) => {
    const href = li.querySelector(".wp-block-post-featured-image a")?.href;
    if (href && !li.querySelector(".full-link")) {
      const a = document.createElement("a");
      a.href = href;
      a.className = "full-link";
      a.setAttribute("aria-label", "Ver actividad");
      li.appendChild(a);
    }
  });

  // --- Swiper ---
  if (typeof Swiper !== "undefined") {
    const swipers = document.querySelectorAll(".swiper");
    if (swipers.length) {
      swipers.forEach((container) => {
        const slides = container.querySelectorAll(".swiper-slide");
        if (!slides.length) return; // No inicializar si no hay slides

        const pagination = container.querySelector(".swiper-pagination");

        new Swiper(container, {
          slidesPerView: 5,
          spaceBetween: 30,
          pagination: pagination
            ? { el: pagination, dynamicBullets: true }
            : false,
          dynamicBullets: true,
          navigation: false,
          breakpoints: {
            320: { slidesPerView: 1 },
            576: { slidesPerView: 2 },
            768: { slidesPerView: 3 },
            1024: { slidesPerView: 4 },
            1200: { slidesPerView: 5 },
          },
        });
      });
    }
  }
});

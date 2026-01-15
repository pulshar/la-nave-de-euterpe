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
          el.style.transitionDelay = `${i * 80}ms`;
        });
      });

      if ("IntersectionObserver" in window) {
        const observer = new IntersectionObserver(
          (entries, obs) => {
            entries.forEach((entry) => {
              if (entry.isIntersecting) {
                entry.target.classList.add("in-view");
                obs.unobserve(entry.target);
              }
            });
          },
          {
            threshold: 0.1,
          }
        );

        sections.forEach((section) => observer.observe(section));
        sections.forEach((section) => {
          const rect = section.getBoundingClientRect();
          if (rect.top < window.innerHeight && rect.bottom > 0) {
            section.classList.add("in-view");
          }
        });
      } else {
        sections.forEach((section) => section.classList.add("in-view"));
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
    searchInput.value = "";
    searchInput.focus();
  });

  overlay.addEventListener("click", (e) => {
    if (e.target === overlay) overlay.classList.remove("active");
  });

  // --- Vista grid/list ---
  const wrapper = document.querySelector(".actividades-wrapper");
  const buttons = document.querySelectorAll(".view-btn");
  const progHistorica = document.querySelector(".programacion-historica");

  if (wrapper && buttons.length) {
    const savedView = localStorage.getItem("actividades-view") || "view-list";
    wrapper.classList.remove("view-grid", "view-list");
    wrapper.classList.add(savedView);
    if (progHistorica) {
      progHistorica.classList.remove("view-grid", "view-list");
      progHistorica.classList.add(savedView);
    }

    buttons.forEach((button) => {
      const view = "view-" + button.dataset.view;
      const isActive = view === savedView;
      button.classList.toggle("active", isActive);
      button.setAttribute("aria-pressed", isActive);

      button.addEventListener("click", () => {
        wrapper.classList.remove("view-grid", "view-list");
        wrapper.classList.add(view);
        if (progHistorica) {
          progHistorica.classList.remove("view-grid", "view-list");
          progHistorica.classList.add(view);
        }

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

  // Add html attribute to sidebar elements
  const sidebar = document.querySelector(".sidebar.programacion");
  if (sidebar) {
    const isMobile = window.matchMedia("(max-width: 781px)").matches;
    if (!isMobile) sidebar.setAttribute("data-lenis-prevent", "");
  }
  // --- Swipers ---
  const heroSlider = document.querySelector(".hero-swiper");
  if (heroSlider) {
    new Swiper(".hero-swiper", {
      slidesPerView: 2,
      centeredSlides: true,
      spaceBetween: 24,
      pagination: { el: ".swiper-pagination", clickable: true },
      navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
      },
      autoplay: { delay: 5000 },
      speed: 600,
      loop: true,
      breakpoints: {
        320: { slidesPerView: 1 },
        560: { slidesPerView: 2 },
      },
    });
  }
  const colaboradoresSlider = document.querySelector(".colaboradores-swiper");
  if (colaboradoresSlider) {
    new Swiper(".colaboradores-swiper", {
      slidesPerView: 6,
      spaceBetween: 24,
      pagination: { el: ".swiper-pagination", dynamicBullets: true },
      dynamicBullets: true,
      navigation: false,
      freeMode: {
        enabled: true,
        sticky: true,
        momentumRatio: 0.6,
        momentumVelocityRatio: 0.6,
      },
      breakpoints: {
        320: { slidesPerView: 1 },
        480: { slidesPerView: 2 },
        640: { slidesPerView: 3 },
        768: { slidesPerView: 4 },
        1024: { slidesPerView: 5 },
        1200: { slidesPerView: 6 },
      },
    });
  }

  // Selecciona todas las imágenes dentro del contenido del post para FancyBox

  // --- Imágenes sueltas ---
  const images = document.querySelectorAll(
    ".wp-block-image img, .entry-content img, .tribe-events-single-event-description img"
  );
  if (images.length > 0) {
    images.forEach((img) => {
      const caption = img.getAttribute("alt") || ""; // cogemos alt como caption
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
    });
  }

  // --- Imágenes dentro de bloques Gallery ---
  const galleries = document.querySelectorAll(".wp-block-gallery a img");
  if (galleries.length > 0) {
    galleries.forEach((img) => {
      const caption = img.getAttribute("alt") || "";
      const link = img.closest("a");
      if (link) {
        link.setAttribute("data-fancybox", "gallery");
        link.setAttribute("data-caption", caption);
      }
    });
  }

  // --- Accordion ---
  document.querySelectorAll(".accordion-header").forEach((header) => {
    header.addEventListener("click", () => {
      const item = header.parentElement;
      const content = item.querySelector(".accordion-content");

      // Cierra los demás
      document.querySelectorAll(".accordion-content").forEach((c) => {
        if (c !== content) {
          c.style.maxHeight = null;
          c.classList.remove("open");
          c.previousElementSibling.classList.remove("active");
        }
      });

      // Alterna el actual
      if (content.classList.contains("open")) {
        content.style.maxHeight = null;
        content.classList.remove("open");
        header.classList.remove("active");
      } else {
        content.style.maxHeight = content.scrollHeight + "px";
        content.classList.add("open");
        header.classList.add("active");
      }
    });
  });

  const path = window.location.pathname;
  // PRODUCCIONES (página + singles)
  if (path.includes("/producciones") || path.includes("/produccion/")) {
    // Página Producciones
    const pageRoot = document.querySelector(
      '.breadcrumbs span.post-page.current-item[property="name"]'
    );
    if (pageRoot) {
      pageRoot.textContent = "Producciones";
    }

    // Single Producciones
    const singleRoot = document.querySelector(
      '.breadcrumbs .produccion-root span[property="name"]'
    );
    if (singleRoot) {
      singleRoot.textContent = "Producciones";
    }
  }

  const intervaloResize = setInterval(function () {
    if (window.tribe && window.tribe.events && window.tribe.events.views) {
      window.dispatchEvent(new Event("resize"));
    }
  }, 50);

  setTimeout(() => {
    clearInterval(intervaloResize);
  }, 1000);
});

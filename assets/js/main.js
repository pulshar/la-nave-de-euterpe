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
    searchInput.value = "";
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
      slidesPerView: 1,
      spaceBetween: 0,
      pagination: { el: ".swiper-pagination", clickable: true },
      navigation: false,
      autoplay: { delay: 5000 },
      speed: 1200,
      loop: true,
      effect: "fade",
    });
  }
  const colaboradoresSlider = document.querySelector(".colaboradores-swiper");
  if (colaboradoresSlider) {
    new Swiper(".colaboradores-swiper", {
      slidesPerView: 5,
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
        320: { slidesPerView: 2 },
        480: { slidesPerView: 2 },
        768: { slidesPerView: 3 },
        1024: { slidesPerView: 4 },
        1200: { slidesPerView: 5 },
      },
    });
  }

  // Selecciona todas las imágenes dentro del contenido del post para FancyBox

  // --- Imágenes sueltas ---
  const images = document.querySelectorAll(
    ".wp-block-image img, .entry-content img"
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
});

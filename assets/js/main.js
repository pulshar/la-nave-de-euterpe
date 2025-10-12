document.addEventListener("DOMContentLoaded", () => {

   if (!document.body.classList.contains("wp-admin")) {
     document.documentElement.classList.add("is-frontend");
   }
  // --- Lenis smooth scroll ---
  if (typeof Lenis !== "undefined") {
    const lenis = new Lenis({
      smooth: true,
      lerp: 0.2,
      duration: 0.8,
    });

    const sections = document.querySelectorAll(".anim");
    if (sections.length) {
      sections.forEach((section) => {
        section.querySelectorAll(":scope > *").forEach((el, i) => {
          el.style.transitionDelay = `${i * 100}ms`;
        });
      });

      const reveal = () => {
        sections.forEach((section) => {
          const top = section.getBoundingClientRect().top;
          if (top < window.innerHeight * 0.8) {
            section.classList.add("in-view");
          }
        });
      };

      lenis.on("scroll", reveal);

      function raf(time) {
        lenis.raf(time);
        requestAnimationFrame(raf);
      }

      requestAnimationFrame(raf);
      reveal();
    }
  }

  // --- Vista grid/list ---
  const wrapper = document.querySelector(".actividades-wrapper");
  const buttons = document.querySelectorAll(".view-btn");

  if (wrapper && buttons.length) {
    const savedView = localStorage.getItem("actividades-view") || "view-grid";
    wrapper.className = "actividades-wrapper " + savedView;

    buttons.forEach((button) => {
      const isActive = "view-" + button.dataset.view === savedView;
      button.classList.toggle("active", isActive);
      button.setAttribute("aria-pressed", isActive);

      button.addEventListener("click", () => {
        const view = "view-" + button.dataset.view;
        wrapper.className = "actividades-wrapper " + view;

        buttons.forEach((b) => {
          const isActive = b === button;
          b.classList.toggle("active", isActive);
          b.setAttribute("aria-pressed", isActive);
        });

        localStorage.setItem("actividades-view", view);
      });
    });
  }

  // --- Overlay link en li ---
  const listItems = document.querySelectorAll(".post-overlay li");
  if (listItems.length) {
    listItems.forEach((li) => {
      const href = li.querySelector(".wp-block-post-featured-image a")?.href;
      if (!href) return;

      if (!li.querySelector(".full-link")) {
        const a = document.createElement("a");
        a.href = href;
        a.className = "full-link";
        a.setAttribute("aria-label", "Ver actividad");
        li.appendChild(a);
      }
    });
  }
});

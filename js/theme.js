(function () {
  const link   = document.getElementById("theme-link");
  const toggle = document.getElementById("theme-toggle");
  const icon   = document.getElementById("theme-icon");

  if (!link) return;
  
  const darkHref = "/css/style_nuit.css";
  const lightHref = "/css/style.css";
  const sunIcon   = "/images/soleil.png";
  const moonIcon  = "/images/lune.png";

  function setTheme(theme){
    if (theme === "dark") {
      link.href = darkHref;
      if (icon) {
        icon.src = sunIcon;
        icon.alt = "passer au mode clair";
      }
    } else {
      theme = "light";
      link.href = lightHref;
      if (icon) {
        icon.src = moonIcon;
        icon.alt = "passer au mode nuit";
      }
    }

    // Enregistrement de la préférence
    if (window.etudavizCookie) {
      window.etudavizCookie.savePref("theme", theme);
    } else {
      localStorage.setItem("theme", theme);
    }
  }

  // Chargement du thème au démarrage
  let saved = null;

  if (window.etudavizCookie) {
    saved = window.etudavizCookie.loadPref("theme");
  }

  if (!saved) {
    saved = localStorage.getItem("theme");
  }

  const initialTheme = (saved === "dark" || saved === "light") ? saved : "light";
  setTheme(initialTheme);

  // Gestion du clic sur le bouton (s'il existe)
  if (toggle) {
    toggle.addEventListener("click", () => {
      const isDark = link.href.includes("style_nuit.css");
      setTheme(isDark ? "light" : "dark");
    });
  }
})();

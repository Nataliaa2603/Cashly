function toggleTheme() {
    const currentTheme = document.documentElement.getAttribute("data-theme") || "light";
    const newTheme = currentTheme === "dark" ? "light" : "dark";
    
    document.documentElement.setAttribute("data-theme", newTheme);
    localStorage.setItem("cashly_theme", newTheme);

    // Disparar un evento personalizado para actualizar los componentes que dependen del tema (ej. Chart.js)
    window.dispatchEvent(new CustomEvent("themeChanged", { detail: { theme: newTheme } }));
}

// Aplicar el tema guardado al cargar la página de inmediato
(function () {
    const savedTheme = localStorage.getItem("cashly_theme") || "light";
    document.documentElement.setAttribute("data-theme", savedTheme);
})();
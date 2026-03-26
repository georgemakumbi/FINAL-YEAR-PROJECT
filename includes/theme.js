(() => {
    const storageKey = "siteTheme";

    const prefersDark =
        window.matchMedia &&
        window.matchMedia("(prefers-color-scheme: dark)").matches;
    const saved = localStorage.getItem(storageKey);
    const isDark = saved ? saved === "dark" : prefersDark;

    document.body.classList.toggle("dark", isDark);

    let toggleWrap = document.querySelector("[data-theme-toggle]");
    if (!toggleWrap) {
        toggleWrap = document.createElement("div");
        toggleWrap.className = "theme-toggle";
        toggleWrap.setAttribute("data-theme-toggle", "");
        toggleWrap.innerHTML = `
            <span class="theme-label">Theme</span>
            <label class="theme-switch">
                <input type="checkbox" id="themeToggle" aria-label="Toggle dark mode">
                <span class="theme-slider"></span>
            </label>
            <span class="theme-mode" aria-live="polite"></span>
        `;
        document.body.appendChild(toggleWrap);
    }

    const input = toggleWrap.querySelector("#themeToggle");
    const modeLabel = toggleWrap.querySelector(".theme-mode");

    if (!input || !modeLabel) {
        return;
    }

    const applyLabel = () => {
        const dark = document.body.classList.contains("dark");
        modeLabel.textContent = dark ? "Dark" : "Light";
        input.checked = dark;
    };

    input.addEventListener("change", () => {
        const dark = input.checked;
        document.body.classList.toggle("dark", dark);
        localStorage.setItem(storageKey, dark ? "dark" : "light");
        applyLabel();
    });

    applyLabel();
})();

(() => {
    const storageKey = "siteTheme";

    const prefersDark =
        window.matchMedia &&
        window.matchMedia("(prefers-color-scheme: dark)").matches;
    const saved = localStorage.getItem(storageKey);
    const isDark = saved ? saved === "dark" : prefersDark;

    document.body.classList.toggle("dark", isDark);

    const setTheme = (dark) => {
        document.body.classList.toggle("dark", dark);
        localStorage.setItem(storageKey, dark ? "dark" : "light");
        updateButtons(dark);
    };

    const updateButtons = (dark) => {
        document.querySelectorAll("[data-theme-toggle-btn]").forEach((btn) => {
            const sun = btn.querySelector(".theme-icon-sun");
            const moon = btn.querySelector(".theme-icon-moon");
            if (sun && moon) {
                sun.style.display = dark ? "none" : "block";
                moon.style.display = dark ? "block" : "none";
            }
        });
    };

    // Wire up top-bar theme toggle buttons
    document.querySelectorAll("[data-theme-toggle-btn]").forEach((btn) => {
        btn.addEventListener("click", (e) => {
            e.preventDefault();
            const dark = !document.body.classList.contains("dark");
            setTheme(dark);
        });
    });

    // Legacy: bottom-right toggle (if present on non-admin pages)
    let toggleWrap = document.querySelector("[data-theme-toggle]");
    if (!toggleWrap && !document.querySelector("[data-theme-toggle-btn]")) {
        toggleWrap = document.createElement("div");
        toggleWrap.className = "theme-toggle";
        toggleWrap.setAttribute("data-theme-toggle", "");
        toggleWrap.innerHTML = [
            '<span class="theme-label">Theme</span>',
            '<label class="theme-switch">',
            '<input type="checkbox" id="themeToggle" aria-label="Toggle dark mode">',
            '<span class="theme-slider"></span>',
            '</label>',
            '<span class="theme-mode" aria-live="polite"></span>',
        ].join("");
        document.body.appendChild(toggleWrap);
    }

    if (toggleWrap) {
        const input = toggleWrap.querySelector("#themeToggle");
        const modeLabel = toggleWrap.querySelector(".theme-mode");

        if (input && modeLabel) {
            const applyLabel = () => {
                const dark = document.body.classList.contains("dark");
                modeLabel.textContent = dark ? "Dark" : "Light";
                input.checked = dark;
            };

            input.addEventListener("change", () => {
                const dark = input.checked;
                setTheme(dark);
            });

            applyLabel();
        }
    }

    updateButtons(isDark);
})();

(function () {
  "use strict";

  let autoRefreshEnabled = false;
  let autoRefreshTimer = null;
  const autoRefreshInterval = 30000;

  document.addEventListener("DOMContentLoaded", function () {
    bindWidgetRefresh();
    bindWidgetCollapse();
    bindFilterMode();
    bindRefreshAll();
    bindAutoRefreshToggle();
    restoreCollapsedWidgets();
    animateCounters();
  });

  function bindWidgetRefresh() {
    document.querySelectorAll("[data-widget-refresh]").forEach(function (button) {
      button.addEventListener("click", function () {
        const widgetName = button.getAttribute("data-widget-refresh");
        refreshWidget(widgetName);
      });
    });
  }

  function bindWidgetCollapse() {
    document.querySelectorAll("[data-widget-collapse]").forEach(function (button) {
      button.addEventListener("click", function () {
        const container = button.closest("[data-widget-container]");
        if (!container) return;

        const body = container.querySelector(".widget-body");
        if (!body) return;

        container.classList.toggle("collapsed");

        const collapsed = container.classList.contains("collapsed");
        body.style.display = collapsed ? "none" : "";
        localStorage.setItem("dashboard-widget-" + getWidgetName(container), collapsed ? "1" : "0");
      });
    });
  }

  function bindFilterMode() {
    const select = document.getElementById("dashboard-view-mode");
    if (!select) return;

    select.addEventListener("change", function () {
      const value = select.value;
      document.querySelectorAll("[data-widget-container]").forEach(function (container) {
        const group = container.getAttribute("data-widget-group");
        container.style.display = value === "all" || value === group ? "" : "none";
      });
    });
  }

  function bindRefreshAll() {
    const button = document.querySelector("[data-dashboard-refresh-all]");
    if (!button) return;

    button.addEventListener("click", function () {
      document.querySelectorAll("[data-widget-container]").forEach(function (container) {
        const widgetName = getWidgetName(container);
        refreshWidget(widgetName);
      });
    });
  }

  function bindAutoRefreshToggle() {
    const button = document.querySelector("[data-dashboard-autorefresh-toggle]");
    if (!button) return;

    button.addEventListener("click", function () {
      autoRefreshEnabled = !autoRefreshEnabled;

      if (autoRefreshEnabled) {
        button.textContent = "Auto Refresh: On";
        autoRefreshTimer = setInterval(function () {
          document.querySelectorAll("[data-widget-container]").forEach(function (container) {
            refreshWidget(getWidgetName(container), true);
          });
        }, autoRefreshInterval);
      } else {
        button.textContent = "Auto Refresh: Off";
        clearInterval(autoRefreshTimer);
      }
    });
  }

  function refreshWidget(widgetName, silent = false) {
    const target = document.getElementById("widget-" + widgetName);
    if (!target) return;

    if (!silent) {
      target.classList.add("widget-loading");
    }

    fetch("/dashboard/widget?widget=" + encodeURIComponent(widgetName), {
      method: "GET",
      headers: {
        "X-Requested-With": "XMLHttpRequest",
      },
    })
      .then(function (response) {
        if (!response.ok) {
          throw new Error("Failed to refresh widget: " + widgetName);
        }
        return response.text();
      })
      .then(function (html) {
        target.innerHTML = html;
        target.classList.remove("widget-loading");
        animateCounters();
      })
      .catch(function (error) {
        target.classList.remove("widget-loading");
        console.error(error);
      });
  }

  function restoreCollapsedWidgets() {
    document.querySelectorAll("[data-widget-container]").forEach(function (container) {
      const widgetName = getWidgetName(container);
      const isCollapsed = localStorage.getItem("dashboard-widget-" + widgetName) === "1";
      const body = container.querySelector(".widget-body");

      if (isCollapsed && body) {
        container.classList.add("collapsed");
        body.style.display = "none";
      }
    });
  }

  function animateCounters() {
    document.querySelectorAll("[data-counter]").forEach(function (element) {
      const finalValue = parseInt(element.textContent, 10) || 0;
      let current = 0;
      const step = Math.max(1, Math.ceil(finalValue / 20));

      element.textContent = "0";

      const timer = setInterval(function () {
        current += step;
        if (current >= finalValue) {
          element.textContent = String(finalValue);
          clearInterval(timer);
          return;
        }

        element.textContent = String(current);
      }, 20);
    });
  }

  function getWidgetName(container) {
    return container.getAttribute("data-widget-name");
  }
})();

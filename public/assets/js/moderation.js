(function () {
  "use strict";

  let autoRefreshEnabled = false;
  let autoRefreshTimer = null;
  const autoRefreshInterval = 30000;

  document.addEventListener("DOMContentLoaded", function () {
    bindWidgetRefresh();
    bindWidgetCollapse();
    bindRefreshAll();
    bindAutoRefreshToggle();
    restoreCollapsedWidgets();
    animateCounters();
  });

  function bindWidgetRefresh() {
    document.querySelectorAll("[data-moderation-widget-refresh]").forEach(function (button) {
      button.addEventListener("click", function () {
        const widgetName = button.getAttribute("data-moderation-widget-refresh");
        refreshWidget(widgetName);
      });
    });
  }

  function bindWidgetCollapse() {
    document.querySelectorAll("[data-moderation-widget-collapse]").forEach(function (button) {
      button.addEventListener("click", function () {
        const container = button.closest("[data-moderation-widget-container]");
        if (!container) return;

        const body = container.querySelector(".widget-body");
        if (!body) return;

        container.classList.toggle("collapsed");

        const collapsed = container.classList.contains("collapsed");
        body.style.display = collapsed ? "none" : "";
        localStorage.setItem(
          "moderation-widget-" + getWidgetName(container),
          collapsed ? "1" : "0"
        );
      });
    });
  }

  function bindRefreshAll() {
    const button = document.querySelector("[data-moderation-refresh-all]");
    if (!button) return;

    button.addEventListener("click", function () {
      document.querySelectorAll("[data-moderation-widget-container]").forEach(function (container) {
        refreshWidget(getWidgetName(container));
      });
    });
  }

  function bindAutoRefreshToggle() {
    const button = document.querySelector("[data-moderation-autorefresh-toggle]");
    if (!button) return;

    button.addEventListener("click", function () {
      autoRefreshEnabled = !autoRefreshEnabled;

      if (autoRefreshEnabled) {
        button.textContent = "Auto Refresh: On";
        autoRefreshTimer = setInterval(function () {
          document
            .querySelectorAll("[data-moderation-widget-container]")
            .forEach(function (container) {
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
    const target = document.getElementById("moderation-widget-" + widgetName);
    if (!target) return;

    if (!silent) {
      target.classList.add("widget-loading");
    }

    fetch("/moderation/widget?widget=" + encodeURIComponent(widgetName), {
      method: "GET",
      headers: {
        "X-Requested-With": "XMLHttpRequest",
      },
    })
      .then(function (response) {
        if (!response.ok) {
          throw new Error("Failed to refresh moderation widget: " + widgetName);
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
    document.querySelectorAll("[data-moderation-widget-container]").forEach(function (container) {
      const widgetName = getWidgetName(container);
      const isCollapsed = localStorage.getItem("moderation-widget-" + widgetName) === "1";
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
    return container.getAttribute("data-moderation-widget-name");
  }
})();

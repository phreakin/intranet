(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", function () {
    const shell = document.querySelector(".intel-shell");

    initSidebar(shell);
    initDashboardWidgets();
    initCopyShareButtons();
    initTooltips();
    initAutoDismissAlerts();
    initConfirmActions();
    initDynamicChips();
    initSearchShortcut();
  });

  function initSidebar(shell) {
    if (!shell) {
      return;
    }

    const desktopBreakpoint = 992;
    const storageKey = "intranet.sidebar.collapsed";
    const desktopToggle = document.querySelector("[data-sidebar-toggle]");
    const mobileToggle = document.querySelector("[data-sidebar-mobile-toggle]");
    const sidebar = shell.querySelector(".intel-sidebar");
    const topbar = shell.querySelector(".intel-topbar");
    const content = shell.querySelector(".intel-content");

    function isDesktop() {
      return window.innerWidth >= desktopBreakpoint;
    }

    function setDesktopToggleState(expanded) {
      if (!desktopToggle) {
        return;
      }

      desktopToggle.setAttribute("aria-expanded", String(expanded));
      desktopToggle.setAttribute("title", expanded ? "Collapse sidebar" : "Expand sidebar");
    }

    function setMobileToggleState(open) {
      if (!mobileToggle) {
        return;
      }

      mobileToggle.setAttribute("aria-expanded", String(open));
      mobileToggle.setAttribute("title", open ? "Close navigation" : "Open navigation");
    }

    function syncShellState() {
      const collapsed = window.localStorage.getItem(storageKey) === "1";

      if (isDesktop()) {
        shell.setAttribute("data-sidebar-state", collapsed ? "collapsed" : "expanded");
        shell.setAttribute("data-sidebar-open", "false");
        setDesktopToggleState(!collapsed);
        setMobileToggleState(false);
        return;
      }

      shell.setAttribute("data-sidebar-state", "expanded");
      setDesktopToggleState(true);
    }

    function closeMobileSidebar() {
      if (!isDesktop()) {
        shell.setAttribute("data-sidebar-open", "false");
        setMobileToggleState(false);
      }
    }

    if (desktopToggle) {
      desktopToggle.addEventListener("click", function () {
        if (!isDesktop()) {
          return;
        }

        const collapsed = shell.getAttribute("data-sidebar-state") === "collapsed";
        const nextState = collapsed ? "expanded" : "collapsed";

        shell.setAttribute("data-sidebar-state", nextState);
        window.localStorage.setItem(storageKey, nextState === "collapsed" ? "1" : "0");
        setDesktopToggleState(nextState !== "collapsed");
      });
    }

    if (mobileToggle) {
      mobileToggle.addEventListener("click", function (event) {
        event.stopPropagation();
        const open = shell.getAttribute("data-sidebar-open") === "true";
        shell.setAttribute("data-sidebar-open", open ? "false" : "true");
        setMobileToggleState(!open);
      });
    }

    [topbar, content].forEach(function (region) {
      if (!region) {
        return;
      }

      region.addEventListener("click", function () {
        closeMobileSidebar();
      });
    });

    if (sidebar) {
      sidebar.addEventListener("click", function (event) {
        event.stopPropagation();
      });
    }

    document.addEventListener("keydown", function (event) {
      if (event.key === "Escape") {
        closeMobileSidebar();
      }
    });

    window.addEventListener("resize", function () {
      syncShellState();

      if (isDesktop()) {
        setMobileToggleState(false);
      }
    });

    syncShellState();
  }

  function initCopyShareButtons() {
    document.querySelectorAll(".copy-share, [data-copy]").forEach(function (button) {
      button.addEventListener("click", function () {
        const value = button.getAttribute("data-copy") || button.dataset.url;

        if (!value || !navigator.clipboard) {
          return;
        }

        const originalLabel = button.dataset.originalLabel || button.textContent.trim();
        button.dataset.originalLabel = originalLabel;

        navigator.clipboard.writeText(value).then(function () {
          button.textContent = "Copied";
          button.setAttribute("data-copied", "true");

          window.setTimeout(function () {
            button.textContent = originalLabel;
            button.removeAttribute("data-copied");
          }, 1200);
        });
      });
    });
  }

  function initTooltips() {
    if (typeof bootstrap === "undefined") {
      return;
    }

    document.querySelectorAll("[data-bs-toggle='tooltip']").forEach(function (element) {
      new bootstrap.Tooltip(element);
    });
  }

  function initAutoDismissAlerts() {
    document.querySelectorAll(".alert[data-auto-dismiss]").forEach(function (alert) {
      const timeout = Number.parseInt(alert.getAttribute("data-auto-dismiss"), 10) || 4000;

      window.setTimeout(function () {
        alert.classList.add("is-dismissing");
        window.setTimeout(function () {
          alert.remove();
        }, 220);
      }, timeout);
    });
  }

  function initConfirmActions() {
    document.querySelectorAll("[data-confirm]").forEach(function (element) {
      const eventName = element.tagName === "FORM" ? "submit" : "click";

      element.addEventListener(eventName, function (event) {
        const message = element.getAttribute("data-confirm") || "Are you sure?";

        if (!window.confirm(message)) {
          event.preventDefault();
        }
      });
    });
  }

  function initDynamicChips() {
    const statusClassMap = {
      new: "chip-new",
      hot: "chip-hot",
      trending: "chip-trending",
      popular: "chip-popular",
      reported: "chip-reported",
    };

    document.querySelectorAll("[data-status]").forEach(function (element) {
      const status = (element.getAttribute("data-status") || "").toLowerCase().trim();
      const cssClass = statusClassMap[status];

      if (cssClass) {
        element.classList.add(cssClass);
      }
    });
  }

  function initSearchShortcut() {
    const search = document.querySelector(".intel-search input[type='search'], [data-search]");

    if (!search) {
      return;
    }

    document.addEventListener("keydown", function (event) {
      const activeElement = document.activeElement;
      const activeTag = activeElement ? activeElement.tagName : "";
      const isTypingContext =
        activeElement &&
        (activeElement.isContentEditable ||
          activeTag === "INPUT" ||
          activeTag === "TEXTAREA" ||
          activeTag === "SELECT");

      if (
        event.key === "/" &&
        !event.metaKey &&
        !event.ctrlKey &&
        !event.altKey &&
        !isTypingContext
      ) {
        event.preventDefault();
        search.focus();
        search.select();
      }
    });
  }

  function initDashboardWidgets() {
    const dashboard = document.querySelector("[data-dashboard-shell]");

    if (!dashboard || !window.fetch) {
      return;
    }

    const filter = dashboard.querySelector("[data-dashboard-filter]");
    const refreshAll = dashboard.querySelector("[data-dashboard-refresh-all]");
    const autoRefreshToggle = dashboard.querySelector("[data-dashboard-autorefresh-toggle]");
    const intervalMs =
      Number.parseInt(dashboard.getAttribute("data-dashboard-autorefresh-interval"), 10) || 60000;
    const collapsedKey = "intranet.dashboard.collapsed";
    const autoRefreshKey = "intranet.dashboard.autorefresh";
    let autoRefreshTimer = null;

    function readCollapsedState() {
      try {
        return JSON.parse(window.localStorage.getItem(collapsedKey) || "{}");
      } catch (error) {
        return {};
      }
    }

    function writeCollapsedState(state) {
      window.localStorage.setItem(collapsedKey, JSON.stringify(state));
    }

    function applyFilter(value) {
      dashboard.querySelectorAll("[data-widget-container]").forEach(function (widget) {
        const group = widget.getAttribute("data-widget-group");
        const isVisible = value === "all" || group === value;

        widget.hidden = !isVisible;
      });
    }

    function applyCollapsedState() {
      const state = readCollapsedState();

      dashboard.querySelectorAll("[data-widget-container]").forEach(function (widget) {
        const name = widget.getAttribute("data-widget-name");
        const body = widget.querySelector("[data-widget-body]");
        const collapsed = Boolean(state[name]);

        widget.classList.toggle("is-collapsed", collapsed);
        if (body) {
          body.hidden = collapsed;
        }
      });
    }

    function setLoading(widget, loading) {
      widget.classList.toggle("is-loading", loading);
    }

    function refreshWidget(widgetName) {
      const widget = dashboard.querySelector('[data-widget-name="' + widgetName + '"]');
      const body = widget ? widget.querySelector("[data-widget-body]") : null;

      if (!widget || !body) {
        return Promise.resolve();
      }

      setLoading(widget, true);

      return window
        .fetch("/dashboard/widgets/" + encodeURIComponent(widgetName), {
          headers: {
            "X-Requested-With": "XMLHttpRequest",
          },
        })
        .then(function (response) {
          if (!response.ok) {
            throw new Error("Widget refresh failed");
          }

          return response.text();
        })
        .then(function (html) {
          body.innerHTML = html;
        })
        .catch(function () {
          body.innerHTML =
            '<div class="empty-state">Widget refresh failed. Try again in a moment.</div>';
        })
        .finally(function () {
          setLoading(widget, false);
        });
    }

    function refreshVisibleWidgets() {
      const widgets = Array.from(dashboard.querySelectorAll("[data-widget-container]")).filter(function (
        widget
      ) {
        return !widget.hidden;
      });

      return Promise.all(
        widgets.map(function (widget) {
          return refreshWidget(widget.getAttribute("data-widget-name") || "");
        })
      );
    }

    function syncAutoRefreshButton(enabled) {
      if (!autoRefreshToggle) {
        return;
      }

      autoRefreshToggle.textContent = enabled ? "Auto Refresh: On" : "Auto Refresh: Off";
      autoRefreshToggle.setAttribute("aria-pressed", String(enabled));
    }

    function enableAutoRefresh() {
      window.localStorage.setItem(autoRefreshKey, "1");
      syncAutoRefreshButton(true);

      if (autoRefreshTimer) {
        window.clearInterval(autoRefreshTimer);
      }

      autoRefreshTimer = window.setInterval(function () {
        refreshVisibleWidgets();
      }, intervalMs);
    }

    function disableAutoRefresh() {
      window.localStorage.setItem(autoRefreshKey, "0");
      syncAutoRefreshButton(false);

      if (autoRefreshTimer) {
        window.clearInterval(autoRefreshTimer);
        autoRefreshTimer = null;
      }
    }

    if (filter) {
      filter.addEventListener("change", function () {
        applyFilter(filter.value);
      });
      applyFilter(filter.value);
    }

    dashboard.querySelectorAll("[data-widget-refresh]").forEach(function (button) {
      button.addEventListener("click", function () {
        const widgetName = button.getAttribute("data-widget-refresh");

        if (widgetName) {
          refreshWidget(widgetName);
        }
      });
    });

    dashboard.querySelectorAll("[data-widget-collapse]").forEach(function (button) {
      button.addEventListener("click", function () {
        const widget = button.closest("[data-widget-container]");
        const body = widget ? widget.querySelector("[data-widget-body]") : null;

        if (!widget || !body) {
          return;
        }

        const widgetName = widget.getAttribute("data-widget-name") || "";
        const state = readCollapsedState();
        const nextCollapsed = !widget.classList.contains("is-collapsed");

        widget.classList.toggle("is-collapsed", nextCollapsed);
        body.hidden = nextCollapsed;
        state[widgetName] = nextCollapsed;
        writeCollapsedState(state);
      });
    });

    if (refreshAll) {
      refreshAll.addEventListener("click", function () {
        refreshVisibleWidgets();
      });
    }

    if (autoRefreshToggle) {
      autoRefreshToggle.addEventListener("click", function () {
        const enabled = window.localStorage.getItem(autoRefreshKey) === "1";

        if (enabled) {
          disableAutoRefresh();
        } else {
          enableAutoRefresh();
        }
      });
    }

    applyCollapsedState();

    if (window.localStorage.getItem(autoRefreshKey) === "1") {
      enableAutoRefresh();
    } else {
      syncAutoRefreshButton(false);
    }
  }
})();

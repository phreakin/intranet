if (window.jQuery) {
  $(function () {
    const $shell = $('.intel-shell');
    const desktopBreakpoint = 992;

    function applySidebarState() {
      const collapsed = window.localStorage.getItem('intel.sidebar.collapsed') === '1';
      if (window.innerWidth >= desktopBreakpoint) {
        $shell.attr('data-sidebar-state', collapsed ? 'collapsed' : 'expanded');
        $shell.removeAttr('data-sidebar-open');
      } else {
        $shell.attr('data-sidebar-state', 'expanded');
      }
    }

    applySidebarState();

    $('[data-sidebar-toggle]').on('click', function () {
      const collapsed = $shell.attr('data-sidebar-state') === 'collapsed';
      const next = collapsed ? 'expanded' : 'collapsed';
      $shell.attr('data-sidebar-state', next);
      window.localStorage.setItem('intel.sidebar.collapsed', next === 'collapsed' ? '1' : '0');
    });

    $('[data-sidebar-mobile-toggle]').on('click', function () {
      const open = $shell.attr('data-sidebar-open') === 'true';
      $shell.attr('data-sidebar-open', open ? 'false' : 'true');
    });

    $('.intel-content, .intel-topbar').on('click', function () {
      if (window.innerWidth < desktopBreakpoint) {
        $shell.attr('data-sidebar-open', 'false');
      }
    });

    $(window).on('resize', applySidebarState);

    $('.copy-share').on('click', function () {
      const $button = $(this);
      const url = $button.data('url');
      const original = $button.data('original-label') || $button.text();
      $button.data('original-label', original);

      if (navigator.clipboard && url) {
        navigator.clipboard.writeText(url).then(function () {
          $button.text('Copied');
          window.setTimeout(function () {
            $button.text(original);
          }, 1200);
        });
      }
    });
  });
}

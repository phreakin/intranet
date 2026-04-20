if (window.jQuery) {
  $(function () {
    $('.copy-share').on('click', function () {
      const url = $(this).data('url');
      navigator.clipboard.writeText(url);
      $(this).text('Copied');
    });
  });
}

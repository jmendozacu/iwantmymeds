$j(document).ready(function() {
    form = $j('#prescription-form');
    checkbox = $j('.prescription-radiobox');
    $j('input.prescription-radiobox').on('change', function() {
      $j('input.prescription-radiobox').not(this).prop('checked', false);
  });
});

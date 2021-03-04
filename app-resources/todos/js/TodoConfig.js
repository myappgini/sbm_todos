// * open detail modal windows
$j('body').on('click', '.config-todos', function () {
    [$this, $li, data] = this_obj(this);
    ajax_todo(data).done(function (res) {
      const $modal = $j('#modal-todo');
      $modal.length > 0 && $modal.remove();
      $j('body').append(res);
      $j('#modal-todo').modal('show');
      resizeModal($j('#modal-todo'));
    })
  });

  
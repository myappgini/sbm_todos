const ajax_todo = function (data) {
  return $j.ajax({
    method: "post",
    url: "hooks/todos/functions_ajax.php",
    dataType: "html",
    data
  });
}

const input_edit = function (text) {
  return $j('<input />').prop({
    'type': 'text',
    'value': text.trim(), //set text box value from div current text
    'class': 'input-edit-task',
    'data-cmd': 'edit-task',
  });
}

const this_obj = function (obj) {
  const $this = $j(obj);
  const $li = $this.closest('.task-content');
  const cmd = $this.data('cmd');
  const ix = $li.data('ix')
  return [$this, $li, {
    cmd,
    ix
  }];
}

$j(function () {
  ajax_todo({
    cmd: 'option-todo'
  }).done(function (res) {
    $j('nav .navbar-collapse').append(res);
    get_values();
  });
});

// * Open tasks functions, show deletes & remove all deleted tasks
$j('body').on('click', '.todo-dropdown-content, .view-trash, .back-todos', function () {
  [$this, $li, data] = this_obj(this);
  $j('div.todos-content').html('Loading Content...');
  ajax_todo(data).done(function (res) {
    $j('div.todos-content').html(res);
    $j('.todo-list').sortable({
      placeholder: 'sort-highlight',
      handle: '.handle',
      forcePlaceholderSize: true,
      zIndex: 999999,
    });
    moment_date('.due-tag');
    get_values();
    $j('.text.task-text').each(function(){
      $j(this).html(urlify($j(this).text()));
    })
  })
  $li.hasClass('open') ? true : $li.toggleClass('open');
})

$j("body").on("sortstop", ".todo-list", function (event, ui) {
  let sort_array = [];
  $j(this).children('li').each(function(){
    sort_array.push($j(this).data('ix'));
  })
  data.sort_array= sort_array;
  data.cmd="sort-list";
  ajax_todo(data).done(function (res){
    console.log(res);
  })
});

$j('body').on('click', '.close-remove', function () {
  $j('.dropdown.todo-dropdown').removeClass('open');
});

// * Done to-do
$j('body').on('click', '.todo-task-check', function () {
  [$this, $li, data] = this_obj(this);
  data.complete = $this.is(":checked") ? true : false;
  ajax_todo(data).done(function (res) {
    console.log(res);
    data.complete ? $li.addClass('done') : $li.removeClass('done');
    get_values();
    refreshBar(data.ix);
  })
});

// * Add to-do
$j('body').on('click', '.add-todo-task', function () {
  [$this, $li, data] = this_obj(this);
  data.task = $j('.task-to-add').val()
  if (data.task ==="") return;
  ajax_todo(data).done(function (res) {
    $j('.todo-list').append(res);
    get_values()
    $j(".form-control.task-to-add").select();
  })
});

// * Delete/Remove/recover to-do
$j('body').on('click', '.todo-task-delete, .todo-task-recover', function () {
  [$this, $li, data] = this_obj(this);
  ajax_todo(data).done(res => {
    console.log(res);
    get_values()
  })
  $li.remove();
});

// * Edit to-do
$j('body').on('click focusout', '.task-text, .input-edit-task', function () {
  [$this, $li, data] = this_obj(this);
  let tb = $li.find('input.input-edit-task');
  if (data.cmd === undefined) return
  if (tb.length) {
    text = urlify(tb.val());//verificar si hay url
    $this.html(text); //remove text box & put its current value as text to the div
    data.newtext = tb.val(),
      ajax_todo(data).done(function (res) {
        console.log(res)
      });
  } else {
    tb = input_edit($this.text()); //construct text box
    $this.empty().append(tb); //add new text box
    tb.focus(); //put text box on focus
  }
});

//* click on link
$j('body').on('click','.auto-link', function (e) {
  e.stopPropagation();
})

$j(document).keyup(function (e) {
  if ($j(".input-edit-task").is(":focus") && (e.keyCode == 13)) {
    $j('.input-edit-task').trigger('focusout');
  }
  if ($j(".form-control.task-to-add").is(":focus") && (e.keyCode == 13)) {
    $j('button.add-todo-task').trigger('click');
  }
});

// * open detail modal windows
$j('body').on('click', '.todo-task-detail', function () {
  [$this, $li, data] = this_obj(this);
  ajax_todo(data).done(function (res) {
    const $modal = $j('#modal-todo');
    $modal.length > 0 && $modal.remove();
    $j('body').append(res);
    $j('#modal-todo').modal('show');
    resizeModal($j('#modal-todo'));
    users_list();
    $j('#due-task').addClass('always_shown').parents('.input-group').datetimepicker({
      toolbarPlacement: 'top',
      sideBySide: true,
      showClear: true,
      showTodayButton: true,
      showClose: true,
      icons: {
        close: 'glyphicon glyphicon-ok'
      },
      format: AppGini.datetimeFormat('dt'),
      locale: 'en'
    });
    $j('.text.task-text, .message-timeline').each(function(){
      $j(this).html(urlify($j(this).text()));
    })
  })
});

// * send task to user
$j('body').on('click', '.send-taks-user', function () {
  [$this, $li, data] = this_obj(this);
  const preserve = $li.find('input.preserve-task');
  data.preserve = preserve.is(":checked") ? true : false;
  data.user = $j('#todo-list-users').val();
  ajax_todo(data).done(function (res) {
    console.log(res);
    get_values();
    if (!data.preserve) {
      $j(`ul.todo-list li[data-ix='${data.ix}']`).remove();
      const $modal = $j('#modal-todo');
      $modal.length > 0 && $modal.modal('hide');
    }
  })
});

//* set due
$j('body').on('click', '.set-due', function () {
  [$this, $li, data] = this_obj(this);
  const val = $li.find('input#due-task');
  data.due = val.val();
  ajax_todo(data).done(function (res) {
    console.log(res);
    get_values();
  })
});

//* set progress
$j('body').on('click', '.set-progress', function () {
  [$this, $li, data] = this_obj(this);
  const val = $li.find('input#progress-task').val();
  data.progress = val;
  ajax_todo(data).done(function (res) {
    console.log(res);
    $j('.progress-bar.task-bar').css('width', `${val}%`).attr('aria-valuenow', val);
    refreshBar(data.ix);
    get_values();
  })
});

function refreshBar(ix=false){
  if (!ix) return;
  data.ix= ix;
  data.cmd='get-progress';
  ajax_todo(data).done(function(res){
    val = parseInt(res) || 0;
    task_li = $j('.todo-list').find(`[data-ix='${ix}']`);
    task_bar= task_li.find('.progress');
    if (val >= 100){
      task_bar.css('opacity','.2')
    }else{
      task_bar.css('opacity','1')
    }
    task_bar= task_li.find('.progress-bar');
    task_bar.css('width', `${val}%`).attr('aria-valuenow', val);
  })
}

function get_values() {
  ajax_todo({
    cmd: 'get-values'
  }).done(function (res) {
    let data = JSON.parse(res);
    console.log('update values');
    const val = ((data.progress)/data.listed*100) || 0;
    $j('.label-tasks').text(`${val.toFixed(1)}%`);
    $j('.label-trash').text(`${data.deleted}`);
    $j('.progress-bar.todos-bar').css('width', `${val.toFixed(2)}%`).attr('aria-valuenow', val);
  })
}

function users_list() {
  $j('#users-list').select2({
    width: '100%',
    formatNoMatches: function (term) {
      return 'No matches found!';
    },
    minimumResultsForSearch: 5,
    loadMorePadding: 200,
    escapeMarkup: function (m) {
      return m;
    },
    ajax: {
      url: 'hooks/todos/getUsers.php',
      dataType: 'json',
      cache: true,
      data: function (term, page) {
        return {
          s: term,
          p: page
        };
      },
      results: function (resp, page) {
        console.log(resp);
        return resp;
      }
    }
  }).on('change', function (e) {
    $j('#todo-list-users').val(e.added.id);
  });
}

function moment_date(selector) {
  console.log("humanizing")
  $j(selector).each(function () {
    const val = $j(this).text();
    const mom = moment(val).fromNow();
    $j(this).text(mom);
  });
};

// * resize height modals windows
function resizeModal(mod) {
  mod.on('shown.bs.modal', function () {
    var wh = $j(window).height(),
      mb = mod.find('.modal-body').outerHeight(),
      mhfoh = mod.find('.modal-header').outerHeight() + mod.find('.modal-footer').outerHeight(),
      val = wh - mhfoh - 80;
    mod.find('.modal-body').css({
      height: val
    });
  })
}

//* check is exist an url in text and convert it in a llink
function urlify(text) {
  var urlRegex = /(https?:\/\/[^\s]+)/g;
  return text.replace(urlRegex, function(url) {
    return '<a class="auto-link"  href="' + url + '">' + url + '</a>';
  })
}

//122---115---133--145---150---151---149---144---138---141---144---132---after add detail function 182---225 end?---240---280
const tasks = { //example
  "tasks": {
    "602705f9a348a": {
      "task": "task to complete",
      "complete": true,
      "added": "12.02.21 05:02:29",
      "due": false,
      "details": [{
        "new_task": "task to complete",
        "added_date": "12.02.21 05:02:29"
      }, {
        "complete": "marked as completed",
        "date": "12.02.21 05:02:40"
      }],
      "deleted": false,
      "date_deleted": false,
      "uid": "602705f9a348a"
    },
    "602706032792f": {
      "task": "task to do!",
      "complete": false,
      "added": "12.02.21 05:02:39",
      "due": false,
      "details": [{
        "new_task": "task to do",
        "added_date": "12.02.21 05:02:39"
      }, {
        "tile": "task to do!",
        "date": "12.02.21 06:02:56"
      }],
      "deleted": false,
      "date_deleted": false,
      "uid": "602706032792f"
    },
    "60270620e6e7f": {
      "task": "task to send and preserve",
      "complete": true,
      "added": "12.02.21 05:02:08",
      "due": false,
      "details": [{
        "new_task": "task to send and preserve",
        "added_date": "12.02.21 05:02:08"
      }, {
        "send_to": "alejandro",
        "date": "12.02.21 06:02:40"
      }, {
        "send_to": "alejandro",
        "date": "12.02.21 06:02:48"
      }, {
        "complete": "marked as completed",
        "date": "12.02.21 06:02:32"
      }],
      "deleted": false,
      "date_deleted": false,
      "uid": "60270620e6e7f"
    },
    "6027062b4653d": {
      "task": "change this title--to new title?",
      "complete": false,
      "added": "12.02.21 05:02:19",
      "due": false,
      "details": [{
        "new_task": "change this title",
        "added_date": "12.02.21 05:02:19"
      }, {
        "tile": "change this title--to new title?",
        "date": "12.02.21 05:02:18"
      }],
      "deleted": false,
      "date_deleted": false,
      "uid": "6027062b4653d"
    },
    "602713a329304": {
      "task": "tarea from alejandro",
      "complete": false,
      "added": "12.02.21 06:02:38",
      "due": false,
      "details": [{
        "new_task": "tarea from alejandro",
        "added_date": "12.02.21 06:02:38"
      }, {
        "task_from": "admin",
        "date": "12.02.21 06:02:47"
      }],
      "deleted": false,
      "date_deleted": false,
      "uid": "602713a329304",
      "from": "alejandro",
      "from_date": "12.02.21 06:02:47"
    }
  },
  "length": 8,
  "deleted": 3,
  "listed": 5,
  "completed": 2,
  "deleted_tasks": {
    "6027064303cf1": {
      "task": "task to delete",
      "complete": false,
      "added": "12.02.21 05:02:49",
      "due": false,
      "details": [{
        "new_task": "task to delete",
        "added_date": "12.02.21 05:02:49"
      }, {
        "deleted_date": "12.02.21 05:02:43"
      }],
      "deleted": true,
      "date_deleted": "12.02.21 05:02:43",
      "uid": "6027064303cf1"
    },
    "60270648a5086": {
      "task": "task to send",
      "complete": false,
      "added": "12.02.21 05:02:03",
      "due": false,
      "details": [{
        "new_task": "task to send",
        "added_date": "12.02.21 05:02:03"
      }, {
        "deleted_date": "12.02.21 05:02:48"
      }],
      "deleted": true,
      "date_deleted": "12.02.21 05:02:48",
      "uid": "60270648a5086",
      "send_to": "alejandro",
      "send_to_date": "12.02.21 05:02:48"
    },
    "60270a1d359a5": {
      "task": "other task to send",
      "complete": false,
      "added": "12.02.21 06:02:03",
      "due": false,
      "details": [{
        "new_task": "other task to send",
        "added_date": "12.02.21 06:02:03"
      }, {
        "send_to": "alejandro",
        "date": "12.02.21 06:02:09"
      }, {
        "deleted_date": "12.02.21 06:02:09"
      }],
      "deleted": true,
      "date_deleted": "12.02.21 06:02:09",
      "uid": "60270a1d359a5"
    }
  }
}
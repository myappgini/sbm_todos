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
  });
}

const this_obj = function (obj) {
  const $this = $j(obj);
  const $li = $this.closest('li');
  const cmd = $this.data('cmd');
  const ix = $li.data('ix')
  return [$this, $li, cmd, ix];
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
  [$this, $li, cmd, ix] = this_obj(this);
  console.log(cmd)
  $j('div.todos-content').html('Loading Content...');
  ajax_todo({
    cmd
  }).done(function (res) {
    $j('div.todos-content').html(res);
    $j('.todo-list').sortable({
      placeholder: 'sort-highlight',
      handle: '.handle',
      forcePlaceholderSize: true,
      zIndex: 999999
    });
    get_values();
  })
  $li.hasClass('open') ? true : $li.toggleClass('open');
})

$j('body').on('click', '.close-remove', function () {
  $j('.dropdown.todo-dropdown').removeClass('open');
});

// * Done to-do
$j('body').on('click', '.todo-task-check', function () {
  [$this, $li, cmd, ix] = this_obj(this);
  const data = {
    cmd,
    ix,
    complete: $this.is(":checked") ? true : false,
  }
  ajax_todo(data).done(function (res) {
    console.log(res);
    data.complete ? $li.addClass('done') : $li.removeClass('done');
    get_values();
  })
});

// * Add to-do
$j('body').on('click', '.add-todo-task', function () {
  [$this, $li, cmd, ix] = this_obj(this);
  const data = {
    cmd,
    task: $j('.task-to-add').val()
  }
  ajax_todo(data).done(function (res) {
    $j('.todo-list').append(res);
    get_values()
    $j(".form-control.task-to-add").select();
  })
});

// * Delete/Remove/recover to-do
$j('body').on('click', '.todo-task-delete, .todo-task-recover', function () {
  [$this, $li, cmd, ix] = this_obj(this);
  ajax_todo({
    cmd,
    ix
  }).done(res => {
    console.log(res);
    get_values()
  })
  $li.remove();
});

// * Edit to-do
$j('body').on('click focusout', '.task-text, .input-edit-task', function () {
  [$this, $li, cmd, ix] = this_obj(this);
  let tb = $li.find('input.input-edit-task');

  if (tb.length) {
    $this.text(tb.val()); //remove text box & put its current value as text to the div
    const data = {
      cmd: 'edit-task',
      ix,
      newtext: tb.val(),
    }
    ajax_todo(data).done(function (res) {
      console.log(res)
    });
  } else {
    tb = input_edit($this.text()); //construct text box
    $this.empty().append(tb); //add new text box
    tb.focus(); //put text box on focus
  }
});

$j(document).keyup(function (e) {
  if ($j(".input-edit-task").is(":focus") && (e.keyCode == 13)) {
    $j('.input-edit-task').trigger('focusout');
  }
  if ($j(".form-control.task-to-add").is(":focus") && (e.keyCode == 13)) {
    $j('button.add-todo-task').trigger('click');
  }
});

function get_values() {
  ajax_todo({
    cmd: 'get-values'
  }).done(function (res) {
    let data = JSON.parse(res);
    console.log('update');
    $j('.label-tasks').text(`${data.completed}/${data.listed}`);
    $j('.label-trash').text(`${data.deleted}`);
    $j('.progress-bar').css('width', `${data.completed/data.listed*100}%`).attr('aria-valuenow', data.completed / data.listed); //.text(`${data.completed/data.listed*100}%`);
  })
}

//122---115---133--145---150---151---149---144---138---141---144---
const tasks = {
  "tasks": [{
      "task": {
        "task": "esta es una nueva tarea 1",
        "complete": false,
        "added": "fecha y hora",
        "due": "fecha y hora de vencimiento",
        "edited": ["oldvalue1", "oldvalue2", "oldvalue3"],
        "deleted": false,
        "date_deleted": "feha y hora de borrado"
      }
    },
    {
      "task": {
        "task": "Esta es otra tarea 2",
        "complete": false,
        "added": "fecha y hora",
        "due": "fecha y hora de vencimiento",
        "edited": ["oldvalue1", "oldvalue2", "oldvalue3"],
        "deleted": false,
        "date_deleted": "feha y hora de borrado"
      }
    },
    {
      "task": {
        "task": "Esta es otra tarea 4",
        "complete": false,
        "added": "fecha y hora",
        "due": "fecha y hora de vencimiento",
        "edited": ["oldvalue1", "oldvalue2", "oldvalue3"],
        "deleted": false,
        "date_deleted": "feha y hora de borrado"
      }
    }
  ]
}
<?php
    $settings = [
        // list todo's options
        "todos"=>[
            "progress_bar_options"=>[
                "enable"=>true,
                "class"=>"todos-bar progress-bar-success",
                "attr"=>"",
            ],
        ],
        //task detail modal and header options
        'modal_header'=>[
            "headline"=>"To-Do Task Detail",
            "id"=>"modal-todo",
            "size"=>"",
            "dismiss"=>true,
            "header_class"=>"bg-gray",
            "body_class"=>" bg-gray todo-details",
        ],
        'modal_footer'=>[
            "footer_class"=>"bg-gray",
            "close_btn"=>[
                "enable"=>true,
                "text"=>"Close",
                "color"=>"default",
                "size"=>"xs",
                "class"=>"",
                "attr"=>"data-dismiss='modal'",
                "icon"=>[
                    "enable"=>true,
                    "icon"=>"glyphicon glyphicon-remove",
                ]
            ]
        ],
        //task config modal and header options
        'modal_header_config'=>[
            "headline"=>"Admin To-Do Config",
            "id"=>"modal-todo",
            "size"=>"",
            "dismiss"=>true,
            "header_class"=>"bg-gray",
            "body_class"=>" bg-gray todo-config",
        ],
        'modal_footer_config'=>[
            "footer_class"=>"bg-gray",
            "close_btn"=>[
                "enable"=>true,
                "text"=>"Close",
                "color"=>"default",
                "size"=>"xs",
                "class"=>"",
                "attr"=>"data-dismiss='modal'",
                "icon"=>[
                    "enable"=>true,
                    "icon"=>"glyphicon glyphicon-remove",
                ]
            ]
        ],
        //send task box options
        'send_box_options'=>[
            "headline"=>"Send Task to user",
            "color"=>"success",
            "solid"=>false,
            "with-border"=>false,
            "class"=>"",
            "attr"=>"",
            "box-tool"=>[
                "enable"=>false,
                "collapsable"=>true,
                "removable"=>true,
            ],
        ],
        //send taks button options
        'send_options'=>[
            "send_btn"=>[
                "enable"=>true,
                "text"=>"Send",
                "color"=>"primary",
                "size"=>"xs",
                "class"=>"send-taks-user pull-right",
                "attr"=>"data-cmd='send-task-user'",
                "icon"=>[
                    "enable"=>true,
                    "icon"=>"glyphicon glyphicon-send",
                ],
            ],
        ],
        //due task box options
        'due_box_options'=>[
            "headline"=>"Due Task",
            "color"=>"warning",
            "solid"=>false,
            "with-border"=>false,
            "class"=>"",
            "attr"=>"",
            "box-tool"=>[
                "enable"=>false,
                "collapsable"=>true,
                "removable"=>false,
            ],
        ],
        //set due taks button options
        'due_options'=>[
            "set_due_btn"=>[
                "enable"=>true,
                "text"=>"Set due",
                "color"=>"primary",
                "size"=>"xs",
                "class"=>"set-due pull-right",
                "attr"=>"data-cmd='set-due'",
                "icon"=>[
                    "enable"=>true,
                    "icon"=>"glyphicon glyphicon-time",
                ],
            ],
        ],
        //progress task box options
        
        //set progress taks button options
        'progress_options'=>[
            'box'=>[
                "headline"=>"Progress Task",
                "color"=>"primary",
                "solid"=>false,
                "with-border"=>false,
                "class"=>"",
                "attr"=>"",
                "box-tool"=>[
                    "enable"=>false,
                    "collapsable"=>true,
                    "removable"=>false,
                ],
            ],
            "set_btn"=>[
                "enable"=>true,
                "text"=>"Set progress",
                "color"=>"primary",
                "size"=>"xs",
                "class"=>"set-progress pull-right",
                "attr"=>"data-cmd='set-progress'",
                "icon"=>[
                    "enable"=>true,
                    "icon"=>"glyphicon glyphicon-scale",
                ],
            ],
            "progress_bar"=>[
                "enable"=>true,
                "class"=>"task-bar progress-bar-primary",
                "attr"=>"",
                "width"=>"",
            ],
        ],
    ];
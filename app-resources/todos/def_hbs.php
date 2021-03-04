<?php
//not functional yet
include "ElementsHbs.php";

var_dump(defaults_options());

function defaults_options()//detail modal windows options
{
    $btn = new Elements;
    $mh = new Elements;
    $btn->SetText('Hola Mundo');
    // $close_btn = $btn;
    // $close_btn['text']="close";
    // $close_btn['icon']['icon']="glyphicon glyphicon-remove";

    return [
        'modal_header'=>$mh->GetModalHeader(),
        'modal_footer'=>[
            "footer_color"=>"",
            "close_btn"=>$btn->GetBtn(),
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
    ];
}

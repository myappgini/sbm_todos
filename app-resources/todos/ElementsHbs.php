<?php

class Elements
{
    private $id = "";
    private $enable = true;
    private $text = "Text";
    private $color = "default";
    private $size = "";
    private $class = "";
    private $header_class = "";
    private $body_class = "";
    private $footer_class = "";
    private $dismiss = true;
    private $attr = "";
    private $icon_enable = true;
    private $icon = "";

    public function GetBtn()
    {
        return [
            "enable"        =>  $this->enable,
            "id"            =>  $this->id,
            "text"          =>  $this->text,
            "color"         =>  $this->color,
            "size"          =>  $this->size,
            "class"         =>  $this->class,
            "attr"          =>  $this->attr,
            "icon"          =>  $this->GetIcon(),
        ];
    }
    public function GetIcon()
    {
        return [
            "enable"        =>  $this->icon_enable,
            "icon"          =>  $this->icon,
        ];
    }

    public function GetModalHeader()
    {
        return [
            "headline"      =>  $this->text,
            "id"            =>  $this->id,
            "class"         =>  $this->class,
            "size"          =>  $this->size,
            "dismiss"       =>  $this->dismiss,
            "header_class"  =>  $this->header_class,
            "body_class"    =>  $this->body_class,
            "footer_class"  =>  $this->footer_class,
        ];
    }
    public function GetModalFooter()
    {
        return [
            "footer_class"  =>  $this->footer_class,
        ];
    }

    public function SetAttr($var = "")
    {
        $this->attr=$var;
        return;
    }
    public function SetText($var = "")
    {
        $this->text=$var;
        return;
    }
}

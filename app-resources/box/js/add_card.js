
function add_card(body_content,settings={}) {

    let set = {
        color:"success",
        tools:{
            enable:true,
            remove:false,
            collapse:false,
        },
        title:" Box card for Appgini "
    }
    set = $j.extend({},set,settings);

    let box = $j('<div />', {
        class: `box box-solid box-${set.color}`
    })
    let header = $j('<div />', {
        class: `box-header with-border`
    })
    let boxTool = $j('<div />',{
        class:"box-tools pull-right"
    }).append(function(){
        let t={};
        if(!set.tools.enable) return;
        if(set.tools.remove){
            t = tool_btn("remove");
        }
        if(set.tools.collapse){
            t = tool_btn("plus");
        }else{
            t = tool_btn("minus")
        }
        return t;
    })
    let title = $j('<h3/>',{
        class:"box-title",
        text:set.title
    });

    header.append(title).append(boxTool);
    
    let body = $j('<div />',{
        class:"box-body"
    }).append(body_content);

    box.append(header).append(body)
    return box;
}

//type must be: plus, minus or remove
tool_btn = (type = false) => {
    if (!type) return;
    let dw = type === "remove" ? "remove" : "collapse";
    let btn = $j('<button/>', {
        type: "button",
        class: "btn btn-box-tool",
        "data-widget": dw,
    }).append(`<i class="glyphicon glyphicon-${type}"></i>`);
    return btn;
}
# To Do List

## To Do List plugin

A simple list for managing tasks, you can add as many as you need, fulfill them, delete them and edit them.
You can view the total of tasks and those that still need to be completed.
A progress bar and the amount of tasks in the trash can are displayed.

## Soon

 - The recycle bin will be accessible.
 - It will be possible to view the mutations of each of the tasks (modification history).
 - Tasks can be transferred to other users.
 - Expiration date may be added. 

## Install

got to plugin folder.
dowload todos ZIP pack into plugins folder in your project, and unzip it into todos folder.

[download link](https://github.com//myappgini/sbm_todos/archive/main.zip)

or use **git** into your plugin folder:

if you already use git in ypur project add like submodule
```cmd
$ git submodule add https://github.com/myappgini/sbm_todos.git todos
```

## Manual Install

After downloading the ZIP file. Open it and unzip it in the plugin folder inside the all folder.
Inside the ```plugins/todos/app-resources``` folder there are two more folders, the miams must be copied into the hooks folder.
once the copy is finished, the resulting structure should be:
```
hooks/
    box/
    todos/
```

Edit the footer-extras.php file with your favorite editor.
add the following lines to the top of the file:

```php
<?php
include ('hooks/box/scripts.php');
include ('hooks/todos/scripts.php');
?>
```
## Use

Select To Do from plugin menu in admin area.

Follow the steps.

Then next to install needed files and enjoy.
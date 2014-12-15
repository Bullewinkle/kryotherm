<?php ob_start() ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Редактор текста</title>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<style type="text/css">body {background-color:threedface; border: 0px 0px; padding: 0px 0px; margin: 0px 0px}</style>
</head>
<body>
<div align="center">
<script language="javascript">
<!--//
// this function updates the code in the textarea and then closes this window
function do_save() {
        window.opener.currentTextArea.value = htmlCode.getCode();
        window.close();
        window.opener.focus();
}
//-->
</script>
<?php
// make sure these includes point correctly:
include_once ('config.php');
include_once ('editor_class.php');

// create a new instance of the wysiwygPro class:
$editor = new wysiwygPro();

// add a custom save button:
$editor->addbutton('Save', 'before:print', 'do_save();', WP_WEB_DIRECTORY.'images/save.gif', 22, 22, 'undo');

// add a custom cancel button:
$editor->addbutton('Cancel', 'before:print', 'window.close();window.opener.focus();', WP_WEB_DIRECTORY.'images/cancel.gif', 22, 22, 'undo');

// add a spacer:
$editor->addspacer('', 'after:cancel');

// print the editor to the browser:
$editor->print_editor('100%', 450);

?>
<script language="javascript">
<!--//
        // insert code into WysiwygPro using JavaScript!
        document.getElementById('htmlCode').value = window.opener.currentTextArea.value
//-->
</script>
</div>
</body>
</html>
<?php ob_end_flush() ?>
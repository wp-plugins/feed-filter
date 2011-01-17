<?php

$controls = new FeedFilterControls();

class FeedFilterControls {

    var $options = null;
    
    function is_action($action) {
        if (empty($_REQUEST['act'])) return false;
        if ($_REQUEST['act'] != $action) return false;
        if (check_admin_referer()) return true;
        die('Invalid call');
    }

     function text($name, $size=20) {
        echo '<input name="options[' . $name . ']" type="text" size="' . $size . '" value="';
        echo htmlspecialchars($this->options[$name]);
        echo '"/>';
    }

     function textarea($name) {
        echo '<textarea name="options[' . $name . ']" style="width: 100%; height: 50px">';
        echo htmlspecialchars($this->options[$name]);
        echo '</textarea>';
    }

    function select($name, $options) {
        $value = $this->options[$name];

        echo '<select name="options[' . $name . ']">';
        foreach ($options as $key => $label) {
            echo '<option value="' . $key . '"';
            if ($value == $key)
                echo ' selected';
            echo '>' . htmlspecialchars($label) . '</option>';
        }
        echo '</select>';
    }

    function button($action, $label, $message=null) {
        if ($message == null) {
            echo '<input class="button-secondary" type="submit" value="' . $label . '" onclick="this.form.act.value=\'' . $action . '\'"/>';
        } else {
            echo '<input class="button-secondary" type="button" value="' . $label . '" onclick="this.form.act.value=\'' . $action . '\';return confirm(\'' .
            htmlspecialchars($message) . '\')"/>';
        }
    }

    function init() {
        echo '<script type="text/javascript">
            jQuery(document).ready(function(){
                jQuery("textarea").focus(function() {
                    jQuery("textarea").css("height", "50px");
                    jQuery(this).css("height", "400px");
                });
            });
            </script>
            ';
        echo '<input name="act" type="hidden" value=""/>';
        wp_nonce_field();
    }
}

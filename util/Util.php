<?php
class Util {
   
    /****************************************************************
     * Gets the path
     ***************************************************************/
    public static function getProjectPath() {
        return "/cs6252/lab7_kimberlymarroquin";
    }
    
    /****************************************************************
     * Gets the action
     ***************************************************************/
    public static function getAction($old_action) {
        if ($old_action === '') {
            $new_action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            if ($new_action === NULL) {
                $new_action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                if ($new_action === NULL) {
                    $new_action = '';
                }
            }
            return $new_action;
        } else {
            return $old_action;
        }
    }
}

?>
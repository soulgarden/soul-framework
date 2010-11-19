<?php
if(!defined('SYSPATH')) header("HTTP/1.0 404 Not Found");

final class FormsBuilder {

    private $_form;
    
    public function __construct($form) {
        $this->_form = $form;
    }
    
    public function run() {

        $html = '';
        
        foreach ($this->_form['fields'] as $id => $field) {

            if (empty($field['type'])) {
                $field['type'] = 'text';    
            }
            
            if (!is_callable(array('FormsBuilder', $field['type'].'Builder'))){
                    throw new Exception('Тип поля '.$field['type'].' не существует.');
            }
            
            if (empty($field['name'])) {
                $field['name'] = $field['type'];    
            }
            
            if (empty($field['input_id'])) {
                $field['input_id'] = 'input_'.$field['name'];
            }
            
            if (empty($field['input_class'])) {
                $field['input_class'] = 'input_'.$field['name']; 
            }
            
            if (empty($field['caption_id'])) {
                $field['caption_id'] = 'caption_'.$field['name'];
            }
            
            if (empty($field['caption_class'])) {
                $field['caption_class'] = 'caption_'.$field['name']; 
            }
            
            if (empty($field['container_id'])) {
                $field['container_id'] = 'container_'.$field['name'];
            }
            
            if (empty($field['container_class'])) {
                $field['container_class'] = 'container_'.$field['name']; 
            }
            
            if (empty($field['field_id'])) {
                $field['field_id'] = 'field_'.$field['name'];
            }
            
            if (empty($field['field_class'])) {
                $field['field_class'] = 'field_'.$field['name']; 
            }  
            
            if (empty($field['value'])) {
                $field['value'] = '';    
            }
            
            if (empty($field['error_id'])) {
                $field['error_id'] = 'error_'.$field['name'];    
            }
            
            if (empty($field['error_class'])) {
                $field['error_class'] = 'error_'.$field['name'];    
            }
            
            if (empty($field['error'])) {
                $field['error'] = '';    
            }
            
            $builder = $field['type'].'Builder';
            $html .= $this->$builder($field);
        }
        
        if (empty($field['form_id'])) {
            $this->_form['form_id'] = 'form_'.$this->_form['name'];    
        }
            
        if (empty($field['form_class'])) {
            $this->_form['form_class'] = 'form_'.$this->_form['name'];    
        }
        
        $this->_form['html'] = '
        <form name="'.$this->_form['name'].'" method="'.$this->_form['method'].'" action="'.$this->_form['action'].'" enctype="'.$this->_form['enctype'].'">
        <div id="'.$this->_form['form_id'].'" class="'.$this->_form['form_class'].'">'.$html.'</div>
        </form>';
        
        if ($this->_form['debug']) {

           $this->_form['html'] .= '<pre>'.htmlspecialchars($this->_form['js'], ENT_QUOTES).'</pre></p>';
        }
    }
    
    public function getForm() {
        return $this->_form;
    }

    protected function textBuilder($field) {

        if (!empty($field['help'])) {
            
            $this->_form['js'] .= '
field'.$field['name'].' = document.getElementById(\''.$field['input_id'].'\');
    if (field'.$field['name'].'.value == \'\') {
        field'.$field['name'].'.value = \''.$field['help'].'\';
    }
field'.$field['name'].'.onfocus = function() {
    if (field'.$field['name'].'.value == \''.$field['help'].'\') {
        field'.$field['name'].'.value = \'\';
    }
}
field'.$field['name'].'.onblur = function() {
    if (field'.$field['name'].'.value == \'\') {
        field'.$field['name'].'.value = \''.$field['help'].'\';
    }
}';
        }
        
        if(empty($field['size'])) {
            $field['size'] = '';
        }
        
        if(empty($field['maxlength'])) {
            $field['maxlength'] = '';
        }
        
        if(!empty($field['caption']) && !empty($field['required'])) {
            $field['caption'] .= '*';
        }

        return '<div id="'.$field['container_id'].'" class="'.$field['container_class'].'">
            <div id="'.$field['caption_id'].'" class="'.$field['caption_class'].'">'.$field['caption'].'</div>
            <div id="'.$field['field_id'].'" class="'.$field['field_class'].'"><input type="text" id="'.$field['input_id'].'" class="'.$field['input_class'].'" name="'.$field['name'].'" value="'.$field['value'].'" maxlength="'.$field['maxlength'].'" size="'.$field['size'].'" /></div>
            <div id="'.$field['error_id'].'" class="'.$field['error_class'].'">'.$field['error'].'</div>
        </div>';
    }
    
    public function radioBuilder($field) {
    
        if (empty($field['caption'])) {
            $field['caption'] = '';
        }
        
        $radio = '<div id="'.$field['container_id'].'" class="'.$field['container_class'].'"><div id="'.$field['caption_id'].'" class="'.$field['caption_class'].'">'.$field['caption'].'</div>';

        foreach ($field['options'] as $option) {
            
            if(is_array($option)) {       
                
                if(substr($option['value'], -1) == '=') {
                    $checked = 'checked';
                    $option['value'] = substr($option['value'], 0, -1);
                }
                else {
                    $checked = '';
                }
                $radio .= '<div class="'.$field['field_class'].'"><input type="radio" name="'.$field['name'].'" '.$checked.' class="'.$field['input_class'].'" value="'.$option['value'].'" /> '.$option['caption'].'</div>'; 
            }
            else {
                if(substr($option, -1) == '=') {
                    $checked = 'checked';
                    $option = substr($option, 0, -1);
                }
                else {
                    $checked = '';
                }
                $radio .= '<div class="'.$field['field_class'].'"><input type="radio" name="'.$field['name'].'" '.$checked.' class="'.$field['input_class'].'" value="'.$option.'" /> '.$option.'</div>'; 
            
            }
        }
        $radio .= '<div id="'.$field['error_id'].'" class="'.$field['error_class'].'">'.$field['error'].'</div></div>';
        
        return $radio;
    }
    
    protected function intBuilder($field) {

        if (!empty($field['help'])) {
            
            $this->_form['js'] .= '
field'.$field['name'].' = document.getElementById(\''.$field['input_id'].'\');
    if (field'.$field['name'].'.value == \'\') {
        field'.$field['name'].'.value = \''.$field['help'].'\';
    }
field'.$field['name'].'.onfocus = function() {
    if (field'.$field['name'].'.value == \''.$field['help'].'\') {
        field'.$field['name'].'.value = \'\';
    }
}
field'.$field['name'].'.onblur = function() {
    if (field'.$field['name'].'.value == \'\') {
        field'.$field['name'].'.value = \''.$field['help'].'\';
    }
}';
        }
        
        if(empty($field['size'])) {
            $field['size'] = '';
        }
        
        if(empty($field['maxlength'])) {
            $field['maxlength'] = '';
        }
        
        if(!empty($field['caption']) && !empty($field['required'])) {
            $field['caption'] .= '*';
        }

        return '<div id="'.$field['container_id'].'" class="'.$field['container_class'].'">
            <div id="'.$field['caption_id'].'" class="'.$field['caption_class'].'">'.$field['caption'].'</div>
            <div id="'.$field['field_id'].'" class="'.$field['field_class'].'"><input type="text" id="'.$field['input_id'].'" class="'.$field['input_class'].'" name="'.$field['name'].'" value="'.$field['value'].'" maxlength="'.$field['maxlength'].'" size="'.$field['size'].'" /></div>
            <div id="'.$field['error_id'].'" class="'.$field['error_class'].'">'.$field['error'].'</div>
        </div>';
    }
    
    protected function engBuilder($field) {

        if (!empty($field['help'])) {
            
            $this->_form['js'] .= '
field'.$field['name'].' = document.getElementById(\''.$field['input_id'].'\');
    if (field'.$field['name'].'.value == \'\') {
        field'.$field['name'].'.value = \''.$field['help'].'\';
    }
field'.$field['name'].'.onfocus = function() {
    if (field'.$field['name'].'.value == \''.$field['help'].'\') {
        field'.$field['name'].'.value = \'\';
    }
}
field'.$field['name'].'.onblur = function() {
    if (field'.$field['name'].'.value == \'\') {
        field'.$field['name'].'.value = \''.$field['help'].'\';
    }
}';
        }
        
        if(empty($field['size'])) {
            $field['size'] = '';
        }
        
        if(empty($field['maxlength'])) {
            $field['maxlength'] = '';
        }
        
        if(!empty($field['caption']) && !empty($field['required'])) {
            $field['caption'] .= '*';
        }

        return '<div id="'.$field['container_id'].'" class="'.$field['container_class'].'">
            <div id="'.$field['caption_id'].'" class="'.$field['caption_class'].'">'.$field['caption'].'</div>
            <div id="'.$field['field_id'].'" class="'.$field['field_class'].'"><input type="text" id="'.$field['input_id'].'" class="'.$field['input_class'].'" name="'.$field['name'].'" value="'.$field['value'].'" maxlength="'.$field['maxlength'].'" size="'.$field['size'].'" /></div>
            <div id="'.$field['error_id'].'" class="'.$field['error_class'].'">'.$field['error'].'</div>
        </div>';
    }
    
    protected function customBuilder($field) {

        if (!empty($field['help'])) {
            
            $this->_form['js'] .= '
field'.$field['name'].' = document.getElementById(\''.$field['input_id'].'\');
    if (field'.$field['name'].'.value == \'\') {
        field'.$field['name'].'.value = \''.$field['help'].'\';
    }
field'.$field['name'].'.onfocus = function() {
    if (field'.$field['name'].'.value == \''.$field['help'].'\') {
        field'.$field['name'].'.value = \'\';
    }
}
field'.$field['name'].'.onblur = function() {
    if (field'.$field['name'].'.value == \'\') {
        field'.$field['name'].'.value = \''.$field['help'].'\';
    }
}';
        }
        
        if(empty($field['size'])) {
            $field['size'] = '';
        }
        
        if(empty($field['maxlength'])) {
            $field['maxlength'] = '';
        }
        
        if(!empty($field['caption']) && !empty($field['required'])) {
            $field['caption'] .= '*';
        }

        return '<div id="'.$field['container_id'].'" class="'.$field['container_class'].'">
            <div id="'.$field['caption_id'].'" class="'.$field['caption_class'].'">'.$field['caption'].'</div>
            <div id="'.$field['field_id'].'" class="'.$field['field_class'].'"><input type="text" id="'.$field['input_id'].'" class="'.$field['input_class'].'" name="'.$field['name'].'" value="'.$field['value'].'" maxlength="'.$field['maxlength'].'" size="'.$field['size'].'" /></div>
            <div id="'.$field['error_id'].'" class="'.$field['error_class'].'">'.$field['error'].'</div>
        </div>';
    }
    
    protected function emailBuilder($field) {

        if (!empty($field['help'])) {
            
            $this->_form['js'] .= '
field'.$field['name'].' = document.getElementById(\''.$field['input_id'].'\');
    if (field'.$field['name'].'.value == \'\') {
        field'.$field['name'].'.value = \''.$field['help'].'\';
    }
field'.$field['name'].'.onfocus = function() {
    if (field'.$field['name'].'.value == \''.$field['help'].'\') {
        field'.$field['name'].'.value = \'\';
    }
}
field'.$field['name'].'.onblur = function() {
    if (field'.$field['name'].'.value == \'\') {
        field'.$field['name'].'.value = \''.$field['help'].'\';
    }
}';
        }
        
        if(empty($field['size'])) {
            $field['size'] = '';
        }
        
        if(empty($field['maxlength'])) {
            $field['maxlength'] = '';
        }
        
        if(!empty($field['caption']) && !empty($field['required'])) {
            $field['caption'] .= '*';
        }

        return '<div id="'.$field['container_id'].'" class="'.$field['container_class'].'">
            <div id="'.$field['caption_id'].'" class="'.$field['caption_class'].'">'.$field['caption'].'</div>
            <div id="'.$field['field_id'].'" class="'.$field['field_class'].'"><input type="text" id="'.$field['input_id'].'" class="'.$field['input_class'].'" name="'.$field['name'].'" value="'.$field['value'].'" maxlength="'.$field['maxlength'].'" size="'.$field['size'].'" /></div>
            <div id="'.$field['error_id'].'" class="'.$field['error_class'].'">'.$field['error'].'</div>
        </div>';
    }
    
    protected function passwordBuilder($field) {    
        
        if(empty($field['size'])) {
            $field['size'] = '';
        }
        
        if(empty($field['maxlength'])) {
            $field['maxlength'] = '';
        }
        
        if(!empty($field['caption']) && !empty($field['required'])) {
            $field['caption'] .= '*';
        }

        return '<div id="'.$field['container_id'].'" class="'.$field['container_class'].'">
            <div id="'.$field['caption_id'].'" class="'.$field['caption_class'].'">'.$field['caption'].'</div>
            <div id="'.$field['field_id'].'" class="'.$field['field_class'].'"><input type="password" id="'.$field['input_id'].'" class="'.$field['input_class'].'" name="'.$field['name'].'" value="'.$field['value'].'" maxlength="'.$field['maxlength'].'" size="'.$field['size'].'" /></div>
            <div id="'.$field['error_id'].'" class="'.$field['error_class'].'">'.$field['error'].'</div>                                                    
        </div>';
    }
    
    public function hiddenBuilder($field) {
    
        return '<div id="'.$field['container_id'].'" class="'.$field['container_class'].'"><input type="hidden" name="'.$field['name'].'" id="'.$field['input_id'].'" class="'.$field['input_class'].'" value="'.$field['value'].'" /></div>';
    }

    public function field_confirm_build($field) {

        $name1 = $field['name1'];
        $fieldid1 = $name1.'field';
        
        $name2 = $field['name2'];
        $fieldid2 = $name2.'field';

        if (!$field['name1']) {
            $field['name1'] = 'confirm1';
        }

        if (!$field['name2']) {
            $field['name2'] = 'confirm2';
        }

        if (!$field['caption2']) {
             $field['caption2'] = $field['caption1'];
        }
        
        $field['caption1'] .= '*';
        $field['caption2'] .= '*';

        $type = ($field['type'] == 'password') ? 'password' : 'text';
        
        if ($field['help1'] && ($field['type'] != 'password')) {
        
        $this->_script .= '
        field'.$name1.' = document.getElementById(\''.$fieldid1.'\');
            if (field'.$name1.'.value == \'\') {
                field'.$name1.'.value = \''.$field['help1'].'\';
            }
            field'.$name1.'.onfocus = function() {
                if (field'.$name1.'.value == \''.$field['help1'].'\') {
                    field'.$name1.'.value = \'\';
                }
            }
            field'.$name1.'.onblur = function() {
                if (field'.$name1.'.value == \'\') {
                    field'.$name1.'.value = \''.$field['help1'].'\';
                }
            }
            ';
        }
        
        if ($field['help2'] && ($field['type'] != 'password')) {
             $this->_script .= '
             field'.$name2.' = document.getElementById(\''.$fieldid2.'\');
             if (field'.$name2.'.value == \'\') {

                 field'.$name2.'.value = \''.$field['help2'].'\';
             }
             field'.$name2.'.onfocus = function() {
                 if (field'.$name2.'.value == \''.$field['help2'].'\') {
                     field'.$name2.'.value = \'\';
                 }
             }
             field'.$name2.'.onblur = function() {
                 if (field'.$name2.'.value == \'\') {
                     field'.$name2.'.value = \''.$field['help2'].'\';
                 }
             }';
        }
        
        $this->_form.= '<tr><td '.$field['css_class'].'>'.$field['caption1'].'</td><td><input type="'.$type.'" name="'.$name1.'" id="'.$fieldid1.'" maxlength="'.$field['maxlength'].'" size="'.$field['size'].'"></td></tr>';
        $this->_form.= '<tr><td '.$field['css_class'].'>'.$field['caption2'].'</td><td><input type="'.$type.'" name="'.$name2.'" id="'.$fieldid2.'" maxlength="'.$field['maxlength'].'" size="'.$field['size'].'"></td></tr>';
    }

    public function textareaBuilder($field) {

        if (!empty($field['help'])) {
            
            $this->_form['js'] .= '
field'.$field['name'].' = document.getElementById(\''.$field['input_id'].'\');
    if (field'.$field['name'].'.value == \'\') {
        field'.$field['name'].'.value = \''.$field['help'].'\';
    }
field'.$field['name'].'.onfocus = function() {
    if (field'.$field['name'].'.value == \''.$field['help'].'\') {
        field'.$field['name'].'.value = \'\';
    }
}
field'.$field['name'].'.onblur = function() {
    if (field'.$field['name'].'.value == \'\') {
        field'.$field['name'].'.value = \''.$field['help'].'\';
    }
}';
        }
        
        if (empty($field['disabled'])) {
           $field['disabled'] = '';
        }

        if (empty($field['readonly'])) {
            $field['readonly'] = '';    
        }
        
        if (empty($field['cols'])) {
            $field['cols'] = '';
        }
        
        if (empty($field['rows'])) {
            $field['rows'] = '';
        }
        
        if(!empty($field['caption']) && !empty($field['required'])) {
            $field['caption'] .= '*';
        }

        return '<div id="'.$field['container_id'].'" class="'.$field['container_class'].'">
            <div id="'.$field['caption_id'].'" class="'.$field['caption_class'].'">'.$field['caption'].'</div>
            <div id="'.$field['field_id'].'" class="'.$field['field_class'].'"><textarea id="'.$field['input_id'].'" class="'.$field['input_class'].'" name="'.$field['name'].'" cols="'.$field['cols'].'" rows="'.$field['rows'].'" '.$field['disabled'].' '.$field['readonly'].'>'.$field['value'].'</textarea></div>
            <div id="'.$field['error_id'].'" class="'.$field['error_class'].'">'.$field['error'].'</div>                                                    
        </div>';
    }

    public function checkboxBuilder($field) {

        if (empty($field['caption'])) {
            $field['caption'] = '';
        }
        
        $checkbox = '<div id="'.$field['container_id'].'" class="'.$field['container_class'].'"><div id="'.$field['caption_id'].'" class="'.$field['caption_class'].'">'.$field['caption'].'</div>';

        if (!empty($field['options'])) {
                
            foreach ($field['options'] as $option) {
                
                if(is_array($option)) {
                    
                    if(substr($option['value'], -1) == '=') {
                        $checked = 'checked';
                        $option['value'] = substr($option['value'], 0, -1);
                    }
                    else {
                        $checked = '';
                    }
                    $checkbox .= '<div class="'.$field['field_class'].'"><input type="checkbox" name="'.$field['name'].'" '.$checked.' class="'.$field['input_class'].'" value="'.$option['value'].'" /> '.$option['caption'].'</div>'; 
                }
                else {
                    
                    if(substr($option, -1) == '=') {
                        $checked = 'checked';
                        $option = substr($option, 0, -1);
                    }
                    else {
                        $checked = '';
                    }
                    $checkbox .= '<div class="'.$field['field_class'].'"><input type="checkbox" name="'.$field['name'].'" '.$checked.' class="'.$field['input_class'].'" value="'.$option.'" /> '.$option.'</div>';      
                }    
            }
        }

        $checkbox .= '<div id="'.$field['error_id'].'" class="'.$field['error_class'].'">'.$field['error'].'</div></div>';
        
        return $checkbox;
    }
    
    public function selectBuilder($field) {

        if (!empty($field['required']) && !empty($field['caption'])) {
            $field['caption'] .= '*';
        }
        $multiple = (!empty($field['multiple'])) ? 'multiple' : '';
        $size = (!empty($field['size'])) ? $field['size'] : '';
        $disabled = (!empty($field['disabled'])) ? 'disabled' : '';
        
        $select = '<div id="'.$field['container_id'].'" class="'.$field['container_class'].'"><div id="'.$field['caption_id'].'" class="'.$field['caption_class'].'">'.$field['caption'].'</div><select name="'.$field['name'].'" size="'.$size.'" '.$multiple.' id="'.$field['field_id'].'" class="'.$field['field_class'].'" '.$disabled.'>';

        if (!empty($field['options'])) {
            
            foreach ($field['options'] as $option) {
            
                if(is_array($option)) { 
                     
                    if(substr($option['value'], -1) == '=') {
                        $selected = 'selected';
                        $option['value'] = substr($option['value'], 0, -1);
                    }
                    else {
                        $selected = '';
                    }
                    $select .= '<option '.$selected.' value="'.$option['value'].'" /> '.$option['caption'];
                }
                else {
                    
                    if(substr($option, -1) == '=') {
                        $selected = 'selected';
                        $option = substr($option, 0, -1);
                    }
                    else {
                        $selected = '';
                    }
                    $select .= '<option '.$selected.' value="'.$option.'" /> '.$option;
                }
            }
        }

        $select .= '</select><div id="'.$field['error_id'].'" class="'.$field['error_class'].'">'.$field['error'].'</div></div>';
        
        return $select;
    }
    
    public function fileBuilder($field) {
    
        $this->_form['enctype'] = 'multipart/form-data';
        
        if (empty($field['size'])) $field['size'] = '';
        
        if (!empty($field['required']) && !empty($field['caption'])) {
            $field['caption'] .= '*';
        }
        
        return '<div id="'.$field['container_id'].'" class="'.$field['container_class'].'">
            <div id="'.$field['caption_id'].'" class="'.$field['caption_class'].'">'.$field['caption'].'</div>
            <div id="'.$field['field_id'].'" class="'.$field['field_class'].'"><input type="file" id="'.$field['input_id'].'" class="'.$field['input_class'].'" name="'.$field['name'].'" size="'.$field['size'].'" /></div>
            <div id="'.$field['error_id'].'" class="'.$field['error_class'].'">'.$field['error'].'</div>
        </div>';
    }
    
    public function field_date_build($field) {

        if ((!$field['from_year']) || (!is_numeric($field['from_year']))) {
             $field['from_year'] = 1900;
        }

        if ((!$field['until_year']) || (!is_numeric($field['until_year']))) {
             $field['until_year'] = 2010;
        }

        if ($field['from_year'] > $field['until_year']) {

            $from = $field['from_year'];
            $until = $field['until_year'];

            $field['until_year'] = $from;
            $field['from_year']  = $until;
        }
            
        if (!$field['year_name']) {
            $field['year_name'] = 'year';
        }
            
        if (!$field['day_name']) {
            $field['day_name'] = 'day';
        }
            
        if (!$field['mounth_name']) {
            $field['mounth_name'] = 'year';
        }

        while ($field['from_year'] <= $field['until_year']) {
            $options_year .= '<option value="'.$field['from_year'].'">'.$field['from_year'].'</option>';
            $field['from_year']++;
        }

        $i = 1;
        while ($i <= 12) {
            $options_mounth .= '<option value="'.$i.'">'.$i.'</option>';
            $i++;
        }
            
        $i = 1;
        while ($i <= 31) {
            $options_day .= '<option value="'.$i.'">'.$i.'</option>';
            $i++;
        }
            
        $select_day = '<select name="'.$field['day_name'].'">'.$options_day.'</select>';
        $select_mounth = '<select name="'.$field['mounth_name'].'">'.$options_mounth.'</select>';
        $select_year = '<select name="'.$field['year_name'].'">'.$options_year.'</select>';
        
        $this->_form.= '<tr><td '.$field['css_class'].'>'.$field['caption'].'</td><td>'.$select_day.$select_mounth.$select_year.'</td></tr>';
    }
    
    public function submitBuilder($field) {

        if (empty($field['value'])) {
            $field['value'] = 'Отправить';
        }

        if (!empty($field['src'])) {
           $type = 'image';
        }
        else {
            $type = 'submit';
            $field['src'] = '';
        }
        return '<div id="'.$field['container_id'].'" class="'.$field['container_class'].'"><input type="'.$type.'" name="'.$field['name'].'" id="'.$field['input_id'].'" class="'.$field['input_class'].'" value="'.$field['value'].'" src="'.$field['src'].'" /></div>';
    }
    
}

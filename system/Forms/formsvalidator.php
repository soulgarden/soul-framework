<?php
if(!defined('SYSPATH')) header("HTTP/1.0 404 Not Found");

final class FormsValidator {

    private $_form;
    
    public function __construct($form) {

        $this->_form  = $form;
    }
    
    public function getForm() {
        return $this->_form;
    }
    
    public function run() {
        
        foreach ($this->_form['fields'] as $id => $field) {
            
            if (empty($field['type'])) {
                $field['type'] = 'text';    
            }
            
            if (!is_callable(array('FormsValidator', $field['type'].'Validator'))){
                    throw new Exception('Тип поля '.$field['type'].' не существует.');
            }
            
            if (empty($field['name'])) {
                $field['name'] = $field['type'];    
            }
            
            $name = $field['name']; 
            
            //дополнительная проверка, тк при чебоксах и радиокнопках value может быть пустым
            if (($this->_form['method'] == 'post') && (!empty($_POST[$name]))) {
                $field['value'] = $_POST[$name];    
            }
            elseif(!empty($_GET[$name])) {
                $field['value'] = $_GET[$name];
            }
            else {
                $field['value'] = '';
            }
            
            if (empty($field['value'])) {
                $field['value'] = '';    
            }
            
            $validator = $field['type'].'Validator';
            $this->_form['fields'][$id] = $this->$validator($field);
        }
        
        //если форма валидна, очщаем поля
        if (($this->_form['clear'] == true) && ($this->_form['valid'] == true)) {
            foreach ($this->_form['fields'] as $id => $field) {
                $field['value'] = '';
                $this->_form['fields'][$id] = $field;
            }
        }
    }
    
    private function textValidator($field) {

        if ( (!empty($field['required'])) && (empty($field['value'])) ) {
            if (empty($field['error'])) {
                $field['error'] = 'Обязательное поле <b>'.$field['caption'].'</b> не заполнено.<br />';
            }
            $this->_form['valid'] = false;
        }
        else {
            $field['value'] = htmlspecialchars($field['value'], ENT_QUOTES);
        } 
        return $field;
    }
    
    private function textareaValidator($field) {
        
        if ( (!empty($field['required'])) && (empty($field['value'])) ) {
            if (empty($field['error'])) {
                $field['error'] = 'Обязательное поле <b>'.$field['caption'].'</b> не заполнено.<br />';
            }
            $this->_form['valid'] = false;
        }
        else {
            $field['value'] = htmlspecialchars($field['value'], ENT_QUOTES);
        } 
        return $field;
    }
        
    private function radioValidator($field) {
        
        if ( (!empty($field['required'])) && (empty($field['value'])) ) {
            if (empty($field['error'])) {
                $field['error'] = 'Обязательное поле <b>'.$field['caption'].'</b> не заполнено.<br />';
            }
            $this->_form['valid'] = false;
        }
        else {
            
            $check = false;
            
            foreach ($field['options'] as $option) {
               
                if (!is_array($option)) {  
                   
                    if(substr($option, -1) == '=') {
                   
                        $option = substr($option, 0, -1);
                    }
                             
                    if ((!empty($field['value'])) && ($field['value'] == $option)) {
                        $check = true;
                        break;
                    }
                }
                else {
                    if(substr($option['value'], -1) == '=') {
                   
                        $option['value'] = substr($option['value'], 0, -1);
                    }
                    
                    if ((!empty($field['value'])) && ($field['value'] == $option['value'])) {
                        $check = true;
                        break;
                    }
                }
            }
            
            if ((!empty($field['value'])) && ($check == false)) {
                $field['error'] = 'Выбрано несуществующее значение';
                $this->_form['valid'] = false;
            }
            else {  
                $field['value'] = htmlspecialchars($field['value'], ENT_QUOTES);
            }
        } 
        return $field;
    }
    
    private function intValidator($field) {
        
        if ( (!empty($field['required'])) && (empty($field['value'])) ) {
            if (empty($field['error1'])) {
                $field['error'] = 'Обязательное поле <b>'.$field['caption'].'</b> не заполнено.<br />';
            }
            else {
                $field['error'] = $field['error1'];
            }
            $this->_form['valid'] = false;
        }
        else {
            if (!empty($field['value'])) {
                
                if (!preg_match('/^[0-9]+$/', $field['value'])) {

                    if (empty($field['error2'])) {
                        $field['error'] = 'Поле <b>'.$field['caption'].'</b> должно содепжать только цифры.<br />';
                    }
                    else {
                        $field['error'] = $field['error2'];
                    }
                    $this->_form['valid'] = false;
                }
            }
        }
        return $field;
    }
    
    private function customValidator($field) {
        
        if ( (!empty($field['required'])) && (empty($field['value'])) ) {
            if (empty($field['error1'])) {
                $field['error'] = 'Обязательное поле <b>'.$field['caption'].'</b> не заполнено.<br />';
            }
            else {
                $field['error'] = $field['error1'];
            }
            $this->_form['valid'] = false;
        }
        else {
            if (!empty($field['value'])) {
               
                if (empty($field['pattern'])) {
                    throw new Exception('Полю <b>'.$field['caption'].'</b> типа custom не передан обязательный параметр pattern.<br />');
                }
                if (!preg_match($field['pattern'], $field['value'])) {

                    if (empty($field['error2'])) {
                        $field['error'] = 'Поле <b>'.$field['caption'].'</b> имеет неправильный формат.<br />';
                    }
                    else {
                        $field['error'] = $field['error2'];
                    }
                    $this->_form['valid'] = false;
                }
                else {
                    $field['value'] = htmlspecialchars($field['value'], ENT_QUOTES);
                }
            }
        }
        return $field;
    }
    
    private function emailValidator($field) {

        if ( (!empty($field['required'])) && (empty($field['value'])) ) {
            if (empty($field['error1'])) {
                $field['error'] = 'Обязательное поле <b>'.$field['caption'].'</b> не заполнено.<br />';
            }
            else {
                $field['error'] = $field['error1'];
            }
            $this->_form['valid'] = false;
        }
        else {
            if (!empty($field['value'])) {
                $pattern = '#^[-0-9a-z_\.]+@[-0-9a-z^\.]+\.[a-z]{2,6}$#i';
                if (!preg_match($pattern, $field['value'])) {
                    if (empty($field['error2'])) {
                        $field['error'] = 'Поле <b>'.$field['caption'].'</b> имеет неправильный формат.<br />';
                    }
                    else {
                        $field['error'] = $field['error2'];
                    }
                    $this->_form['valid'] = false;
                }
            }
        }
        return $field;
    }
    
    private function passwordValidator($field) {

        if ( (!empty($field['required'])) && (empty($field['value'])) ) {
            if (empty($field['error'])) {
                $field['error'] = 'Обязательное поле <b>'.$field['caption'].'</b> не заполнено.<br />';
            }
            $this->_form['valid'] = false;
        }
        else {
            $field['value'] = htmlspecialchars($field['value'], ENT_QUOTES); 
        }
        return $field;
    }
    
    private function submitValidator($field) {

        return $field;
    }
    
    private function hiddenValidator($field) {
        $field['value'] = htmlspecialchars($field['value'], ENT_QUOTES);
        return $field;
    }
    
     private function checkboxValidator($field) {
        
        if ( (!empty($field['required'])) && (empty($field['value'])) ) {
            if (empty($field['error'])) {
                $field['error'] = 'Обязательное поле <b>'.$field['caption'].'</b> не заполнено.<br />';
            }
            $this->_form['valid'] = false;
        }
        else {
            
            $check = false;
            
            foreach ($field['options'] as $option) {

                if (!is_array($option)) {  
                   
                    if(substr($option, -1) == '=') {
                   
                        $option = substr($option, 0, -1);
                    }
                   
                    if ((!empty($field['value'])) && ($field['value'] == $option)) {
                         $check = true;
                         break;
                     }
                }
                else {
                    if(substr($option['value'], -1) == '=') {
                   
                        $option['value'] = substr($option['value'], 0, -1);
                    }
                   
                    if ((!empty($field['value'])) && ($field['value'] == $option['value'])) {
                        $check = true;
                        break;
                    }
                }
            }

            if ((!empty($field['value'])) && ($check == false)) {
                $field['error'] = 'Выбрано несуществующее значение';
                $this->_form['valid'] = false;
            }
            else {  
                $field['value'] = htmlspecialchars($field['value'], ENT_QUOTES);
            }
        } 
        return $field;
    }

    private function engValidator($field) {

        if ( (!empty($field['required'])) && (empty($field['value'])) ) {
            if (empty($field['error1'])) {
                $field['error'] = 'Обязательное поле <b>'.$field['caption'].'</b> не заполнено.<br />';
            }
            else {
                $field['error'] = $field['error1'];
            }
            $this->_form['valid'] = false;
        }
        else {
            if (!empty($field['value'])) {
                
                if (!preg_match('/^[a-z]+$/i', $field['value'])) {

                    if (empty($field['error2'])) {
                        $field['error'] = 'Поле <b>'.$field['caption'].'</b> должно содепжать только анлийские символы.<br />';
                    }
                    else {
                        $field['error'] = $field['error2'];
                    }
                    $this->_form['valid'] = false;
                }
            }
        }
        return $field;
    }
    
    private function field_confirm_validate($field) {

        if (!$field['name1']) {
            $field['name1'] = 'confirm1';
        }

        if (!$field['name2']) {
            $field['name2'] = 'confirm2';
        }
    
        $name1 = $field['name1'];
        $name2 = $field['name2'];
        $value1 = $this->_incVars[$name1];
        $value2 = $this->_incVars[$name2];
        
        if (!trim($value1)) {
            $this->_errors .= 'Обязательное поле <b>'.$field['caption1'].'</b> не заполнено.<br />';
            $this->is_valid[] = false;
        }
        else {
            if ($field['type'] == 'email') {
                $pattern = '#^[-0-9a-z_\.]+@[-0-9a-z^\.]+\.[a-z]{2,6}$#i';
                if (!preg_match($pattern, $value1)) {
                    $this->_errors .= 'Обязательное поле <b>'.$field['caption'].'</b> имеет неправильный формат.<br />';
                    $this->is_valid[] = false;
                    $err1 = true;
                }
            }
        }
        
        if (!trim($value2)) {
            $this->_errors .= 'Обязательное поле <b>'.$field['caption2'].'</b> не заполнено.<br />';
            $this->is_valid[] = false;
        }
        else {
            if ($field['type'] == 'email') {
                $pattern = '#^[-0-9a-z_\.]+@[-0-9a-z^\.]+\.[a-z]{2,6}$#i';
                if (!preg_match($pattern, $value2)) {
                    $this->_errors .= 'Обязательное поле <b>'.$field['caption'].'</b> имеет неправильный формат.<br />';
                    $this->is_valid[] = false;
                    $err2 = true;
                }
            }
        }

        if ($value1 != $value2) {
            $this->_errors .= 'Поля <b>'.$field['caption1'].'</b> и <b>'.$field['caption2'].'</b> не равны.<br />';
            $this->is_valid[] = false;
        }
        elseif((!$err1) && (!$err2)) {
            $this->_vars[$name1] = htmlentities($value1, ENT_QUOTES);
            $this->_vars[$name2] = htmlentities($value2, ENT_QUOTES);
            $this->is_valid[] = true;
        }
        else {
             $this->is_valid[] = false;
        }
    }

    private function fileValidator($field) {
        
        $name = $field['name'];

		if ((!empty($field['required'])) && ($_FILES[$name]['error'] == 4) ) {
            
            if (empty($field['error1'])) {
                $field['error'] = 'Файл не был выбран.<br />';
            }
            else {
                $field['error'] = $field['error1'];
            }
            $this->_form['valid'] = false; 
        }
		else {
            if ($_FILES[$name]['error'] != 4) {
        
                if($_FILES[$name]['error'] == 1) {
                    if (empty($field['error2'])) {
                        $field['error'] = 'Превышен допустимый размер файла<br />';
                    }
                    else {
                        $field['error'] = $field['error2'];
                    }
                    $this->_form['valid'] = false;
                }
                elseif($_FILES[$name]['error'] == 3) {
                    if (empty($field['error3'])) {
                        $field['error'] = 'Файл не был загружен полностью, пожалуйста повторите загрузку.<br />';
                    }
                    else {
                        $field['error'] = $field['error3'];
                    }
                    $this->_form['valid'] = false;
                }
                else {
         
                    if ((!empty($field['mimes'])) && (!in_array($_FILES[$name]['type'], $field['mimes']))) {
                
                        if (empty($field['error4'])) {
                            $field['error'] = 'Загрузка файла с разшерением '.$ext.' запрещена.<br />';
                        }
                        else {
                            $field['error'] = $field['error4'];
                        }
                        $this->_form['valid'] = false;
                    }
			        else {
                        
                        if (empty($field['dir'])) $field['dir'] = '';
                
                        if (!empty($field['random_name'])) {
                    
                            $path_info = pathinfo($_FILES[$name]['name']);
                    
                            $_FILES[$name]['name'] = uniqid("").'.'.$path_info['extension'];
                        }
                        else {
                            if (empty($field['filename'])) {
                                $_FILES[$name]['name'] = basename($_FILES[$name]['name']);
                            }
                        }
                
                        if(!preg_match('/^([0-9a-z.]+)$/i', $_FILES[$name]['name'])) {
                            if (empty($field['error5'])) {
                                $field['error'] = 'Имя файла должно состоять только из английских букв или цифр.<br />';
                            }
                            else {
                                $field['error'] = $field['error5'];
                            }
                            $this->_form['valid'] = false;
                        }    
                        elseif (file_exists($field['dir'].$_FILES[$name]['name'])) {
                   
                            if (empty($field['error6'])) {
                                $field['error'] = 'Файл с таким именем уже существует.<br />';
                            }
                            else {
                                $field['error'] = $field['error6'];
                            }
                            $this->_form['valid'] = false;
                       }
                       else {
                    
                           if (!copy($_FILES[$name]['tmp_name'], $field['dir'].$_FILES[$name]['name'])) {
                    
                                if (empty($field['error7'])) {
                                    $field['error'] = 'Не удается переместить загруженный файл.<br />';
                                }
                                else {
                                    $field['error'] = $field['error7'];
                                }
                                $this->_form['valid'] = false;
                            }
                            else {
                                if (!empty($field['width'])) {
                                    $this->imageresize($field['dir'].$_FILES[$name]['name'], $field['width'], 70);
                                }
                            }
                        }
                    }
                } 
		    }
        }           
          
        return $field;
    }
	
	 private function selectValidator($field) {
        
        $name = $field['name'];
        
        if ((!empty($field['required'])) && (empty($field['value']))) {
            
            if (empty($field['error1'])) {
                $field['error'] = 'Значение не выбрано.<br />';
            }
            else {
                $field['error'] = $field['error1'];
            }
            $this->_form['valid'] = false;
        }
        else {
            
            $check = false;
            
            foreach ($field['options'] as $option) {

                if (!is_array($option)) {  
                   
                    if(substr($option, -1) == '=') {
                   
                        $option = substr($option, 0, -1);
                    }
                   
                    if ((!empty($field['value'])) && ($field['value'] == $option)) {
                        $check = true;
                        break;
                    } 
                }
                else {
                    if(substr($option['value'], -1) == '=') {
                   
                        $option['value'] = substr($option['value'], 0, -1);
                    }
                   
                    if ((!empty($field['value'])) && ($field['value'] == $option['value'])) {
                        $check = true;
                        break;
                    }
                }
            }
           
           if (!empty($field['value']) && ($check == false)) {
                $field['error'] = 'Выбрано несуществующее значение';
                $this->_form['valid'] = false;
           }
           else {  
               $field['value'] = htmlspecialchars($field['value'], ENT_QUOTES);
           }
        }
        return $field;
    }
    
    public function field_date_validate($field) {

        $dayname  = $field['day_name'];
        $dayvalue = $this->_incVars[$dayname];

        $mounthname  = $field['mounth_name'];
        $mounthvalue = $this->_incVars[$mounthname];

        $yearname  = $field['year_name'];
        $yearvalue = $this->_incVars[$yearname];

        if ( (!$field['from_year']) || (!is_numeric($field['from_year'])) ) {
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

        if ( (!trim($dayvalue)) || (!trim($mounthvalue)) || (!trim($yearvalue)) ) {
            $this->_errors .= 'Обязательный пункт поля <b>'.$field['caption'].'</b> не выбран.<br />';
            $this->is_valid[] = false;
        }
        else {
            if ( (($dayvalue >= 1) && ($dayvalue <= 31)) && (($mounthvalue >=1) && ($mounthvalue <= 12)) && (($yearvalue >= $field['from_year']) && ($yearvalue <= $field['until_year'])) ) {
                $this->is_valid[] = true;
                $this->_vars[$dayname] = htmlentities($dayvalue, ENT_QUOTES);
                $this->_vars[$mounthname] = htmlentities($mounthvalue, ENT_QUOTES);
                $this->_vars[$yearname] = htmlentities($yearvalue, ENT_QUOTES);
            }
            else {
                $this->_errors .= 'В поле <b>'.$field['caption'].' выбрано значение, которого не предлагалось.</b><br />';
                $this->is_valid[] = false;
            }
        }
    }
    
    private function encodestring($string) {
      $string = strtr($string,"абвгдеёзийклмнопрстуфхъыэ_", "abvgdeeziyklmnoprstufh'iei");
      $string = strtr($string,"АБВГДЕЁЗИЙКЛМНОПРСТУФХЪЫЭ_", "ABVGDEEZIYKLMNOPRSTUFH'IEI");
      $string = strtr($string, array('ж' => 'zh', 'ц' => 'ts', 'ч' => 'ch', 'ш' => 'sh',
                                     'щ' => 'shch','ь' => '', 'ю' => 'yu', 'я' => 'ya',
                                     'Ж' => 'ZH', 'Ц' => 'TS', 'Ч' => 'CH', 'Ш' => 'SH',
                                     'Щ' => 'SHCH', 'Ь' => '', 'Ю' => 'YU', 'Я' => 'YA',
                                     'ї' => 'i', 'Ї' => 'Yi', 'є' => 'ie', 'Є' => 'Ye', ' ' => '_'
                                     )
                     );
      return $string;
    }
    
    private function imageresize($file, $width, $quality) {

        $size = GetImageSize($file);
        
        $height = $width / ($size[0] / $size[1]);
        
        $image = imagecreatetruecolor($width, $height);
        
        if ($size[2]==2) {
            $temp = imagecreatefromjpeg($file);
        }
        elseif ($size[2]==3) {
            $temp = imagecreatefrompng($file);
        }
        else if ($size[2]==1) {
            $temp = imagecreatefromgif($file);
        }
       
        imagecopyresampled($image, $temp, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
      
        if ($size[2]==2) {
            imagejpeg($image, $file, $quality);
        }
        elseif ($size[2]==1) {
            imagegif($image, $file);
        }
        elseif ($size[2]==3) {
            imagepng($image, $file);
        }
        
        imagedestroy($image);
        imagedestroy($temp);
    }
}
?>

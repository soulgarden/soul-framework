<?php
if(!defined('SYSPATH')) header("HTTP/1.0 404 Not Found");

class TplCompiler {
    
    private $_tpl_object;
    private $_engine;
    private static $_compilers;

    public function __construct(Tpl $engine) {
        $this->_engine = $engine;

        if(!self::$_compilers)
        {
            $this->_load_compilers();
        }
    }

    public function compile($src) {
        $escape = array("####".uniqid()."####","####".uniqid()."####");

        $src = str_replace(array("[[","]]"),$escape,$src);

        $compiled = preg_replace_callback('#\[\s*(.*?)\s*\]#ismu',(array($this, "_process")),$src);
        $compiled = str_replace($escape, array("[","]"), $compiled);

        return $compiled;
    }

    private function _process($tag)
    {
        foreach(self::$_compilers as $pattern=>$compiler)
        {
            if(mb_substr($tag[1],0,mb_strlen($pattern))==$pattern)
            {
                return $compiler->compile($pattern,$tag);
            }
        }

        return self::$_compilers['var']->compile('var',$tag);
    }

    private function _load_compilers()
    {
        $classes = get_declared_classes();
        $this->_compilers = array();
        foreach($classes as $class)
        {
            if(substr($class,0,16) == "DudeTplCompiler_" && $class!="DudeTplCompiler_Default")
            {
                $compiler = new $class();
                $compiler->engine = $this->_engine;
                if(!is_a($compiler,"DudeTplCompiler_Default"))
                {
                    continue;
                }

                $patterns = is_array($compiler->patterns) ? $compiler->patterns : array();
                foreach($patterns as $pattern)
                {
                    if(isset($this->_compilers[$pattern]))
                    {
                        $tmp = $this->_compilers[$pattern];
                        throw new Exception("Pattern '$pattern' already was added with '".get_class($tmp)."'");
                    }
                    self::$_compilers[$pattern] = $compiler;
                }
            }
        }
    }
}

abstract class DudeTplCompiler_Default
{
    public $patterns = array();
    public $engine;
    protected $_stack = array();

    public function compile($pattern, $tag)
    {
        return $tag[0];
    }

    protected function embed_var($str)
    {
        $objvars = "";
        if($objpos = strpos($str,"->"))
        {
            $objvars = substr($str,$objpos);
            $str = substr($str,0,$objpos);
        }
        $vars = explode(".",$str);
        return "\$this->_vars"."['".join("']['",$vars)."']$objvars";
    }

    protected function parse_vars($str)
    {
        return preg_replace_callback('#(\$[a-z_\.]\w*(?:\.\w*)*)#ismx',array($this,"parse_var"),$str);
        //return preg_replace('#\.(\w+)#',"['\\1']",$str); //TODO: forgot what the shit is this ...
    }

    protected function parse_var($var)
    {
        return $this->embed_var(ltrim(is_array($var) ? $var[1] : $var,'$'));
    }
}

class DudeTplCompiler_Comment extends DudeTplCompiler_Default
{
    public $patterns = array("*");

    public function compile($pattern, $tag)
    {
        return;
    }
}

class DudeTplCompiler_Variable extends DudeTplCompiler_Default
{
    public $patterns = array("var","\$");

    public function compile($pattern,$tag)
    {
        return "<?php echo ".$this->parse_var($tag[1])."?>";
    }
}

class DudeTplCompiler_EscapedVariable extends DudeTplCompiler_Default
{
    public $patterns = array("::",":");

    public function compile($pattern,$tag)
    {
        switch($pattern)
        {
            case "::":
                return "<?php echo urlencode(".$this->parse_var(ltrim($tag[1],":")).")?>";
                break;

            case ":":
                return "<?php echo htmlspecialchars(".$this->parse_var(ltrim($tag[1],":")).")?>";
                break;
        }
    }
}

class DudeTplCompiler_Conditions extends DudeTplCompiler_Default
{
    public $patterns = array("!?", "?", "??", "/?", "if", "elseif", "else", "/if");

    public function compile($pattern, $tag)
    {
        $str = $tag[1];
        switch($pattern)
        {
            case "if":
            case "?":
                $str = "<?php if (".$this->parse_vars(trim(mb_strstr($str," ")))."):?>";
                break;
            
            case "??":
            case "elseif":
                $str = "<?php elseif (".$this->parse_vars(trim(mb_strstr($str," ")))."):?>";
                break;
                    
            case "!?":
            case "else":
                $str = "<?php else:?>";
                break;

            case "/if":
            case "/?":
                $str = "<?php endif;?>";
                break;
        }

        return $str;
    }
}

class DudeTplCompiler_Foreach extends DudeTplCompiler_Default
{
    public $patterns = array("&","/&","foreach","/foreach");

    public function compile($pattern, $tag)
    {
        $str = $tag[1];
        switch($pattern)
        {
            case "foreach":
            case "&":
                $str = trim(substr($str,strlen($pattern)));
                list($from, $key, $item) = array_map("trim",explode(" ",$str));
                $key = !empty($key) ? $key : "_id";

                if(empty($from))
                {
                    throw new Exception("DudeTpl Compilation Exception: please add param to iterate with 'foreach' block. Example: [&items]");
                }
                if(!empty($key) && !preg_match("#^[a-z0-9_]#i",$key))
                {
                    throw new Exception("DudeTpl Compilation Exception: 'foreach' block key parameter can be only [a-z0-9_] characters.");
                }


                $args['from'] = $this->parse_var($from);
                $args['item'] = ($dotpos = strrpos($from,"."))!==false ? substr($from,$dotpos+1) : $from;
                $args['item'] = $this->parse_var($item ? "\$$item" : '_'.substr($args['item'],0,-1));
                $args['key'] = "\$this->_vars['$key']";
                $args['hash'] = md5($from);
                array_unshift($this->_stack,$args);

                $str = "<?php\n";
                $str .= "\$_from_$args[hash] = $args[from];\n";
                $str .= "if((is_array(\$_from_$args[hash]) || is_object(\$_from_$args[hash])) && count(\$_from_$args[hash])):\n";
                $str .= "foreach(\$_from_$args[hash] as ".(@$args['key'] ? "$args[key]=>" : "")."$args[item]): ";
                $str .= "?>";
                break;
                    
            case "/foreach":
            case "/&":
                $args = array_shift($this->_stack);
                $str = "<?php endforeach; endif; unset(\$_from_$args[hash]);?>";
                break;
        }

        return $str;
    }
}

class DudeTplCompiler_Function extends DudeTplCompiler_Default
{
    public $patterns = array("@");

    public function compile($pattern, $tag)
    {
        $str = trim(substr($tag[1],1));
        $params = array_map("trim",explode(" ",$this->parse_vars($str)));

        $func = array_shift($params);

        $params = "array(".join(",",$params).")";
        return "<? echo \$this->_call_func('$func',$params) ?>";
    }
}

class DudeTplCompiler_BlockFunction extends DudeTplCompiler_Default
{
    public $patterns = array("#","/#");

    public function compile($pattern, $tag)
    {
        $functions = $this->engine->get_block_functions();

        switch($pattern)
        {
            case "#":
                $str = trim(substr($tag[1],1));
                $params = array_map("trim",explode(" ",$this->parse_vars($str)));

                $func = array_shift($params);

                if(!$functions[$func])
                {
                    throw new Exception("DudeTpl Compilation Exception: block function '$func' was not added into DudeTpl object");
                }
                $params = join(",",$params);

                array_unshift($this->_stack,array("func"=>$func,"params"=>$params));

                $str = "<?php \$this->_call_block_func('$func', false, '', $params); ob_start();?>";
                break;
            case "/#":
                $args = array_shift($this->_stack);
                if(!$args)
                {
                    throw new Exception("DudeTpl Compilation Exception: was found closing block function tag '/#', but opened functions not found");
                }
                $str = "<?php echo \$this->_call_block_func('$args[func]', true, ob_get_clean(), $args[params] );?>";
                break;
        }

        return $str;
    }
}
class DudeTplCompiler_Include extends DudeTplCompiler_Default {
    
    public $patterns = array('inc');

    public function compile($pattern, $tag) {

        $template = trim(mb_strstr($tag[1]," "));
        
        $this->engine->fetch($template);
                                   
        $src = '<?php require (\''.$this->engine->_compile_dir.'/'.md5($template.'tpl').'\') ?>';
    
        return $src;
    }
}
class DudeTplCompiler_P extends DudeTplCompiler_Default {
    
    public $patterns = array('=');

    public function compile($pattern, $tag) {
        
        $arr = explode(' ',(trim(ltrim($tag[1],"="))));

        $src = '<?php '.$this->parse_vars($arr[0]).$arr[1].$this->parse_vars($arr[2]).'; ?>';
    
        return $src;
    }
}

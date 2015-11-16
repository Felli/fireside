<?php

if (!defined("IN_ESOTALK")) exit;

/**
 * A class that encapsulates a handlebars template renderer and provides helpers
 * to be used in the handlebars templates
 *
 * @package esoTalk
 */
class FSRenderer extends ETPluggable {


/**
 * The master view that will be used to render the page. The master view is a "wrapper" that contains all
 * the common elements of a page (header, footer, etc.) and that will render actual view within it.
 * @var string
 */
public $masterView = "default.master";

public function getCompiledTemplate($template, $data = array())
{

    require_once(PATH_VENDOR."/zordius/lightncandy/src/lightncandy.php");

    // Get the filename for the handlebars template
    $file = $this->getTemplatePath($template);
    // TODO(jsonnull): implement error messages
    /*if (!file_exists($file)) {
        return;
    }*/

    // Get the maximum last modifiction time of the file.
    $lastModTime = 0;
    $lastModTime = max($lastModTime, filemtime($file));

    // Construct a filename for the compiled template.
    $compiled = PATH_ROOT."/cache/templates/".str_replace('/','_',$template).".php";

    // If this file doesn't exist, or if it is out of date, generate and write it.
    if (!file_exists($compiled) or filemtime($compiled) < $lastModTime) {

        // Get the contents of each of the files, fixing up image URL paths for CSS files.
        $handlebars = file_get_contents($file);

        // TODO(jsonnull): move helpers into separate functions so they do not need to be inlined
        if ($handlebars) {
            $phpStr = '<?php if (!defined("IN_ESOTALK")) exit; ?>';
            $phpStr = $phpStr . LightnCandy::compile($handlebars, Array(
                'flags' => LightnCandy::FLAG_ADVARNAME
                    | LightnCandy::FLAG_JS
                    | LightnCandy::FLAG_SPVARS
                    | LightnCandy::FLAG_WITH
                    | LightnCandy::FLAG_ERROR_EXCEPTION,
                'basedir' => Array(
                    PATH_TEMPLATES.'/partials'
                ),
                'helpers' => Array(
                    'translate' => 'FSRenderer::helperTranslate',
                    'url' => 'FSRenderer::helperUrl',
                    'memberUrl' => 'FSRenderer::helperMemberUrl',
                    'relativeTime' => 'FSRenderer::helperRelativeTime',
                    'lookup' => 'FSRenderer::helperLookup',
                    'searchUrl' => 'FSRenderer::helperSearchUrl',
                    'urlEncode' => 'FSRenderer::helperUrlEncode',
                    'selfUrl' => 'FSRenderer::helperSelfUrl'
                ),
                'blockhelpers' => Array(
                    'and' => 'FSRenderer::blockHelperAnd'
                ),
                'fileext' => Array(
                    '.hbs'
                )
            ));
            file_put_contents($compiled, $phpStr);
        }

        // Minify and write the contents.

    }

    $compiledTemplate = include($compiled);
    return $compiledTemplate;
}


/**
 * Gets the full filepath to the specified template.
 *
 * @param string $template The name of the template to get the filepath of.
 * @return string The filepath of the template.
 */
public function getTemplatePath($template)
{
    // If the view has a file extension, assume it contains the full file path and use it as is.
    if (pathinfo($template, PATHINFO_EXTENSION) == "hbs") return $template;

    // Check the skin to see if it contains this view.
    // TODO(jsonnull): uncomment
    // if (file_exists($skinView = ET::$skin->view($view))) return $skinView;

    // Check loaded plugins to see if one of them contains the view.
    // TODO(jsonnull): uncomment
    /*foreach (ET::$plugins as $k => $v) {
        if (file_exists($pluginView = $v->view($view))) return $pluginView;
    }*/

    // Otherwise, just return the default template.
    return PATH_TEMPLATES."/$template.hbs";
}


/**
 * Handlebars helper to lookup translations.
 *
 * @param array $args Translation lookup index and optionally a default text.
 * @param array $named Not supported.
 * @return string The translated text.
 */
public static function helperTranslate($args, $named) {
    if (count($args)) {
        $default = isset($args[1])? $args[1] : false;
        return ET::translate($args[0], $default);
    }
    else return "";
}


/**
 * Handlebars helper to construct URLs.
 *
 * @param array $args URL to lookup and optional string to concat before lookup.
 * @param array $named Not supported.
 * @return string A relative URL.
 */
public static function helperUrl($args, $named) {
    if (count($args)) {
        $concat = "";
        $i = 1;
        while (isset($args[$i])) {
            $concat = $concat.$args[$i];
            $i++;
        }
        return URL($args[0].$concat);
    }
    else return "";
}


/**
 * Handlebars helper to construct path for a member. Intended to be used with
 * the url helper.
 *
 * @param array $args member ID, optionally username and pane of user profile
 * @param array $named Not supported.
 * @return string A (not fully constructed URL)
 */
public static function helperMemberUrl($args, $named) {
    if (count($args)) {
        $username = isset($args[1])? $args[1] : "";
        $pane = isset($args[2])? $args[2] : "";
        return memberUrl($args[0], $username, $pane);
    }
    else return "";
}


/**
 * Handlebars helper to construct URLs for members.
 *
 * @param array $args URL to lookup and optional string to concat before lookup.
 * @param array $named Not supported.
 * @return string A relative URL.
 */
public static function helperRelativeTime($args, $named) {
    if (count($args)) {
        $precise = isset($args[1])? $args[1] : false;
        return relativeTime($args[0], $precise);
    }
    else return "";
}


/**
 * Handlebars helper to look up an element in an array by index.
 *
 * @param array $args Array to look in and index to retrieve
 * @param array $named Not supported.
 * @return mixed The element at the given index.
 */
public static function helperLookup($args, $named) {
    if (count($args)) {
        $index = isset($args[1])? $args[1] : 0;
        return Array($args[0][$index], 'value');
    }
    else return "";
}


/**
* Handlebars helper to construct path for a search. Intended to be used with
* the url helper.
 *
 * @param array $args search and optionally channel to search in
 * @param array $named Not supported.
 * @return mixed The element at the given index.
 */
public static function helperSearchUrl($args, $named) {
    if (count($args)) {
        $channel = isset($args[1])? $args[1] : "all";
        return searchURL($args[0], $channel);
    }
    else return "";
}


/**
* Handlebars helper to urlencode parameter
 *
 * @param array $args String to encode.
 * @param array $named Not supported.
 * @return string The encoded result.
 */
public static function helperUrlEncode($args, $named) {
    if (count($args)) {
        return urlencode($args[0]);
    }
    else return "";
}


/**
* Handlebars helper to get controller URL
 *
 * @param array $args Not supported.
 * @param array $named Not supported.
 * @return string The encoded result.
 */
public static function helperSelfUrl($args, $named) {
    return ET::$controller->selfURL;
}

/**
 * Handlebars block helper to conditionally render block if two conditions are met
 *
 * @param string $cx Handlebars context inside the block
 * @param array $args URL to lookup and optional string to concat before lookup.
 * @param array $named Not supported.
 * @return mixed Context inside the block if conditions are met, otherwise null.
 */
public static function blockHelperAnd($cx, $args, $named) {
    if (count($args) == 2) {
        if ($args[0] == $args[1]) {
            return $cx;
        }
    }
    else return null;
}

}

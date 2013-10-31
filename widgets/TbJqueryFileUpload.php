<?php
/**
 * TbJqueryFileUpload class file.
 * @author Christoffer Niska <christoffer.niska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2013-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package yiistrap-widgets.widgets
 */

/**
 * Methods accessible through the 'TbWidget' class:
 * @method string resolveId($id = null)
 * @method string publishAssets($path, $forceCopy = false)
 * @method void registerCssFile($url, $media = '')
 * @method void registerScriptFile($url, $position = null)
 * @method string resolveScriptVersion($filename, $minified = false)
 * @method boolean registerPlugin($name, $selector, $options = array(), $position = CClientScript::POS_END)
 * @method boolean registerEvents($selector, $events, $position = CClientScript::POS_END)
 * @method CClientScript getClientScript()
 */
class TbJqueryFileUpload extends CInputWidget
{
    /**
     * @var string the button label text.
     */
    public $label = 'Select file';

    /**
     * @var mixed url to call with ajax when a file is uploaded.
     */
    public $url;

    /**
     * @var array HTML attributes for the button element.
     */
    public $buttonOptions = array();

    /**
     * @var array initial options that should be passed to the plugin.
     */
    public $options = array();

    /**
     * @var string path to widget assets.
     */
    public $assetPath;

    /**
     * @var bool whether to register scripts through the client script component.
     */
    public $registerScripts = true;

    /**
     * Initializes the widget.
     */
    public function init()
    {
        parent::init();
        if (!isset($this->url)) {
            throw new CException('You must specify an url property.');
        }
        Yii::import('bootstrap.behaviors.TbWidget');
        $this->attachBehavior('tbWidget', new TbWidget);
        if (!isset($this->assetPath)) {
            $this->assetPath = Yii::getPathOfAlias('vendor.blueimp.jquery-file-upload');
        }
        if (!isset($this->buttonOptions['class'])) {
            TbHtml::addCssClass('btn btn-primary', $this->htmlOptions);
        }
        TbHtml::addCssClass('fileinput-button', $this->buttonOptions);
        TbArray::defaultValue('dataType', 'json', $this->options);
    }

    /**
     * Runs the widget.
     */
    public function run()
    {
        list($name, $id) = $this->resolveNameID();
        $this->resolveId($id);
        if ($this->hasModel()) {
            $input = TbHtml::activeFileField($this->model, $this->attribute, $this->htmlOptions);
        } else {
            $input = TbHtml::fileField($name, $this->value, $this->htmlOptions);
        }
        echo TbHtml::tag('span', $this->buttonOptions, $this->label . ' ' . $input);
        $this->options['url'] = $this->url;

        if ($this->registerScripts) {
            $options = !empty($this->options) ? CJavaScript::encode($this->options) : '';
            $cs = $this->getClientScript();
            $cs->registerCoreScript('jquery');
            $this->publishAssets($this->assetPath);
            $this->registerCssFile('css/jquery.fileupload-ui.css');
            $this->registerScriptFile('js/vendor/jquery.ui.widget.js', CClientScript::POS_HEAD);
            $this->registerScriptFile('js/jquery.iframe-transport.js', CClientScript::POS_HEAD);
            $this->registerScriptFile('js/jquery.fileupload.js', CClientScript::POS_HEAD);
            $script = <<<EOD
    jQuery('#{$id}')
        .fileupload({$options})
        .prop('disabled', !jQuery.support.fileInput)
        .parent().addClass(jQuery.support.fileInput ? undefined : 'disabled');
EOD;
            $cs->registerScript(__CLASS__ . '#' . $id, $script);
        }
    }
} 
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
class TbFileUpload extends CInputWidget
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
     * @var array options that are passed to the plugin.
     */
    public $pluginOptions = array();

    /**
     * @var string path to widget assets.
     */
    public $assetPath;

    /**
     * @var bool whether to register the associated JavaScript script files.
     */
    public $registerJs = true;

    /**
     * @var bool whether to bind the plugin to the associated dom element.
     */
    public $bindPlugin = true;

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
        TbArray::defaultValue('dataType', 'json', $this->pluginOptions);
    }

    /**
     * Runs the widget.
     */
    public function run()
    {
        list($name, $id) = $this->resolveNameID();
        $this->resolveId($id);

        $this->pluginOptions['url'] = $this->url;
        if (!$this->bindPlugin) {
            $this->htmlOptions['data-plugin'] = 'fileupload';
            $this->htmlOptions['data-plugin-options'] = CJSON::encode($this->pluginOptions);
        }

        if ($this->hasModel()) {
            $input = TbHtml::activeFileField($this->model, $this->attribute, $this->htmlOptions);
        } else {
            $input = TbHtml::fileField($name, $this->value, $this->htmlOptions);
        }
        echo TbHtml::tag('span', $this->buttonOptions, $this->label . ' ' . $input);

        if ($this->assetPath !== false) {
            $this->publishAssets($this->assetPath);
            $this->registerCssFile('css/jquery.fileupload-ui.css');

            if ($this->registerJs) {
                $this->getClientScript()->registerCoreScript('jquery');
                $this->registerScriptFile('js/vendor/jquery.ui.widget.js', CClientScript::POS_END);
                $this->registerScriptFile('js/jquery.iframe-transport.js', CClientScript::POS_END);
                $this->registerScriptFile('js/jquery.fileupload.js', CClientScript::POS_END);
            }
        }

        if ($this->bindPlugin) {
            $options = !empty($this->pluginOptions) ? CJavaScript::encode($this->pluginOptions) : '';
            $script = <<<EOD
jQuery('#{$id}')
    .fileupload({$options})
    .prop('disabled', !jQuery.support.fileInput)
    .parent().addClass(jQuery.support.fileInput ? undefined : 'disabled');
EOD;
            $this->getClientScript()->registerScript(__CLASS__ . '#' . $id, $script, CClientScript::POS_END);
        }
    }
} 
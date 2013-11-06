<?php
/**
 * TbWysihtml5 class file.
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
class TbWysihtml5 extends CInputWidget
{
    /**
     * @var integer width of the text area (in pixels).
     */
    public $width = 800;

    /**
     * @var integer height of the text area (in pixels).
     */
    public $height = 300;

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
        Yii::import('bootstrap.behaviors.TbWidget');
        $this->attachBehavior('tbWidget', new TbWidget);
        if (!isset($this->assetPath)) {
            $this->assetPath = Yii::getPathOfAlias('vendor.jhollingworth.bootstrap-wysihtml5');
        }
        TbHtml::addCssStyle('width: ' . $this->width . 'px; height: ' . $this->height . 'px;', $this->htmlOptions);
    }

    /**
     * Runs the widget.
     */
    public function run()
    {
        list($name, $id) = $this->resolveNameID();
        $this->resolveId($id);

        if (!$this->bindPlugin) {
            $this->htmlOptions['data-plugin-options'] = CJSON::encode($this->pluginOptions);
        }

        if ($this->hasModel()) {
            echo CHtml::activeTextArea($this->model, $this->attribute, $this->htmlOptions);
        } else {
            echo CHtml::textArea($name, $this->value, $this->htmlOptions);
        }

        if ($this->assetPath !== false) {
            $this->publishAssets($this->assetPath);
            $this->registerCssFile('dist/bootstrap-wysihtml5-0.0.2.css');

            if ($this->registerJs) {
                $this->registerScriptFile('lib/js/wysihtml5-0.3.0.js', CClientScript::POS_END);
                $this->registerScriptFile('dist/bootstrap-wysihtml5-0.0.2.min.js', CClientScript::POS_END);
            }
        }

        if ($this->bindPlugin) {
            $options = !empty($this->pluginOptions) ? CJavaScript::encode($this->pluginOptions) : '';
            $this->getClientScript()->registerScript(
                __CLASS__ . '#' . $id,
                "jQuery('#{$id}').wysihtml5({$options});",
                CClientScript::POS_END
            );
        }
    }
}
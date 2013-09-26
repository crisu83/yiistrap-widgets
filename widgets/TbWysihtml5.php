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
     * @var array initial options that should be passed to the plugin.
     */
    public $options = array();

    /**
     * @var string path to widget assets.
     */
    public $assetPath;

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
        if (isset($this->htmlOptions['name'])) {
            $name = $this->htmlOptions['name'];
        }
        if ($this->hasModel()) {
            echo CHtml::activeTextArea($this->model, $this->attribute, $this->htmlOptions);
        } else {
            echo CHtml::textArea($name, $this->value, $this->htmlOptions);
        }
        $options = !empty($this->options) ? CJavaScript::encode($this->options) : '';
        $cs = $this->getClientScript();
        $this->publishAssets($this->assetPath);
        $this->registerCssFile('dist/bootstrap-wysihtml5-0.0.2.css');
        $this->registerScriptFile('lib/js/wysihtml5-0.3.0.js', CClientScript::POS_HEAD);
        $this->registerScriptFile('dist/bootstrap-wysihtml5-0.0.2.min.js', CClientScript::POS_HEAD);
        $cs->registerScript(__CLASS__ . '#' . $id, "jQuery('#{$id}').wysihtml5({$options});");
    }
}
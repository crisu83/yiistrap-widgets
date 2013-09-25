<?php
/**
 * BootstrapDateTimePicker class file.
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
class BootstrapDateTimePicker extends CInputWidget
{
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
            $this->assetPath = Yii::getPathOfAlias('vendor.smalot.bootstrap-datetimepicker');
        }
    }

    /**
     * Runs the widget.
     */
    public function run()
    {
        list($name, $id) = $this->resolveNameID();
        $id = $this->resolveId();

        if ($this->hasModel()) {
            echo TbHtml::activeTextField($this->model, $this->attribute, $this->htmlOptions);
        } else {
            echo TbHtml::textField($name, $this->value, $this->htmlOptions);
        }

        $options = !empty($this->options) ? CJavaScript::encode($this->options) : '';
        $this->publishAssets($this->assetPath);
        $this->registerCssFile('/css/datetimepicker.css');
        $this->registerScriptFile(
            '/js/' . $this->resolveScriptVersion('bootstrap-datetimepicker.js'),
            CClientScript::POS_HEAD
        );
        $this->getClientScript()->registerScript(__CLASS__ . '#' . $id, "jQuery('#{$id}').datetimepicker({$options});");
    }
}
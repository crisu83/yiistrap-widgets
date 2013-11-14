<?php
/**
 * TbSelect2 class file.
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
class TbSelect2 extends CInputWidget
{
    /**
     * @var array raw data (key=>value).
     */
    public $data = array();

    /**
     * @var boolean whether to create a normal select element or a hidden field.
     */
    public $asDropDownList = true;

    /**
     * @var array options that are passed to the plugin.
     */
    public $pluginOptions = array();

    /**
     * @var string path to widget assets.
     */
    public $assetPath;

    /**
     * @var bool whether to register the associated script files.
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
        if ($this->asDropDownList === false) {
            $this->data = $this->normalizeData($this->data);
        }
        if (!isset($this->assetPath)) {
            $this->assetPath = Yii::getPathOfAlias('vendor.ivaynberg.select2');
        }
        if (TbArray::popValue('block', $this->htmlOptions, false)) {
            TbHtml::addCssClass('input-block-level', $this->htmlOptions);
        }
    }

    /**
     * Runs the widget.
     */
    public function run()
    {
        list($name, $id) = $this->resolveNameID();
        $id = $this->resolveId($id);

        if (!$this->asDropDownList && !isset($this->pluginOptions['data'])) {
            $this->pluginOptions['data'] = $this->data;
        }
        if (!$this->bindPlugin) {
            $this->htmlOptions['data-plugin'] = 'select2';
            $this->htmlOptions['data-plugin-options'] = CJSON::encode($this->pluginOptions);
        }

        echo TbHtml::openTag('div', array('class' => 'select2'));
        if ($this->hasModel()) {
            if ($this->asDropDownList) {
                echo TbHtml::activeDropDownList($this->model, $this->attribute, $this->data, $this->htmlOptions);
            } else {
                echo TbHtml::activeHiddenField($this->model, $this->attribute, $this->htmlOptions);
            }
        } else {
            if ($this->asDropDownList) {
                echo TbHtml::dropDownList($name, $this->value, $this->data, $this->htmlOptions);
            } else {
                echo TbHtml::hiddenField($name, $this->value, $this->htmlOptions);
            }
        }
        echo '</div>';

        if ($this->assetPath !== false) {
            $this->publishAssets($this->assetPath);
            $this->registerCssFile('/select2.css');

            if ($this->registerJs) {
                $this->registerScriptFile('/select2.js', CClientScript::POS_END);
            }
        }

        if ($this->bindPlugin) {
            $options = !empty($this->pluginOptions) ? CJavaScript::encode($this->pluginOptions) : '';
            $this->getClientScript()->registerScript(
                __CLASS__ . '#' . $id,
                "jQuery('#{$id}').select2({$options});",
                CClientScript::POS_END
            );
        }
    }

    /**
     * Normalized the given data into the format supported by select2.
     * @param array $rawData the raw data (key=>value).
     * @return array the normalized data.
     * @see http://ivaynberg.github.io/select2/
     */
    protected function normalizeData($rawData)
    {
        $data = array();
        foreach ($rawData as $key => $value) {
            if (is_array($value)) {
                $item = array('text' => $key, 'children' => $this->normalizeData($rawData[$key]));
            } else {
                $item = array('id' => $key, 'text' => $value);
            }
            $data[] = $item;
        }
        return $data;
    }
}
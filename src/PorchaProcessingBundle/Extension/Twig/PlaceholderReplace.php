<?php
namespace PorchaProcessingBundle\Extension\Twig;

use PorchaProcessingBundle\Util\PlaceHolders;

class PlaceholderReplace
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function replaceWithFields($str, $form) {

        $vars = array();
        $columnPropertyMap = PlaceHolders::getKhatianPageColumnPropertyMapping();
        foreach (PlaceHolders::getFields() as $field => $label) {
            if (isset($columnPropertyMap[$field])) {
               $vars["[[".$field."]]"] = $this->renderField($field, $form[$columnPropertyMap[$field]]);
            }
        }

        return strtr($str, $vars);
    }

    public function renderField($name, $field) {
        return $this->container->get('templating')->render('PorchaProcessingBundle:Template:single_field.html.twig', array('name' => $name, 'field' => $field));
    }

    public function replaceWithData($str, $khatian, $searchParams = array()) {

        $vars = array();

        $placeholders = PlaceHolders::getFields();
        foreach ($khatian as $field => $value) {

            if (array_key_exists($field, $placeholders)) {
                $value = $this->displayMarkup($value);
                if (array_key_exists($field, $searchParams) and !empty($searchParams[$field])) {
                    $value = str_replace($searchParams[$field], '<span class="t-highlight">' . $searchParams[$field] . '</span>', $value);
                }
            } else {
                $value = '';
            }
            $vars["[[".$field."]]"] = $value;

        }

        return strtr($str, $vars);
    }

    private function displayMarkup($value) {
        // Added urlencode because ' ' break span entry-area-fraction makrup
        $value = str_replace(" ", '&nbsp;', urlencode($value));
        return urldecode(nl2br($value));
    }

    public function replaceWithTooltip($str, $form) {

        $vars = array();

        foreach (PlaceHolders::getFields() as $field => $label) {
            if (!empty($form[$field])) {
                //$vars["[[".$field."]]"] = '<span title="'.$label.'">[['.$field.']]</span>';
                $vars["[[".$field."]]"] = '';
            }
        }

        return strtr($str, $vars);
    }
}